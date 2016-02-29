<?php
class Login{
    function __construct(){
        require '../../koble_til_database.php';
        $this->conn = $conn;
        //Time before you have to sign in again
        $this->expiration = 60*30;
        $this->session_length = 20;
        $this->error = array();
    }
    
    function login($session_id){
        /*
         * Check if the session of that id exists
        */
        if(strlen($session_id) != $this->session_length){
            //Invalid session id length
            return false;
        }
        
        $timelimit = time() - $this->expiration;
        $get_sess = $this->conn->query("SELECT userID FROM lib_Session WHERE sessionID = '" . $session_id . "' AND timestamp > " . $timelimit);
        if($get_sess->num_rows > 0){
            if($sess = $get_sess->fetch_assoc()){
                //Update the timestamp of the session to grant the user a new expiration time
                $this->renew($session_id);
                return $sess['userID'];
            }
        }
        //Session has been outdated
        $this->error[] = "&Oslash;kten har utg&aring;tt.";
        return false;
    }
    
    function create_session($rfid, $pin){
        /*
         * Check if the rfid and pin are correct
        */
        
        $get_user = "SELECT userID, pin FROM lib_User WHERE rfid = '" . $rfid . "'";
        $get_user_qry = $this->conn->query($get_user);
        
        if ($get_user_qry->num_rows > 0) {
            if($user = $get_user_qry->fetch_assoc()){
                if($pin == $user['pin']){
                    //Credentials are correct
                    $session_id = $this->generate_session_id();
                    $create_session = "INSERT INTO lib_Session (sessionID, userID, timestamp) 
                        VALUES ('" . $session_id . "', '" . $user['userID'] . "', '" . time() . "')";
                    $create_session_qry = $this->conn->query($create_session);
                    if($create_session_qry === TRUE){
                        //Success
                        return $session_id;
                    }else{
                        //Failed to save session
                        $this->error[] = "Klarte ikke &aring; lagre &oslash;kten.";
                    }
                }else{
                    //Wrong pin
                    $this->error[] = "PIN Koden er feil.";
                }
            }else{
                //Most likely error in the query, but lets say the user wasn't found
                $this->error[] = "Brukeren ble ikke funnet.";
            }
        }else{
            //User not found
            $this->error[] = "Brukeren ble ikke funnet.";
        }
        
        return false;
    }
    
    function renew($session_id){
        $renew = "UPDATE lib_Session SET timestamp = " . time() . " WHERE sessionID = '" . $session_id . "'";
        $renew_qry = $this->conn->query($renew);
        if ($renew_qry === TRUE) {
            //Success
        } else {
            //Failed to renew
        }
    }
    
    function logout($sessionID = null, $userID = null){
        if($sessionID != null){
            $where = "sessionID = '" . $sessionID . "'";
        }else{
            $where = "userID = '" . $userID . "'"
        }
        $logout = "UPDATE lib_Session SET active = 0 WHERE " . $where;
        $logout_qry = $this->conn->query($logout);
        if ($logout_qry === TRUE) {
            //Success
            return true;
        } else {
            //Failed to logout
            $this->error[] = "Klarte ikke &aring; logge ut.";
            return false;
        }
    }
    
    function generate_session_id(){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $str = '';
        for ($i = 0; $i < $this->session_length; $i++) {
            $str .= $characters[rand(0, $charactersLength - 1)];
        }
        return $str;
    }
}
