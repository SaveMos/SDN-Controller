<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width = device-width">
    <link rel="stylesheet" href="../style/ModificaFlusso.css">
    <script src="../js_scripts/ModificaFlusso.js"></script>
    <title>Configuratore di Flusso</title>
</head>


<body onload="EventHandler()">
    <p> <a class="option_link" href="../index.php">Home</a> </p>
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

                <label>Subnet Mask Sorgente:</label>
                <div class="IP_address_input_container">
                    <input id="Sorg_Subnet_Mask_1" name="Sorg_Subnet_Mask_1" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
                    <span>.</span>
                    <input id="Sorg_Subnet_Mask_2" name="Sorg_Subnet_Mask_2" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
                    <span>.</span>
                    <input id="Sorg_Subnet_Mask_3" name="Sorg_Subnet_Mask_3" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
                    <span>.</span>
                    <input id="Sorg_Subnet_Mask_4" name="Sorg_Subnet_Mask_4" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
                    <span> </span>
                    <input class = 'Mask_CheckBox' type='checkbox' id='MaskCheckBox_Sorg' name='MaskCheckBox_Sorg'>
                    <label for='MaskCheckBox_Sorg'>Host</label> 
                </div>

                <br><br>

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

                <label>Subnet Mask Destinatario:</label>
                <div class="IP_address_input_container">
                    <input id="Dest_Subnet_Mask_1" name="Dest_Subnet_Mask_1" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
                    <span>.</span>
                    <input id="Dest_Subnet_Mask_2" name="Dest_Subnet_Mask_2" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
                    <span>.</span>
                    <input id="Dest_Subnet_Mask_3" name="Dest_Subnet_Mask_3" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
                    <span>.</span>
                    <input id="Dest_Subnet_Mask_4" name="Dest_Subnet_Mask_4" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
                    <span> </span>
                    <input class = 'Mask_CheckBox' type='checkbox' id='MaskCheckBox_Dest' name='MaskCheckBox_Dest'>
                    <label for='MaskCheckBox_Dest'>Host</label> 
                </div>
            </div>

            <br>

            <div id="check_box_container" name="check_box_container">

                <?php
                echo "<h3 class='info1'>Seleziona gli Switch da cui desideri far passare il flusso.</h3>";
                echo "<div id='check_box_container_interno'>";
                echo "<p class='infoImportante'>NOTA BENE!</p>";
                echo "<p class='info2'>L'ordine con cui selezioni gli switch è importante, 
                    quindi spunta le checkbox degli switch nello stesso ordine con cui vorresti far passare i pacchetti</p>";
                echo "</div>";
                echo "<p class='info3'> DPID degli Switch attualmente Online:<p>";

                echo "<div id='check_box_container_interno'>";

                $SwitchList = $_SESSION["SwitchList"];
                $num_switch = count($_SESSION["SwitchList"]);

                for ($i = 0; $i < $num_switch; $i++) {
                    $s_i = get_object_vars($SwitchList[$i]);
                    echo "<input class = 'Switch_CheckBox' type='checkbox' id='switch_check$i' name='switch_check$i'> 
                        <label for='switch_check$i'>" . $s_i['DPID'] . "</label> 
                        <span class = 'Switch_CheckBoxPos' id='switch_checkPos$i' name='switch_checkPos$i' >-</span>
                        <br>";
                }

                echo "</div>";

                echo "<input type='hidden' id='PositionArray' name='PositionArray'>";
                ?>
            </div>

            <input id="insertACLButton" name="insertACLButton" class="ACL_button" type="submit" value="Avvia">
        </form>
    </div>




</body>

</html>