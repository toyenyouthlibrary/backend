<?php
if(!isset($_POST['search']) && !isset($_GET['book']) && !isset($_GET['search'])){
    //If the user has not searched anything
?>
<style>
body{
    background-color: #1d4a6c;
}
#text{
    background-color: transparent;
    margin-top: 160px;
    padding-top: 0;
}
input{
    width: 100%;
    padding: 20px;
    border: 0;
}
</style>
<div id="deichman_logo">
    <img src="<?php echo DEPENDENCIES_LOC; ?>imgs/deichman_logo.png" />
</div><div id="biblo_logo">
    <img src="<?php echo DEPENDENCIES_LOC; ?>imgs/biblo_logo.png" />
</div>
<div id="text">
    <form action="" method="POST">
        <input type="text" name="search" placeholder="S&oslash;k her">
    </form>
</div>
<div id="footnotes" class="bottom_left">
    <a href="http://tung.deichman.no/frontend/start/"><img src="<?php echo DEPENDENCIES_LOC; ?>imgs/cross.png" /></a>
</div>
<div id="footnotes" class="bottom_right">
    <img src="<?php echo DEPENDENCIES_LOC; ?>imgs/soke_p.png" />
</div>

<?php
}else if(isset($_POST['search']) || isset($_GET['search'])){
    if(isset($_POST['search'])){
        header("Location: ".URL."&search=".$_POST['search']);
    }
        ?>
<style>
body{
    background-color: #4c9041;
}
h2{
    margin-bottom: 5px;
    line-height: 36px;
}
#text{
    margin-top: 140px;
    padding: 20px;
    height: calc(100vh - 100px - 140px - 40px);
}
input{
    width: 100%;
    padding: 20px;
}
td{
    padding: 5px 0;
}
tr{
    cursor: pointer;
}
tr:first-of-type{
    cursor: default;
}

#frem_tilbake img{
    height: 15px;
    margin-right: 20px;
}
</style>
<div id="deichman_logo">
    <img src="<?php echo DEPENDENCIES_LOC; ?>imgs/deichman_logo.png" />
</div><div id="biblo_logo">
    <img src="<?php echo DEPENDENCIES_LOC; ?>imgs/biblo_logo.png" />
</div>
<div id="text">
    <h2><img src="<?php echo DEPENDENCIES_LOC; ?>imgs/magnifier.png" /> Resultater</h2>
<?php
    //Add this JS file x_amount_of_results_pr_page.js
    //If the user has searched some shit
    require ROOT.'search.class.php';
    $search = new Search($_GET['search']);
    if($search->error == ''){
        ?>
        
            <table cellspacing=0>
                <tr>
                    <th>Tittel</th>
                    <th>Forfatter</th>
                    <th>Type</th>
                    <th>Språk</th>
                </tr>
        <?php
        foreach($search->result as $res){
            echo '<tr onclick="window.location.href= \''.URL.'&book='.$res['bookID'].'&return='.$_GET['search'].'\'">';
            echo '<td>'.$res['title'].'</td>';
            echo '<td>'.$res['author'].'</td>';
            echo '<td>'.$res['type'].'</td>';
            echo '<td>'.$res['language'].'</td>';
            echo '</tr>';
        }
        ?>
        </table>
        <?php
    }else{
        echo $search->error;
    }?>
<div id="frem_tilbake" style="position: absolute; bottom: 10px; left: 10px;">
        <img src="<?php echo DEPENDENCIES_LOC; ?>imgs/arrow_left_black.png" id="left" /><img src="<?php echo DEPENDENCIES_LOC; ?>imgs/arrow_right_black.png" id="right" />
    </div>
</div>
<div id="footnotes" class="bottom_left">
    <a href="http://tung.deichman.no/frontend.php?index=search"><img src="<?php echo DEPENDENCIES_LOC; ?>imgs/cross.png" /></a>
</div>
<div id="footnotes" class="bottom_right">
    <img src="<?php echo DEPENDENCIES_LOC; ?>imgs/soke_p.png" />
</div>
<?php
}else if(isset($_GET['book'])){
    require ROOT.'admin/info.class.php';
    $info = new Info("books", $_GET['book']);
    $i = $info->getInfo();
    $author = $i['author'];
    if(count(explode(",", $author)) > 1){
        $author = explode(",", $author)[1]." ".explode(",", $author)[0];
    }
    ?>
<style>
    body{
        background-color: #e3ba26;
    }
    h2{
        margin-bottom: 5px;
        line-height: 36px;
    }
    #text{
        margin-top: 140px;
        padding: 20px;
        height: calc(100vh - 100px - 140px - 40px);
    }
    #logo{
        width: 200px;
        display: inline-block;
    }
    #logo img{
        max-width: 200px;
    }
    #info{
        width: calc(100% - 200px);
        display: inline-block;
        vertical-align: top;
    }
    #info h3{
        padding-bottom: 1em;
    }
    #info i{
        display: block;
    }
    #info p{
        margin-bottom: 5px;
    }
    #stars img{
        width: 30px;
    }
</style>
<div id="deichman_logo">
    <img src="<?php echo DEPENDENCIES_LOC; ?>imgs/deichman_logo.png" />
</div><div id="biblo_logo">
    <img src="<?php echo DEPENDENCIES_LOC; ?>imgs/biblo_logo.png" />
</div>
<div id="text">
    <h2><img src="<?php echo DEPENDENCIES_LOC; ?>imgs/magnifier.png" /> Resultat</h2>
    <div id="logo">
        <img src="<?php echo DEPENDENCIES_LOC; ?>imgs/missing_cover.png" />
        <div id="stars">
            <?php
            if($i['rating']['average_stars'] != 0){
                for($int = 1; $int <= 6; $int++){
                    if($int <= $i['rating']['average_stars']){
                        echo '<img src="/frontend/static/imgs/stars/fyll.png">';
                    }else{
                        echo '<img src="/frontend/static/imgs/stars/utenfyll.png">';
                    }
                }
            }
            ?>
        </div>
    </div><div id="info">
        <h3><?php echo $i['title']; ?>, <?php echo $author; ?>.</h3>
        <!--<i>Synopsis</i>--><?php
            //Print the type of the book
            echo "<p>Type: ".ucfirst($i['type'])."</p>";
            //Include the list class to get the shelf name
            include_once ROOT.'admin/list.class.php';
            $lists = new Lists("shelves");
            //Include the scan_book class to get the book status
            include ROOT.'scan_book.class.php';
            $sb = new ScanBook();
            //
            require ROOT.'../../koble_til_database.php';
            $get_books = "SELECT * FROM lib_RFID WHERE bookID = '".$i['bookID']."'";
            $get_books_qry = $conn->query($get_books);
            $bookz = array('utlant' => 0, 'pa_biblo' => array());
            if($get_books_qry->num_rows > 0){
                while($book = $get_books_qry->fetch_assoc()){
                    //Check if book is lended
                    if($sb->isLended($book['RFID'])){
                        $bookz['utlant']++;
                    }else{
                        $bookz['pa_biblo'][] = $lists->getShelfName($book['_shelfID']);
                    }
                }
            }
            if($bookz['utlant'] != 0 || count($bookz['pa_biblo']) > 0){
                echo '<p>På Biblo: '.count($bookz['pa_biblo']).'</p>';
                if(count($bookz['pa_biblo']) > 1){
                    echo '<p>Hyller: ';
                }else{
                    echo '<p>Hylle: ';
                }
                $hyller = "";
                foreach($bookz['pa_biblo'] as $hylle){
                    $hyller .= $hylle.', ';
                }
                $hyller = rtrim($hylle, ', ');
                echo $hyller;
                echo '</p>';
                echo '<p>Utlånt: '.$bookz['utlant'].'</p>';
            }
        ?>
    </div>
    <a style="position:absolute;bottom:10px;left: 10px;" href="http://tung.deichman.no/frontend.php?index=search&search=<?php echo $_GET['return']; ?>"><img style="height: 15px;" src="<?php echo DEPENDENCIES_LOC; ?>imgs/arrow_left_black.png" /></a>
</div>
<div id="footnotes" class="bottom_left" >
    <a href="http://tung.deichman.no/frontend.php?index=search"><img src="<?php echo DEPENDENCIES_LOC; ?>imgs/cross.png" /></a>
</div>
<div id="footnotes" class="bottom_right">
    <img src="<?php echo DEPENDENCIES_LOC; ?>imgs/soke_p.png" />
</div>
    <?php 
}else{
    echo "Ops.. Her har det skjedd noe feil.";
}
?>