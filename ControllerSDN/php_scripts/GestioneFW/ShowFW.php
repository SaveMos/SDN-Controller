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
    <title>ShowFW</title>
</head>

<body>

    <div>
        <a class="option_link" href="../../index.php">Home</a>
        <a class="option_link" href="GestioneFW.php">Gestione Firewall</a>
    </div>
    <h1>Regole installate nel Firewall</h1>

    <?php
    $Controller = fixObject($_SESSION["Controller"]);
    echo "<p> Nel Firewall sono state installate " . ($Controller->getNumber_Of_FW_Rules()) . " regole. </p>";
    ?>

    <?php

    $ret = json_decode($Controller->getFWRulesInstallate());

    $num = count($ret);

    if ($num > 0) {

        $key = (array_keys(get_object_vars($ret[0])));
        $key = $key[0];

        for ($i = 0; $i < $num; $i++) {
            $rule = (get_object_vars($ret[$i]));

            echo "<form method='post' action='DeleteFWRules/DeleteSingleFWRule.php'>";
            echo "<button class='FlushSingleRule_Button' type='submit'> Elimina Regola [ " . trim($rule[$key]) . " ]</button>";
            echo "<input type='hidden' id='RuleName' name='RuleName' value = " . trim($rule[$key]) . ">";
            echo "</form>";

            echo "<pre>";
            print_r(get_object_vars($ret[$i]));
            echo "</pre>";
            echo "<br><br>";
        }
    }

    ?>

</body>

</html>