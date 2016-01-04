<?php
require('../koble_til_database.php');
session_start();
$rfid = $_POST["rfid"];

$get_book = "SELECT * FROM lib_User WHERE rfid='" . $rfid . "'";
$get_book_info_result = $conn->query($get_book);

$books=array();
if ($get_book_info_result->num_rows > 0) {
    // output data of each row
    $book=0;
    while ($row = $get_book_info_result->fetch_assoc()) {
        array_push($books, "TRUE");
    }
    die(json_encode($books));
} else {
    die(json_encode("FALSE"));
}
?>