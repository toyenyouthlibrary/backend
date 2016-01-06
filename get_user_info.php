<?php
//kobler til database
require('../koble_til_database.php');

//init av variabler
$username=$_POST["username"];
$error_array["error"]="";
$rfid=$_POST["rfid"];
$user_info = array();
/*
 * variabelen $conn er hentet fra koble_til_database.php
 */

//sjekker om variablene er satt

//henter info og pusher det inn i ett array

if(isset($_POST["username"])) {
    $user_info = array();
    $get_info = "SELECT * FROM lib_User WHERE username='" . $username . "'";
    $get_info_result = $conn->query($get_info);
    if ($get_info_result->num_rows > 0) {
        while ($row = $get_info_result->fetch_assoc()) {
            $user_info["rfid"] = $row["rfid"];
            $user_info["userID"] = $row["userID"];
            $user_info["username"] = $row["username"];

        }
    } else {
        $error_array["error"] = "Brukeren ".$username." finnes ikke i v&aring;re systemer.";
        die(json_encode($error_array));
    }
}
if(isset($_POST["rfid"])){
    $user_info = array();
    $get_info = "SELECT * FROM lib_User WHERE rfid='" . $rfid . "'";
    $get_info_result = $conn->query($get_info);
    if ($get_info_result->num_rows > 0) {
        while ($row = $get_info_result->fetch_assoc()) {
            $user_info["rfid"] = $row["rfid"];
            $user_info["userID"] = $row["userID"];
            $user_info["username"] = $row["username"];
        }
    } else {
        $error_array["error"] = "Ingen registrerte brukere med denne RFIDen";
        die(json_encode($error_array));
    }
}

if(empty($user_info["userID"])){
    $user_info["error"]="Klarte ikke &aring; finne informasjon om brukeren.";
    echo json_encode($user_info);
}

$contact_nr=array();
$test_uname = "SELECT * FROM lib_User_Contact WHERE userID='" . $user_info['userID'] . "'";
$test_uname_result = $conn->query($test_uname);
if ($test_uname_result->num_rows > 0) {
    $usernr=0;
    while($row = $test_uname_result->fetch_assoc()) {

        $test_uname2 = "SELECT * FROM lib_Contact WHERE contactID='" . $row["contactID"] . "'";
        $test_uname_result2 = $conn->query($test_uname2);
        if ($test_uname_result2->num_rows > 0) {

            while($row2 = $test_uname_result2->fetch_assoc()) {

                $user_info["firstname"]=$row2["firstname"]; //$user_info["contact_".$usernr]["firstname"]=$row2["firstname"]
                $user_info["email"]=$row2["email"]; // $user_info["contact_".$usernr]["email"]=$row2["email"];
                $usernr++;
            }

        }else{
        }
    }
}else{

}

$user_info["error"]="";
echo json_encode($user_info);
    //end


?>