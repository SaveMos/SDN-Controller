<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width = device-width">
    <link rel="stylesheet" href="../../style/ModificaACL.css">
    <script src="../../js_scripts/ModificaACL.js"></script>
    <title>Modifica ACL</title>
</head>

<body onload="EventHandler()">
    <?php
    session_start();

    if (isset($_SESSION["esito"])) {
        if ($_SESSION["esito"] == 1) {
            echo "<p class='infoImportante2'> " . $_SESSION["esito_msg"] . "</p>";
        } else {
            echo "<p class='infoImportante'> " . $_SESSION["esito_msg"] . "</p>";
        }
        unset($_SESSION["esito"]);
        unset($_SESSION["esito_msg"]);
    }
    ?>
   <div class="nav_bar_container">
        <a class="option_link" href="../../index.php">Home</a>
        <span class="option_link_separator">|</span>
        <a class="option_link" href="GestioneACL.html">Gestione ACL</a>
        <span class="option_link_separator">|</span>
        <a class="option_link" href="ShowACL.php">Guarda le ACL</a>
    </div>
    <h1>Inserisci ACL</h1>

    <div class="greater_option_cointainer">
        <p class="option_voice">Inserisci una Regola</p>
        <form action="AddACLRule.php" class="option_container" method="post" id="Insert_ACL_Form" name="Insert_ACL_Form">


            <div class="input_field_container" id="insert_ACL_IP_container" name="insert_ACL_IP_container">
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

            <div class="input_action_container">
                <label for="insertACL_action">Action:</label>
                <select style=" font-family: 'Courier New', Courier, monospace" name="insertACL_action" id="insertACL_action">
                    <option value="deny">Deny</option>
                    <option value="allow">Allow</option>

                </select>

                <label for="insertACL_Protocol">Protocollo:</label>
                <select style=" font-family: 'Courier New', Courier, monospace" name="insertACL_Protocol" id="insertACL_Protocol">
                    <option value="tcp">TCP</option>
                    <option value="udp">UDP</option>
                    <option value="icmp">ICMP</option>
                </select>
            </div>


            <input id="insertACLButton" name="insertACLButton" class="ACL_button" type="submit" value="Inserisci">

        </form>
    </div>

    <div class="greater_option_cointainer">
        <p class="option_voice">Elimina Regole</p>
        <form class="option_container" method="post" action="DeleteACLRules/DeleteSingleACLRule.php">

            <div class="input_single_field_container">
                <label>#Num della Regola da Eliminare: </label>
                <div class="IP_address_input_container">
                    <input id="Rule_Number_Delete" name="Rule_Number_Delete" class="Rule_Number_input" type="number" value="0" min="0">
                </div>
            </div>
            <input id="deleteACLButton" name="deleteACLButton" class="ACL_button" type="submit" value="Elimina Regola">
        </form>
    </div>

    <div class="greater_option_cointainer">
        <div class="option_container">
            <input id="FlushAll" name="FlushAll" class="ACL_button" type="submit" value="Elimina TUTTE le Regole">
        </div>
    </div>

    

</body>

</html>

<?php

?>