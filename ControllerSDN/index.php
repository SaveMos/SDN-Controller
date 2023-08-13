<?php

require_once("librerie_php/REST_API_Library.php");
require_once("librerie_php/Dijkstra_Library.php");
require_once("librerie_php/Algoritmi_Vari.php");

require_once("classi_php/Switch_SDN.php");
require_once("classi_php/Host_SDN.php");

session_start();

$CONTROLLER_IP = "192.168.1.30";

//$DeviceList['devices'][0]->mac[0]

// Creazione della SwitchList, ossia la lista degli switch nella rete.
$SwitchList = json_decode(getSwitchList($CONTROLLER_IP));
$SwitchListDim = count($SwitchList);

$SwitchList_Definitive = array();
for ($i = 0; $i < $SwitchListDim; $i++) {
    $SwitchList_Definitive[$i] = new Switch_SDN($SwitchList[$i]->switchDPID, $SwitchList[$i]->inetAddress);
}

// Ordinamento della Switch list in senso crescente in base al DPID.
usort($SwitchList_Definitive , 'Comparatore_DPID');

// Creazione della lista dei collegamenti InterSwitch, ossia link che collegano due switch tra loro.
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

$DeviceList_Definitiva = array();

// Creazione della Device List, ossia la Lista degli Host.
for ($i = 0; $i < $cleanDeviceList_Dim; $i++) {
    $DeviceList_Definitiva[$i] = new Host_SDN(
        $cleanDeviceList[$i]->mac[0],
        $cleanDeviceList[$i]->ipv4[0],
        $cleanDeviceList[$i]->ipv6[0],
        $cleanDeviceList[$i]->vlan[0],
        $cleanDeviceList[$i]->attachmentPoint
    );
}

// Creazione della matrice di rappresentazione della topologia della rete.
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


$_SESSION["grafo"] = $graph;
$_SESSION["SwitchList"] = $SwitchList_Definitive;
$_SESSION["DeviceList"] = $DeviceList_Definitiva;
$_SESSION["SwitchLinkList"] = $InterSwitchLinkList_Definitive;

 //$Nodi_Obbligati = [1 , 3];
 //Print_Path(SPF($graph, 2, 4, $Nodi_Obbligati) , 2 , 4);

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

    <p> <a class="option_link" href="php_scripts/ModificaFlusso.php">Modifica un Flusso</a> </p>

    <p> <a class="option_link" href="php_scripts/ModificaACL.php">Modifica una ACL</a> </p>

    <p> <a class="option_link" href="php_scripts/ModificaFW.php">Modifica il Firewall</a> </p>


</body>

</html>

<?php


echo "<br>########################### INFO ########################### <br><br>";

echo "Sono stati rilevati " . $cleanDeviceList_Dim . " dispositivi Host validi. <br>";

echo "<br>############################################################ <br><br>";



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
    $No_Link = 99999;

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
    return $No_Link;
}


?>