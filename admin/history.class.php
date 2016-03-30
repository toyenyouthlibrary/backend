<?php
if(!class_exists("History")){
class History{
    function __construct(){
        //Connect to db
        require '../../../koble_til_database.php';
        //Required variables
        $this->conn = $conn;
        
        
        //Always same table because the sorting only depends on which rows to include
        $this->tbl = "lib_User_Book";
    }
    
    function getHistory(){
        $res = array();
        
        //Query to find all relevant entries, by whether their outdates were matching the timeframe
        $get_history = "SELECT TIMESTAMPDIFF(SECOND,outDate,inDate) AS timediff, outDate, inDate, userID, bookID FROM $this->tbl ORDER BY outDate";
        $get_history_qry = $this->conn->query($get_history);
        if ($get_history_qry->num_rows > 0) {
            while($history = $get_history_qry->fetch_assoc()){
                $temp_res = array(
                    'outDate' => $history['outDate'],
                    'inDate' => $history['inDate']
                );
                //Get user info
                $get_user = "SELECT username FROM lib_User WHERE userID = ".$history['userID']." AND active = '1'";
                $get_user_qry = $this->conn->query($get_user);
                if($get_user_qry->num_rows > 0){
                    if($user = $get_user_qry->fetch_assoc()){
                        $temp_res['username'] = $user['username'];
                        
                        //Get book info
                        $temp_res['book_title'] = "fak u.";
                        
                        $res[] = $temp_res;
                    }
                }
            }
        }else{
            
        }
        
        return $res;
    }
}
}