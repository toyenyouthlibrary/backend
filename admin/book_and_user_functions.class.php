<?php
if(!class_exists("CreateUsersAndBooks")){
class CreateUsersAndBooks{
    function __construct($type){
        //Connect to db
        require '../../../koble_til_database.php';
        //Required variables
        $this->conn = $conn;
        $this->error = "";
        $this->type = $type;
        
        if($type == "book"){
            $active_required = true;
            $this->delete_4real = false;
            $this->log_registered = true;
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
            $active_required = true;
            $this->delete_4real = false;
            $this->log_registered = true;
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
        }else if($type == "shelf"){
            $active_required = false;
            $this->delete_4real = true;
            $this->log_registered = false;
            $this->tbl = "lib_Shelf";
            $this->fields = array(
                'shelfNR',
                'name'
            );
        }
        
        $this->active = "";
        if($active_required){
            $this->active = "AND active = '1'";
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
        
        if($this->log_registered){
            $fields .= "registered";
            $vars .= "'".$date."'";
        }
        
        $fields = trim($fields, ",");
        $vars = trim($vars, ",");
        
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
        
        //Insert new row
        $insert_row =
            "INSERT INTO $this->tbl ($fields) VALUES ($vars)";
        $insert_row_result = $this->conn->query($insert_row);
        if ($insert_row_result===TRUE) {
            //Success
            $select_row = "SELECT ".$this->type."ID FROM $this->tbl WHERE ".$this->fields[0]." = '".$original_variables[$this->fields[0]]."' ".$this->active." ORDER BY ".$this->type."ID DESC";
            $select_row_qry = $this->conn->query($select_row);
            if($select_row_qry->num_rows > 0){
                if($row = $select_row_qry->fetch_assoc()){
                    if(isset($row['userID'])){
                        return $row['userID'];
                    }else if(isset($row['bookID'])){
                        return $row['bookID'];
                    }else if(isset($row['shelfID'])){
                        return $row['shelfID'];
                    }
                }
            }
            $this->error = "Fant ikke brukeren.";
        }else{
            $this->error = "Klarte ikke å lage ny bruker.";
            echo $insert_row;
            echo $this->conn->error;
        }
        return false;
    }
    
    function delete($id){
        if($this->delete_4real){
            $delete_row = "DELETE FROM $this->tbl WHERE ".$this->type."ID = '".$id."'";
            $delete_row_res = $this->conn->query($delete_row);
            $this->error = $delete_row_res;
            $this->msg = $this->conn->affected_rows;
            if($this->conn->affected_rows > 0){
                return true;
            }else{
                $this->error = "Klarte ikke å slette hyllen (mulig at den allerede er slettet?)";
                return false;
            }
        }else{
            $update = "UPDATE $this->tbl SET active = '0' WHERE ".$this->type."ID = '".$id."'";
            $update_qry = $this->conn->query($update);
            if($update_qry === TRUE){
                //Success
                return true;
            }else{
                //Failed
                $this->error = "Klarte ikke å slette brukeren.";
                return false;
            }
        }
        
        return false;
    }
}
}