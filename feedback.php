<?php
//kobler til database
require('../koble_til_database.php');
session_start();

$error = array(
    'missing_data' => 'All n&oslash;dvendig info er ikke sendt.',
    'unknown_feedback' => 'Den etterspurte typen feedback er ikke akseptert.',
    'empty_comment' => 'Kan ikke lagre tomme kommentarer.',
    'not_int' => 'Stjernevurdering m&aring; sendes som et tall.',
    'unaccepted_int' => 'Stjernevurderinger m&aring; v&aelig;re mellom 1 og 10.',
    'nonexistant_user' => 'Den etterspurte brukeren finnes ikke.',
    'nonexistant_book' => 'Den etterspurte boken finnes ikke.',
    'failed_save' => 'Klarte ikke &aring; lagre feedback.'
);


if(!isset($_POST['user_rfid']) || !isset($_POST['book_rfid']) || !isset($_POST['type']) || !isset($_POST['value'])){
    j_die($error['missing_data']);
}
$user_rfid = $_POST['user_rfid'];
$book_rfid = $_POST['book_rfid'];
$type = $_POST['type'];
$value = $_POST['value'];

//Verify the type of feedback
if(!($type == "comment" || $type == "star")){
    j_die($error['unknown_feedback']);
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

//Verify that the user exists
$get_user = "SELECT userID FROM lib_User WHERE rfid = '" . $user_rfid . "'";
$get_user_qry = $conn->query($get_user);

if($get_user_qry->num_rows > 0){
    if($user = $get_user_qry->fetch_assoc()){
        
    }else{
        j_die($error['nonexistant_user']);
    }
}else{
    j_die($error['nonexistant_user']);
}

//Verify that the book exists
$get_book = "SELECT * FROM lib_Book WHERE RFID = '".$book_rfid."'";
$get_book_qry = $conn->query($get_book);
if($get_book_qry->num_rows > 0){
    if($book = $get_book_qry->fetch_assoc()){
        
    }else{
        j_die($error['nonexistant_book']);
    }
}else{
    j_die($error['nonexistant_book']);
}

//Save the comment

$save_feedback = "INSERT INTO lib_Feedback (user_rfid, book_rfid, type, value, timestamp) VALUES ('".$user_rfid."', '".$book_rfid."', '".$type."', '".$value."', '".time()."')";
$save_feedback_qry = $conn->query($save_feedback);
if($save_feedback_qry === TRUE){
    //Success
    j_die("");
} else {
    j_die($error['failed_save']);
}
?>