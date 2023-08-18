function EventHandler() {

    (document.getElementById("FlushAll")).addEventListener("click", ClickFlushAll, false);

    (document.querySelectorAll('.FlushAll_SwitchButton')).forEach(check => {
        check.addEventListener("click", ClickFlushAllSwitch, false);
    });
}

function ClickFlushAll() {
    const conferma = prompt("Se vuoi davvero cancellare TUTTI i flussi? Se si, digita: 'CANCELLA'");
    if (conferma === "CANCELLA") {
        fetch("DeleteFlowRules/DeleteAllFlowRule.php?")
            .then(location.reload());
    } else {
        alert("Operazione Annullata.");
    }

}


function ClickFlushAllSwitch() {
    const dpid = this.name;
    const conferma = prompt("Se vuoi davvero cancellare TUTTI i flussi dello Switch ["+dpid+"]? Se si, digita: 'CANCELLA'");
    
    if (conferma === "CANCELLA") {
        fetch("DeleteFlowRules/DeleteAllFlowRulesOfSwitch.php?DPID="+dpid)   
            .then(location.reload());
    } else {
        alert("Operazione Annullata.");
    }

}