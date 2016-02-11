<?php
//kobler til database
require('../../koble_til_database.php');
session_start();
//init av variabler
//l&aring;ne b&oslash;ker

$error = array(
    'user_rfid_missing' => 'Brukerens RFID mangler.',
    'book_rfid_missing' => 'Bokens RFID mangler.',
    'userid_error' => 'Klarte ikke &aring; finne bruker id i databasen.',
    'bookid_error' => 'Klarte ikke &aring, finne bok id i databasen.',
    'error_bookid' => 'Klarte ikke &aring, finne boken i databasen.',
    'error_userid' => 'Klarte ikke &aring, finne brukeren i databasen.',
    'no_error' => '',
    'could_not_lend' => 'Klarte ikke &aring, registrere bokl&aring,net.',
    'book_lended' => 'Det er allerede noen som l&aring,ner denne boken.',
    'book_not_lended' => 'Det er ingen som har l&aring,nt denne boken.',
    'could_not_deliver' => 'Klarte ikke &aring, avregistrere l&aring,net.'
);

//her trenger vi forskjellige variable
$book_rfid = (isset($_POST["book_rfid"]) ? $_POST["book_rfid"] : j_die($error['book_rfid_missing']));
$bookid=null;
//her b&oslash;r det vurderes om det er lurt at API setter dato for n&aring;r boka er innlevert av forskjellige grunner..
$date= (new DateTime())->format('Y-m-d H:i:s');

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

$lib_user_book_ID=null;
//sjekker om boka er leid ut (egt bare for dev)
$check_book = "SELECT * FROM lib_User_Book WHERE bookID='".$bookid."' AND inDate IS NULL";
$check_book_result = $conn->query($check_book);
if ($check_book_result->num_rows > 0) {
    while ($row = $check_book_result->fetch_assoc()) {
        $lib_user_book_ID=$row["user_book_ID"];
    }
}else{
    j_die($error['book_not_lended']);
}

//alt er klart for &aring; levere boka
$deliver_book = "UPDATE lib_User_Book SET inDate='".$date."' WHERE user_book_ID='".$lib_user_book_ID."'";
$deliver_book_result = $conn->query($deliver_book);
if ($deliver_book_result === TRUE) {
    j_die($error['no_error']);
} else {
    j_die($error['could_not_deliver']);
}

?>