<?php
require('../../koble_til_database.php');
session_start();

$res = array('error' => '');
$error = array(
    'no_shelf' => 'Ingen hylle er blitt registrert'
);

$post_vars = array(
    'obligatory' => array(
        'rfid'
    ), 'optional' => array(
        'shelf_id'
    )
);

//Array that contains all the post information
$vars = $post->verify($post_vars);

$vars['rfid'] = trim($vars['rfid'], ";");
$rfid_arr = explode(";", $vars['rfid']);

//Require and initialize the rfid class
require 'rfid.class.php';
$rfid = new RFID();

$shelf = -1;
$book_ids = array();
for($i = 0; $i < count($rfid_arr); $i++){
    $_res = $rfid->type($rfid_arr[$i]);
    if($_res[0] == 'shelf'){
        if($shelf == -1){
            $shelf = $_res[1];
        }else{
            //Two user RFID's are selected
            j_die($error['multiple_shelf_rfid']);
        }
    }else if($_res[0] == 'book'){
        $book_ids[] = $_res[1];
    }
}

if(isset($vars['shelf_id'])){
    $shelf = $vars['shelf_id'];
}

if($shelf == -1){
    j_die($error['no_shelf']);
}

foreach($book_ids as $book_id){
    /*$get_book = "SELECT * FROM lib_Book WHERE bookID = '".$book_id."'";
    $get_book_qry = $conn->query($get_book);
    if($get_book_qry->num_rows > 0){
        if($book = $get_book_qry->fetch_assoc()){
            
        }
    }*/
    $update_book = "UPDATE lib_Book SET shelfID = '".$shelf."' WHERE bookID = '".$book_id."'";
    $update_book_qry = $conn->query($update_book);
    if ($update_book_qry === TRUE) {
        //Success
    } else {
        //Failed
    }
}

$res['shelf'] = $shelf;
$res['books'] = $book_ids;

echo json_encode($res);
?>
