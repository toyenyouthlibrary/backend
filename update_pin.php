<?php
//kobler til database
require('../../koble_til_database.php');
session_start();
//init av variabler

$error = array(
    'inexistant_user' => 'Brukeren finnes ikke.',
    'failed_to_update' => 'Klarte ikke &aring; lagre pinkoden.'
    'invalid_rfid' => 'Kan ikke oppdatere pin-koden til noe annet enn en bruker.'
);

$post_vars = array(
    'obligatory' => array(
        'pin'
    ),
    'choose_one' => array(
        'username',
        'rfid',
        'userID'
    )
);

//Array that contains all the post information
$vars = $post->verify($post_vars);

if($vars['elegible'][0] == "rfid"){
    //Find the corresponding userID to the rfid and use the id instead
    $vars['elegible'][0] = "userID";
    require 'rfid.class.php';
    $rfid_c = new RFID();
    $rfid_type = $rfid_c->type($vars['elegible'][1]);
    if($rfid_type[0] == "user"){
        $vars['elegible'][1] = $rfid_type[1];
    }else{
        j_die($error['invalid_rfid']);
    }
}

//Can be changed later on to go straight to the UPDATE query, because it won't do nothing if the user doesn't exist...
$user_check = "SELECT userID FROM lib_User WHERE " . $vars['elegible'][0] . " = '" . $vars['elegible'][1] . "'";
$user_check_qry = $conn->query($user_check);
if($user_check_qry->num_rows > 0){
    if($user = $user_check_qry->fetch_assoc()){
        $update_pin = "UPDATE lib_User SET pin = '" . $vars['pin'] . "' WHERE userID = '" . $user["userID"] . "'";
        $update_pin_qry = $conn->query($update_pin);
        if ($update_pin_qry === TRUE) {
            j_die("");
        } else {
            j_die($error['failed_to_update']);
        }
    }else{
        j_die($error['inexistant_user']);
    }
}else{
    j_die($error['inexistant_user']);
}

?>