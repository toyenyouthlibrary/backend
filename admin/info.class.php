<?php

class Info{
    function __construct($type, $id){
        //Connect to db
        require '../../../koble_til_database.php';
        //Required variables
        $this->conn = $conn;
        $this->type = $type;
        $this->id = $id;
        $this->error = "";
        
        if($type == "books"){
            $this->id_field = "bookID";
            $this->tbl = "lib_Book";
            $this->fields = array(
                'bookID',
                'ISBN10',
                'ISBN13',
                'title',
                'original-title',
                'author',
                'type',
                'language',
                'registered'
            );
        }else if($type == "users"){
            $this->id_field = "userID";
            $this->tbl = "lib_User";
            $this->fields = array(
                'userID',
                'username',
                'firstname',
                'lastname',
                'pin',
                'birth',
                'sex',
                'address',
                'address_nr',
                'school',
                'registered',
                'approved_date'
            );
        }
    }
    
    function getInfo(){
        $get_info = "SELECT * FROM $this->tbl WHERE ".$this->fields[0]." = '$this->id' AND active = '1'";
        $get_info_qry = $this->conn->query($get_info);
        if($get_info_qry->num_rows > 0){
            if($info = $get_info_qry->fetch_assoc()){
                $res = array();
                foreach($this->fields as $field){
                    $res[$field] = $info[$field];
                }
                if($this->type == "users"){
                    $res['contact'] = $this->getContactInfo();
                }
                $res['lended'] = $this->getLendHistory();
                $res['rfid'] = $this->getRFID();
                return $res;
            }
        }
        return false;
    }
    
    function getContactInfo(){
        $res = array();
        $get_usercontact = "SELECT contactID FROM lib_User_Contact WHERE ".$this->fields[0]." = '$this->id'";
        $get_usercontact_qry = $this->conn->query($get_usercontact);
        if($get_usercontact_qry->num_rows > 0){
            while($usercontact = $get_usercontact_qry->fetch_assoc()){
                $get_contact = "SELECT * FROM lib_Contact WHERE contactID = '".$usercontact['contactID']."'";
                $get_contact_qry = $this->conn->query($get_contact);
                if($get_contact_qry->num_rows > 0){
                    if($contact = $get_contact_qry->fetch_assoc()){
                        $res[] = array(
                            'contactID' => $contact['contactID'],
                            'phone' => $contact['phone'],
                            'email' => $contact['email']
                        );
                    }
                }
            }
        }
        return $res;
    }
    
    function getLendHistory(){
        $res = array();
        $get_lended = "SELECT * FROM lib_User_Book WHERE ".$this->fields[0]." = '$this->id'";
        $get_lended_qry = $this->conn->query($get_lended);
        if($get_lended_qry->num_rows > 0){
            while($lended = $get_lended_qry->fetch_assoc()){
                $res[] = array(
                    'userID' => $lended['userID'],
                    'bookID' => $lended['bookID'],
                    'outDate' => $lended['outDate'],
                    'inDate' => $lended['inDate'],
                );
            }
        }
        return $res;
    }
    
    function getRFID(){
        $res = array();
        $get_rfid = "SELECT * FROM lib_RFID WHERE ".$this->id_field." = '".$this->id."'";
        $get_rfid_qry = $this->conn->query($get_rfid);
        if($get_rfid_qry->num_rows > 0){
            while($rfid = $get_rfid_qry->fetch_assoc()){
                $res[] = $rfid['RFID'];
            }
        }
        return $res;
    }
}