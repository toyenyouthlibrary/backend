<?php

class Lists{
    function __construct($type, $order = null){
        //Connect to db
        $connect = 'koble_til_database.php';
        if(file_exists('../../../'.$connect)){
            require '../../../'.$connect;
        }else if('../../'.$connect){
            require '../../'.$connect;
        }
        
        //Required variables
        $this->conn = $conn;
        $this->type = $type;
        $this->order = $order;
        
        if($order == null){
            $this->order = "ORDER BY ";
            if($type == "books"){
                $this->order .= "bookID";
            }else if($type == "users"){
                $this->order .= "userID";
            }else if($type == "shelves"){
                $this->order .= "shelfID";
            }
        }
        
        if($type == "books"){
            $active_required = true;
            $this->tbl = "lib_Book";
            $this->fields = array(
                'bookID',
                'ISBN10',
                'ISBN13',
                'title',
                'author',
                'original-title',
                'shelfID'
            );
        }else if($type == "users"){
            $active_required = true;
            $this->tbl = "lib_User";
            $this->fields = array(
                'userID',
                'username',
                'birth',
                'firstname',
                'lastname',
                'school',
                'sex',
                'address',
                'registered',
                'approved_date'
            );
        }else if($type == "shelves"){
            $active_required = false;
            $this->tbl = "lib_Shelf";
            $this->fields = array(
                'shelfID',
                'name'
            );
        }
        
        $this->active = "";
        if($active_required){
            $this->active = "WHERE active = '1'";
        }
    }
    
    function getList(){
        
        $get_list = "SELECT * FROM $this->tbl ".$this->active." ".$this->order." ";
        $get_list_qry = $this->conn->query($get_list);
        $res = array();
        if($get_list_qry->num_rows > 0){
            while($list_item = $get_list_qry->fetch_assoc()){
                $temp_res = array();
                foreach($this->fields as $field){
                    if($field == "shelfID" && $this->type == "books"){
                        $temp_res[$field] = $this->getShelfName($list_item[$field]);
                    }else{
                        $temp_res[$field] = $list_item[$field];
                    }
                }
                $res[] = $temp_res;
            }
        }
        return $res;
    }
    
    function getShelfName($shelfID){
        if($shelfID != 0){
            $get_shelf = "SELECT name FROM lib_Shelf WHERE shelfID = '".$shelfID."' LIMIT 1";
            $get_shelf_qry = $this->conn->query($get_shelf);
            if($get_shelf_qry->num_rows > 0){
                if($shelf = $get_shelf_qry->fetch_assoc()){
                    return $shelf['name'];
                }
            }
        }
        return "Ingen hylle";
    }
}