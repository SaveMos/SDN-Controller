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
    && (SecureSubnetMask($dst_Mask))
    && (SecureSubnetMask($src_Mask))
    && (SecureIPAddress($dst_ip))
    && (SecureIPAddress($src_ip))
) {  
   
    $command = CreaComando($src_ip, $src_Mask, $dst_ip, $dst_Mask, $prot , $srg_port , $dst_port , $dpid , $Priorita , $act);
    $res = $Controller->InsertFWRule($command);
    $Controller->UpdateNumber_OF_FW_Rules();
}

$_SESSION["esito_msg"] = $res;
header("Location: ModificaFW.php ", true, 302);
exit();


function CreaComando($src_ip, $src_mask, $dst_ip, $dst_mask, $prot, $srg_port = "0" , $dst_port = "0" , $dpid = "00:00:00:00:00:00:00:00" , $Priorita = "56" , $act = "DENY")
{
    
    $command = array(
        "src-ip" => ($src_ip . "/" . $src_mask),
        "dst-ip" => ($dst_ip . "/" . $dst_mask),
        "switchid" => $dpid,
        "nw-proto" => $prot,
        "tp-src" => $srg_port,
        "tp-dst" => $dst_port,
        "priority" => $Priorita,
        "action" => $act
    );

    return $command;
}
