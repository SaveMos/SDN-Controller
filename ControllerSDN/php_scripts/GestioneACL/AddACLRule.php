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

$act = SecureTextInput($_POST["insertACL_action"]);

$prot = SecureTextInput($_POST["insertACL_Protocol"]);

$cod = 0;
$res = 0;

if (
    ($act == "allow" || $act == "deny")
    && (SecureSubnetMask($dst_Mask))
    && (SecureSubnetMask($src_Mask))
    && (SecureIPAddress($dst_ip))
    && (SecureIPAddress($src_ip))
) {  
   
    $command = CreaComando($src_ip, $src_Mask, $dst_ip, $dst_Mask, $act , $prot);
    $res = $Controller->InsertACLRule($command);
}

if ($res == '{"status" : "Success! New rule added."}') {
    $cod = 1;
    $_SESSION["esito_msg"] = "La regola e' stata inserita! => <br>" . $res . ".";
} else {
    $cod = 0;
    $_SESSION["esito_msg"] = "La regola non e' stata inserita! => <br>" . $res . ".";
}

$_SESSION["esito"] = $cod;
header("Location: ModificaACL.php ", true, 302);
exit();


function CreaComando($src_ip, $src_mask, $dst_ip, $dst_mask,  $action , $prot)
{
    
    $command = array(
        "src-ip" => ($src_ip . "/" . $src_mask),
        "dst-ip" => ($dst_ip . "/" . $dst_mask),
        "action" => $action,
        "nw-proto" => $prot
    );

   // $command = "{'src-ip':'".($src_ip . "/" . $src_mask)."','dst-ip':'".($dst_ip . "/" . $dst_mask)."','action':'".$action."'}";

    return $command;
}
