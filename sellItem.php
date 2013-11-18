<?php

	include_once 'includes/config.php';
	
	if(!$session->isLoggedIn())	{
		header('Location:login.php');
	}
	
	//This page is used to put items up for auction
	$userId = $session->getUserId();
	$sql = "SELECT `{$db->table('inventory')}`.`item_id` ,`item_name` FROM `{$db->name()}`.`{$db->table('inventory')}` , `{$db->name()}`.`{$db->table('item')}` WHERE `user_id` = '{$userId}' AND `{$db->table('inventory')}`.`item_id` = `{$db->table('item')}`.`item_id`";
	$query = $db->query($sql);
	$sellOptions = '<option value="0" selected="selected"> Select An item To sell </option>';
	while($row = $db->result($query))	{
		$sellOptions .= "<option value=\"{$row->item_id}\">{$row->item_name}</option>";
	}
	$template->setTemplateVar('selloptions', $sellOptions);

	
	//Load the page.
	$template->setPageTitle('Auction House - Sell');
	$template->setPage('sellItem');
	$template->loadPage();
?>