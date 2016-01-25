<?php
/*
 * File that contains all the cronjob activities (functions that are being run on a given interval of time)
*/

if(!isset($_GET['action'])){
    x_die("!isset($_GET['action'])");
}

if($_GET['action'] == "notify_borrowers"){
    /*
     * Check how much time remains on the borrowed books. If little, send warnings to user.
     * Log warnings when sent so that the user won't receive multiple warnings
     * Include hardcoded email / phone number in the log (db)
     * Called every hour.
     * Connected to some kind of setting that says how many warnings one should get, and how frequently
    */
    
}else if($_GET['action'] == "clean_db"){
    /*
     * Remove fields that are not related, and not used. (RFID's that have no existing user/book/etc.)
     * Typically called 1 time a day or week
    */
    
}


function x_die($str){
    /*
     * Log error messages to file or database (must be unreadable to the public)
     * Add info such as $_SERVER['REMOTE_ADDR'], referer
    */
    die();
}