<?php
/*
POST /api/update_shelves.php HTTP/1.1
Host: tung.deichman.no
Cache-Control: no-cache
Content-Type: application/x-www-form-urlencoded

rfid=urn%3Aepc%3Aid%3Asgtin%3A9788251.966241.11258753042&shelf_id=1
*/
require('../../koble_til_database.php');
session_start();

$log = "INSERT INTO `lib_Error` (`timestamp`, `error`, `post`, `get`) VALUES 
    ('".time()."', '', 
    '".str_replace("'", "\'", mb_convert_encoding(json_encode($_POST), "UTF-8", 'HTML-ENTITIES'))."', 
    '".str_replace("'", "\'", mb_convert_encoding(json_encode($_GET), "UTF-8", 'HTML-ENTITIES'))."')";
$log_qry = $conn->query($log);

$res = array('error' => '');
$error = array(
    'no_shelf' => 'Ingen hylle er blitt registrert'
);

if(isset($_GET['shelf_id'])){
    $_POST['shelf_id'] = $_GET['shelf_id'];
}

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
        $book_ids[] = $rfid_arr[$i];
    }
}

if(isset($vars['shelf_id'])){
    $shelf = $vars['shelf_id'];
}

if($shelf == -1){
    j_die($error['no_shelf']);
}

foreach($book_ids as $book_rfid){
    /*$get_book = "SELECT * FROM lib_Book WHERE bookID = '".$book_id."'";
    $get_book_qry = $conn->query($get_book);
    if($get_book_qry->num_rows > 0){
        if($book = $get_book_qry->fetch_assoc()){
            
        }
    }*/
    $update_book = "UPDATE lib_RFID SET `_shelfID` = '".$shelf."' WHERE RFID = '".$book_rfid."'";
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
