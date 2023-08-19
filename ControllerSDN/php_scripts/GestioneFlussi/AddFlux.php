<?php

session_start();

require_once('../../librerie_php/Dijkstra_Library.php');
require_once('../../librerie_php/Algoritmi_Vari.php');

require_once("../../classi_php/Controller_SDN.php");


$src_ip = intval($_POST["Sorg_IP_Addr_1"]) . "." . intval($_POST["Sorg_IP_Addr_2"]) . "." . intval($_POST["Sorg_IP_Addr_3"]) . "." . intval($_POST["Sorg_IP_Addr_4"]);
$dst_ip = intval($_POST["Dest_IP_Addr_1"]) . "." . intval($_POST["Dest_IP_Addr_2"]) . "." . intval($_POST["Dest_IP_Addr_3"]) . "." . intval($_POST["Dest_IP_Addr_4"]);

$Controller = fixObject($_SESSION["Controller"]);

$SwitchList = $Controller->SwitchList;
$num_switch = count($SwitchList);

$Priority = SecureNumber(intval($_POST["priority_flux"]));

$FlowName = SecureTextInput($_POST["flux_name"]);

if($FlowName == ""){
    $FlowName = "flow-mod-";
}

if($Priority > 32767){
    $Priority = 32767;
}

if($Priority < 0){
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

AggiungiRegola($Controller, $Priority, $Indice_Switch_Sorgente, $Indice_Switch_Destinatario, $Position, $Host_Sorgente, $Host_Destinatario, $FlowName);

if ($Bidirezionale == true) {
    $Position_Reverse = (is_null($Position)) ? $Position : array_reverse($Position);
    $FlowName = $FlowName."bidirect-";
    AggiungiRegola($Controller, $Priority,  $Indice_Switch_Destinatario, $Indice_Switch_Sorgente, $Position_Reverse,  $Host_Destinatario, $Host_Sorgente , $FlowName);
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

function AggiungiRegola($Controller, $Priority, $Indice_Switch_Sorgente, $Indice_Switch_Destinatario, $Position, $Host_Sorgente, $Host_Destinatario, $Prefisso)
{
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
        header("Location: ModificaFlusso.php ", true, 302);
        exit();
    }

    if ($Num == 1) {
        // CASO PARTICOLARE --> La sorgente e la Destinazione si trovano nello stesso switch
        $Switch_i = fixObject($Controller->SwitchList[$Path[0]]);

        $in_port = $Host_Sorgente->Get_My_Switch_Port();
        $out_port = $Host_Destinatario->Get_My_Switch_Port();

        $Name = $Prefisso . (rand(1000, 9999)) . $Separatore . (1);
        $command = CreaComando($Switch_i->DPID, $Name, $Priority, $in_port, $out_port, $ipv4_src, $ipv4_dst);

        $res = $Controller->Insert_Flux($command);
        //echo "Codice: ".$res."<br>";
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
        $command = CreaComando($Switch_i_sorg->DPID, $Name, $Priority, $in_port, $ports[0], $ipv4_src, $ipv4_dst);
        $res = $Controller->Insert_Flux($command);
        // echo "Codice: ".$res."<br>";

        // Secondo Switch
        $Name = $Prefisso . (rand(1000, 9999)) . $Separatore . (2);
        $command = CreaComando($Switch_i_dest->DPID, $Name, $Priority, $ports[1], $out_port, $ipv4_src, $ipv4_dst);
        $res = $Controller->Insert_Flux($command);
       // echo "Codice: ".$res."<br>";
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

            $command = CreaComando($Switch_i->DPID, $Name, $Priority, $ports[0], $ports[1], $ipv4_src, $ipv4_dst);
            $res = $Controller->Insert_Flux($command);
           // echo "Codice: ".$res."<br>";
           
        }
    }

    if ($res == '{"status" : "Entry pushed"}') {
        $cod = 1;
    } else {
        $cod = 0;
        $_SESSION["esito_msg"] = "La regola non e' stata inserita! => <br>" . $res . ".";
    }
  
    $_SESSION["esito"] = $cod;
}

function CreaComando($SwitchDPID, $Name, $Priority, $porta_in, $porta_out, $ipv4_src, $ipv4_dst, $Cookie = 0, $Active = true, $Action = "output")
{
    $command = array(
        "switch" => $SwitchDPID,
        "name" => $Name,
    
        //"eth_type" => 0x0800, // Protocollo IPv4
        //"ipv4_src" => "10.0.0.0/24", 
        //"ipv4_dst" => "10.0.0.0/24",
        
        "cookie" => $Cookie,
        "priority" => $Priority, // Massima è 32767, minima è 0.
        "in_port" => $porta_in,
        "active" => $Active,
       
        "actions" => (trim($Action) . "=" . $porta_out)
    );

    //$command = '{"switch":"'.$SwitchDPID.'", "name":"'.$Name.'","eth_type":"0x0800" , "cookie":"0", "priority":"32768", "in_port":"'.$porta_in.'","active":"true", "actions":"output="'.$porta_out.'"}';

    return $command;
}
