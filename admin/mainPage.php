<?php
	/* This is the admin index for the eClubGame. */
	include_once '../includes/config.php';
	
	if($session->isLoggedIn())	{
		//We have to check if the user is eClubAdminUser else redirect him somewhere else.
		$username = $session->getUsernameFromSession();
		if($username != 'eClubAdminUser')	{
			header('Location:../index.php');
		}
	} else {
		header('Location:../index.php');
	}
	
	//This is the main page where all the game changes and updation are done.
	$template->setPageTitle('Admin - Home Page');
	$template->setPage('adminIndex');
	$template->loadPage();
?>
