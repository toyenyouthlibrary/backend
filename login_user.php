<?php
require('../../koble_til_database.php');
session_start();

$error = array(
    'unknown_rfid' => 'Den skannede enheten er ikke registrert.',
    'wrong_pin' => 'Feil PIN-kode.'
);

$post_vars = array(
    'obligatory' => array(
        'rfid'
    )
);

//Array that contains all the post information
$vars = $post->verify($post_vars);

require 'login.class.php';
$login = new Login();

$session = $login->create_session($vars['rfid']);
if($session !== false){
    die(
        json_encode(
            array(
                'error' => '',
                'sessionID' => $session
            )
        )
    );
}else{
    //Failed
}

//That the code reaches this point will only occur if the queries have failed
j_die($error['unknown_rfid']);

?>