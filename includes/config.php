<?php

/*
 * This is the main file that is included in all the Pages.
 */
    session_start();
	//This includes all the constants 
	include_once 'constants.php';
    //This holds all the general functions.
    include_once 'common_functions.php';
    //We invoke the DB first as we need it everywhere else.
    include_once 'mydb.php';
	//Now we invoke the session
    include_once 'session.php';
	//Now we include the template.
    include_once 'template.php';
	
	//We create all the objects needed for the working.
    $db = new DB();
    $session = new Session();
    $template = new Template();
    
?>
