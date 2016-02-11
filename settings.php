<?php
//kobler til database
require('../../koble_til_database.php');
session_start();

$error = array(
    'missing_variables' => 'All n&oslash;dvendig informasjon er ikke sendt.',
    'nonexistant_user' => 'Den etterspurte brukeren finnes ikke i v&aring;re systemer.',
    'failed_to_get_settings' => 'Klarte ikke &aring; finne de etterspurte innstillingene.',
    'failed_to_save_settings' => 'Klarte ikke &aring; lagre innstillingene.',
    'failed_to_update_settings' => 'Klarte ikke &aring; oppdatere innstillingene.',
    'empty_variables' => 'Noe av den sendte informasjonen er tom.',
    'wat' => 'Ukjent foresp&oslash;rsel.'
);

if(!isset($_POST['userID']) || !isset($_POST['action'])){
    j_die($error['missing_variables']);
}

$userid = $_POST['userID'];
//action = view || update
$action = $_POST['action'];

if($userid == "" || $action == ""){
    j_die($error['empty_variables']);
}

if($action == "update"){
    if(!isset($_POST['public_photos']) || !isset($_POST['save_log']) || !isset($_POST['save_visits']) || !isset($_POST['preferred_contact'])){
        j_die($error['missing_variables']);
    }
    $photos = $_POST['public_photos'];
    $log = $_POST['save_log'];
    $visits = $_POST['save_visits'];
    $contact = $_POST['preferred_contact'];
    if($photos == "" || $log == "" || $visits == "" || $contact == ""){
        j_die($error['empty_variables']);
    }
}

$get_user = "SELECT userID FROM lib_User WHERE userID ='" . $userid . "'";
$get_user_qry = $conn->query($get_user);

$res = array('error' => "");

$existant = false;
if ($get_user_qry->num_rows > 0) {
    if($user = $get_user_qry->fetch_assoc()){
        $get_settings = "SELECT * FROM lib_Settings WHERE userID = '".$userid."'";
        $get_settings_qry = $conn->query($get_settings);
        if($get_settings_qry->num_rows > 0){
            //Existant
            $existant = true;
            if($settings = $get_settings_qry->fetch_assoc()){
                $res['settings'] = array(
                    'public_photos' => $settings['public_photos'],
                    'save_log' => $settings['save_log'],
                    'save_visits' => $settings['save_visits'],
                    'preferred_contact' => $settings['preferred_contact']
                );
            }else{
                j_die($error['failed_to_get_settings']);
            }
        }else{
            //Non-existant
        }
        
        if($action == "view"){
            if(!isset($res['settings'])){
                $res['settings'] = array(
                    'public_photos' => '',
                    'save_log' => '',
                    'save_visits' => '',
                    'preferred_contact' => ''
                );
            }
        }else if($action == "update"){
            if($existant){
                $update_settings = "UPDATE lib_Settings SET public_photos = '".$photos."', save_log = '".$log."', 
                    save_visits = '".$visits."', preferred_contact = '".$contact."' WHERE userID='".$userid."'";
                $update_settings_qry = $conn->query($update_settings);
                if ($update_settings_qry === TRUE) {
                    j_die("");
                } else {
                    j_die($error['failed_to_update_settings']);
                }
            }else{
                $insert_settings = "INSERT INTO lib_Settings (userID, public_photos, save_log, save_visits, preferred_contact) VALUES 
                    ('".$userid."', '".$photos."', '".$log."', '".$visits."', '".$contact."')";
                $insert_settings_qry = $conn->query($insert_settings);
                if ($insert_settings_qry === TRUE) {
                    j_die("");
                }else{
                    j_die($error['failed_to_save_settings']);
                }
            }
        }else{
            j_die($error['wat']);
        }
        
        echo json_encode($res);
    }else{
        j_die($error['nonexistant_user']);
    }
} else {
    j_die($error['nonexistant_user']);
}

?>