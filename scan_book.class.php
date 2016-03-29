<?php

class ScanBook{
    function __construct(){
        require ROOT.'../../koble_til_database.php';
        $this->conn = $conn;
    }
    
    function isLended($rfid){
        $get_lastlend = "SELECT * FROM lib_User_Book WHERE bookRFID = '".$rfid."' ORDER BY user_book_ID DESC LIMIT 1";
        $get_lastlend_qry = $this->conn->query($get_lastlend);
        if($get_lastlend_qry->num_rows > 0){
            if($lastlend = $get_lastlend_qry->fetch_assoc()){
                if($lastlend['inDate'] == null){
                    return true;
                }
            }
        }
        return false;
    }
    
    function lend($books){
        
    }
    
    function deliver($books){
        
    }
    
    function get_book_info($book){
        
    }
}