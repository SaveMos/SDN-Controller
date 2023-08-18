<?php
require_once("../../../classi_php/Controller_SDN.php");
require_once('../../../librerie_php/Algoritmi_Vari.php');

session_start();

$Controller = $_SESSION["Controller"];
$Controller = fixObject($Controller);

$id = SecureNumber(intval($_POST["RuleName"]));

$command = '{"ruleid":"'.$id.'"}';
$ret = $Controller->DeleteSingleFWRule($command);

header("Location: ../ShowFW.php ", true, 302);
exit();

?>