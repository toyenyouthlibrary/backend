<?php
//kobler til database
require('../koble_til_database.php');

//init av variabler
$res = array();
$res["error"]="";


$get_books = "SELECT TIMESTAMPDIFF(SECOND,outDate,inDate) AS timediff, outDate FROM lib_User_Book";
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
    $res['error'] = "Ingen b&oslash;ker er utl&aring;nt.";
    die(json_encode($res));
}

$res["total_time_lended"] = convertSecondsToReadable($total_time);
$res["total_times_lended"] = $xx;

echo json_encode($res);

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