
var Position = [];

var Default_Position = [];

function EventHandler() {
    var Checks = document.querySelectorAll('.Switch_CheckBox');
    Checks.forEach(check => {
        check.addEventListener("click", clickCheckBox, false);
    });


    Checks = document.querySelectorAll('.dpid_container');
    Checks.forEach(check => {
        check.addEventListener("dragstart", drag, false);
        check.addEventListener("dragend", drop, false);
        check.addEventListener("dragover", allowDrop_Element, false);
    });

    // Mask_CheckBox
    Checks = document.querySelectorAll('.Mask_CheckBox');
    Checks.forEach(check => {
        check.addEventListener("click", clickMaskCheckBox, false);
    });


    Checks = document.querySelectorAll('.drag_menu_container');
    Checks.forEach(check => {
        check.addEventListener("drop", drop, false);
        check.addEventListener("dragover", allowDrop_Container, false);
    });

    const container = (document.getElementById("check_box_container_interno_sinistro"));

    var NodeList = container.childNodes;

    for (let i = 0; i < NodeList.length; i++) {
        Default_Position[i] = NodeList[i].id;
    }
    /*
    document.addEventListener("dragover", (event) => {
        event.preventDefault();
    });

    document.addEventListener("dragend", (event) => {
        event.preventDefault();
    });

    document.addEventListener("drop", (event) => {
        event.preventDefault();
    });
    */
}


function Update_Positions() {
    var container = (document.getElementById("check_box_container_interno_destro"));

    var NodeList = container.childNodes;
    var dim = NodeList.length;
    Position = [];

    if (dim == 0) {
        return;
    }

    if (dim == 1) {
        Position[0] = NodeList[0].id;

    }

    if (dim > 1) {
        for (let i = 0; i < dim; i++) {
            Position.push(TrovaIndice(NodeList[i].id, Default_Position) - 1);
        }
    }
    NodeList = container.childNodes;
    dim = NodeList.length;

    for (let i = 1; i < dim; i++) {
        var el = NodeList[i];
        el = el.firstChild.nextSibling.nextSibling;
        el.innerHTML = " | " + (i) + "°";
    }

    Position.shift();
    (document.getElementById("PositionArray")).value = JSON.stringify(Position);
    // Carico Position in un campo input per poi passarlo al PHP tramite metodo POST.

    container = (document.getElementById("check_box_container_interno_sinistro"));
    NodeList = container.childNodes;
    dim = NodeList.length;

    for (let i = 1; i < dim; i++) {
        var el = NodeList[i];
        el = el.firstChild.nextSibling.nextSibling;
        el.innerHTML = "";
    }

}


var Valori_Precedenti_Sorgente = [0, 0, 0, 0];
var Valori_Precedenti_Destinatario = [0, 0, 0, 0];

var original_drag_container = "check_box_container_interno_sinistro"; // container originale in cui era l'elemento
// all'inizio gli elementi sono tutti in quello sinistro.
var current_drag_container = null;
var current_element_ahead = null;

function allowDrop_Container() {
   // console.log("Container " + this.id);
    if (current_drag_container) {
        // Se l'elemento ha cambiato container allora ho uno spostamento di container.
        // mi segno il container di provenienza e quello di arrivo.
        current_drag_container = this.id;
    } else {
        original_drag_container = this.id;
        current_drag_container = this.id;
    }
}

function allowDrop_Element() {
    current_element_ahead = this.id;
    
    // se l'elemento in movimento è sopra un altro elemento allora me lo segno.
}

function drag() {
    document.getElementById(this.id).className = "dpid_container_dragging";
   // console.log("dragging");
    document.body.style.cursor = "move";
}

function drop() {
    // scatta al momento del drop
    // console.log(original_drag_container + "   " + current_drag_container);

    document.getElementById(this.id).className = "dpid_container";
    document.body.style.cursor = "auto";

    console.log("Original -> "+original_drag_container);
    console.log("Current -> "+current_drag_container);

    if (current_element_ahead == this.id && original_drag_container != current_drag_container) {
        current_element_ahead = null;
        // il container di destinazione è vuoto e sto inserendo il primo elemento
    }

    const element_id = document.getElementById(this.id);

    console.log("Current Element -> "+current_element_ahead);

    if (current_element_ahead) {
        // console.log("drop " + this.id + " sull'elemento " + current_element_ahead);
        const element_target = document.getElementById(current_element_ahead);

        if (current_element_ahead == this.id && original_drag_container == current_drag_container) {
            return
            // il container di destinazione è vuoto e sto inserendo il primo elemento
        }

        const new_container = element_target.parentNode;

        var NodeList = new_container.childNodes;

        const indice_elemento = IndexOf(element_id);
        const indice_target = IndexOf(element_target);

        if (indice_elemento == -1 || indice_target == -1) {
            return;
        }

        if (indice_elemento >= indice_target) {
            insertAfter(element_target, element_id);
            new_container.insertBefore(element_id, element_target);
        } else {
            insertAfter(element_target, element_id);
        }

        original_drag_container = current_drag_container;
        current_drag_container = null;

    } else {
        //console.log("drop " + this.id + " nel container " + current_drag_container);
        const new_container = document.getElementById(current_drag_container);
        new_container.appendChild(element_id);
        original_drag_container = current_drag_container;
        current_drag_container = null;
    }
    Update_Positions();
}

function insertAfter(referenceNode, newNode) {
    referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
}

function IndexOf(Nodo) {
    const container = Nodo.parentNode;
    var NodeList = container.childNodes;

    for (let i = 0; i < NodeList.length; i++) {
        if (NodeList[i].id == Nodo.id) {
            return i;
        }
    }
    return -1;
}

function TrovaIndice(id, arr) {
    for (let i = 0; i < arr.length; i++) {
        if (arr[i] == id) {
            return i;
        }
    }
    return -1;
}


function clickMaskCheckBox() {
    if (this.name == "MaskCheckBox_Dest") {

        if (((document.getElementById("Dest_Subnet_Mask_1")).readOnly) == false) {

            Valori_Precedenti_Destinatario[0] = (document.getElementById("Dest_Subnet_Mask_1")).value;
            Valori_Precedenti_Destinatario[1] = (document.getElementById("Dest_Subnet_Mask_2")).value;
            Valori_Precedenti_Destinatario[2] = (document.getElementById("Dest_Subnet_Mask_3")).value;
            Valori_Precedenti_Destinatario[3] = (document.getElementById("Dest_Subnet_Mask_4")).value;

            (document.getElementById("Dest_Subnet_Mask_1")).value = 255;
            (document.getElementById("Dest_Subnet_Mask_2")).value = 255;
            (document.getElementById("Dest_Subnet_Mask_3")).value = 255;
            (document.getElementById("Dest_Subnet_Mask_4")).value = 255;

            (document.getElementById("Dest_Subnet_Mask_1")).setAttribute("readonly", true);
            (document.getElementById("Dest_Subnet_Mask_2")).setAttribute("readonly", true);
            (document.getElementById("Dest_Subnet_Mask_3")).setAttribute("readonly", true);
            (document.getElementById("Dest_Subnet_Mask_4")).setAttribute("readonly", true);

        } else {
            (document.getElementById("Dest_Subnet_Mask_1")).value = Valori_Precedenti_Destinatario[0];
            (document.getElementById("Dest_Subnet_Mask_2")).value = Valori_Precedenti_Destinatario[1];
            (document.getElementById("Dest_Subnet_Mask_3")).value = Valori_Precedenti_Destinatario[2];
            (document.getElementById("Dest_Subnet_Mask_4")).value = Valori_Precedenti_Destinatario[3];

            (document.getElementById("Dest_Subnet_Mask_1")).removeAttribute("readonly");
            (document.getElementById("Dest_Subnet_Mask_2")).removeAttribute("readonly");
            (document.getElementById("Dest_Subnet_Mask_3")).removeAttribute("readonly");
            (document.getElementById("Dest_Subnet_Mask_4")).removeAttribute("readonly");
        }

    } else {

        if (((document.getElementById("Sorg_Subnet_Mask_1")).readOnly) == false) {

            Valori_Precedenti_Sorgente[0] = (document.getElementById("Sorg_Subnet_Mask_1")).value;
            Valori_Precedenti_Sorgente[1] = (document.getElementById("Sorg_Subnet_Mask_2")).value;
            Valori_Precedenti_Sorgente[2] = (document.getElementById("Sorg_Subnet_Mask_3")).value;
            Valori_Precedenti_Sorgente[3] = (document.getElementById("Sorg_Subnet_Mask_4")).value;

            (document.getElementById("Sorg_Subnet_Mask_1")).value = 255;
            (document.getElementById("Sorg_Subnet_Mask_2")).value = 255;
            (document.getElementById("Sorg_Subnet_Mask_3")).value = 255;
            (document.getElementById("Sorg_Subnet_Mask_4")).value = 255;

            (document.getElementById("Sorg_Subnet_Mask_1")).setAttribute("readonly", true);
            (document.getElementById("Sorg_Subnet_Mask_2")).setAttribute("readonly", true);
            (document.getElementById("Sorg_Subnet_Mask_3")).setAttribute("readonly", true);
            (document.getElementById("Sorg_Subnet_Mask_4")).setAttribute("readonly", true);

        } else {
            (document.getElementById("Sorg_Subnet_Mask_1")).value = Valori_Precedenti_Sorgente[0];
            (document.getElementById("Sorg_Subnet_Mask_2")).value = Valori_Precedenti_Sorgente[1];
            (document.getElementById("Sorg_Subnet_Mask_3")).value = Valori_Precedenti_Sorgente[2];
            (document.getElementById("Sorg_Subnet_Mask_4")).value = Valori_Precedenti_Sorgente[3];

            (document.getElementById("Sorg_Subnet_Mask_1")).removeAttribute("readonly");
            (document.getElementById("Sorg_Subnet_Mask_2")).removeAttribute("readonly");
            (document.getElementById("Sorg_Subnet_Mask_3")).removeAttribute("readonly");
            (document.getElementById("Sorg_Subnet_Mask_4")).removeAttribute("readonly");
        }
    }
}


//  <input class = "Switch_CheckBox" type="checkbox" id="switch_check1" name="switch_check1"> <label for="switch_check1">a</label> <br>

function clickCheckBox() {
    var Value = this.checked;
    const num = (this.name).substring(12); // prendo il numero

    const lab = document.getElementById("switch_checkLab" + num);


    if (Value == true) {
        Position.push(num); // lo inserisco nella scoreboard
        lab.className = "Switch_CheckBox_label_Selected";
    } else {
        ind = Position.indexOf(num);

        if (ind >= 0) {
            Position.splice(ind, 1); // elimino dalla scoreboard
        }
        lab.className = "Switch_CheckBox_label";
    }

    Update_Positions();
}