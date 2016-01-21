<?php
//kobler til database
require('../koble_til_database.php');
session_start();

$error = array(
    
);

//Verify that the user exists
/*$get_feed = "SELECT * FROM 
    lib_User AS u, lib_Book AS b, lib_Feedback AS f 
    ORDER BY u.registered DESC, b.registered DESC, f.timestamp DESC";*/
$get_feed = "

SELECT * FROM (SELECT 'isUser', username, registered, null, null, null, null FROM lib_User ORDER BY registered DESC) AS u
Union All
SELECT * FROM ('isFeedback', null, null, user_rfid, type, value, timestamp FROM lib_Feedback ORDER BY timestamp DESC) AS f
";
$get_feed_qry = $conn->query($get_feed);

if($get_feed_qry->num_rows > 0){
    while($feed_item = $get_feed_qry->fetch_assoc()){
        print_r($feed_item);
        echo '<br>';
    }
}else{
    echo $conn->error;
    j_die("Query error maddafakka");
}