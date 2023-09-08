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
    <script type="text/javascript" src="..\..\js_scripts\ShowFlussi.js"></script>
    <title>ShowFlux</title>
</head>

<body onload="EventHandler()">

<div class="nav_bar_container">
        <a class="option_link" href="../../index.php">Home</a>
        <span class="option_link_separator">|</span>
        <a class="option_link" href="GestioneFlussi.html">Gestione Flussi</a>
        <span class="option_link_separator">|</span>
        <a class="option_link" href="ModificaFlusso.php">Nuova Regola di Flusso</a>
</div>
    <h1 class='main_title'>Regole di Flusso Installate</h1>

    <div class="general_options_container">
    <?php

    $Controller = fixObject($_SESSION["Controller"]);
    $num_rule = $Controller->UpdateNumber_OF_Flux();

    
    if($num_rule == 0){
            echo "<p class='infoNum'> NON sono state installate regole di Flusso. </p>";
    }else{
        if($num_rule == 1){
            echo "<p class='infoNum'> E' stata installata " . $num_rule . " regola di Flusso. </p>";
        }else{
            echo "<p class='infoNum'> Sono state installate " . $num_rule . " regole di Flusso. </p>";
        }

        echo "<button class='FlushButton FlushAll_Button' id='FlushAll' name='FlushAll'> Flush Totale </button> <span class='infoNum'> Elimina tutte le regole in tutti gli Switch</span>";
    }
    
    ?>
    </div>
    <?php

    $ret = json_decode($Controller->getFlussiInstallati());

    $SwitchRules = get_object_vars($ret);

    $SwitchList = $Controller->SwitchList;
    $num = count($SwitchList);

    for ($i = 0; $i < $num; $i++) {
        $Switch_i = (fixObject($SwitchList[$i]));
        $dpid = $Switch_i->DPID;

        echo "<div class='container_switch_rules'>";
        echo "<h4 class='InfoSwitchFlowRules'>Regole applicate allo switch [ " . $dpid . " ]:</h4>";


        if (isset($SwitchRules[$dpid])) {
            $Num_Regole_i = count($SwitchRules[$dpid]);

            if ($Num_Regole_i > 0) {

                echo "<button class='FlushButton FlushAll_SwitchButton' id='FlushAll_Switch_" . $i . "' name='" . $dpid . "'> Elimina tutte le regole di " . $dpid . "</button>";
                echo "<br>";echo "<br>";
      
                for ($j = 0; $j < $Num_Regole_i; $j++) {
                    $Rule = (array_keys(get_object_vars($SwitchRules[$dpid][$j])));
                    
                    echo "<div class='RuleContainer'>";
                    echo "<form method='post' action='DeleteFlowRules\DeleteSingleFlowRule.php'>";
                    echo "<span class='RuleNumber'>".($j+1)."° Regola</span>";
                    echo "<button class='FlushButton FlushSingleRule_Button' type='submit'> Elimina '" . trim($Rule[0]) . "' </button>";
                    echo "<input type='hidden' id='RuleName' name='RuleName' value = " . trim($Rule[0]) . ">";
                    echo "</form>";

                    echo "<pre>";
                    print_r(get_object_vars($SwitchRules[$dpid][$j]));
                    echo "</pre>";
                    echo "</div>";
                    echo "<br><br>";
                }
            } else {
                echo "<p class='info_noRule'> Nessuna regola applicata!</p>";
            }
        } else {
            echo "<p class='info_noRule'> Nessuna regola applicata!</p>";
        }
        echo "</div>";
        echo "<br><br>";
    }

    ?>

</body>

</html>