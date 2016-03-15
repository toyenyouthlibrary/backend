var current_field_id = 0;

$(document).ready(function(){
    var pressed = false;
    var chars = [];
    $(window).keypress(function(e) {
        var interval = setInterval(function(){
            clearInterval(interval);
        },1);

        chars.push(String.fromCharCode(e.which));

        if (pressed == false) {
            setTimeout(function(){
                if (chars.length >= 32) {
                    var rfid = [];
                    for (var i = 0; i < chars.length; i++) {
                        if (chars[i] != '\x00') {
                            rfid.push(chars[i]);
                        }
                    }
                    var barcode = rfid.join("");
                    console.log("Barcode Scanned: " + barcode);
                    tagInput(barcode);
                }
                chars = [];
                pressed = false;
            },1000);
        }
        pressed = true;
    });
});

function tagInput(rfid){
    if(current_field_id != 0){
        document.getElementById(current_field_id).getElementsByClassName("new")[0].value = rfid;
        document.getElementById(current_field_id).getElementsByClassName("p")[0].innerHTML = rfid;
        document.getElementById(current_field_id).submit();
        console.log("Barcode is valid");
    }
}

function change_rfid(key){
    current_field_id = "rfid_" + key;
    //Update the color of the text so it will be easier to see
    for(var i = 0; document.getElementById('rfid_' + i) !== null; i++){
        document.getElementById("rfid_" + i).getElementsByClassName("p")[0].style.color = "#000";
    }
    document.getElementById(current_field_id).getElementsByClassName("p")[0].style.color = "#F00";
}