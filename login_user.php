<?php
require('../../koble_til_database.php');
session_start();
if(!isset($_GET['rfid'])){
    j_die("Mangler n&oslash;dvendige variabler.");
}
$rfid = $_GET["rfid"];

$get_book = "SELECT * FROM lib_User WHERE rfid='" . $rfid . "'";
$get_book_info_result = $conn->query($get_book);

$books=array("FALSE");
if ($get_book_info_result->num_rows > 0) {
    // output data of each row
    if($row = $get_book_info_result->fetch_assoc()){
        $books[0] = "TRUE";
    }
}
die(json_encode($books));
?>