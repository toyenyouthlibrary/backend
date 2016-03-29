<?php

class Info{
    function __construct($type, $id){
        //Connect to db
        require ROOT.'../../koble_til_database.php';
        //Required variables
        $this->conn = $conn;
        $this->type = $type;
        $this->id = $id;
        $this->error = "";
        
        if($type == "books"){
            $active_required = true;
            $this->id_field = "bookID";
            $this->history_id_field = "bookRFID";
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
                'registered',
            );
        }else if($type == "users"){
            $active_required = true;
            $this->id_field = "userID";
            $this->history_id_field = "userID";
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
        }else if($type == "shelves"){
            $active_required = false;
            $this->id_field = "shelfID";
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
    
    function getInfo(){
        $get_info = "SELECT * FROM $this->tbl WHERE ".$this->id_field." = '$this->id' ".$this->active;
        $get_info_qry = $this->conn->query($get_info);
        if($get_info_qry->num_rows > 0){
            if($info = $get_info_qry->fetch_assoc()){
                $res = array();
                foreach($this->fields as $field){
                    $res[$field] = $info[$field];
                }
                if($this->type == "users"){
                    $res['contact'] = $this->getContactInfo();
                    $res['lended'] = $this->getLendHistory(array($res['userID']));
                }
                $res['rfid'] = $this->getRFID();
                if($this->type == "books"){
                    $RFIDs = array();
                    for($i = 0; $i < count($res['rfid']); $i++){
                        $RFIDs[] = $res['rfid'][$i][0];
                    }
                    $res['lended'] = $this->getLendHistory($RFIDs);
                }
                
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
    
    function getLendHistory($ids){
        $str = "";
        foreach($ids as $id){
            $str .= "$this->history_id_field = '".$id."' OR ";
        }
        $str = trim($str, " OR ");
        $res = array();
        $get_lended = "SELECT * FROM lib_User_Book WHERE $str";
        $get_lended_qry = $this->conn->query($get_lended);
        if($get_lended_qry != null && $get_lended_qry->num_rows > 0){
            while($lended = $get_lended_qry->fetch_assoc()){
                $bookID = "";
                $get_book = "SELECT bookID FROM lib_RFID WHERE RFID = '".$lended['bookRFID']."'";
                $get_book_qry = $this->conn->query($get_book);
                if($get_book_qry->num_rows > 0){
                    if($book = $get_book_qry->fetch_assoc()){
                        $bookID = $book['bookID'];
                    }
                }
                $res[] = array(
                    'userID' => $lended['userID'],
                    'bookID' => $bookID,
                    'bookRFID' => $lended['bookRFID'],
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
                if($rfid['bookID'] == 0){
                    $res[] = array($rfid['RFID']);
                }else{
                    require ROOT.'admin/list.class.php';
                    $list = new Lists("shelves");
                    $res[] = array($rfid['RFID'], $list->getShelfName($rfid['_shelfID']), $rfid['_shelfID']);
                }
            }
        }
        return $res;
    }
}