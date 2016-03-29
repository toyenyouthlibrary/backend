<?php
error_reporting(E_ALL);
define("ROOT", getcwd()."/api/");
define("DEPENDENCIES_LOC", "frontend_resources/");
define("DEPENDENCIES_URL", ROOT."../frontend_resources/");
if(isset($_GET['index'])){
    define("URL", "?index=".$_GET['index']);
}
require '../koble_til_database.php';
session_start();
//Initialize result array with the part that will always be the same
$res = array('error' => '');

$error = array(
    
);


?>
<?php
if(isset($_GET['index'])){
    $index = $_GET['index'];
    $index_a = explode('/', $index);
    if($index_a[0] == "search"){
        $vars['header'] = array('title' => 'Søk');
        require DEPENDENCIES_URL.'header.php';
        require DEPENDENCIES_URL.'p_search.php';
        require DEPENDENCIES_URL.'footer.php';
    }
}else{
    header("Location: /frontend/");
}
?>