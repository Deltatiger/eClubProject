<?php

/*
 * This is the main file that is included in all the Pages.
 */
    session_start();
    //This holds all the general functions.
    include_once 'common_functions.php';
    //We invoke the DB first as we need it everywhere else.
    include_once 'mydb.php';
    $db = new DB();
    //Now we include the Basket class but do not make an instance of it. It is made in the session Class.
    include_once 'basket.php';
    //Now we invoke the session
    include_once 'session.php';
    $session = new Session();
    //Now we include the template.
    include_once 'template.php';
    $template = new Template();
    
?>
