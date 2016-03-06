<?php
//kobler til database
require('../../koble_til_database.php');
session_start();
//init av variabler
//for &aring; lage en enkel bruker

$error = array(
    'missing_info' => 'All n&oslash;dvendig informasjon er ikke sendt.',
    'empty_info' => 'Alle felter m&aring; fylles inn.',
    'username_in_use' => 'Brukernavnet er allerede tatt.',
    'name_in_use' => 'Det finnes allerede en bruker med dette navnet.',
    'failed_to_save_contact' => 'Klarte ikke &aring; lagre kontaktinformasjon.',
    'failed_to_link_contact' => 'Klarte ikke &aring; linke kontaktinformasjonen med brukeren.',
    'failed_to_save_user' => 'Klarte ikke &aring; lagre brukeren.',
    'failed_to_access_userid' => 'Klarte ikke &aring; finne lagret bruker-informasjon i databasen',
    'failed_to_access_contactid' => 'Klarte ikke &aring; finne lagret kontakt-informasjon i databasen'
);

if(!isset($_POST['username']) || !isset($_POST['firstname']) || !isset($_POST['birth']) || 
        !isset($_POST['address_nr']) || !isset($_POST['school']) || !isset($_POST['phone']) || 
        !isset($_POST['address']) || !isset($_POST['lastname']) || !isset($_POST['email'])){
    j_die($error['missing_info']);
}

//Check if any of the post values are equal to ""
foreach ($_POST as $post_val){
    if($post_val == ""){
        j_die($error['empty_info']);
    }
}

$username = $_POST["username"];
$firstname = $_POST['firstname']; 
$lastname = $_POST['lastname']; 
$birth = $_POST['birth'];
$school = $_POST['school']; 
$address = $_POST['address'];
$address_nr = $_POST['address_nr'];
$phone = $_POST['phone'];
$email = $_POST['email'];

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


//sjekker om navnet av en eller annen grunn er i bruk
$test_name = "SELECT * FROM lib_User WHERE firstname='" . $firstname . "' AND lastname = '".$lastname."'";
$test_name_qry = $conn->query($test_name);
if ($test_name_qry->num_rows > 0) {
    //det finnes mer enn 0 rader, ergo finnes navnet allerede
    j_die($error['name_in_use']);
}

$date= (new DateTime())->format('Y-m-d H:i:s');



//oppretter en standardbruker, uten ekstra kontaktinfo
$insert_user =
    "INSERT INTO lib_User (username, firstname, lastname, birth, sex, address_nr, school, pin, address, registered) VALUES
    ('".utf8_encode($username)."', '".$firstname."', '".$lastname."', '".$birth."', '', '".$address_nr."', '".$school."', '', '".$address."', '".$date."')";
$insert_user_result = $conn->query($insert_user);
if ($insert_user_result===TRUE) {
    //Success
}else{
    j_die($error['failed_to_save_user']);
}

$insert_contact = "INSERT INTO lib_Contact (phone, email) VALUES ('" . $phone . "', '" . $email . "')";
$insert_contact_res = $conn->query($insert_contact);
if($insert_contact_res === TRUE){
    //Success
}else{
    j_die($error['failed_to_save_contact']);
}

/*
 * Finn id'en til contact og user og link dem sammen
*/

$get_userid = "SELECT userID FROM lib_User WHERE username = '" . $username . "'";
$get_userid_qry = $conn->query($get_userid);
if($get_userid_qry->num_rows > 0){
    if($userinf = $get_userid_qry->fetch_assoc()){
        $user_id = $userinf["userID"];
    }else{
        j_die($error['failed_to_access_userid']);
    }
}else{
    j_die($error['failed_to_access_userid']);
}

$get_contactid = "SELECT contactID FROM lib_Contact WHERE phone = '" . $phone . "' AND email = '" . $email . "' ORDER BY contactID DESC";
$get_contactid_qry = $conn->query($get_contactid);
if($get_contactid_qry->num_rows > 0){
    if($contactinf = $get_contactid_qry->fetch_assoc()){
        $contact_id = $contactinf["contactID"];
    }else{
        j_die($error['failed_to_access_contactid']);
    }
}else{
    j_die($error['failed_to_access_contactid']);
}

$insert_link = "INSERT INTO lib_User_Contact (contactID, userID) VALUES ('" . $contact_id . "', '" . $user_id . "')";
$insert_link_res = $conn->query($insert_link);
if($insert_link_res === TRUE){
    j_die("");
}else{
    j_die($error['failed_to_link_contact']);
}
?>