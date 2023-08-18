<?php
session_start();
require_once("../../classi_php/Controller_SDN.php");
require_once("../../librerie_php/Algoritmi_Vari.php");
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width = device-width">
    <link rel="stylesheet" href="../../style/ModificaFlusso.css">
    <script type="text/javascript" src="../../js_scripts/ModificaFlusso.js"></script>
    <title>Configuratore di Flusso</title>
</head>


<body onload="EventHandler()">
    <?php

    if (isset($_SESSION["esito"])) {
        if ($_SESSION["esito"] == 1) {
            echo "<p class='infoImportante2'> Regola aggiunta con Successo!</p>";
        } else {
            echo "<p class='infoImportante'> Inserimento Regola Fallito! " . $_SESSION["esito_msg"] . "</p>";
        }
        unset($_SESSION["esito"]);
        unset($_SESSION["esito_msg"]);
    }
    ?>

    <div>
        <a class="option_link" href="../../index.php">Home</a>
        <a class="option_link" href="GestioneFlussi.html">Gestione Flussi</a>
    </div>
    <h1>Configuratore di Flusso</h1>

    <div class="greater_option_cointainer">

        <form action="AddFlux.php" class="option_container" method="post" id="Insert_Flux_Form" name="Insert_Flux_Form">

            <div class="input_field_container">
                <label>Indirizzo IP Sorgente:</label>
                <div class="IP_address_input_container">
                    <input id="Sorg_IP_Addr_1" name="Sorg_IP_Addr_1" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
                    <span>.</span>
                    <input id="Sorg_IP_Addr_2" name="Sorg_IP_Addr_2" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
                    <span>.</span>
                    <input id="Sorg_IP_Addr_3" name="Sorg_IP_Addr_3" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
                    <span>.</span>
                    <input id="Sorg_IP_Addr_4" name="Sorg_IP_Addr_4" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
                </div>
                <br>

                <label>Indirizzo IP Destinatario:</label>
                <div class="IP_address_input_container">
                    <input id="Dest_IP_Addr_1" name="Dest_IP_Addr_1" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
                    <span>.</span>
                    <input id="Dest_IP_Addr_2" name="Dest_IP_Addr_2" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
                    <span>.</span>
                    <input id="Dest_IP_Addr_3" name="Dest_IP_Addr_3" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
                    <span>.</span>
                    <input id="Dest_IP_Addr_4" name="Dest_IP_Addr_4" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
                </div>

                <br>
               
            </div>

            <h3 class='info1'>Altre Opzioni</h3>
            <div id="other_option_container">

                <label for='Other_Option_Bidirezionale_check'> Bidirezionale </label>
                <input class='Other_Option_CheckBox' type='checkbox' id='Other_Option_Bidirezionale_check' name='Other_Option_Bidirezionale_check'>

                <br><br>
                <label for='priority_flux'> Priorità [0 - 32767]</label>
                <input class='Other_Option_CheckBox' type='number' id='priority_flux' name='priority_flux' value="32767" max="32767" min="0" step="1">

                <br><br>
                <label for='flux_name'> Nome del Flusso</label>
                <input class='Other_Option_CheckBox' type='text' id='flux_name' name='flux_name' minlenght="1" maxlenght="50" placeholder="flow-mod">

            </div>

            <div id="check_box_container" name="check_box_container">

                <?php
                $Controller = fixObject($_SESSION["Controller"]);

                $Controller->Update_Controller();

                $_SESSION["Controller"] = $Controller;

                echo "<h3 class='info1'>Seleziona gli Switch da cui desideri far passare il flusso.</h3>";
                echo "<div id='info_container'>";
                echo "<p class='infoImportante'>NOTA BENE! L'ordine con cui selezioni gli switch è importante, 
                    quindi spunta le checkbox degli switch nello stesso ordine con cui vorresti far passare i pacchetti</p>";
                echo "</div>";
                echo "<p class='info2'> DPID degli Switch attualmente online:</p>";

                echo "<div id='check_box_container_interno'>";

                $SwitchList = $Controller->SwitchList;
                $num_switch = count($SwitchList);

                for ($i = 0; $i < $num_switch; $i++) {
                    $s_i = fixObject($SwitchList[$i]);
                    echo "<input class = 'Switch_CheckBox' type='checkbox' id='switch_check$i' name='switch_check$i'> 
                        <label class='Switch_CheckBox_label' for='switch_check$i'>" . $s_i->DPID . "</label> 
                        <span class = 'Switch_CheckBoxPos' id='switch_checkPos$i' name='switch_checkPos$i' >-</span>
                        <br>";
                }

                echo "</div>";

                echo "<input type='hidden' id='PositionArray' name='PositionArray'>";
                ?>
            </div>

            <input id="insertFluxButton" name="insertFluxButton" class="Flux_button" type="submit" value="Aggiungi Flusso">
        </form>
    </div>

    <br>
    <br>


</body>

</html>