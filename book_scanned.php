<?php
if(!defined('ROOT')){ define('ROOT', getcwd().'/'); }
require('../../koble_til_database.php');
session_start();

$error = array(
    'multiple_user_rfid' => 'Du kan bare skanne 1 bruker-enhet.',
    'no_book_rfid' => 'Du m&aring; skanne minst 1 bok.',
    'failed_to_lend_book' => 'Klarte ikke &aring; l&aring;ne boken.',
    'no_user_rfid' => 'For &aring; l&aring;ne b&oslash;ker m&aring; du skanne en bruker-enhet.',
    'nonexistant_book' => 'Boken ble ikke funnet i databasen.',
    'failed_to_deliver_book' => 'Klarte ikke &aring; levere boken.',
    'deliver_success' => '',
    'lend_success' => '',
    'only_one_action_allowed' => 'Du kan ikke l&aring;ne og levere samtidig.'
);

$post_vars = array(
    'obligatory' => array(
        'rfid'
    )
);

//Array that contains all the post information
$vars = $post->verify($post_vars);

$vars['rfid'] = trim($vars['rfid'], ";");
$rfid_arr = explode(";", $vars['rfid']);

//Require and initialize the rfid class
require 'rfid.class.php';
$rfid = new RFID();

$user = -1;
$books = array();
for($i = 0; $i < count($rfid_arr); $i++){
    $res = $rfid->type($rfid_arr[$i]);
    if($res[0] == 'user'){
        if($user == -1){
            $user = $res[1];
        }else{
            //Two user RFID's are selected
            j_die($error['multiple_user_rfid']);
        }
    }else if($res[0] == 'book'){
        $books[] = array('bookID' => $res[1], 'rfid' => $rfid_arr[$i], 'shelfID' => $res[2]);
    }
}
if(count($books) < 1){
    //No books have been selected
    j_die($error['no_book_rfid']);
}

$action = 0;
/*
    Deliver / lend the book
*/
$date= (new DateTime())->format('Y-m-d H:i:s');

$res = array('error' => '', 'type' => 0);
$deliver = array();
$lend = array();
require ROOT.'scan_book.class.php';
$sb = new ScanBook();
for($i = 0; $i < count($books); $i++){
    if($sb->isLended($books[$i]['rfid'])){
        //Book shall be delivered
        if($res['type'] !== "lend"){
            $res['type'] = 'deliver';
            require ROOT.'admin/info.class.php';
            $info = new Info("books", $books[$i]['bookID']);
            $result = $info->getInfo();
            $result['RFID'] = $books[$i]['rfid'];
            $deliver[] = $result;
        }else{
            j_die($error['only_one_action_allowed']);
        }
    }else{
        //Book shall be lended
        //Check if user has been set
        if($user === -1){
            j_die($error['no_user_rfid']);
        }
        if($res['type'] !== "deliver"){
            $res['type'] = 'lend';
            require ROOT.'admin/info.class.php';
            $info = new Info("books", $books[$i]['bookID']);
            $result = $info->getInfo();
            $result['RFID'] = $books[$i]['rfid'];
            $lend[] = array(
                'user' => $user,
                'date' => $date,
                'book' => $result
            );
        }else{
            j_die($error['only_one_action_allowed']);
        }
    }
}

//Lend shiet
for($i = 0; $i < count($lend); $i++){
    $insert_user_book = "INSERT INTO lib_User_Book (userID, outDate, bookRFID) VALUES 
        ('" . $lend[$i]['user'] . "', '" . $lend[$i]['date'] . "', '" . $lend[$i]['book']['RFID'] . "')";
    $insert_user_book_qry = $conn->query($insert_user_book);
    if($insert_user_book_qry === TRUE){
        //Success
        $res['status'][] = array(
            'book_info' => get_book_info($lend[$i]['book']),
            'error' => $error['lend_success']
        );
    }else{
        //Failed lend book
        $res['status'][] = array(
            'book_info' => get_book_info($lend[$i]['book']),
            'error' => $error['failed_to_lend_book']
        );
    }
}

//Deliver all books that are supposed to be delivered
$where_st = "";
for($i = 0; $i < count($deliver); $i++){
    if($where_st == ""){
        $where_st = "WHERE bookRFID = '" . $deliver[$i]['RFID'] . "' ";
    }else{
        $where_st .= "OR bookRFID = '" . $deliver[$i]['RFID'] . "'";
    }
}
if($where_st != ""){
    $deliver_books = "UPDATE lib_User_Book SET inDate = '" . $date . "' " . $where_st . " ORDER BY user_book_ID DESC LIMIT 1";
    $deliver_books_qry = $conn->query($deliver_books);
    if ($deliver_books_qry === TRUE) {
        //Success
        for($i = 0; $i < count($deliver); $i++){
            $res['status'][] = array(
                'book_info' => get_book_info($deliver[$i]),
                'error' => $error['deliver_success']
            );
        }
    } else {
        //Failed
        for($i = 0; $i < count($deliver); $i++){
            $res['status'][] = array(
                'book_info' => get_book_info($deliver[$i]),
                'error' => $error['failed_to_deliver_book']
            );
        }
    }
}

//Get username
$res['username'] = "";
$res['userRFID'] = "";
if($user != 0){
    $get_rfid = "SELECT RFID FROM lib_RFID WHERE userID = '".$user."'";
    $get_rfid_qry = $conn->query($get_rfid);
    if($get_rfid_qry->num_rows > 0){
        if($rfid = $get_rfid_qry->fetch_assoc()){
            $res['userRFID'] = $rfid['RFID'];
        }
    }
    $get_username = "SELECT username FROM lib_User WHERE userID = '".$user."' AND active = 1";
    $get_username_qry = $conn->query($get_username);
    if($get_username_qry->num_rows > 0){
        if($user = $get_username_qry->fetch_assoc()){
            $res['username'] = $user['username'];
        }
    }
}

echo json_encode($res);

function get_book_info($book){
    include 'admin/list.class.php';
    $list = new Lists("shelves");
    $shelf_name = $list->getShelfName($book['shelfID']);
    return array(
        'title' => mb_convert_encoding($book['title'], 'HTML-ENTITIES', "UTF-8"),
        'author' => mb_convert_encoding($book['author'], 'HTML-ENTITIES', "UTF-8"),
        'ISBN10' => $book['ISBN10'],
        'ISBN13' => $book['ISBN13'],
        'delivery_date' => 'xx.xx.xx',
        'shelf' => $shelf_name
    );
}
?>
