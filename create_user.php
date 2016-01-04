<?php
//kobler til database
require('../koble_til_database.php');
session_start();
//init av variabler
//for å lage en enkel bruker
$username = (isset($_POST["username"]) ? $_POST["username"] : NULL);
$rfid = (isset($_POST["rfid"]) ? $_POST["rfid"] : NULL);

//valgfri kontaktinformasjon
$firstname = (isset($_POST["firstname"]) ? $_POST["firstname"] : NULL);
$email = (isset($_POST["email"]) ? $_POST["email"] : NULL);

$error_array["error"]="";

/*
 * variabelen $conn er hentet fra koble_til_database.php
 */

//sjekker om brukernavnet er tatt
$test_uname = "SELECT * FROM lib_User WHERE username='" . $username . "'";
$test_uname_result = $conn->query($test_uname);
if ($test_uname_result->num_rows > 0) {
    //det finnes mer enn 0 rader, ergo finnes brukernavnet
    $error_array["error"]="brukernavn tatt";
    die(json_encode($error_array)); //errorkode for at brukernavn er tatt
}


//sjekker om RFID-en av en eller annen grunn er i bruk
$test_email = "SELECT * FROM lib_User WHERE rfid='" . $rfid . "'";
$test_email_result = $conn->query($test_email);
if ($test_email_result->num_rows > 0) {
    //det finnes mer enn 0 rader, ergo finnes emailen allerede
    $error_array["error"]="rfid tatt";
    die(json_encode($error_array)); //kode for RFID tatt
}


//oppretter kontaktinformasjon og bruker, om kontaktinfo er satt.
if(!empty($firstname) || !empty($email)){
    $userid=0;
    $contactid=0;
//setter inn i tabellen
    $insert_user=
        "INSERT INTO lib_User (username, rfid) VALUES('".$username."', '".$rfid."')";
    $insert_user_result = $conn->query($insert_user);
    if ($insert_user_result===TRUE) {
        //henter auto iden som ble satt
        $userid=$conn->insert_id;

    }else{
        $error_array["error"]="kunne ikke lage bruker";
        die(json_encode($error_array));

    }

    //lager en ny input i lib_Contact med informasjonen gitt tidligere
    $insert_contact=
        "INSERT INTO lib_Contact (firstname, email) VALUES('".$firstname."', '".$email."')";
    $insert_contact_result= $conn->query($insert_contact);
    if ($insert_contact_result===TRUE) {
        //henter sist satte auto-id og setter den lik contact id
        $contactid=$conn->insert_id;

    }else{
        $error_array["error"]="kunne ikke lage contact";
        die(json_encode($error_array));

    }

    //dobbeltsikring

    if($userid!=0 && $contactid!=0) {
        //setter ett nytt element i mange til mange-mellomtabellen med informasjonen hentet ut/generert
        $insert_contact_user =
            "INSERT INTO lib_User_Contact (contactID, userID) VALUES('" . $contactid . "', '" . $userid . "')";
        $insert_contact_user_result = $conn->query($insert_contact_user);
        if ($insert_contact_user_result === TRUE) {
            $error_array["error"]="";
            die(json_encode($error_array));
        } else {
            $error_array["error"]="kunne ikke lage relasjon mellom contact og user";
            die(json_encode($error_array));

        }

    }

    //end
}
else{
    //oppretter en standardbruker, uten kontaktinfo
    $insert_user=
        "INSERT INTO lib_User (username, rfid) VALUES('".$username."', '".$rfid."')";
    $insert_user_result = $conn->query($insert_user);
    if ($insert_user_result===TRUE) {
        $error_array=array();

        $error_array["error"]="";
        die(json_encode($error_array));
    }else{
        $error_array["error"]="kunne ikke lage bruker";
        die(json_encode($error_array));

    }
}


?>