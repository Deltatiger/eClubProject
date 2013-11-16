<?php
	include_once '../includes/config.php';
	
	if(!$session->isAdminUser() || !isset($_POST['ajaxPageName']))	{
		die('You must be an Admin to see this page.');
	}
	
	switch($_POST['ajaxPageName'])	{
		case 'reqItemsList':
			/*
			 * Page : adminAddItems.php
			 */
			$sql = "SELECT `item_name`, `item_id` FROM `{$db->name()}`.`{$db->table('item')}`";
			$query = $db->query($sql);
			$reqItemsList = '';
			while($row = $db->result($query))	{
				$reqItemsList .= "<option value=\"{$row->item_id}\">{$row->item_name}</option>";
			}
			echo $reqItemsList;
			break;
		default: 
			echo 'No such page found.';
	}
?>