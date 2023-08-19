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
    <link rel="stylesheet" href="../../style/style.css">
    <title>ShowACL</title>
</head>

<body>

<div class="nav_bar_container">
        <a class="option_link" href="../../index.php">Home</a>
        <span class="option_link_separator">|</span>
        <a class="option_link" href="GestioneACL.html">Gestione ACL</a>
        <span class="option_link_separator">|</span>
        <a class="option_link" href="ModificaACL.php">Nuova Regola</a>
</div>
    <h1>ACL Installate</h1>

    <?php
    $Controller = fixObject($_SESSION["Controller"]);
    $num_rule = ($Controller->getNumber_Of_ACL_Rules());

    if($num_rule == 0){
            echo "<p class='infoNum'> NON sono state installate regole di ACL. </p>";
    }else{
        if($num_rule == 1){
            echo "<p class='infoNum'> E' stata installata " . $num_rule . " regola di ACL. </p>";
        }else{
            echo "<p class='infoNum'> Sono state installate " . $num_rule . " regole di ACL. </p>";
        }
    }
    
    ?>

    <?php

    $ret = json_decode($Controller->getACLRulesInstallate());

    $num = count($ret);
 
    for ($i = 0; $i < $num; $i++) {
        echo "<div class='RuleContainer'>";
        echo "<span class='RuleNumber'>".($i+1)."° Regola</span>";
        echo "<pre>";             
        print_r(get_object_vars($ret[$i])); 
        echo "</pre>";
        echo "</div>";
        echo "<br><br>";
    }

    ?>

</body>

</html>