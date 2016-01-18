<?php
//kobler til database
require('../koble_til_database.php');
session_start();

$error = array(
    'unknown_feedback' => 'Den etterspurte typen feedback er ikke akseptert.',
    'empty_comment' => 'Kan ikke lagre tomme kommentarer.',
    'not_int' => 'Stjernevurdering m&aring; sendes som et tall.',
    'nonexistant_user' => 'Den etterspurte brukeren finnes ikke.',
    'nonexistant_book' => 'Den etterspurte boken finnes ikke.'
);


if(!isset($_POST['userid']) || !isset($_POST['bookid']) || !isset($_POST['type']) !isset($_POST['value'])){
    j_die('All n&oslash;dvendig info er ikke sendt.')
}
$userid = $_POST['userid'];
$bookid = $_POST['bookid'];
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
    if(!is_int($value)){
        j_die($error['not_int']);
    }
}

//Verify that the user exists
$get_user = "SELECT userID FROM lib_User WHERE userID = '" . $userid . "'";
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
$get_book = "SELECT * FROM lib_Book WHERE bookID = '".$bookid."'";
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



//Function to kill the page
function j_die($str){
    die(
        json_encode(
            array(
                'error' => $str
            )
        )
    );
}
?>