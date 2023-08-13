
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

    var c = document.getElementById("check_box_container");
    c.addEventListener("click", clickCheckBoxContainer, false);
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

    // const n = (document.getElementsByClassName("Switch_CheckBox")).length;

    var Checks = document.querySelectorAll('.Switch_CheckBoxPos');
    Checks.forEach(check => {
        check.innerHTML = '-';
    });

    var c = 0;
    for (var i = 0; i < Position.length; i++) {
        inde = Position[i];
        c = document.getElementById("switch_checkPos" + inde);
        c.innerText = (i + 1);
    }

    (document.getElementById("PositionArray")).value = JSON.stringify(Position);
    // Carico Position in un campo input per poi passarlo al PHP tramite metodo POST.
}

function clickMaskCheckBox() {
    var c = this.name;

    if (c == "MaskCheckBox_Dest") {
        var a = 0, b = 0;
        a = (document.getElementById("Dest_Subnet_Mask_1")).readOnly;

        if (a == false) b = 255;
        else b = 0;

        (document.getElementById("Dest_Subnet_Mask_1")).value = b;
        (document.getElementById("Dest_Subnet_Mask_2")).value = b;
        (document.getElementById("Dest_Subnet_Mask_3")).value = b;
        (document.getElementById("Dest_Subnet_Mask_4")).value = b;

        if (a == true) {
            (document.getElementById("Dest_Subnet_Mask_1")).removeAttribute("readonly");
            (document.getElementById("Dest_Subnet_Mask_2")).removeAttribute("readonly");
            (document.getElementById("Dest_Subnet_Mask_3")).removeAttribute("readonly");
            (document.getElementById("Dest_Subnet_Mask_4")).removeAttribute("readonly");
        } else {
            (document.getElementById("Dest_Subnet_Mask_1")).setAttribute("readonly", true);
            (document.getElementById("Dest_Subnet_Mask_2")).setAttribute("readonly", true);
            (document.getElementById("Dest_Subnet_Mask_3")).setAttribute("readonly", true);
            (document.getElementById("Dest_Subnet_Mask_4")).setAttribute("readonly", true);
        }

    } else {
        var a = 0, b = 0;
        a = (document.getElementById("Sorg_Subnet_Mask_1")).readOnly;

        if (a == false) b = 255;
        else b = 0;

        (document.getElementById("Sorg_Subnet_Mask_1")).value = b;
        (document.getElementById("Sorg_Subnet_Mask_2")).value = b;
        (document.getElementById("Sorg_Subnet_Mask_3")).value = b;
        (document.getElementById("Sorg_Subnet_Mask_4")).value = b;

        if (a == true) {
            (document.getElementById("Sorg_Subnet_Mask_1")).removeAttribute("readonly");
            (document.getElementById("Sorg_Subnet_Mask_2")).removeAttribute("readonly");
            (document.getElementById("Sorg_Subnet_Mask_3")).removeAttribute("readonly");
            (document.getElementById("Sorg_Subnet_Mask_4")).removeAttribute("readonly");
        } else {
            (document.getElementById("Sorg_Subnet_Mask_1")).setAttribute("readonly", true);
            (document.getElementById("Sorg_Subnet_Mask_2")).setAttribute("readonly", true);
            (document.getElementById("Sorg_Subnet_Mask_3")).setAttribute("readonly", true);
            (document.getElementById("Sorg_Subnet_Mask_4")).setAttribute("readonly", true);
        }


    }
}

function clickCheckBoxContainer() {
    //console.log("click");
}