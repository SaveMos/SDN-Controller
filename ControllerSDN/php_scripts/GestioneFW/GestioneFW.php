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
    <p> <a class="option_link" href="../../index.php">Home</a> </p>
    <h1>Gestione Firewall</h1>

    <p>Cosa vorresti fare?</p>
    <?php

    $Controller = $_SESSION["Controller"];
    $Controller = fixObject($Controller);

    $ret = $Controller->ShowFirewallState();
    $State = 0;

    $ButtonText = 0; $SpanText = 0;

    if ($ret == '{"result" : "firewall disabled"}') {
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
    echo "<input id='FW_Button' name='FW_Button' type='submit' value='".$ButtonText."'>  </input>";
    echo "<span id='FW_State' class='".$SpanClass."'> ".$SpanText." </span>";
    echo "<input type='hidden' id='FW_Number_State' name='FW_Number_State' value='" . $State . "'>";
    ?>
    
    </form>
    <ul>
        <li> <a class="option_link" href="ModificaFW.php">Inserire una regola nel Firewall</a></li><br>

        <li> <a class="option_link" href="ShowFW.php">Vedere le Regole del Firewall</a></li><br>
    </ul>

</body>

</html>