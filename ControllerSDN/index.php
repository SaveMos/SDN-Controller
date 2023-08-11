<?php

require_once("librerie_php/REST_API_Library.php");
require_once("librerie_php/Dijkstra_Library.php");
require_once("classi_php/Switch_SDN.php");
require_once("classi_php/Host_SDN.php");

$CONTROLLER_IP = "192.168.1.30";

//print_r($DeviceList['devices']);

//$DeviceList['devices'][0]->mac[0]
$SwitchList = json_decode(getSwitchList($CONTROLLER_IP));
$SwitchListDim = count($SwitchList);

$SwitchList_Definitive = array();
for ($i = 0; $i < $SwitchListDim; $i++) {
    $SwitchList_Definitive[$i] = new Switch_SDN($SwitchList[$i]->switchDPID, $SwitchList[$i]->inetAddress);
    //$SwitchList_Definitive[$i]->Print_Switch();
}

$InterSwitchLinkList = getInterSwitchLinkList($CONTROLLER_IP);
$InterSwitchLinkList = str_replace('-', '_', $InterSwitchLinkList);
// Nel json i nomi degli attributi contenevano il '-' il quale mandava in confusione il sistema
// lo rimpiazzo con il '_'
$InterSwitchLinkList = json_decode($InterSwitchLinkList);

$Num_of_InterSwitchLinkList = count($InterSwitchLinkList);

$InterSwitchLinkList_Definitive = array();

for ($i = 0; $i < $Num_of_InterSwitchLinkList; $i++) {
    $InterSwitchLinkList_Definitive[$i] = new CollegamentoInterSwitch(
        $InterSwitchLinkList[$i]->src_switch,
        $InterSwitchLinkList[$i]->dst_switch,
        $InterSwitchLinkList[$i]->src_port,
        $InterSwitchLinkList[$i]->dst_port,
        false,
        $InterSwitchLinkList[$i]->latency
    );

    if ($InterSwitchLinkList[$i]->direction == "bidirectional") {
        $InterSwitchLinkList_Definitive[$i]->bidirezionale = true;
    }
}

//print_r($InterSwitchLinkList_Definitive);

$DeviceList = get_object_vars(json_decode(getDeviceList($CONTROLLER_IP)));
$Num_Element = count($DeviceList['devices']);
$cleanDeviceList = array();
$cleanDeviceList_Dim = 0;

for ($i = 0; $i < $Num_Element; $i++) {
    if (
        count($DeviceList['devices'][$i]->mac) == 0
        ||  count($DeviceList['devices'][$i]->ipv4) == 0
        ||  count($DeviceList['devices'][$i]->attachmentPoint) == 0
    ) {
        continue;  // Ignoro gli Host con attributi "Anomali"
    }
    $cleanDeviceList[$cleanDeviceList_Dim] = $DeviceList['devices'][$i];
    $cleanDeviceList_Dim++;
}
echo "Sono stati rilevati " . $cleanDeviceList_Dim . " dispositivi Host validi. <br>";

$DeviceList_Definitiva = array();

for ($i = 0; $i < $cleanDeviceList_Dim; $i++) {
    $DeviceList_Definitiva[$i] = new Host_SDN(
        $cleanDeviceList[$i]->mac[0],
        $cleanDeviceList[$i]->ipv4[0],
        $cleanDeviceList[$i]->ipv6[0],
        $cleanDeviceList[$i]->vlan[0],
        $cleanDeviceList[$i]->attachmentPoint
    );
}

$graph = array();
for ($i = 0; $i < $SwitchListDim; $i++) {
    $graph[$i] = array();
}

for ($i = 0; $i < $SwitchListDim; $i++) {
    for ($j = 0; $j < $SwitchListDim; $j++) {

        if ($i > $j) {
            continue;
        }
        if ($i == $j) {
            $graph[$i][$j] = 0;
        } else {
            $switch_i = $SwitchList_Definitive[$i]->DPID;
            $switch_j = $SwitchList_Definitive[$j]->DPID;

            $graph[$i][$j] = Search_InterSwitch_Link($switch_i, $switch_j, $InterSwitchLinkList_Definitive);
            $graph[$j][$i] =  $graph[$i][$j];
            // echo  $switch_i." -- ". $switch_j." ==> ".$graph[$i][$j] . "<br>";
        }
    }
}

//PrintMatrix($graph, $SwitchListDim);

for ($i = 0; $i < $SwitchListDim; $i++) {
    echo "[" . $i . "]  " . $SwitchList_Definitive[$i]->DPID . "<br>";
}

$NodiObbligati = [2];

/*
Dijkstra($graph, 1, 1, $NodiObbligati);
Dijkstra($graph, 1, 5, $NodiObbligati);
Dijkstra($graph, 3, 4, $NodiObbligati);
Dijkstra($graph, 2, 1, $NodiObbligati);
Dijkstra($graph, 5, 2, $NodiObbligati);
*/

Dijkstra2($graph, 1, 3, $NodiObbligati);

//PrintMatrix($graph, $SwitchListDim , $SwitchList_Definitive);

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width = device-width">
    <link rel="stylesheet" href="style/style.css">
    <script src="js_scripts/configurator.js"></script>
    <title>SDN Configurator</title>
</head>

<body>

    <h1>SDN Configurator</h1>
    <p>Cosa vorresti fare?</p>

    <p> <a class="option_link" href="php_scripts/ModificaACL.php">Modifica una ACL</a> </p>

    <p> <a class="option_link" href="php_scripts/ModificaFW.php">Modifica il Firewall</a> </p>


</body>

</html>

<?php


// Funzioni
function PrintMatrix($matr, $dim)
{
    for ($i = 0; $i < $dim; $i++) {
        for ($j = 0; $j < $dim; $j++) {

            echo $matr[$i][$j] . "\t";
        }
        echo "<br>";
    }
}

function Search_InterSwitch_Link($s1, $s2, $linkList)
{
    $num = count($linkList);
    for ($i = 0; $i < $num; $i++) {
        if (
            ($linkList[$i]->srg_DPID == $s1 && $linkList[$i]->dst_DPID == $s2)
            ||
            ($linkList[$i]->srg_DPID == $s2 && $linkList[$i]->dst_DPID == $s1)
        ) {
            return $linkList[$i]->latenza;
        }
    }
    return '-';
}


?>