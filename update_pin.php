<?php
//kobler til database
require('../../koble_til_database.php');
session_start();
//init av variabler

$error = array(
    'missing_info' => 'All n&oslash;dvendig informasjon er ikke sendt.',
    'empty_info' => 'Alle felter m&aring; fylles inn.',
    'missing_elegible' => 'Alle felter er ikke fylt inn, eller det er en feil i databasen.',
    'inexistant_user' => 'Brukeren finnes ikke.',
    'failed_to_update' => 'Klarte ikke &aring; lagre pinkoden.'
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
$vars = array();
//Check for invalid variables
foreach($post_vars['obligatory'] as $obligatory_var){
    if(!isset($_POST[$obligatory_var])){
        j_die($error['missing_info']);
    }else if($_POST[$obligatory_var] == ""){
        j_die($error['empty_info']);
    }
    $vars[$obligatory_var] = $_POST[$obligatory_var];
}
foreach($post_vars['choose_one'] as $elegible_var){
    if(isset($_POST[$elegible_var])){
        if($_POST[$elegible_var] != ""){
            $vars['elegible'] = array($elegible_var, $_POST[$elegible_var]);
        }
    }
}
if(!isset($vars['elegible'])){
    j_die($error['missing_elegible']);
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