<div id="production">
	<p class="largeBoldFont">
		Pending Productions.
	</p>
	<div class="centerDiv pendingProductions">
		<?php $this->printVar('pendingProds'); ?>
		<form action="production.php" method="POST">
			Select An Item to Produce : <select name="prodItem" id="prodItem"> <?php $this->printVar('prodOptions'); ?></select>
		</form>
	</div>
	<div id="prodItemReqStat">
		<!-- The result from AJAX will be added here -->
	</div>
</div>