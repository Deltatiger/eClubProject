<!-- This is the Navigation bar for the regular users-->
<div id="mNavBar">
	<ul>
		<li> <a href="production.php">Production</a> </li>
		<?php if($this->getVar('isAdminUser') == 1) {	?>
			<li> <a href="admin/mainPage.php"> Admin Index </a> </li>
		<?php } ?>
	</ul>
</div>