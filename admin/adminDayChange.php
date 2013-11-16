<?php
	include_once '../includes/config.php';
	
	if($session->isAdminUser())	{
		header('Location:../index.php');
	}
	
	//This page is used to take care of the Day Changes.
	
?>