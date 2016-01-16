<?php

class Stats{
    function __construct($type, $id = 0, $display = array('multipler' => 86400,'amount' => 30)){
        //Connect to db
        require '../../koble_til_database.php';
        //Required variables
        $this->conn = $conn;
        
        $this->type = $type;
        $this->multipler = $display['multipler'];
        $this->amount = $display['amount'];
        $this->id = $id;
        //Always same table because the sorting only depends on which rows to include
        $this->tbl = "lib_User_Book";
    }
    
    function printStats(){
        $res = array();
        //Set default values for all the months
        for($i = 1; $i <= $this->amount; $i++){
            $res[$i]['total_time'] = 0; $res[$i]['total_outDates'] = 0; $res[$i]['total_inDates'] = 0;
        }
        //Calculate the max time ago that will be displayed (in seconds)
        $max_time_ago = time() - ($this->amount*$this->multipler);
        //Check if there is a specific user / book that is requested
        $where_statement = "";
        if($this->id != 0){
            if($this->type == "users"){
                $where_statement = "AND userID = ".$this->id;
            }else if($this->type == "books"){
                $where_statement = "AND bookID = ".$this->id;
            }
        }
        //Query to find all relevant entries, by whether their outdates were matching the timeframe
        $get_books = "SELECT TIMESTAMPDIFF(SECOND,outDate,inDate) AS timediff, outDate FROM $this->tbl 
                WHERE UNIX_TIMESTAMP(outDate) > ".$max_time_ago." AND UNIX_TIMESTAMP(outDate) <= ".time()." ".$where_statement;
        $get_books_qry = $this->conn->query($get_books);
        $total_outDates = 0;
        $total_time = 0;
        if ($get_books_qry->num_rows > 0) {
            while($book = $get_books_qry->fetch_assoc()){
                $month = round((strtotime($book['outDate']) - $max_time_ago)/$this->multipler);
                $total_outDates++;
                $total_time += (int) $book['timediff'];
                $res[$month]['total_time'] += (int) $book['timediff'];
                $res[$month]['total_outDates']++;
            }
        }else{
            
        }
        //Get amount of inDates
        $get_books = "SELECT inDate FROM lib_User_Book 
                WHERE UNIX_TIMESTAMP(inDate) > ".$max_time_ago." AND UNIX_TIMESTAMP(inDate) <= ".time()." ".$where_statement;
        $get_books_qry = $this->conn->query($get_books);
        $total_inDates = 0;
        if ($get_books_qry->num_rows > 0) {
            while($book = $get_books_qry->fetch_assoc()){
                $month = round((strtotime($book['inDate']) - $max_time_ago)/$this->multipler);
                $total_inDates++;
                $res[$month]['total_inDates']++;
            }
        }else{
            
        }
        
        $labels = array();
        for($i = 1; $i <= $this->amount; $i++){
          $labels[] = $i;
        }

        $totals = array(
            'time' => 0,
            'outDates' => 0,
            'inDates' => 0
        );
        
        $outDates = array();
        for($i = 1; $i <= $this->amount; $i++){
          $outDates[] = $res[$i]['total_outDates'];
          $totals['outDates'] += $res[$i]['total_outDates'];
        }

        $inDates = array();
        for($i = 1; $i <= $this->amount; $i++){
          $inDates[] = $res[$i]['total_inDates'];
          $totals['inDates'] += $res[$i]['total_inDates'];
        }
        
        $times = array();
        for($i = 1; $i <= $this->amount; $i++){
            $times[] = $res[$i]['total_time'];
            $totals['time'] += $res[$i]['total_time'];
        }
        
        $final_res = array(
            'totals' => $totals,
            'labels' => $labels,
            'outDates' => $outDates,
            'inDates' => $inDates,
            'times' => $times
        );
        
        return $final_res;
    }
}