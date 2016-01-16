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
                $res[] = array(
                    'name' => $list_item['username'],
                    'age' => 'whatever',
                    'registered' => 'some timestamp',
                    'id' => $list_item['userID'],
                    'rfid' => $list_item['rfid']
                );
            }
        }
        return $res;
    }
}