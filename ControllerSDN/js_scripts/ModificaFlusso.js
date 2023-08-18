
var Position = [];

function EventHandler() {
    var Checks = document.querySelectorAll('.Switch_CheckBox');
    Checks.forEach(check => {
        check.addEventListener("click", clickCheckBox, false);
    });

    // Mask_CheckBox
    Checks = document.querySelectorAll('.Mask_CheckBox');
    Checks.forEach(check => {
        check.addEventListener("click", clickMaskCheckBox, false);
    });

}

//  <input class = "Switch_CheckBox" type="checkbox" id="switch_check1" name="switch_check1"> <label for="switch_check1">a</label> <br>

function clickCheckBox() {
    var Value = this.checked;
    const num = (this.name).substring(12); // prendo il numero


    if (Value == true) {
        Position.push(num); // lo inserisco nella scoreboard
    } else {
        ind = Position.indexOf(num);

        if (ind >= 0) {
            Position.splice(ind, 1); // elimino dalla scoreboard
        }
    }

    Update_Positions();
}

function Update_Positions() {
    var Checks = 0, inde = 0 , i = 0;
    
    Checks = document.querySelectorAll('.Switch_CheckBoxPos');
    Checks.forEach(check => {
        check.innerHTML = '-';
    });

    for (i = 0; i < Position.length; i++) {
        inde = Position[i];
        Checks = document.getElementById("switch_checkPos" + inde);
        Checks.innerText = (i + 1) + '°';   
    }

    (document.getElementById("PositionArray")).value = JSON.stringify(Position);
    // Carico Position in un campo input per poi passarlo al PHP tramite metodo POST.
}

var Valori_Precedenti_Sorgente = [0, 0, 0, 0];
var Valori_Precedenti_Destinatario = [0, 0, 0, 0];

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
