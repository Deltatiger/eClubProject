<?php
	include_once 'includes/config.php';
	
	if($session->isLoggedIn())	{
		header('Location:index.php');
	}
	
	if(isset($_POST['login']))	{
		$username = $_POST['username'];
		$password = $_POST['password'];
		if(!$session->login($username, $password))	{
			$template->setTemplateVar('message', 'Invalid Credentials.');
		} else {
			echo '<script> alert(\'Login Successful.\'); </script>';
			header('Location:index.php');
		}
	}
	
	$template->setPageTitle('eClub - Login');
	$template->setPage('login');
	$template->loadPage();
?>