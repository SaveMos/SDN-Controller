<?php

session_start();

require_once('../librerie_php/Dijkstra_Library.php');
require_once('../librerie_php/Algoritmi_Vari.php');

require_once("../classi_php/Controller_SDN.php");


$src_ip = intval($_POST["Sorg_IP_Addr_1"]) . "." . intval($_POST["Sorg_IP_Addr_2"]) . "." . intval($_POST["Sorg_IP_Addr_3"]) . "." . intval($_POST["Sorg_IP_Addr_4"]);
$dst_ip = intval($_POST["Dest_IP_Addr_1"]) . "." . intval($_POST["Dest_IP_Addr_2"]) . "." . intval($_POST["Dest_IP_Addr_3"]) . "." . intval($_POST["Dest_IP_Addr_4"]);

$dst_Mask = intval($_POST["Dest_Subnet_Mask_1"]) . "." . intval($_POST["Dest_Subnet_Mask_2"]) . "." . intval($_POST["Dest_Subnet_Mask_3"]) . "." . intval($_POST["Dest_Subnet_Mask_4"]);
$src_Mask = intval($_POST["Sorg_Subnet_Mask_1"]) . "." . intval($_POST["Sorg_Subnet_Mask_2"]) . "." . intval($_POST["Sorg_Subnet_Mask_3"]) . "." . intval($_POST["Sorg_Subnet_Mask_4"]);

$Controller = fixObject($_SESSION["Controller"]);

$SwitchList = $Controller->SwitchList;
$num_switch = count($SwitchList);

$Priority = intval($_POST["priority_flux"]);

$DevList = $Controller->DeviceList;
$num_dev = count($DevList);

$SwitchLinkList = $Controller->InterSwitchLinkList;

$Position = json_decode($_POST["PositionArray"]); // Prelevo l'array degli switch obbligatori.
$num_checked = count($Position);

if (isset($_POST["Other_Option_Bidirezionale_check"])) {
    $Bidirezionale = 1;
} else {
    $Bidirezionale = 0;
}

if ($num_checked > 0) {
    $SwitchChecked = array();
    for ($i = 0; $i < $num_checked; $i++) {
        $t = get_object_vars($SwitchList[$Position[$i]]);
        $SwitchChecked[$i] = $t['DPID'];
    }
}

$Indice_Switch_Sorgente = -1;
$Indice_Switch_Destinatario = -1;

$ind = SearchHostByIPAddr($DevList, $src_ip);

if ($ind < 0) {
    $_SESSION["esito"] = 0;
    $_SESSION["esito_msg"] = "Host Sorgente Inesistente!";
    header("Location: ModificaFlusso.php ", true, 302);
    exit();
} else {
    $Host_Sorgente =  fixObject($DevList[$ind]);
    $Switch_Sorgente = $Host_Sorgente->Get_My_Switch();
    $Indice_Switch_Sorgente = Search_Switch($SwitchList, $Switch_Sorgente);
}

//echo $Indice_Switch_Sorgente;

$ind = SearchHostByIPAddr($DevList, $dst_ip);

if ($ind < 0) {
    $_SESSION["esito"] = 0;
    $_SESSION["esito_msg"] = "Host Destinatario Inesistente!";
    header("Location: ModificaFlusso.php ", true, 302);
    exit();
} else {
    $Host_Destinatario =  fixObject($DevList[$ind]);
    $Switch_Destinatario = $Host_Destinatario->Get_My_Switch();
    $Indice_Switch_Destinatario = Search_Switch($SwitchList, $Switch_Destinatario);
}

AggiungiRegola($Controller, $Priority, $Indice_Switch_Sorgente, $Indice_Switch_Destinatario, $Position, $Host_Sorgente, $Host_Destinatario);

if ($Bidirezionale == true) {
    $Position_Reverse = (is_null($Position)) ? $Position : array_reverse($Position);
    AggiungiRegola($Controller, $Priority,  $Indice_Switch_Destinatario, $Indice_Switch_Sorgente, $Position_Reverse,  $Host_Destinatario, $Host_Sorgente);
}

header("Location: ModificaFlusso.php ", true, 302);
exit();

function Search_Switch($SwitchList, $dpid)
{
    $c = count($SwitchList);

    for ($i = 0; $i < $c; $i++) {
        $r = fixObject($SwitchList[$i]);

        if ($r->CheckDPID($dpid)) {
            return $i;
        }
    }
    return -1;
}

function AggiungiRegola($Controller, $Priority, $Indice_Switch_Sorgente, $Indice_Switch_Destinatario, $Position, $Host_Sorgente, $Host_Destinatario)
{
    $Path = SPF($Controller->graph, $Indice_Switch_Sorgente, $Indice_Switch_Destinatario, $Position);

    // Creazione delle Istruzioni
    $Num = count($Path);

    $Switch_i = 0;
    $Name = 0;
    $res = 0;

    if ($Num <= 0) {
        $_SESSION["esito"] = 0;
        $_SESSION["esito_msg"] = "Errore Sconosciuto";
        header("Location: ModificaFlusso.php ", true, 302);
        exit();
    }

    if ($Num == 1) {
        // CASO PARTICOLARE --> La sorgente e la Destinazione si trovano nello stesso switch
        $Name = "flow_mod_" . (rand(1000, 9999)) . "_" . (1);
        $in_port = $Host_Sorgente->Get_My_Switch_Port();
        $out_port = $Host_Destinatario->Get_My_Switch_Port();
        $ports = [$in_port, $out_port];
        $Switch_i = fixObject($Controller->SwitchList[$Path[0]]);

        $command = array(
            "switch" => $Switch_i->DPID,
            "name" => $Name,
            "cookie" => 0,
            "priority" => $Priority,
            "in_port" => $ports[0],
            "active" => true,
            "actions" => ("output=" . $ports[1])
        );

        $res = $Controller->Insert_Flux($command);
    }

    if ($Num == 2) {
        // CASO PARTICOLARE --> La sorgente e la destinazione sono in due switch direttamente collegati
        $in_port = $Host_Sorgente->Get_My_Switch_Port();
        $out_port = $Host_Destinatario->Get_My_Switch_Port();

        $Name = "flow_mod_" . (rand(1000, 9999)) . "_" . (1);

        $Switch_i_sorg = fixObject($Controller->SwitchList[$Path[0]]);
        $Switch_i_dest = fixObject($Controller->SwitchList[$Path[1]]);

        $ports = Get_Ports_to_these_Switch($Controller->InterSwitchLinkList, $Switch_i_sorg->DPID, $Switch_i_dest->DPID);

        // Primo Switch
        $porte = [$in_port, $ports[0]];

        $command = array(
            "switch" => $Switch_i_sorg->DPID,
            "name" => $Name,
            "cookie" => 0,
            "priority" => $Priority,
            "in_port" => $porte[0],
            "active" => true,
            "actions" => ("output=" . $porte[1])
        );

        $res = $Controller->Insert_Flux($command);

        // Secondo Switch
        $Name = "flow_mod_" . (rand(1000, 9999)) . "_" . (2);

        $porte = [$ports[1], $out_port];

        $command = array(
            "switch" => $Switch_i_dest->DPID,
            "name" => $Name,
            "cookie" => 0,
            "priority" => $Priority,
            "in_port" => $porte[0],
            "active" => true,
            "actions" => ("output=" . $porte[1])
        );

        $res = $Controller->Insert_Flux($command);
    }

    if ($Num >= 3) {
        for ($i = 0; $i < $Num; $i++) {
            // CASO GENERICO
            $Name = "flow_mod_" . (rand(1000, 9999)) . "_" . ($i + 1);
            if ($i == 0) {
                $in_port = $Host_Sorgente->Get_My_Switch_Port();

                $Switch_i = fixObject($Controller->SwitchList[$Path[$i]]);
                $Switch_i_next = fixObject($Controller->SwitchList[$Path[$i + 1]]);

                $ports = Get_Ports_to_these_Switch($Controller->InterSwitchLinkList, $Switch_i->DPID, $Switch_i_next->DPID);

                $ports[1] = $ports[0];
                $ports[0] = $in_port; // porta da cui ricevo i messaggi dell'host sorgente.

                //echo "Host Sorgente => ". $Switch_i->DPID.": ";print_r($ports); echo "<br>";
            }

            if ($i > 0 && $i < $Num - 1) {
                $Switch_i_prev = fixObject($Controller->SwitchList[$Path[$i - 1]]);
                $Switch_i = fixObject($Controller->SwitchList[$Path[$i]]);
                $Switch_i_next = fixObject($Controller->SwitchList[$Path[$i + 1]]);

                $ports_prev = Get_Ports_to_these_Switch($Controller->InterSwitchLinkList, $Switch_i_prev->DPID, $Switch_i->DPID);
                $ports_next = Get_Ports_to_these_Switch($Controller->InterSwitchLinkList, $Switch_i->DPID, $Switch_i_next->DPID);

                $ports = [$ports_prev[1], $ports_next[0]];

                //  echo $Switch_i->DPID.": ";print_r($ports);echo "<br>";
            }

            if ($i == $Num - 1) {
                $out_port = $Host_Destinatario->Get_My_Switch_Port();

                $Switch_i_prev = fixObject($Controller->SwitchList[$Path[$i - 1]]);
                $Switch_i = fixObject($Controller->SwitchList[$Path[$i]]);

                $ports = Get_Ports_to_these_Switch($Controller->InterSwitchLinkList, $Switch_i_prev->DPID, $Switch_i->DPID);

                $ports[0] = $ports[1];
                $ports[1] = $out_port; // porta da cui ricevo i messaggi dell'host sorgente.

                // echo "Host Destinatario => ". $Switch_i->DPID.": ";print_r($ports);echo "<br>";
            }

            $command = array(
                "switch" => $Switch_i->DPID,
                "name" => $Name,
                "cookie" => 0,
                "priority" => $Priority,
                "in_port" => $ports[0],
                "active" => true,
                "actions" => ("output=" . $ports[1])
            );

            $res = $Controller->Insert_Flux($command);
        }
    }

    if ($res == '{"status" : "Entry pushed"}' || $res == '') {
        $cod = 1;
    } else {
        $cod = 0;
        $_SESSION["esito_msg"] = "La regola non e' stata inserita! => " . $res . ".";
    }

    $_SESSION["esito"] = $cod;
}
