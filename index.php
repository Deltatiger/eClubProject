<?php
	include_once "includes/config.php";
	
	if(!$session->isLoggedIn())	{
		header('Location:login.php');
	}
	
	$template->setPageTitle("E-Club game");
	$template->setPage("index");
	$template->loadPage();
?>