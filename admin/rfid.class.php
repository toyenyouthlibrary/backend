<?php

class RFID{
    function __construct(){
        //Connect to db
        require '../../../koble_til_database.php';
        //Required variables
        $this->conn = $conn;
        $this->error = "";
    }
    
    function create($type, $id){
        if(!($type == "book" || $type == "user" || $type == "shelf")){
            $this->error = "Ikke godkjent type.";
            return false;
        }
        
        $insert_rfid = "INSERT INTO lib_RFID (RFID, ".$type."ID) VALUES ('ny_rfid_".$this->generateRandomString()."', '" . $id . "')";
        $insert_rfid_res = $this->conn->query($insert_rfid);
        if($insert_rfid_res === TRUE){
            //Success
            return true;
        }else{
            $this->error = "Klarte ikke å linke kontaktinformasjonen med brukeren.";
            return false;
        }
        return false;
    }
    
    function delete($rfid){
        $delete_rfid = "DELETE FROM lib_RFID WHERE RFID = '".$rfid."'";
        $delete_rfid_res = $this->conn->query($delete_rfid);
        $this->error = $delete_rfid_res;
        $this->msg = $this->conn->affected_rows;
        if($this->conn->affected_rows > 0){
            return true;
        }else{
            $this->error = "Klarte ikke å slette RFIDen (mulig at den allerede er slettet?)";
            return false;
        }
    }
    
    function generateRandomString($length = 5) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}