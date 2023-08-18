function EventHandler(){
    //(document.getElementById("FlushAll")).addEventListener("click", ClickFlushAll, false);

}

function ClickFlushAll() {
    const conferma = prompt("Se vuoi davvero cancellare TUTTE le ACL? Se si, digita: 'CANCELLA'");
    if (conferma === "CANCELLA") {
        fetch("DeleteACLRules/DeleteAllACLRules.php?")
            .then(location.reload());
    } else {
        alert("Operazione Annullata.");
    }

}