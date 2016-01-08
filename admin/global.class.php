<?php
class Global_{
    function __construct(){
        require '../../koble_til_database.php';
        $this->conn = $conn;
    }
    
    function getBooks($order_by = "id"){
        
    }
}