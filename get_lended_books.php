<?php
//kobler til database
require('../../koble_til_database.php');
session_start();

$error = array(
    'nonexistant_user' => 'Den etterspurte brukeren finnes ikke i v&aring;re systemer.'
);

//Check if the id is sent and its session exists
if(isset($_POST["id"])){
    //The ID is sent
    require 'login.class.php';
    $login = new Login();
    $response = $login->login($_POST['id']);
    if(is_numeric($response)){
        $user_id = $response;
    }else{
        j_die($error['failed_login']);
    }
}else{
    j_die($error['missing_variables']);
}

$get_user = "SELECT userID FROM lib_User WHERE userID = '" . $user_id . "'";
$get_user_info_result = $conn->query($get_user);

$res = array('error' => "");

if ($get_user_info_result->num_rows > 0) {
    while($user = $get_user_info_result->fetch_assoc()){
        $get_books = "SELECT * FROM lib_User_Book WHERE userID = '".$user['userID']."'";
        $get_books_qry = $conn->query($get_books);
        if($get_books_qry->num_rows > 0){
            while($book = $get_books_qry->fetch_assoc()){
                
                $get_book_details = "SELECT * FROM lib_Book WHERE bookID='".$book['bookID']."'";
                $get_book_details_qry = $conn->query($get_book_details);
                if($get_book_details_qry->num_rows > 0){
                    if($book_details = $get_book_details_qry->fetch_assoc()){
                        //Get RFID
                        $_rfid = "";
                        $get_rfid = "SELECT RFID FROM lib_RFID WHERE bookID = '" . $book_details['bookID'] . "'";
                        $get_rfid_qry = $conn->query($get_rfid);
                        if($get_rfid_qry->num_rows > 0){
                            if($rfid = $get_rfid_qry->fetch_assoc()){
                                $_rfid = $rfid['RFID'];
                            }
                        }
                        //Prevent null variables
                        $inDate = $book['inDate'] ?: "";
                        //Save results
                        $res['books'][] = array(
                            'outDate' => $book['outDate'],
                            'inDate' => $inDate,
                            'RFID' => $_rfid,
                            'ISBN10' => $book_details['ISBN10'],
                            'ISBN13' => $book_details['ISBN13'],
                            'title' => $book_details['title'],
                            'author' => $book_details['author'],
                            'type' => $book_details['type'],
                            'language' => $book_details['language']
                        );
                    }
                }
            }
        }else{
            $res['error'] = "Brukeren har ikke l&aring;nt noen b&oslash;ker.";
        }
        echo json_encode($res);
    }
} else {
    j_die($error['nonexistant_user']);
}
if(!isset($get_books)){
    j_die($error['nonexistant_user']);
}

?>