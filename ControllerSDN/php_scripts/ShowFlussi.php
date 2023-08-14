<?php
require_once("../librerie_php/REST_API_Library.php");
require_once("../librerie_php/Algoritmi_Vari.php");

require_once('../classi_php/Switch_SDN.php');
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

    <p> <a class="option_link" href="../index.php">Home</a> </p>
    <h1>Flussi Installati</h1>

    <?php
    $ret = CallRESTAPI("GET", "http://" . $_SESSION["IP_Controller"] . ":8080/wm/staticentrypusher/list/all/json");
    $ret = json_decode($ret);

    $SwitchRules = get_object_vars($ret);

    $SwitchList = $_SESSION["SwitchList"];
    $num = count($SwitchList);

    for ($i = 0; $i < $num; $i++) {
        $Switch_i = (fixObject($SwitchList[$i]));
        $dpid = $Switch_i->DPID;
        echo "<h4>Regole applicate allo switch [ <bold>" . $dpid . "</bold> ]:</h4>";

        if (isset($SwitchRules[$dpid])) {
            if (count($SwitchRules[$dpid]) > 0) {
                $Num_Regole_i = count($SwitchRules[$dpid]);
                for ($j = 0; $j < $Num_Regole_i; $j++) {
                    echo "<p>" . ($j + 1) . " ) </p>";
                    echo "<pre>"; print_r(get_object_vars($SwitchRules[$dpid][$j])); echo "</pre>";
                    echo "<br><br>";
                }
            }else{
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

<?php
function pp($arr){
    $retStr = '<ul>';
    if (is_array($arr)){
        foreach ($arr as $key=>$val){
            if (is_array($val)){
                $retStr .= '<li>' . $key . ' => ' . pp($val) . '</li>';
            }else{
                $retStr .= '<li>' . $key . ' => ' . $val . '</li>';
            }
        }
    }
    $retStr .= '</ul>';
    return $retStr;
}
?>