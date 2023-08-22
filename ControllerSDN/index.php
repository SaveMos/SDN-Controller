<?php

require_once("librerie_php/Dijkstra_Library.php");
require_once("librerie_php/Algoritmi_Vari.php");

require_once("classi_php/Controller_SDN.php");

session_start();
$Controller = 0;
$CONTROLLER_IP = 0;

if (
    isset($_POST["Controller_IP_Addr_1"])
    && isset($_POST["Controller_IP_Addr_2"])
    && isset($_POST["Controller_IP_Addr_3"])
    && isset($_POST["Controller_IP_Addr_4"])
) {
    // L'utente vuole cambiare indirizzo IP del controllore
    $CONTROLLER_IP = intval($_POST["Controller_IP_Addr_1"]) . "." . intval($_POST["Controller_IP_Addr_2"]) . "." . intval($_POST["Controller_IP_Addr_3"]) . "." . intval($_POST["Controller_IP_Addr_4"]);

    $CONTROLLER_IP = SecureIPAddress($CONTROLLER_IP);

    $Controller = new Controller_SDN($CONTROLLER_IP);

    if (($Controller->ControllerOnline()) == 1) {
        // Creazione della SwitchList, ossia la lista degli switch nella rete.
        $Controller->Update_Controller();
        $_SESSION["IP_Controller"] = $CONTROLLER_IP;
        $_SESSION["Controller"] = $Controller;
    } else {
        unset($_SESSION["IP_Controller"]);
        unset($_SESSION["Controller"]);
        echo "<p class='info0'> L'indirizzo " . $CONTROLLER_IP . " e' offline.</p>";
    }

    unset($_POST["Controller_IP_Addr_1"]);
    unset($_POST["Controller_IP_Addr_2"]);
    unset($_POST["Controller_IP_Addr_3"]);
    unset($_POST["Controller_IP_Addr_4"]);
}

if (
    isset($_SESSION["IP_Controller"]) &&
    isset($_SESSION["Controller"]) &&
    !(isset($_POST["Controller_IP_Addr_1"])
        ||  isset($_POST["Controller_IP_Addr_2"])
        ||  isset($_POST["Controller_IP_Addr_3"])
        ||  isset($_POST["Controller_IP_Addr_4"])
    )
) {
    // L'utente è tornato nella pagina
    $CONTROLLER_IP = $_SESSION["IP_Controller"];

    $Controller = $_SESSION["Controller"];
    $Controller->Update_Controller();
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

    <h1>Configuratore del Controllore SDN</h1>

    <form method="post" id="Insert_IP_Address_Form" name="Insert_IP_Address_Form">
        <label>Seleziona l'indirizzo IP del Controllore SDN:</label>
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

        <li> <a class="option_link" href="php_scripts\GestioneFlussi\GestioneFlussi.html">Gestione Regole di Flusso</a></li> <br>

        <li> <a class="option_link" href="php_scripts\GestioneACL\GestioneACL.html">Gestione ACL</a> </li> <br>

        <li> <a class="option_link" href="php_scripts\GestioneFW\GestioneFW.php">Gestione Firewall</a> </li> <br>

    </ul>


</body>

</html>

<?php

if (
    isset($_SESSION["IP_Controller"]) &&
    isset($_SESSION["Controller"]) &&
    !(isset($_POST["Controller_IP_Addr_1"])
        ||  isset($_POST["Controller_IP_Addr_2"])
        ||  isset($_POST["Controller_IP_Addr_3"])
        ||  isset($_POST["Controller_IP_Addr_4"])
    )
) {
    echo "<br>########################### INFO ########################### <br><br>";

    echo "<p> Il Controllore SDN e' online => ";
    echo "<span class='info01'> IP: " . $CONTROLLER_IP . "</span>";
    echo "</p>";

    echo "<ul>";
    echo "<li class='GeneralInformation'> Sono stati rilevati <span class='info03'>" . $Controller->getNumber_Of_Devices() . "</span> dispositivi Host validi. </li>";
    echo "<br>";
    echo "<li class='GeneralInformation'> Sono stati rilevati <span class='info03'>" . $Controller->getNumber_Of_Switch() . "</span> Switch. </li>";
    echo "<br>";
    echo "<li class='GeneralInformation'> Sono stati rilevati <span class='info03'>" . $Controller->getNumber_Of_InterSwitch_Links() . "</span> collegamenti tra Switch. </li>";
    echo "</ul>";
    echo "<br>############################################################ <br><br>";
}


// Funzioni





?>