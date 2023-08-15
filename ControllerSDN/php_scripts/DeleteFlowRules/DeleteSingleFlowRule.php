<?php
// curl -X DELETE -d '{"name":"flow-mod-1"}' http://<controller_ip>:8080/wm/staticentrypusher/json

require_once("../../classi_php/Controller_SDN.php");
require_once('../../librerie_php/Algoritmi_Vari.php');

session_start();

$Controller = $_SESSION["Controller"];
$Controller = fixObject($Controller);

$command = array(
    "switch" => $_POST["DPID"],
    "name" => $_POST["RuleName"]
);

$ret = $Controller->DeleteSingleFlowRule($command);

print_r($ret);
header("Location: ../ShowFlussi.php ", true, 302);
exit();

?>