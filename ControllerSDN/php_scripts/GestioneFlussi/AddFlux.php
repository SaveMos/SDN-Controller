<?php

session_start();

require_once('../../librerie_php/Dijkstra_Library.php');
require_once('../../librerie_php/Algoritmi_Vari.php');

require_once("../../classi_php/Controller_SDN.php");


$src_ip = intval($_POST["Sorg_IP_Addr_1"]) . "." . intval($_POST["Sorg_IP_Addr_2"]) . "." . intval($_POST["Sorg_IP_Addr_3"]) . "." . intval($_POST["Sorg_IP_Addr_4"]);
$dst_ip = intval($_POST["Dest_IP_Addr_1"]) . "." . intval($_POST["Dest_IP_Addr_2"]) . "." . intval($_POST["Dest_IP_Addr_3"]) . "." . intval($_POST["Dest_IP_Addr_4"]);

$src_ip = SecureIPAddress($src_ip);
$dst_ip = SecureIPAddress($dst_ip);

$Controller = fixObject($_SESSION["Controller"]);

$SwitchList = $Controller->SwitchList;
$num_switch = count($SwitchList);

$Priority = SecureNumber($_POST["priority_flux"]);
$FlowName = SecureTextInput($_POST["flux_name"]);

if ($FlowName == "") {
    $FlowName = "flow-mod-";
} else {
    $FlowName = $FlowName . "-";
}

if ($Priority > 32767) {
    $Priority = 32767;
}

if ($Priority < 0) {
    $Priority = 0;
}

$DevList = $Controller->DeviceList;
$num_dev = count($DevList);

$SwitchLinkList = $Controller->InterSwitchLinkList;

$Position = json_decode($_POST["PositionArray"]); // Prelevo l'array degli switch obbligatori.
$num_checked = count($Position);

$Bidirezionale = 0;
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
    EndProgram();
} else {
    $Host_Sorgente =  fixObject($DevList[$ind]);
    $Switch_Sorgente = $Host_Sorgente->Get_My_Switch();
    $Indice_Switch_Sorgente = Search_Switch($SwitchList, $Switch_Sorgente);
}

$ind = SearchHostByIPAddr($DevList, $dst_ip);

if ($ind < 0) {
    $_SESSION["esito"] = 0;
    $_SESSION["esito_msg"] = "Host Destinatario Inesistente!";
    EndProgram();
} else {
    $Host_Destinatario =  fixObject($DevList[$ind]);
    $Switch_Destinatario = $Host_Destinatario->Get_My_Switch();
    $Indice_Switch_Destinatario = Search_Switch($SwitchList, $Switch_Destinatario);
}

$k1 = array_search($Indice_Switch_Sorgente, $Position);
$k2 = array_search($Indice_Switch_Destinatario, $Position);

if (($k1 !== FALSE) || ($k2 !== FALSE)) {
    if (($k1 !== FALSE) && ($k2 === FALSE)) {
        $_SESSION["esito"] = 0;
        $_SESSION["esito_msg"] = "ERRORE: Lo Switch Sorgente è presente nella lista degli 'Switch Obbligati', occorre rimuoverlo!";
    }

    if (($k1 === FALSE) && ($k2 !== FALSE)) {
        $_SESSION["esito"] = 0;
        $_SESSION["esito_msg"] = "ERRORE: Lo Switch Destinatario è presente nella lista degli 'Switch Obbligati', occorre rimuoverlo!";
    }

    if (($k1 !== FALSE) && ($k2 !== FALSE)) {
        $_SESSION["esito"] = 0;
        $_SESSION["esito_msg"] = "ERRORE: Lo Switch Sorgente E lo switch Destinatario sono presenti nella lista degli 'Switch Obbligati', occorre rimuoverli!";
    }

} else {

    AggiungiRegola($Controller, $Priority, $Indice_Switch_Sorgente, $Indice_Switch_Destinatario, $Position, $Host_Sorgente, $Host_Destinatario, $FlowName);

    if ($Bidirezionale == 1) {
        $Position_Reverse = (is_null($Position)) ? $Position : array_reverse($Position);
        $FlowName = $FlowName . "bidirect-";
        AggiungiRegola($Controller, $Priority,  $Indice_Switch_Destinatario, $Indice_Switch_Sorgente, $Position_Reverse,  $Host_Destinatario, $Host_Sorgente, $FlowName);
    }
}

EndProgram();

?>

<?php
// Funzioni

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

function AggiungiRegola($Controller, $Priority, $Indice_Switch_Sorgente, $Indice_Switch_Destinatario, $Position, $Host_Sorgente, $Host_Destinatario, $Prefisso)
{
    // echo "Position:"; echo $Indice_Switch_Sorgente. " "; print_r($Position); echo $Indice_Switch_Destinatario. " ";echo "<br>";



    $Path = SPF($Controller->graph, $Indice_Switch_Sorgente, $Indice_Switch_Destinatario, $Position);

    $ipv4_src = ($Host_Sorgente->IPv4_Addr);
    $ipv4_dst = ($Host_Destinatario->IPv4_Addr);

    // Creazione delle Istruzioni
    $Num = count($Path);

    $Separatore = "-";

    $Switch_i = 0;
    $Name = 0;
    $res = 0;

    if ($Num <= 0) {
        $_SESSION["esito"] = 0;
        $_SESSION["esito_msg"] = "Errore Sconosciuto";
        return "fallito";
    }

    if ($Num == 1) {
        // CASO PARTICOLARE --> La sorgente e la Destinazione si trovano nello stesso switch
        $Switch_i = fixObject($Controller->SwitchList[$Path[0]]);

        $in_port = $Host_Sorgente->Get_My_Switch_Port();
        $out_port = $Host_Destinatario->Get_My_Switch_Port();

        $Name = $Prefisso . (rand(1000, 9999)) . $Separatore . (1);

        $command = CreaComandoARP($Switch_i->DPID, $Name, $in_port, $out_port, $ipv4_src, $ipv4_dst);
        $res = $Controller->Insert_Flux($command);

        $command = CreaComando($Switch_i->DPID, $Name, $Priority, $in_port, $out_port, $ipv4_src, $ipv4_dst);
        $res = $Controller->Insert_Flux($command);
    }

    if ($Num == 2) {
        // CASO PARTICOLARE --> La sorgente e la destinazione sono in due switch direttamente collegati
        $in_port = $Host_Sorgente->Get_My_Switch_Port();
        $out_port = $Host_Destinatario->Get_My_Switch_Port();

        $Switch_i_sorg = fixObject($Controller->SwitchList[$Path[0]]);
        $Switch_i_dest = fixObject($Controller->SwitchList[$Path[1]]);

        $ports = Get_Ports_to_these_Switch($Controller->InterSwitchLinkList, $Switch_i_sorg->DPID, $Switch_i_dest->DPID);

        // Primo Switch
        $Name = $Prefisso . (rand(1000, 9999)) . $Separatore . (1);

        $command = CreaComandoARP($Switch_i_sorg->DPID, $Name, $in_port, $ports[0], $ipv4_src, $ipv4_dst);
        $res = $Controller->Insert_Flux($command);

        $command = CreaComando($Switch_i_sorg->DPID, $Name, $Priority, $in_port, $ports[0], $ipv4_src, $ipv4_dst);
        $res = $Controller->Insert_Flux($command);

        // Secondo Switch
        $Name = $Prefisso . (rand(1000, 9999)) . $Separatore . (2);

        $command = CreaComandoARP($Switch_i_dest->DPID, $Name, $ports[1], $out_port, $ipv4_src, $ipv4_dst);
        $res = $Controller->Insert_Flux($command);

        $command = CreaComando($Switch_i_dest->DPID, $Name, $Priority, $ports[1], $out_port, $ipv4_src, $ipv4_dst);
        $res = $Controller->Insert_Flux($command);
    }

    if ($Num >= 3) {
        for ($i = 0; $i < $Num; $i++) {
            // CASO GENERICO
            $Name = $Prefisso . (rand(1000, 9999)) . $Separatore . ($i + 1);
            if ($i == 0) {
                $in_port = $Host_Sorgente->Get_My_Switch_Port();

                $Switch_i = fixObject($Controller->SwitchList[$Path[$i]]);
                $Switch_i_next = fixObject($Controller->SwitchList[$Path[$i + 1]]);

                $ports = Get_Ports_to_these_Switch($Controller->InterSwitchLinkList, $Switch_i->DPID, $Switch_i_next->DPID);

                $ports[1] = $ports[0];
                $ports[0] = $in_port; // porta da cui ricevo i messaggi dell'host sorgente.
            }

            if ($i > 0 && $i < $Num - 1) {
                $Switch_i_prev = fixObject($Controller->SwitchList[$Path[$i - 1]]);
                $Switch_i = fixObject($Controller->SwitchList[$Path[$i]]);
                $Switch_i_next = fixObject($Controller->SwitchList[$Path[$i + 1]]);

                $ports_prev = Get_Ports_to_these_Switch($Controller->InterSwitchLinkList, $Switch_i_prev->DPID, $Switch_i->DPID);
                $ports_next = Get_Ports_to_these_Switch($Controller->InterSwitchLinkList, $Switch_i->DPID, $Switch_i_next->DPID);

                $ports = [$ports_prev[1], $ports_next[0]];
            }

            if ($i == $Num - 1) {
                $out_port = $Host_Destinatario->Get_My_Switch_Port();

                $Switch_i_prev = fixObject($Controller->SwitchList[$Path[$i - 1]]);
                $Switch_i = fixObject($Controller->SwitchList[$Path[$i]]);

                $ports = Get_Ports_to_these_Switch($Controller->InterSwitchLinkList, $Switch_i_prev->DPID, $Switch_i->DPID);

                $ports[0] = $ports[1];
                $ports[1] = $out_port; // porta da cui ricevo i messaggi dell'host sorgente.
            }

            $command = CreaComandoARP($Switch_i->DPID, $Name, $ports[0], $ports[1], $ipv4_src, $ipv4_dst);
            $res = $Controller->Insert_Flux($command);

            $command = CreaComando($Switch_i->DPID, $Name, $Priority, $ports[0], $ports[1], $ipv4_src, $ipv4_dst);
            $res = $Controller->Insert_Flux($command);
        }
    }

    if ($res == '{"status" : "Entry pushed"}') {
        $cod = 1;
    } else {
        $cod = 0;
        $_SESSION["esito_msg"] = "La regola non e' stata inserita! => <br>" . $res . ".";
    }

    $_SESSION["esito"] = $cod;
    return "ok";
}

function CreaComando($SwitchDPID, $Name, $Priority, $porta_in, $porta_out, $ipv4_src, $ipv4_dst, $Cookie = 0, $Active = true, $Action = "output")
{
    $command = array(
        "switch" => $SwitchDPID,
        "name" => $Name,

        "eth_type" => "0x0800", // Protocollo IPv4
        "ipv4_src" => ($ipv4_src . "/32"),
        "ipv4_dst" => ($ipv4_dst . "/32"),

        "cookie" => $Cookie,
        "priority" => $Priority, // Massima è 32767, minima è 0.
        "in_port" => $porta_in,
        "active" => $Active,

        "actions" => ($Action . "=" . $porta_out)
    );

    return $command;
}

function CreaComandoARP($SwitchDPID, $Name, $porta_in, $porta_out, $ipv4_src, $ipv4_dst)
{
    $command = array(
        "switch" => $SwitchDPID,
        "name" => $Name . "-ARP",

        "eth_type" => "0x0806", // Pacchetto ARP
        "arp_spa" => ($ipv4_src . "/32"),
        "arp_tpa" => ($ipv4_dst . "/32"),

        "cookie" => 0,
        "priority" => 30000,
        "in_port" => $porta_in,
        "active" => true,
        "actions" => ("output=" . $porta_out . "")
    );
    return $command;
}

function EndProgram()
{
   header("Location: ModificaFlusso.php ", true, 302);
    exit();
}


?>
