<?php
require_once("../../../classi_php/Controller_SDN.php");
require_once('../../../librerie_php/Algoritmi_Vari.php');

session_start();

$Controller = $_SESSION["Controller"];
$Controller = fixObject($Controller);

$id = intval($_POST["Rule_Number_Delete"]);

$command = '{"ruleid":"'.$id.'"}';
$ret = $Controller->DeleteSingleACLRule($command);

if ($ret == '{"status" : "Success! Rule deleted"}') {
    $cod = 1;
    $_SESSION["esito_msg"] = "La regola e' stata eliminata con successo!";
} else {
    $cod = 0;
    $_SESSION["esito_msg"] = "La regola non e' stata eliminata! => <br>" . $ret . ".";
}

$_SESSION["esito"] = $cod;


header("Location: ../ModificaACL.php ", true, 302);
exit();

?>