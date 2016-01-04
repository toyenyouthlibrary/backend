<?php
//kobler til database
require('../koble_til_database.php');
session_start();

$nonexistant_user = 'User does not exist.';

$username = $_GET['username'];

$get_user = "SELECT userID FROM lib_User WHERE username='" . $username . "'";
$get_user_info_result = $conn->query($get_user);

$res = array();

if ($get_user_info_result->num_rows > 0) {
    while($user = $get_user_info_result->fetch_assoc()){
        $get_books = "SELECT * FROM lib_User_Book WHERE userID = '".$user['userID']."'";
        $get_books_qry = $conn->query($get_books);
        if($get_books_qry->num_rows > 0){
            while($book = $get_books_qry->fetch_assoc()){
                $res[$book['bookID']]['outData'] = $book['outDate'];
                $res[$book['bookID']]['inData'] = $book['inDate'];
                $get_book_details = "SELECT * FROM lib_Book WHERE bookID='".$book['bookID']."'";
                $get_book_details_qry = $conn->query($get_book_details);
                if($get_book_details_qry->num_rows > 0){
                    if($book_details = $get_book_details_qry->fetch_assoc()){
                        $res[$book['bookID']]['RFID'] = $book_details['RFID'];
                        $res[$book['bookID']]['ISBN'] = $book_details['ISBN'];
                    }
                }
            }
        }
        echo json_encode($res);
    }
} else {
    $res['error'] = $nonexistant_user;
    die(json_encode($res));
}
if(!isset($get_books)){
    $res['error'] = $nonexistant_user;
    die(json_encode($res));
}

?>