<?php
//kobler til database
require('../koble_til_database.php');

//init av variabler
$user_info = array();

$error = array(
    'nonexistant_user' => 'Brukeren %username% finnes ikke i v&aring;re systemer.',
    'nonexistant_rfid' => 'Ingen registrerte brukere med denne RFIDen.',
    'no_userinfo' => 'Klarte ikke &aring; finne informasjon om brukeren.',
);
/*
 * variabelen $conn er hentet fra koble_til_database.php
 */

//sjekker om variablene er satt

//henter info og pusher det inn i ett array

if(isset($_POST["username"])) {
    $username=$_POST["username"];
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
        j_die(str_replace('%username%', $username, $error['nonexistant_user']));
    }
}
if(isset($_POST["rfid"])){
    $rfid=$_POST["rfid"];
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
        j_die($error['nonexistant_rfid']);
    }
}

if(empty($user_info["userID"])){
    j_die($error['no_userinfo']);
}

$get_books = "SELECT TIMESTAMPDIFF(SECOND,outDate,inDate) AS timediff, outDate FROM lib_User_Book WHERE userID='" . $user_info['userID'] . "'";
$get_books_qry = $conn->query($get_books);
$xx = 0;
$total_time = 0;
if ($get_books_qry->num_rows > 0) {
    while($book = $get_books_qry->fetch_assoc()){
        $xx++;
        if($book['timediff'] == null){
            $total_time += time() - strtotime($book['outDate']);
        }else{
            $total_time += $book['timediff'];
        }
    }
}else{
    //echo "No books borrowed";
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

        }
    }
}else{

}
$user_info['total_times_borrowed'] = $xx;
$user_info['total_time_borrowed'] = convertSecondsToReadable($total_time);

$user_info["error"]="";
echo json_encode($user_info);
    //end

function convertSecondsToReadable($seconds){
    //Find difference in time in a readable format
    $dtF = new DateTime("@0");
    $dtT = new DateTime("@$seconds");
    $readable["months"] = (int) $dtF->diff($dtT)->format('%m');
    $readable["days"] = (int) $dtF->diff($dtT)->format('%a');
    $readable["hours"] = (int) $dtF->diff($dtT)->format('%h');
    $readable["minutes"] = (int) $dtF->diff($dtT)->format('%i');
    $readable["seconds"] = (int) $dtF->diff($dtT)->format('%s');
    $result = "";
    $prev_value = false;
    return $readable;
}

?>