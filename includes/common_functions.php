<?php

/*
 * This contains all the common functions.
 */

function generateRandString($length)	{
    //This generates a random string of $length charecters long
    $randomString = '';
    $range = str_split('abcdefghijklmnopqrstuvwxyz1234567890<>?:"{}!@#$%^&*()_+', '1');
    for($i = 0; $i < $length; $i++)	{
        $randomString .= array_rand($range);
    }
    return $randomString;
}

function addItemToInventory($userId, $itemId, $itemQty = 1)	{
	global $db;
	$sql = "SELECT COUNT(`item_id`) as item_count FROM `{$db->name()}`.`{$db->table('inventory')}` WHERE `item_id` = '{$itemId}' AND `user_id` = '{$userId}'";
	$query = $db->query($sql);
	$result = $db->result($query);
	$db->freeResults($query);
	if($result->item_count > 0)	{
		$sql = "UPDATE `{$db->name()}`.`{$db->table('inventory')}` SET `item_qty` = `item_qty` + '{$itemQty}' WHERE `user_id` = '{$userId}' AND `item_id` = '{$itemId}'";
	} else {
		$sql = "INSERT INTO `{$db->name()}`.`{$db->table('inventory')}` VALUES ('{$userId}', '{$itemId}', '{$itemQty}')";
	}
	$query = $db->query($sql);
	return $query;
}

function getProdQueueCount($userId = NULL)	{
	//Used to get the number of items in the production queue of `$userId`
	global $db, $session;
	if($userId == NULL)	{
		if($session->isLoggedIn())	{
			$userId = $session->getUserId();
		} else {
			return 0;
		}
	}
	if($userId == 0)	{
		return 0;
	}
	$sql = "SELECT COUNT(`item_id`) as prodQueueCount FROM `{$db->name()}`.`{$db->table('production')}` WHERE `user_id` = '{$userId}'";
	$query = $db->query($sql);
	$result = $db->result($query);
	$db->freeResults($query);
	return $result->prodQueueCount;
}

function getCurrentBalance($userId = NULL)	{
	global $db, $session;
	if($userId == NULL)	{
		if($session->isLoggedIn())	{
			$userId = $session->getUserId();
		} else {
			return 0;
		}
	}
	if($userId == 0)	{
		return 0;
	}
	$sql = "SELECT `user_balance` FROM `{$db->name()}`.`{$db->table('user')}` WHERE `user_id` = '{$userId}'";
	$query = $db->query($sql);
	$result = $db->result($query);
	$db->freeResults($query);
	return $result->user_balance;
}
?>
