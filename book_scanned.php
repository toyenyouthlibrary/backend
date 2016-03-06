<?php
/*
Split rfid by ;
Check which of them is a user
Error if more rfid's are users
Last opp alle bøker som lånte
*/
require('../../koble_til_database.php');
session_start();

$error = array(
    'multiple_user_rfid' => 'Du kan bare skanne 1 bruker-enhet.',
    'no_book_rfid' => 'Du m&aring; skanne minst 1 bok.',
    'failed_to_lend_book' => 'Klarte ikke &aring; l&aring;ne boken.',
    'no_user_rfid' => 'For &aring; l&aring;ne b&oslash;ker m&aring; du skanne en bruker-enhet.',
    'nonexistant_book' => 'Boken ble ikke funnet i databasen.',
    'failed_to_deliver_book' => 'Klarte ikke &aring; levere boken.',
    'deliver_success' => 'Du har levert boken.',
    'lend_success' => 'Du har l&aring;nt boken.'
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

$user = 0;
$books = array();
for($i = 0; $i < count($rfid_arr); $i++){
    $res = $rfid->type($rfid_arr[$i]);
    if($res[0] == 'user'){
        if($user == 0){
            $user = $rfid_arr[$i];
        }else{
            //Two user RFID's are selected
            j_die($error['multiple_user_rfid']);
        }
    }else if($res[0] == 'book'){
        $books[] = $res[1];
    }
}
if(count($books) < 1){
    //No books have been selected
    j_die($error['no_book_rfid']);
}

/*
    Deliver / lend the book
*/
$date= (new DateTime())->format('Y-m-d H:i:s');

$res = array('error' => '');
$deliver = array();
for($i = 0; $i < count($books); $i++){
    $get_book = "SELECT bookID, title, author, ISBN FROM lib_Book WHERE bookID = '" . $books[$i] . "'";
    $get_book_qry = $conn->query($get_book);
    if($get_book_qry->num_rows > 0){
        if($book = $get_book_qry->fetch_assoc()){
            //Book exists
            $get_book_user = "SELECT * FROM lib_User_Book WHERE bookID = '" . $book['bookID'] . "' ORDER BY user_book_ID DESC LIMIT 1";
            $get_book_user_qry = $conn->query($get_book_user);
            $lended = false;
            if($get_book_user_qry->num_rows > 0){
                if($book_user = $get_book_user_qry->fetch_assoc()){
                    //Entry for the book exists
                    if($book_user['inDate'] == null){
                        $lended = true;
                    }
                }
            }
            if($lended){
                //Deliver book
                $deliver[] = $book;
            }else{
                if($user != 0){
                    //Lend book
                    $insert_user_book = "INSERT INTO lib_User_Book (userID, outDate, bookID) VALUES 
                        ('" . $user . "', '" . $date . "', '" . $book['bookID'] . "')";
                    $insert_user_book_qry = $conn->query($insert_user_book);
                    if($insert_user_book_qry === TRUE){
                        //Success
                        $res['status'][] = array(
                            'book_info' => get_book_info($book),
                            'error' => $error['lend_success']
                        );
                    }else{
                        //Failed lend book
                        $res['status'][] = array(
                            'book_info' => get_book_info($book),
                            'error' => $error['failed_to_lend_book']
                        );
                    }
                }else{
                    //No user RFID sent
                    $res['status'][] = array(
                        'book_info' => get_book_info($book),
                        'error' => $error['no_user_rfid']
                    );
                }
            }
        }else{
            //Book doesn't exist
            $res['status'][] = array(
                'book_info' => '',
                'error' => $error['nonexistant_book']
            );
        }
    }else{
        //Book doesn't exist
        $res['status'][] = array(
            'book_info' => '',
            'error' => $error['nonexistant_book']
        );
    }
}

//Deliver all books that are supposed to be delivered
$where_st = "";
for($i = 0; $i < count($deliver); $i++){
    if($where_st == ""){
        $where_st = "WHERE bookID = '" . $deliver[$i]['bookID'] . "' ";
    }else{
        $where_st .= "OR bookID = '" . $deliver[$i]['bookID'] . "'";
    }
}
if($where_st != ""){
    $deliver_books = "UPDATE lib_User_Book SET inDate = '" . $date . "' " . $where_st;
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

echo json_encode($res);

function get_book_info($book){
    return array(
        'title' => $book['title'],
        'author' => $book['author'],
        'ISBN' => $book['ISBN']
    );
}
?>
