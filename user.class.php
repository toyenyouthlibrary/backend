<?php

class User{
    public $id = 0;
    private $info = array();
    
    function __construct($user, $pin){
        require('../../koble_til_database.php');
        $this->conn = $conn;
        
        $get_user = "SELECT * FROM lib_User WHERE LOWER(username) = LOWER('" . $user . "') AND pin = '".$pin."'";
        $get_user_qry = $conn->query($get_user);
        
        $res = array('error' => "");
        
        if ($get_user_qry->num_rows > 0) {
            if($user = $get_user_qry->fetch_assoc()){
                $this->id = $user['userID'];
                $this->info = $user;
            }
        }
        
        $get_rfid = "SELECT RFID FROM lib_RFID WHERE userID = '" . $this->id . "'";
        $get_rfid_qry = $conn->query($get_rfid);
        if ($get_rfid_qry->num_rows > 0) {
            if($rfid = $get_rfid_qry->fetch_assoc()){
                $this->rfid = $user['RFID'];
            }
        }
    }
    
    function info(){
        $this->info['approved_date'] = $this->info['approved_date'] ?: "";
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
            'rfid' => $this->rfid,
            'registered' => $this->info['registered'],
            'approved' => $this->info['approved_date']
        );
        return $res;
    }
}