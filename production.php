<?php
	include_once 'includes/config.php';
	
	if(!$session->isLoggedIn())	{
		header('Location:login.php');
	}
	
	/*
	 * Two things are shown in this page.
	 * 1. Pending items in the production Queue.
	 * 2. New items that can be produced.
	 */
	 
	//First we show the Items that are still pending in the Production Queue.
	$currentUserId = $session->getUserId();
	$sql = "SELECT `item_name`, `time_left` FROM `{$db->name()}`.`{$db->table('production')}`, `{$db->name()}`.`{$db->table('item')}` WHERE `{$db->table('production')}`.`item_id` = `{$db->table('item')}`.`item_id` AND `{$db->table('production')}`.`user_id` = '{$currentUserId}'";
	$query = $db->query($sql);
	$pendingProductions = '<table class="type1">';
	$pendingProductions .= '<tr> <th class="name"> Item Name </th> <th class="time"> Time Remaining </th> </tr>';
	while($row = $db->result($query))	{
		$pendingProductions .= "<tr><td class=\"name\">{$row->item_name}</td><td class=\"time\">{$row->time_left} day(s)</td></tr>";
	}
	$pendingProductions .= '</table>';
	$db->freeResults($query);
	if(strlen($pendingProductions) == 86)	{
		$pendingProductions = 'There are no items for Production.';
	}
	$template->setTemplateVar('pendingProds', $pendingProductions);
	
	//Now we show the items that can be produced in a single Drop down Box.
	//We only show items that can be produced and not all the items.
	$sql = "SELECT `item_name`, `{$db->table('item')}`.`item_id` FROM `{$db->name()}`.`{$db->table('item')}`, `{$db->name()}`.`{$db->table('require')}` WHERE `{$db->table('item')}`.`item_id` = `{$db->table('require')}`.`item_id` GROUP BY `{$db->table('item')}`.`item_id` HAVING COUNT(`req_item_id`) > 0";
	$query = $db->query($sql);
	$prodItemList = '<option value="0" selected="selected"> Select an Item </option>';
	while($row = $db->result($query))	{
		$prodItemList .= "<option value=\"{$row->item_id}\"> {$row->item_name}</option>";
	}
	$template->setTemplateVar('prodOptions', $prodItemList);
	
	//Load page stuff.
	$template->setPageTitle('Productions');
	$template->setPage('production');
	$template->loadPage();
?>