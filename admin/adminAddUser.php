<?php
	include_once "../includes/config.php";
	
	if(isset($_POST['addUser']))	{
		//This is to validate the data for adding a new User to the Game.
		$username = $db->escapeString(trim($_POST['newUsername']));
		$usernameClean = strtolower($username);
		$password = $db->escapeString(trim($_POST['newUserPass']));
		$initBalance = $db->escapeString(trim($_POST['newUserInitBal']));
		
		$sql = "SELECT COUNT(*) as usercount FROM `{$db->name()}`.`{$db->table('user')}` WHERE LOWER(`username`) = '{$usernameClean}'";
		$query = $db->query($sql);
		$result = $db->result($query);
		if($result->usercount >0)	{
			//User with the same name already exists.
			$db->freeResults($query);
			$template->setTemplateVar('emessage', 'Username already exists.');
		} else {
			$db->freeResults($query);
			//Insert into the DB.
			$passwordHash = sha1($password);
			$sql = "INSERT INTO `{$db->name()}`.`{$db->table('user')}` VALUES ('{$username}', '{$passwordHash}', NULL, '{$initBalance}')";
			$query = $db->query($sql);
		}
		
	}
?>