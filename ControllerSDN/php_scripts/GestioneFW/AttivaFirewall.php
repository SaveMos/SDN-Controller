<?php
require_once("../../classi_php/Controller_SDN.php");
require_once('../../librerie_php/Algoritmi_Vari.php');

session_start();

$Controller = $_SESSION["Controller"];
$Controller = fixObject($Controller);

$Stat = intval($_POST["FW_Number_State"]);

if($Stat == 1){
    // Va Spento
    $ret = $Controller->EnableFirewall(false);
}else{
    // Va acceso
    $ret = $Controller->EnableFirewall(true);
}

header("Location: GestioneFW.php ", true, 302);
exit();

?>