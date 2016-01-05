<?php
//kobler til database
require('../koble_til_database.php');
session_start();

$error = array(
    'no_books' => 'Ingen b&oslash;ker er registrert.'
);


$get_books = "SELECT * FROM lib_Book WHERE bookID = 0";
$get_books_qry = $conn->query($get_books);

$res = array('error' => "");

if ($get_books_qry->num_rows > 0) {
    while($book = $get_books_qry->fetch_assoc()){
        $res['books'][] = array(
            'id' => $book['bookID'],
            'RFID' => $book['RFID'],
            'ISBN' => $book['ISBN']
        );
    }
    if($res == array('error' => "")){
        $res['error'] = $error['no_books'];
    }
    echo json_encode($res);
}else{
    $res['error'] = $error['no_books'];
    echo json_encode($res);
}

?>
<!--
{"error":"","books":[{"id":null,"RFID":"1","ISBN":"256-17-915-5679-7"},{"id":null,"RFID":"36","ISBN":"038-17-788-7359-1"}]}
{
   "error": "",
   "books": [
      {
         "id": null,
         "RFID": "1",
         "ISBN": "256-17-915-5679-7"
      },
      {
         "id": null,
         "RFID": "36",
         "ISBN": "038-17-788-7359-1"
      }
   ]
}

{"error":"Ingen bøker er registrert.","books":[]}
-->
