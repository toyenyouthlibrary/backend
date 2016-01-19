<?php
//kobler til database
require('../koble_til_database.php');
session_start();
//init av variabler
//l&aring;ne b&oslash;ker

$error = array(
    'user_rfid_missing' => 'Brukerens RFID mangler.',
    'book_rfid_missing' => 'Bokens RFID mangler.',
    'userid_error' => 'Klarte ikke &aring, finne bruker id i databasen.',
    'bookid_error' => 'Klarte ikke &aring, finne bok id i databasen.',
    'error_bookid' => 'Klarte ikke &aring, finne boken i databasen.',
    'error_userid' => 'Klarte ikke &aring, finne brukeren i databasen.',
    'no_error' => '',
    'could_not_lend' => 'Klarte ikke &aring, lagre l&aring,net.',
    'book_lended' => 'Noen har allerede l&aring,nt denne boken.'
);

//her trenger vi forskjellige variable
$user_rfid = (isset($_POST["user_rfid"]) ? $_POST["user_rfid"] : j_die($error['user_rfid_missing']));
$book_rfid = (isset($_POST["book_rfid"]) ? $_POST["book_rfid"] : j_die($error['book_rfid_missing']));
$userid=null;
$bookid=null;

//her b&oslash;r det vurderes om det er lurt at API setter dato for n&aring;r boka er innlevert av forskjellige grunner..
$date= (new DateTime())->format('Y-m-d H:i:s');

//pr&oslash;ver queryen:
//her burde være en rutine som sjekker om boka allerede er l&aring;nt ut, og merker den som innlevert.
//henter ut brukerid fra rfid:
$get_user_id = "SELECT * FROM lib_User WHERE rfid='".$user_rfid."'";
$get_user_id_result = $conn->query($get_user_id);
if ($get_user_id_result->num_rows > 0) {
    while ($row = $get_user_id_result->fetch_assoc()) {
        $userid=$row["userID"];
    }
} else {
    j_die($error['userid_error']);
}

//henter ut bokid med Rfid
$get_book_id = "SELECT * FROM lib_Book WHERE RFID='".$book_rfid."'";
$get_book_id_result = $conn->query($get_book_id);
if ($get_book_id_result->num_rows > 0) {
    while ($row = $get_book_id_result->fetch_assoc()) {
        $bookid=$row["bookID"];
    }
} else {
    j_die($error['bookid_error']);
}

//dobbeltsikrer variablene
if($bookid==null || $bookid==0){
    j_die($error['error_bookid']);
}
if($userid==null || $userid==0){
    j_die($error['error_userid']);
}

//sjekker om boka allerede er leid ut (egt bare for dev)
$check_book = "SELECT * FROM lib_User_Book WHERE bookID='".$bookid."' AND inDate IS NULL";
$check_book_result = $conn->query($check_book);
if ($check_book_result->num_rows > 0) {
    j_die($error['book_lended']);
}

//alt er klart for &aring; leie boka
$lend_book = "INSERT INTO lib_User_Book(userID, outDate, bookID) VALUES ('".$userid."', '".$date."', '".$bookid."')";
    $lend_book_result = $conn->query($lend_book);
if ($lend_book_result === TRUE) {
    j_die($error['no_error']);
} else {
    j_die($error['could_not_lend']);

}

//

?>