<?php
//kobler til database
require('../../koble_til_database.php');
session_start();

//Error messages
$error = array(
    'nonexistant_user' => 'Brukeren %username% finnes ikke i v&aring;re systemer.',
    'nonexistant_rfid' => 'Ingen registrerte brukere med denne RFIDen.',
    'no_userinfo' => 'Klarte ikke &aring; finne informasjon om brukeren.',
    
    'failed_login' => 'Klarte ikke &aring; logge inn brukeren.',
    'missing_variables' => 'Login informasjonen har ikke blitt sendt med.'
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

$get_user = "SELECT * FROM lib_User WHERE userID = '" . $user_id . "'";
$get_user_qry = $conn->query($get_user);
if($get_user_qry->num_rows > 0){
    if($user = $get_user_qry->fetch_assoc()){
        //User is found in DB and info is passed on to result array
        $res = array(
            'error' => '',
            'userID' => $user['userID'],
            'username' => $user['username'],
            'firstname' => $user['firstname'],
            'lastname' => $user['lastname'],
            'birth' => $user['birth'],
            'sex' => $user['sex'],
            'school' => $user['school'],
            'address' => $user['address'],
            'registered' => $user['registered'],
            'approved_date' => $user['approved_date']
        );
        
        //To avoid returning null variables
        if($res['approved_date'] == null){
            $res['approved_date'] = "";
        }
    }else{
        j_die($error['nonexistant_user']);
    }
}else{
    j_die($error['nonexistant_user']);
}

//Get the RFID
$res['rfid'] = '';
$get_rfid = "SELECT RFID FROM lib_RFID WHERE userID = '" . $user_id . "'";
$get_rfid_qry = $conn->query($get_rfid);
if($get_rfid_qry->num_rows > 0){
    if($rfid = $get_rfid_qry->fetch_assoc()){
        $res['rfid'] = $rfid['RFID'];
    }
}

//Get the total times, and time, the user has been borrowing books
$get_books = "SELECT TIMESTAMPDIFF(SECOND,outDate,inDate) AS timediff, outDate FROM lib_User_Book WHERE userID='" . $res['userID'] . "'";
$get_books_qry = $conn->query($get_books);
$res['total_times_borrowed'] = 0;
$total_time = 0;
if ($get_books_qry->num_rows > 0) {
    while($book = $get_books_qry->fetch_assoc()){
        $res['total_times_borrowed']++;
        
        if($book['timediff'] == null){
            $total_time += time() - strtotime($book['outDate']);
        }else{
            $total_time += $book['timediff'];
        }
    }
}else{
    //The user hasn't borrowed any books
}

//Calculate the total time the book has been borrowed (in a readable time format)
$res['total_time_borrowed'] = convertSecondsToReadable($total_time);

//Add a contact array to the result
$res['contact'] = array();

//Find the link between the user and the contact entries
$get_user_contact = "SELECT * FROM lib_User_Contact WHERE userID = '" . $res['userID'] . "'";
$get_user_contact_qry = $conn->query($get_user_contact);
if ($get_user_contact_qry->num_rows > 0) {
    //Loop through all contact rows that are related to the user
    while($user_contact = $get_user_contact_qry->fetch_assoc()) {
        //Get the contact info from the id
        $get_contact = "SELECT * FROM lib_Contact WHERE contactID='" . $user_contact["contactID"] . "'";
        $get_contact_qry = $conn->query($get_contact);
        if ($get_contact_qry->num_rows > 0) {
            if($contact = $get_contact_qry->fetch_assoc()) {
                //Add the information to the result array
                $res['contact'][] = array(
                    'phone' => $contact['phone'],
                    'email' => $contact['email']
                );
            }
        }else{
            //The row from lib_User_Contact is not linking to an existing row
        }
    }
}else{
    //The user doesn't have any contact information
}

//Print the result
echo json_encode($res);

function convertSecondsToReadable($seconds){
    //Find difference in time in a readable format
    $dtF = new DateTime("@0");
    $dtT = new DateTime("@$seconds");
    $readable["months"] = (int) $dtF->diff($dtT)->format('%m');
    $readable["days"] = (int) $dtF->diff($dtT)->format('%a');
    $readable["hours"] = (int) $dtF->diff($dtT)->format('%h');
    $readable["minutes"] = (int) $dtF->diff($dtT)->format('%i');
    $readable["seconds"] = (int) $dtF->diff($dtT)->format('%s');
    $result = "";
    $prev_value = false;
    return $readable;
}

?>