<?php

require_once('../../classi_php/Controller_SDN.php');
require_once('../../librerie_php/Algoritmi_Vari.php');

session_start();

// Aggiornare il Controllore
$Controller = fixObject($_SESSION["Controller"]);
$Controller->Update_Controller();
$_SESSION["Controller"] = $Controller;

$src_ip = intval($_POST["Sorg_IP_Addr_1"]) . "." . intval($_POST["Sorg_IP_Addr_2"]) . "." . intval($_POST["Sorg_IP_Addr_3"]) . "." . intval($_POST["Sorg_IP_Addr_4"]);
$src_Mask = SecureNumber(intval($_POST["Sorg_Subnet_Mask"]));

$dst_ip = intval($_POST["Dest_IP_Addr_1"]) . "." . intval($_POST["Dest_IP_Addr_2"]) . "." . intval($_POST["Dest_IP_Addr_3"]) . "." . intval($_POST["Dest_IP_Addr_4"]);
$dst_Mask = SecureNumber(intval($_POST["Dest_Subnet_Mask"]));

$src_ip = trim($src_ip);
$dst_ip = trim($dst_ip);

$act = SecureTextInput($_POST["insertFW_action"]);
$prot = SecureTextInput($_POST["insertFW_Protocol"]);

$srg_port = SecureNumber($_POST["Srg_Port"]);
$dst_port = SecureNumber($_POST["Dest_Port"]);

$Priorita = SecureNumber($_POST["Priority"]);

$dpid = trim($_POST["SwitchFW"]);



$cod = 0;
$res = 0;



if (
    ($act == "ALLOW" || $act == "DENY")
    && ($prot == "ICMP" || $prot = "UDP" || $prot == "TCP" || $prot == "all")
    && (SecureSubnetMask($dst_Mask))
    && (SecureSubnetMask($src_Mask))
    && (SecureIPAddress($dst_ip))
    && (SecureIPAddress($src_ip))
) {

    if ($prot == "ICMP") {
        $command = CreaComandoARP($src_ip, $src_Mask, $dst_ip, $dst_Mask, $act);
        $res = $Controller->InsertFWRule($command);

        $command = CreaComandoICMP($src_ip, $src_Mask, $dst_ip, $dst_Mask, $act);
        $res = $Controller->InsertFWRule($command);

        $command = CreaComandoARP($dst_ip, $dst_Mask, $src_ip, $src_Mask, $act);
        $res = $Controller->InsertFWRule($command);

        $command = CreaComandoICMP($dst_ip, $dst_Mask, $src_ip, $src_Mask, $act);
        $res = $Controller->InsertFWRule($command);

    } elseif ($prot == "all") {
        $command = CreaComandoARP($src_ip, $src_Mask, $dst_ip, $dst_Mask, $act);
        $res = $Controller->InsertFWRule($command);

        $command = CreaComandoARP($dst_ip, $dst_Mask, $src_ip, $src_Mask, $act);
        $res = $Controller->InsertFWRule($command);

        $command = CreaComandoALL($src_ip, $src_Mask, $dst_ip, $dst_Mask, $act);
        $res = $Controller->InsertFWRule($command);

        $command = CreaComandoALL($dst_ip, $dst_Mask, $src_ip, $src_Mask, $act);
        $res = $Controller->InsertFWRule($command);
    } elseif($prot == "TCP" || $prot == "UDP"){
    }


    $Controller->UpdateNumber_OF_FW_Rules();
}

$_SESSION["esito_msg"] = $res;
header("Location: ModificaFW.php ", true, 302);
exit();

function CreaComandoALL($src_ip, $src_mask, $dst_ip, $dst_mask, $act = "ALLOW")
{
    // Permettere o negare flussi tra i due Host
    $command = array(
        "src-ip" => ($src_ip . "/" . $src_mask),
        "dst-ip" => ($dst_ip . "/" . $dst_mask),
        "action" => $act
    );
    return $command;
}


function CreaComandoARP($src_ip, $src_mask, $dst_ip, $dst_mask, $act = "ALLOW")
{
    // Permettere o negare ARP tra i due Host
    $command = array(
        "src-ip" => ($src_ip . "/" . $src_mask),
        "dst-ip" => ($dst_ip . "/" . $dst_mask),
        "dl-type" => "ARP",
        "action" => $act
    );
    return $command;
}


function CreaComandoICMP($src_ip, $src_mask, $dst_ip, $dst_mask, $act = "ALLOW")
{
    $command = array(
        "src-ip" => ($src_ip . "/" . $src_mask),
        "dst-ip" => ($dst_ip . "/" . $dst_mask),
        "nw-proto" => "ICMP",
        "action" => $act
    );
    return $command;
}

function CreaComando1($src_ip, $src_mask, $dst_ip, $dst_mask, $prot, $srg_port = "0", $dst_port = "0", $dpid = "00:00:00:00:00:00:00:00", $Priorita = 1, $act = "DENY")
{
    $command = array(
        "src-ip" => ($src_ip . "/" . $src_mask),
        "dst-ip" => ($dst_ip . "/" . $dst_mask),
        "switchid" => $dpid,
        "nw-proto" => $prot,
        "tp-src" => $srg_port,
        "tp-dst" => $dst_port,
        "priority" => $Priorita, // il numero più basso indica una priorità più alta.
        "action" => $act
    );

    return $command;
}
