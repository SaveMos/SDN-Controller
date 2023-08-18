function EventHandler() {
    //CambiaStatoFirewall();
    //(document.getElementById("FW_Button")).addEventListener("click", ClickAttivaFW, false);
}

function ClickAttivaFW() {
    fetch("../php_scripts/AttivaFirewall.php?")
        .then(CambiaStatoFirewall());
}

function CambiaStatoFirewall(){
    const stato = (document.getElementById("FW_Number_State")).value;

    if(stato == 0){
        (document.getElementById("FW_State")).innerText = "Il Firewall è Attivo";
        (document.getElementById("FW_State")).className = "info2";
        (document.getElementById("FW_Button")).innerText = "Disattiva Firewall";
        (document.getElementById("FW_Number_State")).value = 1;
    }else{
        (document.getElementById("FW_State")).innerText = "Il Firewall NON è Attivo";
        (document.getElementById("FW_State")).className = "info1";
        (document.getElementById("FW_Button")).innerText = "Attiva Firewall";
        (document.getElementById("FW_Number_State")).value = 0;
    }
   
}