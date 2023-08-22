<?php
require_once("../../../classi_php/Controller_SDN.php");
require_once('../../../librerie_php/Algoritmi_Vari.php');

session_start();

$Controller = $_SESSION["Controller"];
$Controller = fixObject($Controller);

$id = SecureTextInput($_POST["RuleName"]);

$ret = $Controller->DeleteSingleFlowRule('{"name":"'.$id.'"}');

header("Location: ../ShowFlussi.php ", true, 302);
exit();
?>