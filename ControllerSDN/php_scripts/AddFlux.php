<?php

session_start();

require_once('../librerie_php/REST_API_Library.php');
require_once('../librerie_php/Dijkstra_Library.php');
require_once('../librerie_php/Algoritmi_Vari.php');
require_once('../classi_php/Host_SDN.php');
require_once('../classi_php/Switch_SDN.php');

$src_ip = intval($_POST["Sorg_IP_Addr_1"]) . "." . intval($_POST["Sorg_IP_Addr_2"]) . "." . intval($_POST["Sorg_IP_Addr_3"]) . "." . intval($_POST["Sorg_IP_Addr_4"]);
$dst_ip = intval($_POST["Dest_IP_Addr_1"]) . "." . intval($_POST["Dest_IP_Addr_2"]) . "." . intval($_POST["Dest_IP_Addr_3"]) . "." . intval($_POST["Dest_IP_Addr_4"]);

$dst_Mask = intval($_POST["Dest_Subnet_Mask_1"]) . "." . intval($_POST["Dest_Subnet_Mask_2"]) . "." . intval($_POST["Dest_Subnet_Mask_3"]) . "." . intval($_POST["Dest_Subnet_Mask_4"]);
$src_Mask = intval($_POST["Sorg_Subnet_Mask_1"]) . "." . intval($_POST["Sorg_Subnet_Mask_2"]) . "." . intval($_POST["Sorg_Subnet_Mask_3"]) . "." . intval($_POST["Sorg_Subnet_Mask_4"]);

$SwitchList = $_SESSION["SwitchList"];
$num_switch = count($SwitchList);

$DevList = $_SESSION["DeviceList"];
$num_dev = count($DevList);

$SwitchLinkList = $_SESSION["SwitchLinkList"];

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


// Calcolo del Percorso Ottimo
$graph = $_SESSION["grafo"];
$Path = SPF($graph, $Indice_Switch_Sorgente, $Indice_Switch_Destinatario, $Position);


// Creazione delle Istruzioni
$Num = count($Path);

//print_r($Path);echo $Num . "<br>";

$Switch_i = 0;
$Name = 0;

if ($Num <= 0) {
    $_SESSION["esito"] = 0;
    $_SESSION["esito_msg"] = "Errore Sconosciuto";
    header("Location: ModificaFlusso.php ", true, 302);
    exit();
}

if ($Num == 1) {
    
        // CASO PARTICOLARE --> La sorgente e la Destinazione si trovano nello stesso switch
        $Name = "flow-mod-" . ($_SESSION['Number_Flux']) . "_" . (1) . "_" . (rand(0, 800));
        $in_port = $Host_Sorgente->Get_My_Switch_Port();
        $out_port = $Host_Destinatario->Get_My_Switch_Port();
        $ports = [$in_port, $out_port];
        $Switch_i = fixObject($SwitchList[$Path[0]]);

        $command = array(
            "switch" => $Switch_i->DPID,
            "name" => $Name,
            "cookie" => 0,
            "priority" => $Priority,
            "in_port" => $ports[0],
            "active" => true,
            "actions" => ("output=" . $ports[1])
        );

        $res = Insert_Flux($_SESSION["IP_Controller"], $command);
    
}


if ($Num == 2) {
    // CASO PARTICOLARE --> La sorgente e la destinazione sono in due switch direttamente collegati
    $in_port = $Host_Sorgente->Get_My_Switch_Port();
    $out_port = $Host_Destinatario->Get_My_Switch_Port();

    $Name = "flow-mod-" . ($_SESSION['Number_Flux']) . "_" . (1) . "_" . (rand(0, 800));

    $Switch_i_sorg = fixObject($SwitchList[$Path[0]]);
    $Switch_i_dest = fixObject($SwitchList[$Path[1]]);

    $ports = Get_Ports_to_these_Switch($SwitchLinkList, $Switch_i_sorg->DPID, $Switch_i_dest->DPID);

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

    $res = Insert_Flux($_SESSION["IP_Controller"], $command);

    // Secondo Switch
    $Name = "flow-mod-" . ($_SESSION['Number_Flux']) . "_" . (2) . "_" . (rand(0, 800));

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

    $res = Insert_Flux($_SESSION["IP_Controller"], $command);
}


if ($Num >= 3) {
    for ($i = 0; $i < $Num; $i++) {
        // CASO GENERICO
        $Name = "flow-mod-" . ($_SESSION['Number_Flux']) . "_" . ($i + 1) . "_" . (rand(0, 800));
        if ($i == 0) {
            $in_port = $Host_Sorgente->Get_My_Switch_Port();

            $Switch_i = fixObject($SwitchList[$Path[$i]]);
            $Switch_i_next = fixObject($SwitchList[$Path[$i + 1]]);

            $ports = Get_Ports_to_these_Switch($SwitchLinkList, $Switch_i->DPID, $Switch_i_next->DPID);

            $ports[1] = $ports[0];
            $ports[0] = $in_port; // porta da cui ricevo i messaggi dell'host sorgente.

            //echo "Host Sorgente => ". $Switch_i->DPID.": ";print_r($ports); echo "<br>";
        }

        if ($i > 0 && $i < $Num - 1) {
            $Switch_i_prev = fixObject($SwitchList[$Path[$i - 1]]);
            $Switch_i = fixObject($SwitchList[$Path[$i]]);
            $Switch_i_next = fixObject($SwitchList[$Path[$i + 1]]);

            $ports_prev = Get_Ports_to_these_Switch($SwitchLinkList, $Switch_i_prev->DPID, $Switch_i->DPID);
            $ports_next = Get_Ports_to_these_Switch($SwitchLinkList, $Switch_i->DPID, $Switch_i_next->DPID);

            $ports = [$ports_prev[1], $ports_next[0]];

            //  echo $Switch_i->DPID.": ";print_r($ports);echo "<br>";
        }

        if ($i == $Num - 1) {
            $out_port = $Host_Destinatario->Get_My_Switch_Port();

            $Switch_i_prev = fixObject($SwitchList[$Path[$i - 1]]);
            $Switch_i = fixObject($SwitchList[$Path[$i]]);

            $ports = Get_Ports_to_these_Switch($SwitchLinkList, $Switch_i_prev->DPID, $Switch_i->DPID);

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

        $res = Insert_Flux($_SESSION["IP_Controller"], $command);
    }
}





//print_r($res);echo "<br>";

$Position = json_decode($_POST["PositionArray"]); // Prelevo l'array degli switch obbligatori.


$temp = $Host_Sorgente;
$Host_Sorgente = $Host_Destinatario;
$Host_Destinatario = $temp;


if ($Bidirezionale == true) {

    $Position_Reverse = (is_null($Position)) ? $Position : array_reverse($Position);

    $Path = SPF($graph, $Indice_Switch_Destinatario, $Indice_Switch_Sorgente, $Position_Reverse);

    // Creazione delle Istruzioni
    $Num = count($Path);


    $Switch_i = 0;
    $Name = 0;

    if ($Num <= 0) {

        $_SESSION["esito"] = 0;
        $_SESSION["esito_msg"] = "Errore Sconosciuto";
        header("Location: ModificaFlusso.php ", true, 302);
        exit();
    }


    if ($Num == 1) {
        // CASO PARTICOLARE --> La sorgente e la Destinazione si trovano nello stesso switch
        $Name = "flow-mod-" . ($_SESSION['Number_Flux']) . "_" . (1) . "_" . (rand(0, 800));
        $in_port = $Host_Sorgente->Get_My_Switch_Port();
        $out_port = $Host_Destinatario->Get_My_Switch_Port();
        $ports = [$in_port, $out_port];
        $Switch_i = fixObject($SwitchList[$Path[0]]);

        $command = array(
            "switch" => $Switch_i->DPID,
            "name" => $Name,
            "cookie" => 0,
            "priority" => $Priority,
            "in_port" => $ports[0],
            "active" => true,
            "actions" => ("output=" . $ports[1])
        );

        $res = Insert_Flux($_SESSION["IP_Controller"], $command);
    }


    if ($Num == 2) {
        // CASO PARTICOLARE --> La sorgente e la destinazione sono in due switch direttamente collegati
        $in_port = $Host_Sorgente->Get_My_Switch_Port();
        $out_port = $Host_Destinatario->Get_My_Switch_Port();

        $Name = "flow-mod-" . ($_SESSION['Number_Flux']) . "_" . (1) . "_" . (rand(0, 800));

        $Switch_i_sorg = fixObject($SwitchList[$Path[0]]);
        $Switch_i_dest = fixObject($SwitchList[$Path[1]]);

        $ports = Get_Ports_to_these_Switch($SwitchLinkList, $Switch_i_sorg->DPID, $Switch_i_dest->DPID);

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

        $res = Insert_Flux($_SESSION["IP_Controller"], $command);

        // Secondo Switch
        $Name = "flow-mod-" . ($_SESSION['Number_Flux']) . "_" . (2) . "_" . (rand(0, 800));

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

        $res = Insert_Flux($_SESSION["IP_Controller"], $command);
    }


    if ($Num >= 3) {
        for ($i = 0; $i < $Num; $i++) {
            // CASO GENERICO
            $Name = "flow-mod-" . ($_SESSION['Number_Flux']) . "_" . ($i + 1) . "_" . (rand(0, 800));
            if ($i == 0) {
                $in_port = $Host_Sorgente->Get_My_Switch_Port();

                $Switch_i = fixObject($SwitchList[$Path[$i]]);
                $Switch_i_next = fixObject($SwitchList[$Path[$i + 1]]);

                $ports = Get_Ports_to_these_Switch($SwitchLinkList, $Switch_i->DPID, $Switch_i_next->DPID);

                $ports[1] = $ports[0];
                $ports[0] = $in_port; // porta da cui ricevo i messaggi dell'host sorgente.

                //echo "Host Sorgente => ". $Switch_i->DPID.": ";print_r($ports); echo "<br>";
            }

            if ($i > 0 && $i < $Num - 1) {
                $Switch_i_prev = fixObject($SwitchList[$Path[$i - 1]]);
                $Switch_i = fixObject($SwitchList[$Path[$i]]);
                $Switch_i_next = fixObject($SwitchList[$Path[$i + 1]]);

                $ports_prev = Get_Ports_to_these_Switch($SwitchLinkList, $Switch_i_prev->DPID, $Switch_i->DPID);
                $ports_next = Get_Ports_to_these_Switch($SwitchLinkList, $Switch_i->DPID, $Switch_i_next->DPID);

                $ports = [$ports_prev[1], $ports_next[0]];

                //  echo $Switch_i->DPID.": ";print_r($ports);echo "<br>";
            }

            if ($i == $Num - 1) {
                $out_port = $Host_Destinatario->Get_My_Switch_Port();

                $Switch_i_prev = fixObject($SwitchList[$Path[$i - 1]]);
                $Switch_i = fixObject($SwitchList[$Path[$i]]);

                $ports = Get_Ports_to_these_Switch($SwitchLinkList, $Switch_i_prev->DPID, $Switch_i->DPID);

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

            $res = Insert_Flux($_SESSION["IP_Controller"], $command);
        }
    }

    //print_r($res);echo "<br>";
}


if ($res == '{"status" : "Entry pushed"}') {
    $cod = 1;
} else {
    $cod = 0;
}

$_SESSION["esito"] = $cod;
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
