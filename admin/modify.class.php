<?php

class Modify{
    function __construct(){
        //Connect to db
        require '../../../koble_til_database.php';
        //Required variables
        $this->conn = $conn;
        $this->error = "";
    }
    
    function update($tbl, $identifier, $original_vars){
        if($tbl == "lib_Book"){
            $fields = array(
                'ISBN10',
                'ISBN13',
                'title',
                'original-title',
                'author',
                'type',
                'language'
            );
        }else if($tbl == 'lib_User'){
            $fields = array(
                'firstname',
                'lastname',
                'birth',
                'sex',
                'address_nr',
                'school',
                'pin',
                'address',
                'approved_date'
            );
        }else if($tbl == 'lib_Contact'){
            $fields = array(
                'phone',
                'email'
            );
        }else if($tbl == 'lib_RFID'){
            $fields = array(
                'RFID'
            );
        }else{
            $this->error = "Det er ikke mulig å endre denne tabellen.";
            return false;
        }
        $vars = "";
        foreach($fields as $field){
            if(isset($original_vars[$field])){
                $vars .= "`".$field."` = '".$original_vars[$field]."', ";
            }
        }
        $vars = trim($vars, ", ");
        if($vars == ""){
            return true;
        }
        
        //Actually do the shit
        $update = "UPDATE $tbl SET $vars WHERE ".$identifier[0]." = '".$identifier[1]."'";
        echo $update."<br><br>";
        $update_qry = $this->conn->query($update);
        if ($update_qry === TRUE) {
            //Success
            return true;
        } else {
            //Failed
            echo $this->conn->error;
            $this->error = "Klarte ikke å oppdatere databasen.";
            return false;
        }
        
        return false;
    }
}