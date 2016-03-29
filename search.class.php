<?php
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
                $search = mb_convert_encoding($string,"UTF-8","HTML-ENTITIES");
                
                $sql = "SELECT bookID, title, author, language FROM lib_Book WHERE LOWER(Concat(bookID, ISBN10, ISBN13, author, title, `original-title`, registered)) like LOWER('%$search%')";
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
                    
                } else {
                    $this->error = "Ingen resultater";
                }
            } else {
                $this->error = "Du må søke etter noe!";
            }

        }

    }
}