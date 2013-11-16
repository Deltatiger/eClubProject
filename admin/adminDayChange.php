<?php
	include_once '../includes/config.php';
	
	if(!$session->isAdminUser())	{
		header('Location:../index.php');
	}
	
	//This page is used to take care of the Day Changes.
	if(isset($_POST['changeDay']))	{
		//We have to change the day in the config table first.
		$sql = "SELECT `config_value` FROM `{$db->name()}`.`{$db->table('config')}` WHERE `config_name` = 'day_count'";
		$query = $db->query($sql);
		$result = $db->result($query);
		$currentDay = intval($result->config_value);
		$db->freeResults($query);
		
		$currentDay += 1;
		$sql = "UPDATE `{$db->name()}`.`{$db->table('config')}` SET `config_value` = '{$currentDay}' WHERE `config_name` = 'day_count'";
		$query = $db->query($sql);
		if(!$query)	{
			$template->setTemplateVar('message', 'Day Change Failed. Try again.');
		} else {
			//Now update the productions
			$sql = "UPDATE `{$db->name()}`.`{$db->table('production')}` SET `time_left` = `time_left` - 1";
			$query = $db->query($sql);
			
			//We add the items with time_left = 0 to the inventory of the user's inventory.
			$sql = "SELECT `user_id`, `item_id` FROM `{$db->name()}`.`{$db->table('production')}` WHERE `time_left` = '0'";
			$query = $db->query($sql);
			while($row = $db->result($query))	{
				$sql = "SELECT COUNT(`item_qty`) as item_exists FROM `{$db->name()}`.`{$db->table('inventory')}` WHERE `user_id` = '{$row->user_id}' AND `item_id` = '{$row->item_id}'";
				$query2 = $db->query($sql);
				$result2 = $db->result($query2);
				if($result2->item_exists > 0)	{
					$sql = "UPDATE `{$db->name()}`.`{$db->table('inventory')}` SET `item_qty` = `item_qty` + 1 WHERE `user_id` = '{$row->user_id}' AND `item_id` = '{$row->item_id}'";
					$db->freeResults($query2);
					$query2 = $db->query($sql);
				} else {
					$db->freeResults($query2);
					$sql = "INSERT INTO `{$db->name()}`.`{$db->table('inventory')}` VALUES ('{$row->user_id}', '{$row->item_id}', 1)";
					$query2 = $db->query($sql);
				}
			}
			
			//Delete all the invalid ones.
			$db->freeResults($query);
			$sql = "DELETE FROM `{$db->name()}`.`{$db->table('production')}` WHERE `time_left` = '0'";
			$query = $db->query($sql);
			
			//Now we update all the Auctions.
			$sql = "UPDATE `{$db->name()}`.`{$db->table('auction')}` SET `time_left` = `time_left` - 1 WHERE `time_left` != 0";
			$query = $db->query($sql);
			
			//We process all the auctions which are having 0 time_left.
			$sql = "SELECT * FROM `{$db->name()}`.`{$db->table('auction')}` WHERE `time_left` = '0'";
			$query = $db->query($sql);
			while($row = $db->result($query))	{
				if($row->current_bidder == NULL)	{
					//We have to remove the item from the auction and add it back to the users inventory.
					$userId = $row->user_id;
					$itemId = $row->item_id;
					$itemQty = $row->item_qty;
					addItemToInventory($userId, $itemId, $itemQty);
				} else {
					//We have a bidder. We have to add the item to the bidder.
					$userId = $row->current_bidder;
					$itemId = $row->item_id;
					$itemQty = $row->item_qty;
					addItemToInventory($userId, $itemId, $itemQty);
					//Transfer the money to the user who sold the item.
					$sellerId = $row->user_id;
					$priceToTransfer = $row->current_bid;
					$sql = "UPDATE `{$db->name()}`.`{$db->table('user')}` SET `user_balance` = `user_balance` + {$priceToTransfer} WHERE `user_id` = '{$sellerId}'";
					$query = $db->query($sql);
				}
			}
			
			//Delete all the processed ones.
			$sql = "DELETE FROM `{$db->name()}`.`{$db->table('auction')}` WHERE `time_left` = '0'";
			$query = $db->query($sql);
			
			$template->setTemplateVar('message', 'Day Change Completed.');
		}
	}
	
	$template->setPageTitle('Admin Day Change');
	$template->setPage('adminDayChange');
	$template->loadPage();
?>