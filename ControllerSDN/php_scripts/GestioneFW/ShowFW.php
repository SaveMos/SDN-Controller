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
    <title>ShowFW</title>
</head>

<body>

    <div class="nav_bar_container">
        <a class="option_link" href="../../index.php">Home</a>
        <span class="option_link_separator">|</span>
        <a class="option_link" href="GestioneFW.php">Gestione Firewall</a>
        <span class="option_link_separator">|</span>
        <a class="option_link" href="ModificaFW.php">Nuova Regola</a>
    </div>
    <h1 class="main_title">Regole installate nel Firewall</h1>

    <?php
    $Controller = fixObject($_SESSION["Controller"]);
    $num_rule = ($Controller->getNumber_Of_FW_Rules());

    if($num_rule == 0){
            echo "<p class='infoNum'> Nel Firewall NON sono state installate regole. </p>";
    }else{
        if($num_rule == 1){
            echo "<p class='infoNum'> Nel Firewall è stata installata " . $num_rule . " regola. </p>";
        }else{
            echo "<p class='infoNum'> Nel Firewall sono state installate " . $num_rule . " regole. </p>";
        }
    }
    
    ?>

    <?php

    $ret = json_decode($Controller->getFWRulesInstallate());

    $num = count($ret);

    if ($num > 0) {

        $key = (array_keys(get_object_vars($ret[0])));
        $key = $key[0];

        for ($i = 0; $i < $num; $i++) {
            $rule = (get_object_vars($ret[$i]));
            echo "<div class='RuleContainer'>";
            echo "<form method='post' action='DeleteFWRules/DeleteSingleFWRule.php'>";
            echo "<span class='RuleNumber'>".($i+1)."° Regola</span>";
            echo "<button class='FlushSingleRule_Button' type='submit'> Elimina [ " . trim($rule[$key]) . " ]</button>";
            echo "<input type='hidden' id='RuleName' name='RuleName' value = " . trim($rule[$key]) . ">";
            echo "</form>";

           
            echo "<pre>";
            print_r(get_object_vars($ret[$i]));
            echo "</pre>";
            echo "</div>";
            echo "<br><br>";
        }
    }

    ?>

</body>

</html>