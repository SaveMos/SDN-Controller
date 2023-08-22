<?php
require_once("../../../classi_php/Controller_SDN.php");
require_once('../../../librerie_php/Algoritmi_Vari.php');

session_start();

$Controller = $_SESSION["Controller"];
$Controller = fixObject($Controller);

echo $Controller->DeleteAllFlowRules();
?>