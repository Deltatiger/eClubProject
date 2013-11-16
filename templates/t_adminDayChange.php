<div id="mBody">
	<form action="adminDayChange.php" method="POST" id="dayChangeForm">
		<input type="submit" name="changeDay" value="Change Day"/>
	</form>
	<div class="msgContainer">
		<?php $this->printVar('message'); ?>
	</div>
</div>