<?php

require_once("librerie_php/Dijkstra_Library.php");
require_once("librerie_php/Algoritmi_Vari.php");

require_once("classi_php/Controller_SDN.php");

session_start();


if (isset($_POST["Controller_IP_Addr_1"]) || isset($_SESSION["IP_Controller"])) {

    $CONTROLLER_IP = 0;
    if (!isset($_SESSION["IP_Controller"])) {
        $CONTROLLER_IP = intval($_POST["Controller_IP_Addr_1"]) . "." . intval($_POST["Controller_IP_Addr_2"]) . "." . intval($_POST["Controller_IP_Addr_3"]) . "." . intval($_POST["Controller_IP_Addr_4"]);
    } else {
        $CONTROLLER_IP = $_SESSION["IP_Controller"];
    }

    $_SESSION["IP_Controller"] = $CONTROLLER_IP;

    $Controller = new Controller_SDN($CONTROLLER_IP);

    // Creazione della SwitchList, ossia la lista degli switch nella rete.
    $Controller->Update_SwitchList();
   
    // Creazione della lista dei collegamenti InterSwitch, ossia link che collegano due switch tra loro.
    $Controller->Update_InterSwitchLinkLIst();
    
    $Controller->Update_DeviceList();
    // Creazione della matrice di rappresentazione della topologia della rete.
    $Controller->Update_Graph();

    $_SESSION["Controller"] = $Controller;
   
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width = device-width">
    <link rel="stylesheet" href="style/style.css">
    <script type="text/javascript" src="js_scripts/configurator.js"></script>
    <title>SDN Configurator</title>
</head>

<body>

    <h1>SDN Configurator</h1>

    <form method="post" id="Insert_IP_Address_Form" name="Insert_IP_Address_Form">

        <label>Indirizzo IP del Controllore SDN:</label>
        <input id="Controller_IP_Addr_1" name="Controller_IP_Addr_1" class="IP_Addr_Number_input" type="number" value="192" max="255" min="0">
        <span>.</span>
        <input id="Controller_IP_Addr_2" name="Controller_IP_Addr_2" class="IP_Addr_Number_input" type="number" value="168" max="255" min="0">
        <span>.</span>
        <input id="Controller_IP_Addr_3" name="Controller_IP_Addr_3" class="IP_Addr_Number_input" type="number" value="1" max="255" min="0">
        <span>.</span>
        <input id="Controller_IP_Addr_4" name="Controller_IP_Addr_4" class="IP_Addr_Number_input" type="number" value="30" max="255" min="0">

        <input id="insertFluxButton" name="insertFluxButton" class="Flux_button" type="submit" value="Conferma">
    </form>

    <p>Cosa vorresti fare?</p>

    <ul>

        <li> <a class="option_link" href="php_scripts\GestioneFlussi\GestioneFlussi.html">Gestione Flussi</a></li> <br>

        <li> <a class="option_link" href="php_scripts\GestioneACL\GestioneACL.html">Gestione ACL</a> </li> <br>

        <li> <a class="option_link" href="php_scripts\GestioneFW\GestioneFW.php">Gestione Firewall</a> </li> <br>

    </ul>


</body>

</html>

<?php

if (isset($_POST["Controller_IP_Addr_1"]) || isset($_SESSION["IP_Controller"])) {
    echo "<br>########################### INFO ########################### <br><br>";

    echo "Indirizzo IP Corrente del Controllore SDN: " . $Controller->controller_ip . "<br><br>";

    echo "Sono stati rilevati " . count($Controller->DeviceList). " dispositivi Host validi. <br><br>";

    echo "<br>############################################################ <br><br>";
}


// Funzioni





?>