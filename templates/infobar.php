<div id="infoBar">
	<div id="infoLeftPart">
		Welcome <?php $this->printVar('username'); ?>
	</div>
	<div id="infoRightPart">
		You have &#8377;<?php $this->printVar('currentBalance'); ?> &nbsp;
		Production Queue : <?php $this->printVar('prodQueueCount');?> Item(s).
	</div>
	<div class="clearDiv"></div>
</div>