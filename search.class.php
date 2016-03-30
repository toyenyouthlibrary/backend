<?php
if(!class_exists("Search")){
class Search{
    function __construct($string){
        require ROOT.'../../koble_til_database.php';
        $this->result = array();
        $this->error = '';

        if ($conn->connect_error) {
            $conn->close();
            $this->error = "Noe gikk galt: " . $conn->connect_error;
        }
        
        if($this->error == ""){
            $conn->set_charset("utf8");

            if (isset($string) && $string != "") {
                //Direct search
                $search = mb_convert_encoding($string,"UTF-8","HTML-ENTITIES");
                $sql = "SELECT bookID, title, author, language, type FROM lib_Book WHERE LOWER(Concat(bookID, ISBN10, ISBN13, author, title, `original-title`, registered)) LIKE LOWER('%$search%')";
                $res = $conn->query($sql);
                
                if ($res != null && $res->num_rows > 0) {

                    while ($row = $res->fetch_assoc()) {
                        $temp_arr = array();
                        foreach ($row as $key => $value) {
                            $value = mb_convert_encoding($value,"HTML-ENTITIES","UTF-8");

                            $temp_arr[$key] = $value;
                        }
                        $this->result[] = $temp_arr;
                    }
                    
                }
                //Check if something contains all the words searched
                $search = mb_convert_encoding($string,"UTF-8","HTML-ENTITIES");
                $where_st = "";
                $search_split = explode(" ", $search);
                foreach($search_split as $search_word){
                    $where_st .= "LOWER(Concat(author, title, `original-title`)) LIKE LOWER('%$search_word%') AND ";
                }
                $where_st = trim($where_st, " AND ");
                $sql = "SELECT bookID, title, author, language, type FROM lib_Book WHERE $where_st";
                $res = $conn->query($sql);
                
                if ($res != null && $res->num_rows > 0) {

                    while ($row = $res->fetch_assoc()) {
                        $temp_arr = array();
                        foreach ($row as $key => $value) {
                            $value = mb_convert_encoding($value,"HTML-ENTITIES","UTF-8");

                            $temp_arr[$key] = $value;
                        }
                        if(!in_array($temp_arr , $this->result)){
                            $this->result[] = $temp_arr;
                        }
                    }
                    
                }
                
                if(count($this->result) == 0){
                    $this->error = "Ingen resultater";
                }
            } else {
                $this->error = "Du må søke etter noe!";
            }

        }

    }
}
}