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
	
	if(isset($_POST['addUser']))	{
		//This is to validate the data for adding a new User to the Game.
		$username = $db->escapeString(trim($_POST['newUsername']));
		$usernameClean = strtolower($username);
		$password = $db->escapeString(trim($_POST['newUserPass']));
		$initBalance = $db->escapeString(trim($_POST['newUserInitBal']));
		
		$sql = "SELECT COUNT(`user_name`) as usercount FROM `{$db->name()}`.`{$db->table('user')}` WHERE LOWER(`user_name`) = '{$usernameClean}'";
		$query = $db->query($sql);
		$result = $db->result($query);
		if($result->usercount >0)	{
			//User with the same name already exists.
			$db->freeResults($query);
			$template->setTemplateVar('message', 'Username already exists.');
		} else {
			$db->freeResults($query);
			//Insert into the DB.
			$passwordHash = sha1($password);
			$sql = "INSERT INTO `{$db->name()}`.`{$db->table('user')}` VALUES ('{$username}', '{$passwordHash}', NULL, '{$initBalance}')";
			$query = $db->query($sql);
			$template->setTemplateVar('message', 'User Added succesfully.');
		}
	}
	
	$template->setPageTitle('Admin - Add User');
	$template->setPage('adminAddUser');
	$template->loadPage();
?>