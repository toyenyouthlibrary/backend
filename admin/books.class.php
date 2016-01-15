<?php
class Books{
    function __construct(){
        require '../../koble_til_database.php';
        $this->conn = $conn;
    }
    
    function printAll($order_by = "bookID"){
        $get_books = "SELECT * FROM lib_Book ORDER BY ".$order_by;
        $get_books_qry = $this->conn->query($get_books);
        $res = array();
        while($book = $get_books_qry->fetch_assoc()){
            $res[] = array(
                'title' => 'lol',
                'author' => 'u',
                'published' => 'be',
                'amount' => 'fgt',
                'id' => $book['bookID']
            );
        }
        return $res;
    }
    
    function printStats($display_unit = "dag", $id = 0){
        $amount = array(
            'm&aring;ned' => 12,
            'dag' => 30,
            'time' => 12
        );
        $multipler = array(
            'm&aring;ned' => 2592000,
            'dag' => 86400,
            'time' => 3600
        );
        $res = array();
        
        for($i = 1; $i <= $amount[$display_unit]; $i++){
            $res[$i]['total_time'] = 0; $res[$i]['total_outDates'] = 0; $res[$i]['total_inDates'] = 0;
        }
        $max_time_ago = time() - ($amount[$display_unit]*$multipler[$display_unit]);
        $and_id_equals_userid = "";
        if($id != 0){
            $and_id_equals_userid = "AND userID = ".$id;
        }
        $get_books = "SELECT TIMESTAMPDIFF(SECOND,outDate,inDate) AS timediff, outDate FROM lib_User_Book 
                WHERE UNIX_TIMESTAMP(outDate) > ".$max_time_ago." AND UNIX_TIMESTAMP(outDate) <= ".time()." ".$and_id_equals_userid;
        $get_books_qry = $this->conn->query($get_books);
        $total_outDates = 0;
        $total_time = 0;
        if ($get_books_qry->num_rows > 0) {
            while($book = $get_books_qry->fetch_assoc()){
                $month = round((strtotime($book['outDate']) - $max_time_ago)/$multipler[$display_unit]);
                $total_outDates++;
                $total_time += (int) $book['timediff'];
                $res[$month]['total_time'] += (int) $book['timediff'];
                $res[$month]['total_outDates']++;
            }
        }else{
            
        }
        //Get amount of inDates
        $get_books = "SELECT inDate FROM lib_User_Book 
                WHERE UNIX_TIMESTAMP(inDate) > ".$max_time_ago." AND UNIX_TIMESTAMP(inDate) <= ".time()." ".$and_id_equals_userid;
        $get_books_qry = $this->conn->query($get_books);
        $total_inDates = 0;
        if ($get_books_qry->num_rows > 0) {
            while($book = $get_books_qry->fetch_assoc()){
                $month = round((strtotime($book['inDate']) - $max_time_ago)/$multipler[$display_unit]);
                $total_inDates++;
                $res[$month]['total_inDates']++;
            }
        }else{
            
        }
        
        $labels = array();
        for($i = 1; $i <= $amount[$display_unit]; $i++){
          $labels[] = $i;
        }

        $outDates = array();
        for($i = 1; $i <= $amount[$display_unit]; $i++){
          $outDates[] = $res[$i]['total_outDates'];
        }

        $inDates = array();
        for($i = 1; $i <= $amount[$display_unit]; $i++){
          $inDates[] = $res[$i]['total_inDates'];
        }
        
        $res['stats'] = array(
            'labels' => $labels,
            'outDates' => $outDates,
            'inDates' => $inDates
        );
        
        return $res;
    }
}