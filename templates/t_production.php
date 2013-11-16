<div id="mBody">
	Pending Productions : <br />
	<?php $this->printVar('pendingProds'); ?>
	
	
	<form action="production.php" method="POST">
		Select An Item to Produce : <select name="prodItem" id="prodItem"> <?php $this->printVar('prodOptions'); ?></select>
	</form>
	
	<div id="prodItemReqStat">
		<!-- The result from AJAX will be added here -->
	</div>
</div>