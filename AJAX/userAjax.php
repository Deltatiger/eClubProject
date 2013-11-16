<?php
	include_once '../includes/config.php';
	
	if(!$session->isLoggedIn() || !isset($_POST['ajaxPageName']))	{
		header('Location:login.php');
	}
	
	//This page is used to hold all the details about the user's Ajax methods.
	switch($_POST['ajaxPageName'])	{
		case 'prodItemReqStat':
			/*
			 * Page Name : production.php
			 */
			//This is used to check whether the given item can be produced by the user.
			$prodItemId = $_POST['itemId'];
			$sql = "SELECT `item_name`, `req_item_qty`, `item_qty` 
				FROM (SELECT * FROM `{$db->name()}`.`{$db->table('require')}` WHERE `item_id` = '{$prodItemId}') as `temp_require`
				LEFT JOIN `{$db->name()}`.`{$db->table('inventory')}`
				ON `temp_require`.`req_item_id` = `{$db->table('inventory')}`.`item_id`
				LEFT JOIN `{$db->name()}`.`{$db->table('item')}`
				ON `temp_require`.`req_item_id` = `{$db->table('item')}`.`item_id`";
			$query = $db->query($sql);
			$itemStats = '';
			$canProduce = true;
			while($row = $db->result($query))	{
				if($row->item_qty == NULL)	{
					$row->item_qty = 0;
				}
				$itemStats .= "{$row->item_name} Req : {$row->req_item_qty} Inventory : {$row->item_qty} ";
				if ($row->item_qty >= $row->req_item_qty )	{
					$itemStats .= 'Green';
				} else {
					$itemStats .= 'Red';
					$canProduce = false;
				}
				$itemStats .= '<br />';
			}
			echo $itemStats;
			if($canProduce)	{
				echo '<a href="#" id="produceSelectedItem"> Produce Item </a>';
			} else {
				echo 'You can\'t make that item. You require the items in Red.';
			}
			break;
		case 'prodItemConfirm':
			/*
			 * Page Name : production.php
			 */
			//This is used to confirm whether the items can be produced or not. If yes then produce it.
			//We Check again just to make sure that there is not malfunction of the AJAX
			$prodItemId = $_POST['itemId'];
			$sql = "SELECT `req_item_id`, `req_item_qty`, `item_qty`
				FROM (SELECT * FROM `{$db->name()}`.`{$db->table('require')}` WHERE `item_id` = '{$prodItemId}') as `temp_require`
				LEFT OUTER JOIN `{$db->name()}`.`{$db->table('inventory')}`
				ON `temp_require`.`req_item_id` = `{$db->table('inventory')}`.`item_id`";
			$query = $db->query($sql);
			$itemReqList = '';
			while($row = $db->result($query))	{
				if($row->item_qty < $row->req_item_qty)	{
					echo 1;
					$db->freeResults($query);
					break;
				} else {
					$itemReqList["{$row->req_item_id}"] = intval($row->req_item_qty);
				}
			}
			$db->freeResults($query);
			//Now the user has all the items. Proceed to update the Users inventory.
			$userId = $session->getUserId();
			foreach($itemReqList as $itemId => $itemQty)	{
				$sql = "UPDATE `{$db->name()}`.`{$db->table('inventory')}` SET `item_qty` = `item_qty` - {$itemQty} WHERE `item_id` = '{$itemId}' AND `user_id` = '{$userId}'";
				$query = $db->query($sql);
				//This is the fail safe so that a revert of data is possible.
				if($query)	{
					$itemReqList["{$itemId}"] = -$itemReqList["{$itemId}"];
				} else {
					//TODO : Perform revert procedures and throw and error.
					echo '2';
					break;
				}
			}
			
			//Now we add the item to the production queue.
			$sql = "SELECT `item_make_time` FROM `{$db->name()}`.`{$db->table('item')}` WHERE `item_id` = '{$prodItemId}'";
			$query = $db->query($sql);
			$result = $db->result($query);
			$itemMakeTime = $result->item_make_time;
			$db->freeResults($query);
			
			$sql = "INSERT INTO `{$db->name()}`.`{$db->table('production')}` VALUES ('{$userId}', '{$prodItemId}', '{$itemMakeTime}')";
			$query = $db->query($sql);
			echo '0';
			break;
		default:
			echo 'Invalid Page Name. Check your Code.';
	}
?>