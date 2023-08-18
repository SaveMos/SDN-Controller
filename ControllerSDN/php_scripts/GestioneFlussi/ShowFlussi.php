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
    <script type="text/javascript" src="../../js_scripts/ShowFlussi.js"></script>
    <title>ShowFlux</title>
</head>

<body onload="EventHandler()">

    <div>
        <a class="option_link" href="../../index.php">Home</a>
        <a class="option_link" href="GestioneFlussi.html">Gestione Flussi</a>
    </div>
    <h1>Flussi Installati</h1>

    <?php
    $Controller = fixObject($_SESSION["Controller"]);
    echo "<p> Sono state installate " . ($Controller->UpdateNumber_OF_Flux()) . " regole di flusso. </p>";
    ?>


    <button class="FlushAll_Button" id="FlushAll" name="FlushAll"> Flush Totale </button> <span> Elimina Tutte le regole in tutti gli Switch</span>
    <?php

    $ret = json_decode($Controller->getFlussiInstallati());

    $SwitchRules = get_object_vars($ret);

    $SwitchList = $Controller->SwitchList;
    $num = count($SwitchList);

    for ($i = 0; $i < $num; $i++) {
        $Switch_i = (fixObject($SwitchList[$i]));
        $dpid = $Switch_i->DPID;

        echo "<h4>Regole applicate allo switch [ " . $dpid . " ]:</h4>";


        if (isset($SwitchRules[$dpid])) {
            if (count($SwitchRules[$dpid]) > 0) {

                echo "<button class='FlushAll_SwitchButton' id='FlushAll_Switch_" . $i . "' name='" . $dpid . "'> Elimina tutte le regole di " . $dpid . "</button>";

                $Num_Regole_i = count($SwitchRules[$dpid]);
                for ($j = 0; $j < $Num_Regole_i; $j++) {
                    echo "<p>" . ($j + 1) . " ) </p>";

                    $Rule = (array_keys(get_object_vars($SwitchRules[$dpid][$j])));

                    echo "<form method='post' action='DeleteFlowRules\DeleteSingleFlowRule.php'>";
                    echo "<button class='FlushSingleRule_Button' type='submit'> Elimina Regola </button>";
                    echo "<input type='hidden' id='RuleName' name='RuleName' value = " . trim($Rule[0]) . ">";
                    echo "</form>";

                    echo "<pre>";
                    print_r(get_object_vars($SwitchRules[$dpid][$j]));
                    echo "</pre>";

                    echo "<br><br>";
                }
            } else {
                echo "<p class='info_noRule'> Nessuna regola applicata!</p>";
            }
        } else {
            echo "<p class='info_noRule'> Nessuna regola applicata!</p>";
        }
        echo "<br><br>";
    }

    ?>

</body>

</html>