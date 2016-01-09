<?php
session_start();
//Generate user credentials
$users = array(
    'admin' => 'cheesecake',
    ''
);

$die = true;
if(!isset($_SESSION['user']) || $_SESSION['user'] == null){
    //User is not logged in yet, but might have sent the login details in the url
    if(!isset($_GET['user']) || !isset($_GET['pass'])){
        
    }else{
        if(isset($users[$_GET['user']]) && $users[$_GET['user']] == $_GET['pass']){
            $die = false;
            $inf = array(
                'name' => $_GET['user'],
                'pass' => $_GET['pass']
            );
        }
    }
}else{
    $user = $_SESSION['user'];
    $user_pieces = explode(',', $user, 2);
    if(count($user_pieces) == 2){
        if(isset($users[$user_pieces[0]]) && $users[$user_pieces[0]] == $user_pieces[1]){
            $die = false;
            $inf = array(
                'name' => $user_pieces[0],
                'pass' => $user_pieces[1]
            );
        }
    }
}
if($die){
    unset($_SESSION['user']);
    die("Du m&aring; v&aelig;re logget inn for &aring; ha tilgang til denne siden.");
}else{
    $_SESSION['user'] = $inf['name'].','.$inf['pass'];
    if(isset($_GET['user']) || isset($_GET['pass'])){
        header("Location: ../admin/");
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin | T&oslash;yen Ungdomsbibliotek</title>
    <link href="style.css" rel="stylesheet" />
    <link rel="stylesheet" href="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.css">
    <script src="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.js"></script>
</head>
<body>
<div id="Menu">
    <ul>
        <li style="padding-left: 0;padding: 10px 0">
            <a href="../admin/" style="
                font-size: 20pt;
                height: initial; 
                line-height: initial;
                text-align: center;">JPanel</a>
        </li>
        <li><a href="?index=books">B&oslash;ker</a></li>
        <ul>
            <li><a href="?index=books/stats">Stats</a></li>
            <li><a href="?index=books/upload">Last opp bok</a></li>
            <li><a href="?index=books/borrower">Se l&aring;ner</a></li>
            <li><a href="?index=books/log">Se l&aring;ne historikk</a></li>
        </ul>
        <li><a href="?index=users">Brukere</a></li>
        <ul>
            <li><a href="?index=users/stats">Stats</a></li>
            <li><a href="?index=users/create">Lag bruker</a></li>
        </ul>
    </ul>
</div><!--
--><div id="Page">
<?php

if(isset($_GET['index'])){
    $index = $_GET['index'];
    $index_a = explode('/', $index);
    if($index_a[0] == "books"){
        require 'books.class.php';
        $books = new Books;
        if(!isset($index_a[1])){
            $books->printAll();
        }else if($index_a[1] == "stats"){
            if(count($index_a) == 3){
                $books->printStats("dag", (int) $index_a[2]);
            }else{
                $books->printStats("dag");
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

if(isset($index)){
    echo "<script>var page = '".$index."';</script>";
}

?>
</div>
</body>
</html>