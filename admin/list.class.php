<?php

class Lists{
    function __construct($type, $order = null, $filter = ""){
        //Connect to db
        require '../../koble_til_database.php';
        //Required variables
        $this->conn = $conn;
        $this->type = $type;
        $this->order = $order;
        $this->filter = $filter;
        
        if($order == null){
            if($type == "books"){
                $this->order = "bookID";
            }else if($type == "users"){
                $this->order = "userID";
            }
        }
        
        if($type == "books"){
            $this->tbl = "lib_Book";
        }else if($type == "users"){
            $this->tbl = "lib_User";
        }
    }
    
    function getList(){
        
        $get_list = "SELECT * FROM $this->tbl ORDER BY ".$this->order.$this->filter;
        $get_list_qry = $this->conn->query($get_list);
        $res = array();
        while($list_item = $get_list_qry->fetch_assoc()){
            if($this->type == "books"){
                //Formatting of the result of the books list
                $res[] = array(
                    'title' => 'lol',
                    'author' => 'u',
                    'published' => 'be',
                    'amount' => 'fgt',
                    'id' => $list_item['bookID']
                );
            }else if($this->type == "users"){
                //Formatting of the result of user list
                $approved = false;
                if($list_item['approved_date'] != null){
                    $approved = true;
                }
                $res[] = array(
                    'username' => $list_item['username'],
                    'age' => $list_item['age'],
                    'name' => $list_item['name'],
                    'class' => $list_item['class'],
                    'school' => $list_item['school'],
                    'sex' => $list_item['sex'],
                    'address' => $list_item['address'],
                    'registered' => $list_item['registered'],
                    'id' => $list_item['userID'],
                    'rfid' => $list_item['rfid'],
                    'approved' => $approved
                );
            }
        }
        return $res;
    }
}