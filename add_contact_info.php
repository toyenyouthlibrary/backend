<?php
//kobler til database
require('../koble_til_database.php');
session_start();

$error = array(
    'missing_info' => 'All n&oslash;dvendig informasjon er ikke sendt.',
    'nonexistant_user' => 'Den etterspurte brukeren finnes ikke',
    'failed_to_save_contact' => 'Klarte ikke &aring; lagre kontaktinformasjonen.',
    'failed_to_get_contact' => 'Klarte ikke &aring; finne kontaktinformasjonen.',
    'failed_to_get_user' => 'Klarte ikke &aring; finne brukerinformasjonen.'
);

if(!isset($_POST['username']) || !isset($_POST['phone']) || !isset($_POST['email'])){
    j_die($error['missing_info']);
}

$username = $_POST["username"];
$phone = $_POST["phone"];
$email = $_POST['email'];

/*
 * variabelen $conn er hentet fra koble_til_database.php
 */

//sjekker om brukernavnet er tatt
$test_uname = "SELECT * FROM lib_User WHERE username='" . $username . "'";
$test_uname_result = $conn->query($test_uname);
if ($test_uname_result->num_rows != 0) {
    j_die($error['nonexistant_user']);
}else{
    if($userinfo = $test_uname_result->fetch_assoc()){
        $userID = $userinfo['userID'];
    }else{
        j_die($error['failed_to_get_user']);
    }
}

//oppretter en standardbruker, uten ekstra kontaktinfo
$insert_user=
    "INSERT INTO lib_Contact (phone, email) VALUES
    ('".$phone."', '".$email."')";
$insert_user_result = $conn->query($insert_user);
if ($insert_user_result===TRUE) {
    
}else{
    j_die($error['failed_to_save_contact']);
}

$get_contact = "SELECT contactID FROM lib_Contact WHERE phone = '".$phone."' AND email = '".$email."' ORDER BY contactID DESC LIMIT 1";
$get_contact_qry = $conn->query($get_contact);

if($get_contact_qry->num_rows > 0){
    if($contact = $get_contact_qry->fetch_assoc()){
        $insert_contact = "INSERT INTO lib_User_Contact (contactID, userID) VALUES ('".$contact['contactID']."', '".$userID."')";
        j_die("");
    }else{
        j_die($error['failed_to_get_contact']);
    }
}else{
    j_die($error['failed_to_get_contact']);
}


?>