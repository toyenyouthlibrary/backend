<?php
require '../../../koble_til_database.php';
session_start();
//Fech the user credentials from file that is not publicly available ^_^
require '../../../admin_credentials.php';

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
    <title>Adminpanel | Tøyen Ungdomsbibliotek</title>
    <link href="style.css" rel="stylesheet" />
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
</head>
<body>
<?php

/*
    User verification
*/

$die = true;

if(!isset($_POST['user']) || !isset($_POST['pass'])){
    
}else{
    if(isset($users[$_POST['user']]) && $users[$_POST['user']] == $_POST['pass']){
        $die = false;
        $inf = array(
            'name' => $_POST['user'],
            'pass' => $_POST['pass']
        );
    }
}
if(isset($_POST['id']) && exists_id($_POST['id'])){
    $die = false;
}

if($die){
    j_die($error['failed_login']);
}
$res['id'] = 109342903234;

//Link root
if(isset($_POST['debug'])){
    define("URL_ROOT", "?debug&id=".$res['id']."&index=");
}else{
    define("URL_ROOT", "?index=");
}
?>
<div id="menu">
    <ul>
        <li><a href="<?php echo URL_ROOT; ?>books">Bøker</a></li>
        <ul>
            <li><a href="<?php echo URL_ROOT; ?>books/stats">Statistikk</a></li>
            <li><a href="<?php echo URL_ROOT; ?>books/create">Last opp</a></li>
        </ul>
        <li><a href="<?php echo URL_ROOT; ?>users">Brukere</a></li>
        <ul>
            <li><a href="<?php echo URL_ROOT; ?>users/stats">Statistikk</a></li>
            <li><a href="<?php echo URL_ROOT; ?>users/create">Lag ny</a></li>
        </ul>
        <li><a href="<?php echo URL_ROOT; ?>global">Globalt</a></li>
    </ul>
</div>
<div id="body">
<?php
if(isset($_GET['index'])){
    $index = $_GET['index'];
    $index_a = explode('/', $index);
    if($index_a[0] == "books"){
        if(!isset($index_a[1])){
            require 'list.class.php';
            $list = new Lists($index_a[0]);
            $books = $list->getList();
            if($books != false){
                echo '<table cellspacing=0><tr><th>ISBN 10</th><th>ISBN 13</th><th>Tittel</th><th>Original tittel</th><th>Forfatter</th></tr>';
                foreach($books as $book){
                    echo '<tr onclick="window.location.href = \''.URL_ROOT.'books/info/'.$book['bookID'].'\';" style="cursor:pointer;">
                        <td>'.$book['ISBN10'].'</td>
                        <td>'.$book['ISBN13'].'</td>
                        <td>'.$book['title'].'</td>
                        <td>'.$book['original-title'].'</td>
                        <td>'.$book['author'].'</td>
                    </tr>';
                }
                echo '</table>';
            }else{
                echo 'Klarte ikke å laste ned listen av bøker.';
            }
        }else if($index_a[1] == "stats"){
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
        }else if($index_a[1] == "create"){
            if(isset($_POST['title'])){
                require 'book_and_user_functions.class.php';
                $create = new CreateUsersAndBooks("book");
                $new_book = $create->add($_POST);
                if($new_book != false){
                    header("Location: ".URL_ROOT."books/info/".$new_book);
                    echo "Vellykket.";
                }else{
                    echo "Feilet<br>".$create->error;
                }
            }else{
                printCreateForm("book");
            }
            
        }else if($index_a[1] == "info"){
            if(isset($index_a[2])){
                require 'info.class.php';
                $information = new Info($index_a[0], $index_a[2]);
                $info = $information->getInfo();
                if($info != false){
                    print_info($info);
                }else{
                    echo $info->error;
                }
            }else{
                echo "Ingen bok er spesifisert. Velg en bok fra listen av alle bøker.";
            }
        }else if($index_a[1] == "modify"){
            if(isset($index_a[2])){
                require 'modify.class.php';
                $mod = new Modify();
                $update = $mod->update("lib_Book", array('bookID', $index_a[2]), $_POST);
                if($update === true){
                    header("Location: ".URL_ROOT."books/info/".$index_a[2]);
                    echo "Vellykket.";
                }else{
                    echo "Feilet<br>".$mod->error;
                }
            }else{
                echo "Mangler obligatorisk variabel.";
            }
        }else if($index_a[1] == "delete"){
            if(isset($index_a[2])){
                require 'book_and_user_functions.class.php';
                $delete = new CreateUsersAndBooks("book");
                $del = $delete->delete($index_a[2]);
                if($del == true){
                    header("Location: ".URL_ROOT."books");
                    echo "Vellykket.";
                }else{
                    echo "Feilet<br>".$delete->error;
                }
            }else{
                echo "Mangler obligatorisk variabel.";
            }
        }
    }else if($index_a[0] == "users"){
        //User pages
        if(!isset($index_a[1])){
            require 'list.class.php';
            $list = new Lists($index_a[0]);
            $users = $list->getList();
            echo '<table cellspacing=0><tr><th>Brukernavn</th><th>Navn</th><th>Fødselsdato</th><th>Kjønn</th><th>Skole</th></tr>';
            foreach($users as $user){
                echo '<tr onclick="window.location.href = \''.URL_ROOT.'users/info/'.$user['userID'].'\';" style="cursor:pointer;">
                    <td>'.$user['username'].'</td>
                    <td>'.$user['firstname'].' '.$user['lastname'].'</td>
                    <td>'.$user['birth'].'</td>
                    <td>'.$user['sex'].'</td>
                    <td>'.$user['school'].'</td>
                </tr>';
            }
            echo '</table>';
        }else if($index_a[1] == "stats"){
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
        }else if($index_a[1] == "create"){
            if(isset($_POST['firstname'])){
                require 'book_and_user_functions.class.php';
                $create = new CreateUsersAndBooks("user");
                $new_book = $create->add($_POST);
                if($new_book != false){
                    header("Location: ".URL_ROOT."users/info/".$new_book);
                    echo "Vellykket.";
                }else{
                    echo "Feilet<br>".$create->error;
                }
            }else{
                printCreateForm("user");
            }
        }else if($index_a[1] == "delete"){
            if(isset($index_a[2])){
                require 'book_and_user_functions.class.php';
                $delete = new CreateUsersAndBooks("user");
                $del = $delete->delete($index_a[2]);
                if($del == true){
                    header("Location: ".URL_ROOT."users");
                    echo "Vellykket.";
                }else{
                    echo "Feilet<br>".$delete->error;
                }
            }else{
                echo "Mangler obligatorisk variabel.";
            }
        }else if($index_a[1] == "info"){
            if(isset($index_a[2])){
                require 'info.class.php';
                $information = new Info($index_a[0], $index_a[2]);
                $info = $information->getInfo();
                if($info != false){
                    print_info($info);
                }else{
                    echo $info->error;
                }
            }else{
                echo "Ingen bok er spesifisert. Velg en bok fra listen av alle bøker.";
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
    }else if($index_a[0] == "modify"){
        if(isset($index_a[1]) && isset($index_a[2]) && isset($index_a[3])){
            if($index_a[1] == "book"){
                $tbl = "lib_Book";
                $return = "books";
            }else if($index_a[1] == "user"){
                $tbl = "lib_User";
                $return = "users";
            }else if($index_a[1] == "contact"){
                $tbl = "lib_Contact";
                $return = "users";
            }else if($index_a[1] == "rfid"){
                $tbl = "lib_RFID";
                $return = $index_a[2];
            }
            
            if(isset($tbl)){
                require 'modify.class.php';
                $mod = new Modify();
                if($tbl == "lib_RFID"){
                    $update = $mod->update($tbl, array("RFID", $_POST['original']), array('RFID' => $_POST['new']));
                }else{
                    $update = $mod->update($tbl, array($index_a[1].'ID', $index_a[2]), $_POST);
                }
                if($update === true){
                    header("Location: ".URL_ROOT.$return."/info/".$index_a[3]);
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
                    header("Location: ".URL_ROOT."users/info/".$index_a[2]);
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
                        header("Location: ".URL_ROOT."users/info/".$index_a[3]);
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
                        header("Location: ".URL_ROOT.$index_a[2]."s/info/".$index_a[3]);
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
                        header("Location: ".URL_ROOT.$index_a[3]."s/info/".$index_a[4]);
                        echo 'Vellykket';
                    }else{
                        echo 'Feilet<br>'.$rfid->error;
                    }
                }else{
                    echo 'Mangler obligatorisk variabel.';
                }
            }else{
                echo 'Mangler obligatorisk variabel.';
            }
        }else{
            
        }
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
    }else{
        $type = "book";
        echo '<h2>Bokinformasjon</h2>';
    }
    //Print general info
    echo '<table cellspacing=0><form method="POST" action="'.URL_ROOT.'modify/'.$type.'/'.$info[$type."ID"].'/'.$info[$type."ID"].'">';
    foreach($info as $key => $inf){
        if(!is_array($inf)){
            if($key != "userID" && $key != "bookID" && $key != "username" && $key != "registered"){
                if($key == "sex"){
                    echo '<tr><td>Kjønn</td><td><select name="'.$key.'">';
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
                    if($inf != null){
                        $selected = 'selected="selected"';
                    }
                    echo '<option value="'.$date.'" '.$selected.'>Godkjent '.$inf.'</option>';
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
            echo '<table cellspacing=0><tr><th>Telefon</th><th colspan=2>Email</th></tr>';
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
            echo '<tr><td colspan=3><input type="button" value="Legg til" onclick="window.location.href = \''.URL_ROOT.'contact/add/'.$info[$type."ID"].'\'" /></td></tr>';
            echo '</table>';
        }
    }
    
    //RFID
    echo '<h2>RFID</h2>';
    if(isset($info['rfid']) && is_array($info['rfid'])){
        echo '<table cellspacing=0>';
        foreach($info['rfid'] as $key => $rfid){
            echo '<tr><td>';
            //echo '<form action="'.URL_ROOT.'modify/rfid" method="POST" id="rfid_'.$key.'">';
            echo '<form action="'.URL_ROOT.'modify/rfid/'.$type.'s/'.$info[$type."ID"].'" method="POST" id="rfid_'.$key.'">';
            echo '<p class="p">'.$rfid.'</p>';
            echo '<input type="hidden" name="original" class="original" value="'.$rfid.'" />';
            echo '<input type="hidden" name="new" class="new" /></form></td>';
            echo '<td><input type="button" value="Endre" onclick="change_rfid('.$key.')" /> <input type="button" value="Slett" onclick="window.location.href=\''.URL_ROOT.'rfid/delete/'.$rfid.'/'.$type.'/'.$info[$type."ID"].'\'" /></td>';
            echo '</tr>
            ';
        }
        echo '</table>';
    }
    echo '<input type="button" onclick="window.location.href = \''.URL_ROOT.'rfid/create/'.$type.'/'.$info[$type."ID"].'\'" value="Legg til RFID">';
    
    //Lended history
    echo '<h2>Lånehistorikk</h2>';
    if(isset($info['lended']) && is_array($info['lended'])){
        echo '<table cellspacing=0><tr><th>Bruker ID</th><th>Bok ID</th><th>Utlånsdato</th><th>Innleveringsdato</th><th>Innleveringsfrist</th></tr>';
        foreach($info['lended'] as $lended){
            echo '<tr>';
            echo '<td><a href="'.URL_ROOT.'users/info/'.$lended['userID'].'">'.$lended['userID'].'</td>';
            echo '<td><a href="'.URL_ROOT.'books/info/'.$lended['bookID'].'">'.$lended['bookID'].'</td>';
            echo '<td>'.$lended['outDate'].'</td>';
            echo '<td>'.$lended['inDate'].'</td>';
            echo '<td></td>';
            echo '</tr>
            ';
        }
        echo '</table>';
    }
    //Button to delete user
    echo '<h3>Slett</h3><input type="button" value="Slett" onclick="window.location.href = \''.URL_ROOT.$type.'s/delete/'.$info[$type."ID"].'\'">';
    
    echo "</div>";
    
    display_rfid_script();
}

function printCreateForm($type){
    require 'book_and_user_functions.class.php';
    $create = new CreateUsersAndBooks($type);
    ?>
    <div class="padding">
    <form method="POST" action="<?php echo URL_ROOT.$type."s/create" ?>">
    <table cellspacing=0>
    <?php
    foreach($create->fields as $field){
        echo '<tr><td>'.$field.'</td><td><input type="text" maxlength=300 name="'.$field.'" /></td></tr>';
    }
    ?>
    <tr><td colspan=2><button><?php if($type == "user"){ echo "Lag bruker"; }else{ echo "Last opp bok";}?></button></td></tr>
    </table>
    </form>
    </div>
    <?php
}

function display_rfid_script(){
    echo '<script src="rfid_scanner.js"></script>';
}
?>

</div>
</body>
</html>