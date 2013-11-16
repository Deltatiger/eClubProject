<?php
	include_once "../includes/config.php";
	
	if(!$session->isAdminUser())	{
		header('Location:../index.php');
	}
	
	if(isset($_POST['addNewItem']))	{
		//Get all the required items and add them to the DB.
	}
	
	if(isset($_POST['registerItem']))	{
		//This is used to process the item details and Add it to the Database.
		$itemName = $db->escapeString(trim($_POST['newItemName']));
		$itemNameClean = strtolower($itemName);
		$itemProdTime = $db->escapeString(trim($_POST['newItemProdTime']));
		if(!ctype_digit($itemProdTime))	{
			$itemProdTime = 0;
		}
		$itemDependsCount = intVal($db->escapeString(trim($_POST['newItemDependCount'])));
		
		//First check if an item with the same name already exists and raise an exception if it does.
		$sql = "SELECT COUNT(`item_id`) as itemCount FROM `{$db->name()}`.`{$db->table('item')}` WHERE LOWER(`item_name`) = '{$itemNameClean}'";
		$query = $db->query($sql);
		$result = $db->result($query);
		if($result->itemCount > 0)	{
			$db->freeResults($query);
			$template->setTemplateVar('message', 'Item with the Same name already Exists.');
		} else {
			//This means we can add the details to the Database.
			$sql = "INSERT INTO `{$db->name()}`.`{$db->table('item')}` VALUES ('{$itemName}', '{$itemProdTime}', NULL)";
			$query = $db->query($sql);
			
			if($itemDependsCount > 0)	{
				//Now we add all the dependencies of the currently inserted item.
				//We need the id of the item currently inserted.
				$sql = "SELECT `item_id` FROM `{$db->name()}`.`{$db->table('item')}` WHERE LOWER(`item_name`) = '{$itemNameClean}'";
				$query = $db->query($sql);
				$result = $db->result($query);
				$newItemId = $result->item_id;
				$db->freeResults($query);
				//Start the loop to add the items into a temp array for processing.
				$reqItemsList = '';
				for($i = 1; $i <= $itemDependsCount; $i++)	{
					$reqItemId = $_POST['itemReq'.$i];
					$reqItemQty = intval(trim($_POST['itemQty'.$i]));
					if(isset($reqItemsList["{$reqItemId}"]))	{
						$reqItemsList["{$reqItemId}"] += $reqItemQty;
					} else {
						$reqItemsList["{$reqItemId}"] = $reqItemQty;
					}
				}
				//Now we should have to list with only unique entries.
				foreach($reqItemsList as $itemId => $itemQty)	{
					$sql = "INSERT INTO `{$db->name()}`.`{$db->table('require')}` VALUES ('{$newItemId}', '{$itemId}', '{$itemQty}')";
					$query = $db->query($sql);
					if(!$query)	{
						die('Some problem with Insertion. Check the Code.');
					}
				}
			}
			$template->setTemplateVar('message', 'Item Successfully Added to the Database.');
		}
	}
	
	$template->setPageTitle("Admin - Add Item");
	$template->setPage("adminItem");
	$template->loadPage();
?>