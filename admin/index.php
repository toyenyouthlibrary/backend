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
        require 'books.class.php';
        $books = new Books;
        if(!isset($index_a[1])){
            $res['books'] = $books->printAll();
        }else if($index_a[1] == "stats"){
            if(count($index_a) == 3){
                $res['stats'] = $books->printStats("dag", (int) $index_a[2]);
            }else{
                $res['stats'] = $books->printStats("dag");
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
    }
}else{
    //Default page
}

echo json_encode($res);

?>