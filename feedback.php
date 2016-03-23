<?php
//kobler til database
require('../../koble_til_database.php');
session_start();

$error = array(
    'missing_data' => 'All n&oslash;dvendig info er ikke sendt.',
    'unknown_feedback' => 'Den etterspurte typen feedback er ikke akseptert.',
    'empty_comment' => 'Kan ikke lagre tomme kommentarer.',
    'not_int' => 'Stjernevurdering m&aring; sendes som et tall.',
    'unaccepted_int' => 'Stjernevurderinger m&aring; v&aelig;re mellom 1 og 10.',
    'nonexistant_user' => 'Den etterspurte brukeren finnes ikke.',
    'nonexistant_book' => 'Den etterspurte boken finnes ikke.',
    'failed_save' => 'Klarte ikke &aring; lagre feedback.',
    'missing_rfid' => 'Man m&aring; sende 2 RFIDer.',
    'wrong_rfid_types' => 'Man m&aring; sende med 1 bok og 1 bruker.'
);


if(!isset($_POST['rfid']) || !isset($_POST['type']) || !isset($_POST['value'])){
    j_die($error['missing_data']);
}
$rfid = $_POST['rfid'];
$type = $_POST['type'];
$value = $_POST['value'];

//Verify the type of feedback
if(!($type == "comment" || $type == "star")){
    j_die($error['unknown_feedback']);
}

//Verify the RFIDs
$rfid = trim($rfid, ";");
$rfids = explode(";", $rfid);
if(count($rfids) == 2){
    require 'rfid.class.php';
    $rfid = new RFID();
    $user = -1;
    $book = -1;
    for($i = 0; $i < count($rfids); $i++){
        $res = $rfid->type($rfids[$i]);
        if($res[0] == 'user'){
            if($user === -1){
                $user = $rfids[$i];
            }else{
                j_die($error['wrong_rfid_types']);
            }
        }else if($res[0] == 'book'){
            if($book === -1){
                $book = $rfids[$i];
            }else{
                j_die($error['wrong_rfid_types']);
            }
        }
    }
}else{
    j_die($error['missing_rfid']);
}

//Verify the type of value
if($type == "comment"){
    if($value == ""){
        j_die($error['empty_comment']);
    }
}else{
    if(!is_numeric($value)){
        j_die($error['not_int']);
    }else{
        if(intval($value) > 10 || intval($value) < 1){
            j_die($error['unaccepted_int']);
        }
    }
}

//Save the comment
$date= (new DateTime())->format('Y-m-d H:i:s');

$save_feedback = "INSERT INTO lib_Feedback (user_rfid, book_rfid, type, value, timestamp) VALUES ('".$user."', '".$book."', '".$type."', '".$value."', '".$date."')";
$save_feedback_qry = $conn->query($save_feedback);
if($save_feedback_qry === TRUE){
    //Success
    j_die("");
} else {
    j_die($error['failed_save']);
}
?>