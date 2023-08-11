function EventHandler(){
    var list = document.getElementsByClassName("option_voice");
    console.log(list);
    for(const t of list){
        t.addEventListener("mouseover" , ShowOptionDiv , false);
    }

}

function ShowOptionDiv(){
    
}
