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

//User verification
$die = true;

/*if(!isset($_POST['user']) || !isset($_POST['pass'])){
    
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
$res['id'] = 109342903234;*/

die();

if(isset($_GET['index'])){
    $index = $_GET['index'];
    $index_a = explode('/', $index);
    if($index_a[0] == "books"){
        if(!isset($index_a[1])){
            require 'list.class.php';
            $list = new Lists($index_a[0]);
            $res['books'] = $list->getList();
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
        }else if($index_a[1] == "upload"){
            echo "upload book";
        }else if($index_a[1] == "borrower"){
            echo "find borrower of book";
        }else if($index_a[1] == "log"){
            echo "get borrower log of book";
        }else if($index_a[1] == "modify"){
            echo "modify book";
        }
    }else if($index_a[0] == "users"){
        //User pages
        if(!isset($index_a[1])){
            require 'list.class.php';
            $list = new Lists($index_a[0]);
            $res['users'] = $list->getList();
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
    }
}else{
    //Default page
}

echo json_encode($res);
?>