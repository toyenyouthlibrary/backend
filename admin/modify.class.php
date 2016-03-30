<?php
if(!class_exists("Modify")){
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
                'shelfID',
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
                'RFID',
                '_shelfID'
            );
        }else if($tbl == "lib_Shelf"){
            $fields = array(
                'name',
                'shelfNR'
            );
        }else{
            $this->error = "Det er ikke mulig å endre denne tabellen.";
            return false;
        }
        $vars = "";
        foreach($fields as $key => $field){
            $or_var = $original_vars[$field];
            if($key == "RFID"){
                $or_var = trim($or_var, ";");
            }
            if($or_var == "null"){
                $or_var = null;
            }
            if(isset($original_vars[$field])){
                if($or_var == null){
                    $vars .= "`".$field."` = NULL, ";
                }else{
                    $vars .= "`".$field."` = '".$or_var."', ";
                }
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
}