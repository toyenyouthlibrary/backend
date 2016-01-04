<?php
//kobler til database
require('../koble_til_database.php');
session_start();
//init av variabler
//låne bøker

//errorvariabler
$user_rfid_missing="User rfid is missing";
$book_rfid_missing="Book rfid is missing";
$userid_error="Could not get userID";
$bookid_error="Could not get bookID";
$error_bookid="Error with bookID";
$error_userid="Error with userID";
$no_error="";
$could_not_lend="Could not insert values into lend table";
$book_lended="The book with this id is already lended to someone else";
$book_not_lended="The book with this id is not lended to anyone";
$could_not_deliver="Could not deliver book";

//funksjon for å dø :( med errormelding
function error_die($error_message) {
    $error_msg=array();
    $error_msg["error"]=$error_message;
    die(json_encode($error_msg));
}
//her trenger vi forskjellige variable
$book_rfid = (isset($_POST["book_rfid"]) ? $_POST["book_rfid"] : error_die($book_rfid_missing));
$bookid=null;
//her bør det vurderes om det er lurt at API setter dato for når boka er innlevert av forskjellige grunner..
$date= (new DateTime())->format('Y-m-d H:i:s');

//henter ut bokid med Rfid
$get_book_id = "SELECT * FROM lib_Book WHERE RFID='".$book_rfid."'";
$get_book_id_result = $conn->query($get_book_id);
if ($get_book_id_result->num_rows > 0) {
    while ($row = $get_book_id_result->fetch_assoc()) {
        $bookid=$row["bookID"];
    }
} else {
    error_die($bookid_error);
}

//dobbeltsikrer variablene
if($bookid==null || $bookid==0){
    die($error_bookid);
}

$lib_user_book_ID=null;
//sjekker om boka er leid ut (egt bare for dev)
$check_book = "SELECT * FROM lib_User_Book WHERE bookID='".$bookid."' AND inDate IS NULL";
$check_book_result = $conn->query($check_book);
if ($check_book_result->num_rows > 0) {
    while ($row = $check_book_result->fetch_assoc()) {
        $lib_user_book_ID=$row["user_book_ID"];
    }
}else{
    error_die($book_not_lended);
}

//alt er klart for å levere boka
$deliver_book = "UPDATE lib_User_Book SET inDate='".$date."' WHERE user_book_ID='".$lib_user_book_ID."'";
$deliver_book_result = $conn->query($deliver_book);
if ($deliver_book_result === TRUE) {
    error_die($no_error);
} else {
    error_die($could_not_deliver);
}
//end

?>