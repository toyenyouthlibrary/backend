<?php
if(!isset($_POST['search']) && !isset($_GET['book'])){
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
    <img src="<?php echo DEPENDENCIES_LOC; ?>imgs/cross.png" />
</div>
<div id="footnotes" class="bottom_right">
    <img src="<?php echo DEPENDENCIES_LOC; ?>imgs/soke_p.png" />
</div>

<?php
}else if(isset($_POST['search'])){?>
<style>
body{
    background-color: #4c9041;
}
h2{
    margin-bottom: 5px;
    line-height: 36px;
}
#text{
    margin-top: 160px;
    padding: 20px;
    height: calc(100vh - 260px - 160px - 40px);
}
input{
    width: 100%;
    padding: 20px;
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
    //If the user has searched some shit
    require DEPENDENCIES_URL.'..\search.class.php';
    $search = new Search($_POST['search']);
    if($search->error == ''){
        ?>
        
            <table cellspacing=0>
                <tr>
                    <th>Tittel</th>
                    <th>Forfatter</th>
                    <th>Type</th>
                    <th>Status</th>
                </tr>
        <?php
        foreach($search->result as $res){
            echo '<tr onclick="window.location.href= \''.URL.'&book='.$res['bookID'].'\'">';
            echo '<td>'.$res['title'].'</td>';
            echo '<td>'.$res['author'].'</td>';
            echo '<td>'.$res['language'].'</td>';
            echo '<td>'.$res['shelfID'].'</td>';
            echo '</tr>';
        }
        ?>
        </table>
        <?php
    }else{
        echo $search->error;
    }?>
<div id="frem_tilbake">
        <img src="<?php echo DEPENDENCIES_LOC; ?>imgs/arrow_left_black.png" /><img src="<?php echo DEPENDENCIES_LOC; ?>imgs/arrow_right_black.png" />
    </div>
</div>
<div id="footnotes" class="bottom_left">
    <img src="<?php echo DEPENDENCIES_LOC; ?>imgs/cross.png" />
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
        margin-top: 160px;
        padding: 20px;
        height: calc(100vh - 260px - 160px - 40px);
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
    #info i{
        padding: 1em 0;
        display: block;
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
        <img src="<?php echo DEPENDENCIES_LOC; ?>imgs/ronja_roverdatter_cover.png" />
        <div id="stars">
            <img src="<?php echo DEPENDENCIES_LOC; ?>imgs/stars.png" />
        </div>
    </div><div id="info">
        <h3><?php echo $i['title']; ?>, <?php echo $author; ?>.</h3>
        <i>Synopsis</i>
        <p>Hylle: <?php echo $i['shelfID']; ?>. Status: xx.</p>
    </div>
</div>
<div id="footnotes" class="bottom_left">
    <img src="<?php echo DEPENDENCIES_LOC; ?>imgs/cross.png" />
</div>
<div id="footnotes" class="bottom_right">
    <img src="<?php echo DEPENDENCIES_LOC; ?>imgs/soke_p.png" />
</div>
    <?php
}else{
    echo "Ops.. Her har det skjedd noe feil.";
}
?>