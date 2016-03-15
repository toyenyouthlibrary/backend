<?php

class CreateUsersAndBooks{
    function __construct($type){
        //Connect to db
        require '../../../koble_til_database.php';
        //Required variables
        $this->conn = $conn;
        $this->error = "";
        $this->type = $type;
        
        if($type == "book"){
            $this->tbl = "lib_Book";
            $this->fields = array(
                'ISBN10',
                'ISBN13',
                'title',
                'original-title',
                'author',
                'type',
                'language',
            );
        }else if($type == "user"){
            $this->tbl = "lib_User";
            $this->fields = array(
            'username',
            'firstname',
            'lastname',
            'birth',
            'address_nr',
            'school',
            'address',
            'pin'
        );
        }
    }
    
    function add($original_variables){
        $date= (new DateTime())->format('Y-m-d H:i:s');
        
        $fields = ""; $vars = "";
        foreach($this->fields as $field){
            if(isset($original_variables[$field])){
                $fields .= "`".$field."`,";
                $vars .= "'".$original_variables[$field]."',";
            }else{
                $this->error = "Mangler obligatorisk variabel.";
                return false;
            }
        }
        
        $fields .= "registered";
        $vars .= "'".$date."'";
        
        //Check if the user exists
        if($this->type == "user"){
            $get_user = "SELECT userID FROM lib_User WHERE username = '".$original_variables['username']."' AND active = 1";
            $get_user_qry = $this->conn->query($get_user);
            if($get_user_qry->num_rows > 0){
                if($user = $get_user_qry->fetch_assoc()){
                    $this->error = "Brukernavnet er allerede tatt.";
                    return false;
                }
            }
        }
        
        //Insert new user
        $insert_user =
            "INSERT INTO $this->tbl ($fields) VALUES ($vars)";
        $insert_user_result = $this->conn->query($insert_user);
        if ($insert_user_result===TRUE) {
            //Success
            $select_user = "SELECT ".$this->type."ID FROM $this->tbl WHERE ".$this->fields[0]." = '".$original_variables[$this->fields[0]]."' AND active = 1 ORDER BY ".$this->type."ID DESC";
            $select_user_qry = $this->conn->query($select_user);
            if($select_user_qry->num_rows > 0){
                if($user = $select_user_qry->fetch_assoc()){
                    if(isset($user['userID'])){
                        return $user['userID'];
                    }else{
                        return $user['bookID'];
                    }
                }
            }
            $this->error = "Fant ikke brukeren.";
        }else{
            $this->error = "Klarte ikke å lage ny bruker.";
            echo $insert_user;
            echo $this->conn->error;
        }
        return false;
    }
    
    function delete($id){
        $update = "UPDATE $this->tbl SET active = '0' WHERE ".$this->type."ID = '".$id."'";
        $update_qry = $this->conn->query($update);
        if($update_qry === TRUE){
            //Success
            //return true;
        }else{
            //Failed
            $this->error = "Klarte ikke å slette brukeren.";
            return false;
        }
        
        return false;
    }
}