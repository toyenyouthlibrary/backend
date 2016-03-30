<?php
if(!class_exists("ContactInfo")){
class ContactInfo{
    function __construct(){
        //Connect to db
        require '../../../koble_til_database.php';
        //Required variables
        $this->conn = $conn;
        $this->error = "";
    }
    
    function add($userid){
        //Generate an empty field
        
        $insert_contact = "INSERT INTO lib_Contact (phone, email) VALUES ('phone', 'email')";
        $insert_contact_res = $this->conn->query($insert_contact);
        if($insert_contact_res === TRUE){
            //Success
        }else{
            $this->error = "Klarte ikke å lage nytt kontaktfelt.";
            return false;
        }
        
        $get_contact = "SELECT contactID FROM lib_Contact WHERE phone = 'phone' AND email = 'email' ORDER BY contactID DESC LIMIT 1";
        $get_contact_qry = $this->conn->query($get_contact);
        if($get_contact_qry->num_rows > 0){
            if($contact = $get_contact_qry->fetch_assoc()){
                $contact_id = $contact['contactID'];
            }else{
                $this->error = "Fant ikke kontakt feltet";
                return false;
            }
        }else{
            $this->error = "Fant ikke kontakt feltet";
            return false;
        }
        
        $insert_link = "INSERT INTO lib_User_Contact (contactID, userID) VALUES ('" . $contact_id . "', '" . $userid . "')";
        $insert_link_res = $this->conn->query($insert_link);
        if($insert_link_res === TRUE){
            //Success
            return true;
        }else{
            $this->error = "Klarte ikke å linke kontaktinformasjonen med brukeren.";
            return false;
        }
        return false;
    }
    
    function delete($contactid){
        $delete_link = "DELETE FROM lib_User_Contact WHERE contactID = '".$contactid."'";
        $this->conn->query($delete_link);
        if($this->conn->affected_rows == 0){
            $this->error = "Klarte ikke å slette.";
            return false;
        }
        
        $delete_contact = "DELETE FROM lib_Contact WHERE contactID = '".$contactid."'";
        $this->conn->query($delete_contact);
        if($this->conn->affected_rows == 0){
            $this->error = "Klarte ikke å slette kontaktinformasjon.";
            return false;
        }else{
            return true;
        }
        
        return false;
    }
}
}