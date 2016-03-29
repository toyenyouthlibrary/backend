$(function() {
    /*
    Highlight all of the places where the selected RFID is printed
    */
    var url = window.location.href;
    var split = url.split("#");
    if(split.length > 1){
        var rfid = split[split.length - 1];
        var prints = document.getElementsByClassName(rfid);
        for(var i = 0; i < prints.length; i++){
            prints[i].style.color = "#FF0000";
        }
    }
});