<?php
require_once("../../classi_php/Controller_SDN.php");
require_once('../../librerie_php/Algoritmi_Vari.php');

session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width = device-width">
    <link rel="stylesheet" href="../../style/style.css">
    <script src="../../js_scripts/GestioneFW.js"></script>
    <title>Gestione FW</title>
</head>

<body onload="EventHandler()">
    <div class="nav_bar_container">
        <a class="nav_bar_link" href="../../index.php">Home</a>
    </div>
    <h1 class='main_title'>Gestione Firewall</h1>

    <p class='option_question'>Cosa vorresti fare?</p>
    <?php

    $Controller = $_SESSION["Controller"];
    $Controller = fixObject($Controller);


    $State = 0;
    $ButtonText = 0;
    $SpanText = 0;
    $SpanClass = 0;

    if (($Controller->ShowFirewallState()) == 1) {
        $State = 0;
        $ButtonText = "Attiva Firewall";
        $SpanText = "Il firewall NON è attivo";
        $SpanClass = "info1";
    } else {
        $State = 1;
        $ButtonText = "Disattiva Firewall";
        $SpanText = "Il firewall è attivo";
        $SpanClass = "info2";
    }
    ?>

    <form method="post" action="AttivaFirewall.php">

        <?php
        echo "<input id='FW_Button' name='FW_Button' type='submit' value='" . $ButtonText . "'>  </input>";
        echo "<span id='FW_State' class='" . $SpanClass . "'> " . $SpanText . " </span>";
        echo "<input type='hidden' id='FW_Number_State' name='FW_Number_State' value='" . $State . "'>";
        ?>

    </form>
    <dl class="option_link_list_container">
        <dt> <a class="option_link" href="ModificaFW.php">Inserire una regola nel Firewall</a></dt><br>

        <dt> <a class="option_link" href="ShowFW.php">Vedere le Regole del Firewall</a></dt><br>
    </dl>

</body>

</html>