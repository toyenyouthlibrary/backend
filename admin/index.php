<?php
error_reporting(E_ALL);
define("SLASH", "/");
define("ROOT", getcwd().SLASH."..".SLASH);
require '..'.SLASH.'..'.SLASH.'..'.SLASH.'koble_til_database.php';
session_start();
//Fech the user credentials from file that is not publicly available ^_^
//require '..'.SLASH.'..'.SLASH.'..'.SLASH.'admin_credentials.php';

//Initialize result array with the part that will always be the same
$res = array('error' => '');

$error = array(
    'failed_login' => 'Du m&aring; v&aelig;re logget inn for &aring; ha tilgang til denne siden.'
);

/*
    HTML Header
*/
?>
<!DOCTYPE html>
<html>
<head>
    <title>Adminpanel | T�yen Ungdomsbibliotek</title>
    <link href="style.css" rel="stylesheet" />
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <script type="text/javascript" src="../../tablesorter/jquery.tablesorter.js"></script>
    <script>
    $(document).ready(function(){ 
        $("#order_tbl").tablesorter(); 
    }); 
    </script>
</head>
<body>
<?php

/*
    User verification
*/

$die = true;
/*
if(isset($_SESSION['id'])){
    $login = login($_SESSION['id']);
    if($login == false){
        unset($_SESSION['id']);
    }else{
        $die = false;
        $_SESSION['id'] = $_SESSION['id'];
    }
}else if(isset($_POST['user']) && $_POST['user'] != '' && isset($_POST['pass']) && $_POST['pass'] != ''){
    $login = login('', $_POST['user'], $_POST['pass']);
    if($login != false){
        $die = false;
        $_SESSION['id'] = $login;
    }
}

if($die){
    j_die($error['failed_login']);
}
*/

//Link root
if(isset($_POST['debug'])){
    define("URL_ROOT", "?debug&id=".$res['id']."&index=");
}else{
    define("URL_ROOT", "?index=");
}
?>
<div id="menu">
    <ul>
        <li><a href="<?php echo URL_ROOT; ?>list/books">B�ker</a></li>
        <ul>
            <li><a href="<?php echo URL_ROOT; ?>books/stats">Statistikk</a></li>
            <li><a href="<?php echo URL_ROOT; ?>create/book">Last opp</a></li>
        </ul>
        <li><a href="<?php echo URL_ROOT; ?>list/users">Brukere</a></li>
        <ul>
            <li><a href="<?php echo URL_ROOT; ?>users/stats">Statistikk</a></li>
            <li><a href="<?php echo URL_ROOT; ?>create/user">Lag ny</a></li>
        </ul>
        <li><a href="<?php echo URL_ROOT; ?>list/shelves">Hyller</a></li>
        <ul>
            <li><a href="<?php echo URL_ROOT; ?>create/shelf">Lag ny</a></li>
        </ul>
        <!--<li><a href="<?php echo URL_ROOT; ?>global">Globalt</a></li>-->
        <li><a href="<?php echo URL_ROOT; ?>rfid/search">S�k RFID</a></li>
    </ul>
</div>
<div id="body">
<?php
if(isset($_GET['index'])){
    $index = $_GET['index'];
    $index_a = explode('/', $index);
    if($index_a[0] == "books"){
        if($index_a[1] == "stats"){
            require 'stats.class.php';
            //Preset variables if the server doesn't send something specific
            $amount = array(
                'm&aring;ned' => array(
                    'amount' => 12,
                    'multipler' => 2592000
                ), 'dag' => array(
                    'amount' => 30,
                    'multipler' => 86400
                ), 'time' => array(
                    'amount' => 24,
                    'multipler' => 3600
                )
            );
            //$index_a[2] == book id
            if(count($index_a) == 3){
                $stats = new Stats($index_a[0], $index_a[2]);
                $res['stats'] = $stats->printStats();
            }else{
                $stats = new Stats($index_a[0]);
                $res['stats'] = $stats->printStats();
            }
        }
    }else if($index_a[0] == "users"){
        //User pages
        if($index_a[1] == "stats"){
            require 'stats.class.php';
            //Default stats page, without extra variables
            
            //$index_a[2] == book id
            if(count($index_a) == 3){
                $stats = new Stats($index_a[0], $index_a[2]);
                $res['stats'] = $stats->printStats();
            }else{
                //As there are no "global" stats for users yet (haven't come up with a reason to have it, and what it would include)
                $res['error'] = 'Ingen bruker er spesifisert.';
            }
        }
    }else if($index_a[0] == "global"){
        if(!isset($index_a[1])){
            
        }else if($index_a[1] == "history"){
            if(!isset($index_a[2])){
                //Display global history of lended books
                require 'history.class.php';
                $history = new History();
                $res['history'] = $history->getHistory();
            }else{
                
            }
        }
    }else if($index_a[0] == "list"){
        if(isset($index_a[1])){
            $fields = array(
                'shelves' => array(
                    'id' => 'shelfNR',
                    'fields' => array(
                        'ID' => 'shelfNR',
                        'Navn' => 'name'
                    )
                ), 'books' => array(
                    'id' => 'bookID',
                    'fields' => array(
                        'ISBN 10' => 'ISBN10',
                        'ISBN 13' => 'ISBN13',
                        'Tittel' => 'title',
                        'Original tittel' => 'original-title',
                        'Forfatter' => 'author'
                    )
                ), 'users' => array(
                    'id' => 'userID',
                    'fields' => array(
                        'Brukernavn' => 'username',
                        'Navn' => array('firstname', 'lastname'),
                        'F�dselsdato' => 'birth',
                        'Kj�nn' => 'sex',
                        'Skole' => 'school',
                        'Registrert' => 'registered'
                    )
                )
            );
            
            if(isset($fields[$index_a[1]])){
                require 'list.class.php';
                $list = new Lists($index_a[1]);
                $list_res = $list->getList();
                echo '<table id="order_tbl" cellspacing=0><thead><tr>';
                foreach($fields[$index_a[1]]['fields'] as $key => $field){
                    echo '<th>'.$key.'</th>';
                }
                echo '</tr></thead><tbody>';
                foreach($list_res as $info){
                    echo '<tr onclick="window.location.href = \''.URL_ROOT.'info/'.$index_a[1].'/'.$info[$fields[$index_a[1]]['id']].'\';" style="cursor:pointer;">';
                        foreach($fields[$index_a[1]]['fields'] as $field){
                            if($field == "sex"){
                                $sex = array(0 => 'Ikke spesifisert', 1 => 'Gutt', 2 => 'Jente');
                                echo '<td>'.$sex[$info[$field]].'</td>';
                            }else{
                                echo '<td>';
                                if(is_array($field)){
                                    foreach($field as $subfield){
                                        echo $info[$subfield]." ";
                                    }
                                }else{
                                    echo $info[$field];
                                }
                                echo '</td>';
                            }
                        }
                    echo '</tr>';
                }
                echo '</tbody></table>';
            }else{
                echo 'Ukjent.';
            }
        }
    }else if($index_a[0] == "info"){
        //Book
        if(isset($index_a[1]) && isset($index_a[2])){
            require 'info.class.php';
            $information = new Info($index_a[1], $index_a[2]);
            $info = $information->getInfo();
            if($info != false){
                print_info($info);
            }else{
                echo $info->error;
            }
        }else{
            echo "Mangler data :/";
        }
    }else if($index_a[0] == "create"){
        if(isset($index_a[1])){
            $fields = array(
                'book' => array(
                    'verifyer' => 'title'
                ), 'user' => array(
                    'verifyer' => 'firstname'
                ), 'shelf' => array(
                    'verifyer' => 'name'
                )
            );
            if(isset($fields[$index_a[1]])){
                if(isset($_POST[$fields[$index_a[1]]['verifyer']])){
                    //Fix the birth
                    $_POST['birth'] = $_POST['birth_year']."-".$_POST['birth_month']."-".$_POST['birth_day']." 00:00:00";
                    
                    require 'book_and_user_functions.class.php';
                    $create = new CreateUsersAndBooks($index_a[1]);
                    $new = $create->add($_POST);
                    if($new != false){
                        header("Location: ".URL_ROOT."info/".get_plural($index_a[1])."/".$new);
                        echo "Vellykket.";
                    }else{
                        echo "Feilet<br>".$create->error;
                    }
                }else{
                    printCreateForm($index_a[1]);
                }
            }else{
                echo 'Mangler stuff';
            }
        }
    }else if($index_a[0] == "delete"){
        if(isset($index_a[1])){
            if(isset($index_a[2])){
                require 'book_and_user_functions.class.php';
                $delete = new CreateUsersAndBooks($index_a[1]);
                $del = $delete->delete($index_a[2]);
                if($del == true){
                    header("Location: ".URL_ROOT."list/".get_plural($index_a[1]));
                    echo "Vellykket.";
                }else{
                    echo "Feilet<br>".$delete->error;
                }
            }else{
                echo "Mangler obligatorisk variabel.";
            }
        }
    }else if($index_a[0] == "modify"){
        if(isset($index_a[1]) && isset($index_a[2]) && isset($index_a[3])){
            if($index_a[1] == "book"){
                $tbl = "lib_Book";
                $return = "books";
            }else if($index_a[1] == "user"){
                $tbl = "lib_User";
                $return = "users";
                //Fix the birth
                $_POST['birth'] = $_POST['birth_year']."-".$_POST['birth_month']."-".$_POST['birth_day']." 00:00:00";
            }else if($index_a[1] == "contact"){
                $tbl = "lib_Contact";
                $return = "users";
            }else if($index_a[1] == "rfid"){
                $tbl = "lib_RFID";
                $return = $index_a[2];
            }else if($index_a[1] == "shelf"){
                $tbl = "lib_Shelf";
                $return = "shelves";
            }else if($index_a[1] == "setting"){
                $tbl = "lib_Settings";
                $return = "users";
            }
            
            if(isset($tbl)){
                require 'modify.class.php';
                $mod = new Modify();
                if($tbl == "lib_RFID"){
                    $update = $mod->update($tbl, array("RFID", $_POST['original']), array('RFID' => $_POST['new'], '_shelfID' => $_POST['_shelfID']));
                }else{
                    $update = $mod->update($tbl, array($index_a[1].'ID', $index_a[2]), $_POST);
                }
                if($update === true){
                    header("Location: ".URL_ROOT."info/".$return."/".$index_a[3]);
                    echo "Vellykket.";
                }else{
                    echo "Feilet<br>".$mod->error;
                }
            }else{
                echo "Mangler obligatorisk variabel.";
            }
        }else{
            echo "Mangler obligatorisk variabel.";
        }
    }else if($index_a[0] == "contact"){
        if(isset($index_a[1]) && isset($index_a[2])){
            if($index_a[1] == "add"){
                require 'contact_info.class.php';
                $ci = new ContactInfo();
                $res = $ci->add($index_a[2]);
                if($res == true){
                    header("Location: ".URL_ROOT."info/users/".$index_a[2]);
                    echo "Vellykket.";
                }else{
                    echo "Feilet<br>".$ci->error;
                }
            }else if($index_a[1] == "delete"){
                if(isset($index_a[3])){
                    require 'contact_info.class.php';
                    $ci = new ContactInfo();
                    $res = $ci->delete($index_a[2]);
                    if($res == true){
                        header("Location: ".URL_ROOT."info/users/".$index_a[3]);
                        echo "Vellykket.";
                    }else{
                        echo "Feilet<br>".$ci->error;
                    }
                }else{
                    echo "Mangler obligatorisk variabel.";
                }
            }else{
                echo "Mangler obligatorisk variabel.";
            }
        }else{
            echo "Mangler obligatorisk variabel.";
        }
    }else if($index_a[0] == 'rfid'){
        if(isset($index_a[1])){
            if($index_a[1] == 'create'){
                if(isset($index_a[2]) && isset($index_a[3])){
                    require 'rfid.class.php';
                    $rfid = new RFID();
                    $create = $rfid->create($index_a[2], $index_a[3]);
                    if($create === true){
                        header("Location: ".URL_ROOT."info/".get_plural($index_a[2])."/".$index_a[3]);
                        echo 'Vellykket';
                    }else{
                        echo 'Feilet<br>'.$rfid->error;
                    }
                }else{
                    echo 'Mangler obligatorisk variabel.';
                }
            }else if($index_a[1] == 'delete'){
                if(isset($index_a[2]) && isset($index_a[3]) && isset($index_a[4])){
                    require 'rfid.class.php';
                    $rfid = new RFID();
                    $delete = $rfid->delete($index_a[2]);
                    if($delete === true){
                        header("Location: ".URL_ROOT."info/".get_plural($index_a[3])."/".$index_a[4]);
                        echo 'Vellykket';
                    }else{
                        echo 'Feilet<br>'.$rfid->error;
                    }
                }else{
                    echo 'Mangler obligatorisk variabel.';
                }
            }else if($index_a[1] == 'search'){
                echo '<div class="padding">';
                if(!isset($_POST['rfid'])){
                    //Display info
                    echo "Skan RFID<form id='formy' action='".URL_ROOT."rfid/search' method='POST'><input type='hidden' name='rfid' /></form>";
                    display_rfid_script("search");
                }else{
                    //Display results
                    require 'rfid.class.php';
                    $rfid = new RFID();
                    $res = $rfid->search($_POST['rfid']);
                    if($res != false){
                        header("Location: ".URL_ROOT.$res['type']."s/info/".$res['id']);
                    }else{
                        echo "Feilet.<br>".$rfid->error."<br><br>Pr�v � scanne p� nytt";
                        display_rfid_script("search");
                    }
                }
                echo '</div>';
            }else{
                echo 'Mangler obligatorisk variabel.';
            }
        }else{
            echo "Mangler obligatorisk variabel.";
        }
    }else{
        echo "???";
    }
}else{
    //Default page
}

function print_info($info){
    $date = (new DateTime())->format('Y-m-d H:i:s');
    echo '<div class="padding" id="details">';
    if(isset($info['userID'])){
        $type = "user";
        echo '<h2>Brukerinformasjon</h2>';
    }else if(isset($info['bookID'])){
        $type = "book";
        echo '<h2>Bokinformasjon</h2>';
    }else if(isset($info['shelfID']) || isset($info['shelfNR'])){
        $type = "shelf";
        echo '<h2>Hylle informasjon</h2>';
    }else{
        return false;
    }
    //Print general info
    echo '<table cellspacing=0><form method="POST" action="'.URL_ROOT.'modify/'.$type.'/'.$info[$type."ID"].'/'.$info[$type."ID"].'">';
    foreach($info as $key => $inf){
        if(!is_array($inf)){
            if($key != "userID" && $key != "bookID" && ($key != "shelfID" || $type == "book") && $key != "username" && $key != "registered"){
                if($key == "sex"){
                    echo '<tr><td>Kj�nn</td><td><select name="'.$key.'">';
                    $descs = array("Ikke spesifisert", "Gutt", "Jente");
                    for($i = 0;$i < count($descs); $i++){
                        $selected = '';
                        if($i == $inf){
                            $selected = 'selected="selected"';
                        }
                        echo '<option value="'.$i.'" '.$selected.'>'.$descs[$i].'</option>';
                    }
                    echo '</select></td></tr>';
                }else if($key == "approved_date"){
                    echo '<tr><td>Godkjent</td><td><select name="'.$key.'"><option value="null">Ikke godkjent</option>';
                    $selected = '';
                    $approved_date = date("Y-m-d H:i:s");
                    if($inf != null){
                        $selected = 'selected="selected"';
                        $approved_date = $inf;
                    }
                    echo '<option value="'.$approved_date.'" '.$selected.'>Godkjent '.$inf.'</option>';
                    echo '</select></td></tr>';
                }else if($key == "shelfID" && $type != "shelf"){
                    $sel = "";
                    if($inf == 0){
                        $sel = "selected='selected'";
                    }
                    echo '<tr><td>Hylle</td><td><select name="'.$key.'"><option value="0" '.$sel.'>Ingen hylle</option>';
                    include 'list.class.php';
                    $_list = new Lists("shelves");
                    $list = $_list->getList();
                    foreach($list as $shelf){
                        $selected = "";
                        if($inf == $shelf['shelfID']){
                            $selected = "selected='selected'";
                        }
                        echo '<option value="'.$shelf['shelfID'].'" '.$selected.'>'.$shelf['name'].'</option>';
                    }
                    echo '</select></td></tr>';
                }else if($key == 'birth'){
                    $datee = explode(" ", $inf)[0];
                    $date = explode("-", $datee);
                    echo '<tr><td>F�dselsdag</td><td><select name="'.$key.'_day">';
                    for($i = 1; $i <= 31; $i++){
                        if($i < 10){
                            $i = '0'.$i;
                        }
                        $selected = '';
                        if($i."" == "".$date[2]){
                            $selected = 'selected="selected"';
                        }
                        echo "<option value='$i' $selected>".$i."</option>";
                    }
                    echo '</select> <select name="'.$key.'_month">';
                    for($i = 1; $i <= 12; $i++){
                        if($i < 10){
                            $i = '0'.$i;
                        }
                        $selected = '';
                        if($i."" == "".$date[1]){
                            $selected = 'selected="selected"';
                        }
                        echo "<option value='$i' $selected>".$i."</option>";
                    }
                    echo '</select> <select name="'.$key.'_year">';
                    for($i = (int) date("Y"); $i >= ((int) date("Y")) - 80; $i--){
                        $selected = '';
                        if($i == $date[0]){
                            $selected = 'selected="selected"';
                        }
                        echo "<option value='$i' $selected>".$i."</option>";
                    }
                    echo '</select></td></tr>';
                }else if($key == "type"){
                    echo '<tr><td>Type</td><td><select name="'.$key.'">';
                    $types = array('blad', 'bok', 'film', 'lydbok');
                    foreach($types as $typi){ $selected = ''; if($inf == $typi){$selected = 'selected="selected"';} echo "<option value='$typi' $selected>$typi</option>"; }
                    echo '</select></td></tr>';
                }else if($key == "language"){
                    echo '<tr><td>Spr�k</td><td><select name="'.$key.'">';
                    $languages = array('Engelsk', 'Norsk');
                    foreach($languages as $lang){ $selected = ''; if($inf == $lang){$selected = 'selected="selected"';} echo "<option value='$lang' $selected>$lang</option>"; }
                    echo '</select></td></tr>';
                }else{
                    echo '<tr>
                        <td>'.$key.'</td>
                        <td><input type="text" name="'.$key.'" maxlength=400 value="'.$inf.'" /></td>
                    </tr>';
                }
            }else{
                echo '<tr>
                    <td>'.$key.'</td>
                    <td>'.$inf.'</td>
                </tr>';
            }
            
        }
    }
    echo '<tr><td colspan=2><button>Oppdater</button></td></tr></form></table>';
    //Contact info
    if($type == "user"){
        //Contact info
        if(isset($info['contact']) && is_array($info['contact'])){
            echo '<h2>Kontaktinformasjon</h2>';
            echo '<table cellspacing=0><tr><th>Telefon</th><th>Email</th><th colspan=2>Comment</th></tr>';
            foreach($info['contact'] as $contact){
                echo '<tr><form action="'.URL_ROOT.'modify/contact/'.$contact['contactID'].'/'.$info[$type."ID"].'" method="POST">';
                foreach($contact as $key => $cont){
                    if($key != "contactID"){
                        echo '<td><input type="text" maxlength=300 name="'.$key.'" value="'.$cont.'" /></td>';
                    }
                }
                echo '<td><button>Oppdater</button><input type="button" value="Slett" onclick="window.location.href = \''.URL_ROOT.'contact/delete/'.$contact['contactID'].'/'.$info[$type."ID"].'\'" /></td>';
                echo '</form></tr>';
            }
            echo '<tr><td colspan=4><input type="button" value="Legg til" onclick="window.location.href = \''.URL_ROOT.'contact/add/'.$info[$type."ID"].'\'" /></td></tr>';
            echo '</table>';
        }
    }
    
    //Settings
    if($type == 'user'){
        if(isset($info['settings']) && is_array($info['settings'])){
            echo '<h2>Innstillinger</h2>';/*'public_photos' => 0, 'save_log' => 0, 'save_visits' => 0, 'preferred_contact' => 0*/
            echo '<form action="'.URL_ROOT.'modify/setting/'.$info['settings']["settingID"].'/'.$info[$type."ID"].'" method="POST"><table cellspacing=0>';
            foreach($info['settings'] as $keyy => $vall){
                if ($keyy == "settingID"){ echo "<input type='hidden' name='$keyy' value='$vall' />"; }else 
                if($keyy == "preferred_contact"){
                    echo '<tr><td>Foretrukket kontakt</td><td><select name="'.$keyy.'">';
                    //List contact-infos and display them by their comment
                    foreach($info['contact'] as $contact){
                        echo '<option value="'.$contact['contactID'].'">'.$contact['comment'].'</option>';
                    }
                    echo '</select></td></tr>';
                }else{
                    echo '<tr><td>'.$keyy.'</td><td><select name="'.$keyy.'">';
                    $poss_vals = array(0 => 'Ikke tillatt', 1 => 'Tillatt');
                    foreach($poss_vals as $k => $v){ $se = ''; if($k == $vall){$se = 'selected="selected"';} echo '<option value="'.$k.'" '.$se.'>'.$v.'</option>';  }
                    echo '</select></td></tr>';
                }
            }
            echo '<tr><td colspan=2><button>Oppdater</button></td></tr></table></form>';
        }
    }
    
    //RFID
    echo '<h2>RFID</h2>';
    if(isset($info['rfid']) && is_array($info['rfid'])){
        echo '<table cellspacing=0>';
        foreach($info['rfid'] as $key => $rfid){
            echo '<tr><td>';
            //echo '<form action="'.URL_ROOT.'modify/rfid" method="POST" id="rfid_'.$key.'">';
            echo '<form action="'.URL_ROOT.'modify/rfid/'.get_plural($type).'/'.$info[$type."ID"].'" method="POST" id="rfid_'.$key.'">';
            echo '<p class="p '.$rfid[0].'" style="display:inline;">'.$rfid[0].'</p>';
            echo '<input type="hidden" name="original" class="original" value="'.$rfid[0].'" />';
            echo '<input type="hidden" name="new" class="new" />'; 
            if($type == "book"){echo '<input type="hidden" name="_shelfID" value="'.$rfid[2].'" />'; }
            echo '</form></td>';
            echo '<td style="border-right: 1px solid black;"><input type="button" value="Endre" onclick="change_rfid('.$key.')" /> <input type="button" value="Slett" onclick="window.location.href=\''.URL_ROOT.'rfid/delete/'.$rfid[0].'/'.$type.'/'.$info[$type."ID"].'\'" /></td>';
            if($type == "book"){
                echo '<td><form action="'.URL_ROOT.'modify/rfid/'.get_plural($type).'/'.$info[$type."ID"].'" method="POST">';
                echo '<select style="display:inline;" name="_shelfID">';
                $selected = ''; if($rfid[2] == 0){ $selected = "selected='selected'"; }
                echo '<option value="0" '.$selected.'>Ingen Hylle.</option>';
                require ROOT.'../../koble_til_database.php';
                $get_shelves = "SELECT * FROM lib_Shelf";
                $get_shelves_qry = $conn->query($get_shelves);
                if($get_shelves_qry->num_rows > 0){
                    while($shelf = $get_shelves_qry->fetch_assoc()){
                        $selected = ''; if($shelf['shelfID'] == $rfid[2]){ $selected = 'selected="selected"'; }
                        echo '<option value="'.$shelf['shelfID'].'" '.$selected.'>'.$shelf['shelfNR'].' - '.$shelf['name'].'</option>';
                    }
                }
                echo '</select><input type="hidden" name="original" value="'.$rfid[0].'" />';
                echo '<input type="hidden" name="new" value="'.$rfid[0].'" />';
                echo ' <button>Oppdater</button></form></td>';
            }
            echo '</tr>';
        }
        echo '</table>';
    }
    echo '<input type="button" onclick="window.location.href = \''.URL_ROOT.'rfid/create/'.$type.'/'.$info[$type."ID"].'\'" value="Legg til RFID">';
    
    //Lended history
    if($type == "book" || $type == "user"){
        echo '<h2>L�nehistorikk</h2>';
        if(isset($info['lended']) && is_array($info['lended'])){
            echo '<table cellspacing=0><tr><th>Bok RFID</th><th>Bruker ID</th><th>Bok ID</th><th>Utl�nsdato</th><th>Innleveringsdato</th><th>Innleveringsfrist</th></tr>';
            foreach($info['lended'] as $lended){
                echo '<tr>';
                echo '<td><a href="'.URL_ROOT.'info/books/'.$lended['bookID'].'#'.$lended['bookRFID'].'" class="'.$lended['bookRFID'].'">'.$lended['bookRFID'].'</a></td>';
                echo '<td><a href="'.URL_ROOT.'info/users/'.$lended['userID'].'">'.$lended['userID'].'</a></td>';
                echo '<td><a href="'.URL_ROOT.'info/books/'.$lended['bookID'].'#'.$lended['bookRFID'].'">'.$lended['bookID'].'</a></td>';
                echo '<td>'.$lended['outDate'].'</td>';
                echo '<td>'.$lended['inDate'].'</td>';
                echo '<td></td>';
                echo '</tr>
                ';
            }
            echo '</table>';
        }
    }
    
    //Button to delete
    echo '<h3>Slett</h3><input type="button" value="Slett" onclick="window.location.href = \''.URL_ROOT.'delete/'.$type.'/'.$info[$type."ID"].'\'">';
    
    echo "</div>";
    
    display_select_rfid_script();
    display_rfid_script();
}

function printCreateForm($type){
    require 'book_and_user_functions.class.php';
    $create = new CreateUsersAndBooks($type);
    ?>
    <div class="padding">
    <form method="POST" action="<?php echo URL_ROOT."create/".$type; ?>">
    <table cellspacing=0>
    <?php
    foreach($create->fields as $field){
        if($field == 'birth'){
            echo '<tr><td>F�dselsdag</td><td><select name="'.$field.'_day">';
            for($i = 1; $i <= 31; $i++){
                if($i < 10){
                    $i = '0'.$i;
                }
                echo "<option value='$i'>".$i."</option>";
            }
            echo '</select> <select name="'.$field.'_month">';
            for($i = 1; $i <= 12; $i++){
                if($i < 10){
                    $i = '0'.$i;
                }
                echo "<option value='$i'>".$i."</option>";
            }
            echo '</select> <select name="'.$field.'_year">';
            for($i = (int) date("Y"); $i >= ((int) date("Y")) - 80; $i--){
                echo "<option value='$i'>".$i."</option>";
            }
            echo '</select></td></tr>';
        }else if($field == 'type'){
            echo '<tr><td>Type</td><td><select name="'.$field.'">';
            $types = array('blad', 'bok', 'film', 'lydbok');
            foreach($types as $type){ echo "<option value='$type'>$type</option>"; }
            echo '</select></td></tr>';
        }else if($field == 'language'){
            echo '<tr><td>Spr�k</td><td><select name="'.$field.'">';
            $languages = array('Engelsk', 'Norsk');
            foreach($languages as $lang){ $selected = ''; if($inf == $lang){$selected = 'selected="selected"';} echo "<option value='$lang' $selected>$lang</option>"; }
            echo '</select></td></tr>';
        }else{
            echo '<tr><td>'.$field.'</td><td><input type="text" maxlength=300 name="'.$field.'" /></td></tr>';
        }
    }
    ?>
    <tr><td colspan=2><button><?php if($type == "user"){echo "Lag bruker";}else if($type == "book"){echo "Last opp bok";}else{echo "Registrer hylle";} ?></button></td></tr>
    </table>
    </form>
    </div>
    <?php
}

function display_rfid_script($action = "default"){
    echo '<script>var action = "'.$action.'";</script>';
    echo '<script src="rfid_scanner.js"></script>';
}
function display_select_rfid_script(){
    echo '<script src="select_rfid.js"></script>';
}

function get_plural($type){
    if($type == "shelf"){
        return "shelves";
    }else{
        return $type."s";
    }
}
function get_singular($plural){
    if($plural == "shelves"){
        return "shelf";
    }else{
        return rtrim($plural, "s");
    }
}
?>

</div>
</body>
</html>