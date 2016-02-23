<?php
require('../../koble_til_database.php');
session_start();

$error = array(
    'unknown_rfid' => 'Den skannede enheten er ikke registrert.',
    'wrong_pin' => 'Feil PIN-kode.'
);

$post_vars = array(
    'obligatory' => array(
        'rfid',
        'pin'
    )
);

//Array that contains all the post information
$vars = $post->verify($post_vars);

$get_user = "SELECT userID, pin FROM lib_User WHERE rfid = '" . $vars['rfid'] . "'";
$get_user_qry = $conn->query($get_user);

if ($get_user_qry->num_rows > 0) {
    if($user = $get_user_qry->fetch_assoc()){
        if($vars['pin'] == $user['pin']){
            $sess = random_string();
            $_SESSION[$sess] = $user["userID"];
            die(
                json_encode(
                    array(
                        'error' => '',
                        'id' => $sess . ""
                    )
                )
            );
        }else{
            j_die($error['wrong_pin']);
        }
    }
}

function random_string(){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $str = '';
    for ($i = 0; $i < 20; $i++) {
        $str .= $characters[rand(0, $charactersLength - 1)];
    }
    return $str;
}

//That the code reaches this point will only occur if 
j_die($error['unknown_rfid']);

?>