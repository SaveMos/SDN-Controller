<?php

session_start();

require_once('../librerie_php/REST_API_Library.php');
require_once('../librerie_php/Algoritmi_Vari.php');
require_once('../classi_php/Host_SDN.php');
require_once('../classi_php/Switch_SDN.php');

$src_ip = intval($_POST["Sorg_IP_Addr_1"]) . "." . intval($_POST["Sorg_IP_Addr_2"]) . "." . intval($_POST["Sorg_IP_Addr_3"]) . "." . intval($_POST["Sorg_IP_Addr_4"]);
$src_Mask = intval($_POST["Sorg_Subnet_Mask_1"]) . "." . intval($_POST["Sorg_Subnet_Mask_2"]) . "." . intval($_POST["Sorg_Subnet_Mask_3"]) . "." . intval($_POST["Sorg_Subnet_Mask_4"]);

$dst_ip = intval($_POST["Dest_IP_Addr_1"]) . "." . intval($_POST["Dest_IP_Addr_2"]) . "." . intval($_POST["Dest_IP_Addr_3"]) . "." . intval($_POST["Dest_IP_Addr_4"]);
$dst_Mask = intval($_POST["Dest_Subnet_Mask_1"]) . "." . intval($_POST["Dest_Subnet_Mask_2"]) . "." . intval($_POST["Dest_Subnet_Mask_3"]) . "." . intval($_POST["Dest_Subnet_Mask_4"]);

$SwitchList = $_SESSION["SwitchList"];
$num_switch = count($SwitchList);
//print_r($SwitchList);

$DevList = $_SESSION["DeviceList"];
$num_dev = count($DevList);

$Position = json_decode($_POST["PositionArray"]);
$num_checked = count($Position);

if ($num_checked > 0) {
    $SwitchChecked = array();
    for ($i = 0; $i < $num_checked; $i++) {
        $t = get_object_vars($SwitchList[$Position[$i]]);
        $SwitchChecked[$i] = $t['DPID'];
    }
}


$ind = SearchHostByIPAddr($DevList, $src_ip);

$Host_Sorgente =  fixObject($DevList[$ind]);
$Switch_Sorgente = $Host_Sorgente->Get_My_Switch();
$Indice_Switch_Sorgente = Search_Switch($SwitchList , $Switch_Sorgente);
//echo $Indice_Switch_Sorgente;

$ind = SearchHostByIPAddr($DevList, $dst_ip);

$Host_Destinatario =  fixObject($DevList[$ind]);
$Switch_Destinatario = $Host_Destinatario->Get_My_Switch();
$Indice_Switch_Destinatario = Search_Switch($SwitchList , $Switch_Destinatario);

//echo $Indice_Switch_Sorgente ." ". $Indice_Switch_Destinatario."<br>";

function Search_Switch($SwitchList , $dpid){
    $c = count($SwitchList);

    for($i = 0 ; $i < $c ; $i++){
        $r = fixObject($SwitchList[$i]);
       
        if($r->CheckDPID($dpid)){
            return $i;
        }
    }
    return -1;
}



?>