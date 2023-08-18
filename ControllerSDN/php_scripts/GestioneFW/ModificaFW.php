<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width = device-width">
    <link rel="stylesheet" href="../../style/ModificaACL.css">
    <script src="../../js_scripts/ModificaFW.js"></script>
    <title>Modifica Firewall</title>
</head>

<body onload="EventHandler()">
    <?php
    session_start();

    if (isset($_SESSION["esito_msg"])) {

        echo "<p class='infoImportante3'> " . $_SESSION["esito_msg"] . "</p>";

        unset($_SESSION["esito"]);
        unset($_SESSION["esito_msg"]);
    }
    ?>
    <p>
        <a class="option_link" href="../../index.php">Home</a>
        <a class="option_link" href="GestioneFW.php">Gestione Firewall</a>
    </p>
    <h1>Modifica Firewall</h1>

    <div class="greater_option_cointainer">
        <p class="option_voice">Inserisci una nuova Regola</p>
        <form action="AddFWRule.php" class="option_container" method="post" id="Insert_FW_Form" name="Insert_FW_Form">
            <div class="input_field_container" id="insert_FW_IP_container" name="insert_FW_IP_container">
                <label>Ind. IP Sorg:</label>
                <div class="IP_address_input_container">
                    <input id="Sorg_IP_Addr_1" name="Sorg_IP_Addr_1" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
                    <span>.</span>
                    <input id="Sorg_IP_Addr_2" name="Sorg_IP_Addr_2" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
                    <span>.</span>
                    <input id="Sorg_IP_Addr_3" name="Sorg_IP_Addr_3" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
                    <span>.</span>
                    <input id="Sorg_IP_Addr_4" name="Sorg_IP_Addr_4" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
                </div>
                <div class="IP_address_input_container">
                    <span>\</span> <input id="Sorg_Subnet_Mask" name="Sorg_Subnet_Mask" class="IP_Addr_Number_input" type="number" value="0" max="32" min="0" step="1">
                </div>

                <br>

                <label>Ind. IP Dest:</label>
                <div class="IP_address_input_container">
                    <input id="Dest_IP_Addr_1" name="Dest_IP_Addr_1" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
                    <span>.</span>
                    <input id="Dest_IP_Addr_2" name="Dest_IP_Addr_2" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
                    <span>.</span>
                    <input id="Dest_IP_Addr_3" name="Dest_IP_Addr_3" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
                    <span>.</span>
                    <input id="Dest_IP_Addr_4" name="Dest_IP_Addr_4" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
                </div>

                <div class="IP_address_input_container">
                    <span>\</span> <input id="Dest_Subnet_Mask" name="Dest_Subnet_Mask" class="IP_Addr_Number_input" type="number" value="0" max="32" min="0" step="1">
                </div>
            </div>

            <br>
            <div class="IP_address_input_container">
                <span>Switch:</span> <input id="SwitchFW" name="SwitchFW" class="MAC_Addr_Number_input" type="text" value="00:00:00:00:00:00:00:00">
            </div>

            <br> <br>

            <div class="input_action_container">
                <label for="insertFW_action">Action:</label>
                <select style=" font-family: 'Courier New', Courier, monospace" name="insertFW_action" id="insertFW_action">
                    <option value="DENY">Deny</option>
                    <option value="ALLOW">Allow</option>
                </select>

                <label for="insertFW_Protocol">Protocollo:</label>
                <select style=" font-family: 'Courier New', Courier, monospace" name="insertFW_Protocol" id="insertFW_Protocol">
                    <option value="tcp">TCP</option>
                    <option value="udp">UDP</option>
                    <option value="icmp">ICMP</option>
                </select>
            </div>

            <br>

            <div class="IP_address_input_container">
                <span>N° Porta Sorgente:</span> <input id="Srg_Port" name="Srg_Port" class="IP_Addr_Number_input" type="number" value="0" min="0" step="1">
            </div>

            <div class="IP_address_input_container">
                <span>N° Porta Destinataria:</span> <input id="Dest_Port" name="Dest_Port" class="IP_Addr_Number_input" type="number" value="0" min="0" step="1">
            </div>

            <br> 

            <div class="IP_address_input_container">
                <span>Priorità:</span> <input id="Priority" name="Priority" class="IP_Addr_Number_input" type="number" value="0" min="0" max = "56" step="1">
            </div>

            <br>

            <input id="insertFWButton" name="insertFWButton" class="ACL_button" type="submit" value="Inserisci">

            <br><br>
        </form>

       
    </div>

</body>

</html>

<?php

?>