<?php
require '../../koble_til_database.php';
session_start();
//Generate user credentials
$users = array(
    'admin' => 'cheesecake'
);

$res = array();
$res['error'] = '';

$die = true;

//User is not logged in yet, but might have sent the login details in the url
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
if(isset($_POST['id']) && $_POST['id'] == 109342903234){
    $die = false;
}

if($die){
    $res['error'] = 'Du m&aring; v&aelig;re logget inn for &aring; ha tilgang til denne siden.';
    die(json_encode($res));
}
$res['id'] = 109342903234;

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
            //Default stats page, without extra variables
            $stats = new Stats($index_a[0]);
            //$index_a[2] == book id
            if(count($index_a) == 3){
                $res['stats'] = $stats->printStats();
            }else{
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
        if(!isset($index_a[1])){
            require 'list.class.php';
            $list = new Lists($index_a[0]);
            $res['users'] = $list->getList();
        }
    }
}else{
    //Default page
}

echo json_encode($res);
//{"error":"","id":109342903234,"stats":{"stats":{"labels":[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30],"outDates":[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,2,5,1,1,1,0,0,0,0,0,0,0,0],"inDates":[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,2,0,1,0,0,0,0,0,0,1,0]}}}
//{"error":"","id":109342903234,"stats":{"stats":{"labels":[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30],"outDates":[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,2,5,1,1,1,0,0,0,0,0,0,0,0],"inDates":[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,2,0,1,0,0,0,0,0,0,1,0]}}}
?>