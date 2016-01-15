<?php
class Books{
    function __construct(){
        require '../../koble_til_database.php';
        $this->conn = $conn;
    }
    
    function printAll($order_by = "bookID"){
        $get_books = "SELECT * FROM lib_Book ORDER BY ".$order_by;
        $get_books_qry = $this->conn->query($get_books);
        echo '<table>
        <tr>
            <th>Tittel</th>
            <th>Forfatter</th>
            <th>Utgivelsesdato</th>
            <th>Antall</th>
            <th colspan=2 style="text-align: center;">Ekstra</th>
        </tr><tr>
            <td><input type="text" placeholder="Filter" /></td>
            <td><input type="text" placeholder="Filter" /></td>
            <td><input type="text" placeholder="Filter" /></td>
        </tr>';
        while($book = $get_books_qry->fetch_assoc()){
            echo '<tr>
                <td>Tittel x</td>
                <td>Forfatter x</td>
                <td>Utgivelsesdato x</td>
                <td>Anntall x</td>
                <td><a href="?index=books/stats/'.$book['bookID'].'">Stats</a></td>
                <td><a href="?index=books/modify/'.$book['bookID'].'">Endre</a></td>
            </tr>';
        }
        echo '</table>';
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
        //Echo da shit
        $plural = $display_unit;
        if(substr($plural, -1, 1) == "e"){
            $plural .= "ne";
        }else{
            $plural .= 'ene';
        }
        $of_book = "";
        if($id != 0){
            $of_book = " av bok ".$id;
        }
        echo '
        <h2>Stats</h2>
        <p>Antall b&oslash;ker l&aring;nt ut (m&oslash;rk r&oslash;d) versus antall b&oslash;ker levert inn (lys r&oslash;d)<br>
        de siste '.$amount[$display_unit].' '.$plural.'</p>
        <p>Totalt '.$total_outDates.' utl&aring;n og '.$total_inDates.' innleveringer'.$of_book.' i denne perioden. Utl&aring;nt '.$total_time.' sekunder
        <div class="ct-chart ct-perfect-fourth"></div>
        <script>
        var data = {
          // A labels array that can contain any sort of values
          labels: [';
          $resstr = "";
          for($i = 1; $i <= $amount[$display_unit]; $i++){
              $resstr .= $i.",";
          }
          echo trim($resstr, ",");
          echo '],
          // Our series array that contains series objects or in this case series data arrays
          series: [[';
          $resstr = "";
          for($i = 1; $i <= $amount[$display_unit]; $i++){
              $resstr .= $res[$i]['total_outDates'].",";
          }
          echo trim($resstr, ",");
          echo '],[';
          $resstr = "";
          for($i = 1; $i <= $amount[$display_unit]; $i++){
              $resstr .= $res[$i]['total_inDates'].",";
          }
          echo trim($resstr, ",");
          echo ']]
        };

        // As options we currently only set a static size of 300x200 px. We can also omit this and use aspect ratio containers
        // as you saw in the previous example
        var options = {
          height: 300,
          seriesBarDistance: 10,
          axisY: {
            onlyInteger: true
          }
        };

        // Create a new line chart object where as first parameter we pass in a selector
        // that is resolving to our chart container element. The Second parameter
        // is the actual data object. As a third parameter we pass in our custom options.
        new Chartist.Bar(".ct-chart", data, options);
        </script>
        
        <script src="bar_graph_custom.js"></script>
        ';
    }
}