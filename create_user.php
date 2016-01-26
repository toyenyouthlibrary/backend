<?php
//kobler til database
require('../koble_til_database.php');
session_start();
//init av variabler
//for &aring; lage en enkel bruker


$error = array(
    'missing_info' => 'All n&oslash;dvendig informasjon er ikke sendt.',
    'username_in_use' => 'Brukernavnet er allerede tatt.',
    'rfid_in_use' => 'Det finnes allerede en bruker med den RFIDen.',
    'failed_to_save_contact' => 'Klarte ikke &aring; lagre kontaktinformasjon.',
    'failed_to_link_contact' => 'Klarte ikke &aring; linke kontaktinformasjonen med brukeren.',
    'failed_to_save_user' => 'Klarte ikke &aring; lagre brukeren.'
);

if(!isset($_POST['username']) || !isset($_POST['rfid']) || !isset($_POST['firstname']) || !isset($_POST['birth']) || 
        !isset($_POST['sex']) || !isset($_POST['class']) || !isset($_POST['school']) || !isset($_POST['password']) || 
        !isset($_POST['address']) || !isset($_POST['lastname'])){
    j_die($error['missing_info']);
}

$username = $_POST["username"];
$rfid = $_POST["rfid"];
$firstname = $_POST['firstname']; 
$lastname = $_POST['lastname']; 
$birth = $_POST['birth']; 
$sex = $_POST['sex']; 
$class = $_POST['class']; 
$school = $_POST['school']; 
$password = $_POST['password']; 
$address = $_POST['address'];

/*
 * variabelen $conn er hentet fra koble_til_database.php
 */

//sjekker om brukernavnet er tatt
$test_uname = "SELECT * FROM lib_User WHERE username='" . $username . "'";
$test_uname_result = $conn->query($test_uname);
if ($test_uname_result->num_rows > 0) {
    //det finnes mer enn 0 rader, ergo finnes brukernavnet
    j_die($error['username_in_use']); //errorkode for at brukernavn er tatt
}


//sjekker om RFID-en av en eller annen grunn er i bruk
$test_email = "SELECT * FROM lib_User WHERE rfid='" . $rfid . "'";
$test_email_result = $conn->query($test_email);
if ($test_email_result->num_rows > 0) {
    //det finnes mer enn 0 rader, ergo finnes emailen allerede
    j_die($error['rfid_in_use']); //kode for RFID tatt
}

$date= (new DateTime())->format('Y-m-d H:i:s');



//oppretter en standardbruker, uten ekstra kontaktinfo
$insert_user=
    "INSERT INTO lib_User (username, firstname, lastname, birth, sex, class, school, password, address, rfid, registered) VALUES
    ('".utf8_encode($username)."', '".$firstname."', '".$lastname."', '".$birth."', '".$sex."', '".$class."', '".$school."', '".$password."', '".$address."', '".$rfid."', '".$date."')";
$insert_user_result = $conn->query($insert_user);
if ($insert_user_result===TRUE) {
    j_die("");
}else{
    j_die($error['failed_to_save_user']);
}


?>