<?php
class RFID{
    function __construct(){
        require '../../koble_til_database.php';
        $this->conn = $conn;
    }
    
    function type($_rfid){
        $get_rfid = "SELECT * FROM lib_RFID WHERE RFID = '" . $_rfid . "'";
        $get_rfid_qry = $this->conn->query($get_rfid);
        if($get_rfid_qry->num_rows > 0){
            if($rfid = $get_rfid_qry->fetch_assoc()){
                if($rfid['bookID'] != 0){
                    return array('book', $rfid['bookID']);
                }else if($rfid['userID'] != 0){
                    return array('user', $rfid['userID']);
                }else if($rfid['shelfID'] != 0){
                    return array('shelf', $rfid['shelfID']);
                }
            }
        }
    }
}