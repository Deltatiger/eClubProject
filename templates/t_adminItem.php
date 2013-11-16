<!-- Page for Adding Items to the Database -->
<div id="mBody">
	<form action="adminAddItem.php" method="POST">
		<div id="adminNewItemName">
			Item Name : <input type="text" name="newItemName" /> <a href="#" id="addMoreDepends" class="customButtons">+</a>
			Production Time : <input type="text" name="newItemProdTime" value="0"/>
		</div>
		<div id="adminItemDepends">
			<!-- This is the container for the depends on part of an item. -->
			<input type="hidden" value="0" id="addItemDependCount" name="newItemDependCount"/>
		</div>
		<input type="submit" name="registerItem" value="Register Item" />
		<?php $this->printVar('message'); ?>
	</form>
</div>