<?php
require_once("../../librerie_php/Algoritmi_Vari.php");
require_once("../../classi_php/Controller_SDN.php");

session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width = device-width">
    <link rel="stylesheet" href="../../style/ShowFlussi.css">
    <title>ShowACL</title>
</head>

<body>

    <div>
        <a class="option_link" href="../../index.php">Home</a>
        <a class="option_link" href="GestioneACL.html">Gestione ACL</a>
    </div>
    <h1>ACL Installate</h1>

    <?php
    $Controller = fixObject($_SESSION["Controller"]);
    echo "<p> Sono state installate " . ($Controller->getNumber_Of_ACL_Rules()) . " regole di ACL. </p>";
    ?>

    <?php

    $ret = json_decode($Controller->getACLRulesInstallate());

    $num = count($ret);
 
    for ($i = 0; $i < $num; $i++) {
        echo "<pre>";             
        print_r(get_object_vars($ret[$i])); 
        echo "</pre>";
        echo "<br><br>";
    }

    ?>

</body>

</html>