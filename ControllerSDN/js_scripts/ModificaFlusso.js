
var Position = [];

function EventHandler() {
    const Checks = document.querySelectorAll('.Switch_CheckBox');
    Checks.forEach(check => {
        check.addEventListener("click", clickCheckBox, false);
    });

    var c = document.getElementById("check_box_container");
    c.addEventListener("click", clickCheckBoxContainer, false);
}

//  <input class = "Switch_CheckBox" type="checkbox" id="switch_check1" name="switch_check1"> <label for="switch_check1">a</label> <br>

function clickCheckBox() {
    console.log("-------");
    var Value = this.checked;
    const num = (this.name).substring(12); // prendo il numero
    

    if(Value == true){
        Position.push(num); // lo inserisco nella scoreboard
    }else{
        ind = Position.indexOf(num);

        if(ind >= 0){
            Position.splice(ind , 1); // elimino dalla scoreboard
        }
    }

    Update_Positions();
}

function Update_Positions(){
    var c;
    const n = (document.getElementsByClassName("Switch_CheckBox")).length;
    
    const Checks = document.querySelectorAll('.Switch_CheckBoxPos');
    Checks.forEach(check => {
        check.innerHTML='-';
    });

    for(var i = 0 ; i < Position.length; i++){
        inde = Position[i];
        c = document.getElementById("switch_checkPos"+inde);
        c.innerText = (i+1);
    }
}

function clickCheckBoxContainer() {
    //console.log("click");
}