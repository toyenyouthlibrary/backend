<?php

class User{
    public $id = 0;
    private $info = array();
    
    function __construct($user, $pass){
        require('../koble_til_database.php');
        $this->conn = $conn;
        
        $get_user = "SELECT * FROM lib_User WHERE LOWER(username) = LOWER('" . $user . "') AND password = '".$pass."'";
        $get_user_qry = $conn->query($get_user);
        
        $res = array('error' => "");
        
        if ($get_user_qry->num_rows > 0) {
            if($user = $get_user_qry->fetch_assoc()){
                $this->id = $user['userID'];
                $this->info = $user;
            }
        }
    }
    
    function info(){
        $approved = $this->info['approved_date'];
        if($this->info['approved_date'] == null){
            $approved = "";
        }
        $res = array(
            'userID' => $this->info['userID'],
            'username' => $this->info['username'],
            'firstname' => $this->info['firstname'],
            'lastname' => $this->info['lastname'],
            'birth' => $this->info['birth'],
            'sex' => $this->info['sex'],
            'class' => $this->info['class'],
            'school' => $this->info['school'],
            'address' => $this->info['address'],
            'rfid' => $this->info['rfid'],
            'registered' => $this->info['registered'],
            'approved' => $approved
        );
        return $res;
    }
}