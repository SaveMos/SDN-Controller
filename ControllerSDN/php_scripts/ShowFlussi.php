<?php
require_once("../librerie_php/Algoritmi_Vari.php");
require_once("../classi_php/Controller_SDN.php");

session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width = device-width">
    <link rel="stylesheet" href="../style/ModificaFlusso.css">

    <title>Flussi Installati</title>
</head>

<body>

    <div>
        <a class="option_link" href="../index.php">Home</a>
        <a class="option_link" href="GestioneFlussi.html">Gestione Flussi</a>
    </div>
    <h1>Flussi Installati</h1>

    <?php
    $Controller = $_SESSION["Controller"];
    $Controller = fixObject($Controller); 

    echo "<p> Sono stati installati ".($Controller->getNumber_OF_Flux())." flussi. </p>";

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
                $Num_Regole_i = count($SwitchRules[$dpid]);
                for ($j = 0; $j < $Num_Regole_i; $j++) {
                    echo "<p>" . ($j + 1) . " ) </p>";
                    echo "<pre>";
                    print_r(get_object_vars($SwitchRules[$dpid][$j]));
                    echo "</pre>";
                    echo "<br><br>";
                }
            } else {
                echo "<p> Nessuna regola applicata!</p>";
            }
        } else {
            echo "<p> Nessuna regola applicata!</p>";
        }
        echo "<br><br>";
    }

    ?>

</body>

</html>
