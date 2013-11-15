<?php
	include_once "../includes/config.php";
	
	if($session->isLoggedIn())	{
		//We have to check if the user is eClubAdminUser else redirect him somewhere else.
		$username = $session->getUsernameFromSession();
		if($username != 'eClubAdminUser')	{
			header('Location:../index.php');
		}
	} else {
		header('Location:../index.php');
	}
	
	if(isset($_POST['addNewItem'])	{
		
	}
	
	$template->setPageTitle("Admin - Add Item");
	$template->setPage("adminItem");
	$template->loadPage();
?>