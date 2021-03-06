<?php
//kobler til database
require('../../koble_til_database.php');
session_start();

$error = array(
    'nonexistant_book' => 'Boken er ikke registrert.',
    'missing_variables' => 'Ingen rfid mottatt.',
    'invalid_rfid' => 'Denne RFIDen h&oslash;rer ikke til en bok.'
);

if(!isset($_POST['rfid'])){
    j_die($error['missing_variables']);
}

//Get the book ID
$book_rfid = $_POST['rfid'];
$get_bookid = "SELECT bookID FROM lib_RFID WHERE RFID = '" . $book_rfid . "' AND bookID != 0";
$get_bookid_qry = $conn->query($get_bookid);
if($get_bookid_qry->num_rows > 0){
    if($bookid = $get_bookid_qry->fetch_assoc()){
        $book_id = $bookid['bookID'];
    }else{
        j_die($error['invalid_rfid']);
    }
}else{
    j_die($error['invalid_rfid']);
}

$get_book = "SELECT * FROM lib_Book WHERE bookID = '" . $book_id . "'";
$get_book_qry = $conn->query($get_book);

$res = array('error' => "");

if ($get_book_qry->num_rows > 0) {
    if($book = $get_book_qry->fetch_assoc()){
        $total_lended_time = 0;
        $borrowers = array();
        $status = "Ikke l&aring;nt ut";
        
        $get_lending_stats = "SELECT TIMESTAMPDIFF(SECOND,outDate,inDate) AS timediff, userID, inDate, outDate FROM lib_User_Book WHERE bookID = '".$book['bookID']."'";
        $get_lending_stats_qry = $conn->query($get_lending_stats);
        if($get_lending_stats_qry->num_rows > 0){
            while($lending_stats = $get_lending_stats_qry->fetch_assoc()){
                $total_lended_time = $total_lended_time + $lending_stats["timediff"];
                if($lending_stats['inDate'] == null){
                    $status = "L&aring;nt ut";
                    $current = true;
                    //Calculating live timediff
                    $currtime = time() - strtotime($lending_stats['outDate']);
                }else{
                    $current = false;
                    $currtime = false;
                }
                
                $get_user = "SELECT * FROM lib_User WHERE userID = '".$lending_stats['userID']."'";
                $get_user_qry = $conn->query($get_user);
                if($get_user_qry->num_rows > 0){
                    if($user = $get_user_qry->fetch_assoc()){
                        //Get the RFID
                        $_rfid = "";
                        $get_rfid = "SELECT RFID FROM lib_RFID WHERE userID = '" . $user['userID'] . "'";
                        $get_rfid_qry = $conn->query($get_rfid);
                        if($get_rfid_qry->num_rows > 0){
                            if($rfid = $get_rfid_qry->fetch_assoc()){
                                $_rfid = $rfid['RFID'];
                            }
                        }
                        //Store the results
                        $borrowers[] = array(
                            'id' => $user['userID'],
                            'username' => $user['username'],
                            'RFID' => $_rfid,
                            'borrowed_time' => (int) $lending_stats["timediff"],
                            'current' => $current,
                            'current_time' => $currtime
                        );
                    }
                }
            }
            if($borrowers == array()){
                //Book never lended to anyone
            }
        }else{
            //Query failed (SQL error or book never lended to no one)
        }
        
        $borrowers_v2 = array();
        for($i = 0; $i < count($borrowers); $i++){
            $x = registered_already($borrowers[$i], $borrowers_v2);
            if($x != -1){
                $borrowers_v2[$x]['borrowed_time'] += $borrowers[$i]['borrowed_time'];
                $borrowers_v2[$x]['borrowed_times'] += 1;
                if($borrowers[$i]['current'] == true){
                    $borrowers_v2[$x]['current'] = true;
                    $borrowers_v2[$x]['current_time'] = $borrowers[$i]['current_time'];
                }
            }else{
                $borrowers_v2[] = array(
                    'id' => $borrowers[$i]['id'],
                    'username' => $borrowers[$i]['username'],
                    'RFID' => $borrowers[$i]['RFID'],
                    'borrowed_time' => $borrowers[$i]['borrowed_time'],
                    'borrowed_times' => 1,
                    'current' => $borrowers[$i]['current'],
                    'current_time' => $borrowers[$i]['current_time']
                );
            }
        }
        for($y = 0;$y < count($borrowers_v2); $y++){
            if($borrowers_v2[$y]['current_time'] != false){
                $borrowers_v2[$y]['borrowed_time'] = convertSecondsToReadable((int) $borrowers_v2[$y]['borrowed_time'] + (int) $borrowers_v2[$y]['current_time']);
                $borrowers_v2[$y]['current_time'] = convertSecondsToReadable($borrowers_v2[$y]['current_time']);
                $total_lended_time += (int) $borrowers_v2[$y]['current_time'];
            }else{
                $borrowers_v2[$y]['borrowed_time'] = convertSecondsToReadable($borrowers_v2[$y]['borrowed_time']);
            }
        }
        
        //Find the feedback of the book
        $feedback = array('comments' => array(), 'stars' => array());
        $get_feedback = "SELECT * FROM lib_Feedback WHERE book_rfid = '". $book_rfid ."'";
        $get_feedback_qry = $conn->query($get_feedback);
        if ($get_feedback_qry->num_rows > 0) {
            while($feedback_res = $get_feedback_qry->fetch_assoc()){
                $type = $feedback_res['type']."s";
                $feedback[$type][] = array(
                    'user_rfid' => $feedback_res['user_rfid'],
                    'value' => $feedback_res['value'],
                    'timestamp' => $feedback_res['timestamp']
                );
            }
        }else{
            //No feedback :O
        }
        //Calculate average amount of stars
        $average_stars = 0;
        if(count($feedback['stars']) > 0){
            $total_stars = 0;
            for($i = 0; $i < count($feedback['stars']); $i++){
                $total_stars += intval($feedback['stars'][$i]['value']);
            }
            $average_stars = $total_stars / count($feedback['stars']);
        }
        $feedback['average_stars'] = $average_stars;
        
        //Print book info
        $res['book'] = array(
            'ISBN10' => $book['ISBN10'],
            'ISBN13' => $book['ISBN13'],
            'total_lend_time' => convertSecondsToReadable($total_lended_time),
            'total_lend_times' => count($borrowers),
            'borrowers' => $borrowers_v2,
            'feedback' => $feedback
        );
        echo json_encode($res);
    }else{
        //Query failed (nonexistant book or SQL error)
        j_die($error['nonexistant_book']);
    }
} else {
    //Query failed (nonexistant book or SQL error)
    j_die($error['nonexistant_book']);
}

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

function registered_already($borrower, $borrowerarr){
    for($i = 0; $i < count($borrowerarr); $i++){
        if($borrowerarr[$i]['id'] == $borrower['id']){
            return $i;
        }
    }
    return -1;
}

?>