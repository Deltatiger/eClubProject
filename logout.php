<?php
	include_once 'includes/config.php';
	
	if(!$session->isLoggedIn())	{
		header('Location:login.php');
	}
	
	//Log the user out.
	$sessionId = $_SESSION['session_id'];
	$sql = "UPDATE `{$db->name()}`.`{$db->table('session')}` SET `session_user_id` = NULL , `session_login_stat` = '0' WHERE `session_id` = '{$sessionId}'";
	$query = $db->query($sql);
	
	$template->setPageTitle('Logout');
	$template->setPage('logout');
	$template->loadPage();
	
	header('refresh:3;url=login.php');
?>