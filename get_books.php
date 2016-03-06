<?php
//kobler til database
require('../../koble_til_database.php');
session_start();

$error = array(
    'no_books' => 'Ingen b&oslash;ker er registrert.'
);


$get_books = "SELECT * FROM lib_Book";
$get_books_qry = $conn->query($get_books);

$res = array('error' => "");

if ($get_books_qry->num_rows > 0) {
    while($book = $get_books_qry->fetch_assoc()){
        //Find RFID
        $_rfid = "";
        $get_rfid = "SELECT RFID FROM lib_RFID WHERE bookID = '" . $book['bookID'] . "'";
        $get_rfid_qry = $conn->query($get_rfid);
        if($get_rfid_qry->num_rows > 0){
            if($rfid = $get_rfid_qry->fetch_assoc()){
                $_rfid = $rfid['RFID'];
            }
        }
        //Store results
        $res['books'][] = array(
            'id' => $book['bookID'],
            'RFID' => $_rfid,
            'ISBN' => $book['ISBN'],
            'title' => $book['title'],
            'author' => $book['author'],
            'type' => $book['type'],
            'language' => $book['language']
        );
    }
    if($res == array('error' => "")){
        j_die($error['no_books']);
    }
    echo json_encode($res);
}else{
    j_die($error['no_books']);
}

?>