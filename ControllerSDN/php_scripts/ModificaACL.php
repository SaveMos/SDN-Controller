<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width = device-width">
    <link rel="stylesheet" href="../style/ModificaACL.css">
    <script src="../js_scripts/ModificaACL.js"></script>
    <title>Modifica ACL</title>
</head>

<body>
    <p> <a class="option_link" href="../index.php">Home</a> </p>
    <h1>ACL</h1>
   
    <div class="greater_option_cointainer">
        <p class="option_voice">Inserisci una Regola</p>
        <form action="AddACLRule.php" class="option_container" method="post" id="Insert_ACL_Form" name = "Insert_ACL_Form">

            <div class="input_field_container">
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
                <label>Sorg. Subnet Mask:</label>
                <div class="IP_address_input_container">
                    <input id="Sorg_Subnet_Mask_1" name="Sorg_Subnet_Mask_1" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
                    <span>.</span>
                    <input id="Sorg_Subnet_Mask_2" name="Sorg_Subnet_Mask_2" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
                    <span>.</span>
                    <input id="Sorg_Subnet_Mask_3" name="Sorg_Subnet_Mask_3" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
                    <span>.</span>
                    <input id="Sorg_Subnet_Mask_4" name="Sorg_Subnet_Mask_4" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
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
                <label>Dest. Subnet Mask:</label>
                <div class="IP_address_input_container">
                    <input id="Dest_Subnet_Mask_1" name="Dest_Subnet_Mask_1" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
                    <span>.</span>
                    <input id="Dest_Subnet_Mask_2" name="Dest_Subnet_Mask_2" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
                    <span>.</span>
                    <input id="Dest_Subnet_Mask_3" name="Dest_Subnet_Mask_3" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
                    <span>.</span>
                    <input id="Dest_Subnet_Mask_4" name="Dest_Subnet_Mask_4" class="IP_Addr_Number_input" type="number" value="0" max="255" min="0">
                </div>
            </div>

            <div class="input_action_container">
                <label for="insertACL_action">Action:</label>
                <select style=" font-family: 'Courier New', Courier, monospace" name="insertACL_action" id="insertACL_action">
                    <option value="permit">permit</option>
                    <option value="deny">deny</option>
                </select>
            </div>

            <input id="insertACLButton" name="insertACLButton" class="ACL_button" type="submit" value="Avvia">
        </form>
    </div>

    <div class="greater_option_cointainer">
        <p class="option_voice">Elimina una Regola</p>
        <form class="option_container">

            <div class="input_single_field_container">
                <label>#Num della Regola da Eliminare: </label>
                <div class="IP_address_input_container">
                    <input id="Rule_Number_Delete" name="Rule_Number_Delete" class="Rule_Number_input" type="number" value="0" min="0">
                </div>
            </div>
            <input id="deleteACLButton" name="deleteACLButton" class="ACL_button" type="submit" value="Avvia">
        </form>
    </div>

    <div class="greater_option_cointainer">
        <p class="option_voice">Modifica una Regola</p>
        <form class="option_container">
            <input id="modifyACLButton" name="modifyACLButton" class="ACL_button" type="submit" value="Avvia">
        </form>
    </div>

</body>

</html>

<?php

?>