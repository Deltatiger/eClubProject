<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $this->getPageTitle() ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<?php if($this->getVar('isadmin') == 0)	{ ?>
			<script src="templates/jquery.min.js"></script>
			<script src="templates/myJquery.js"></script>
			<link rel="stylesheet" href="templates/common.css" />
			<link rel="stylesheet" href="templates/user.css" />
			<link rel='stylesheet' href="templates/fonts.css" type='text/css'>
		<?php } else { ?>
			<script src="../templates/jquery.min.js"></script>
			<script src="../templates/adminJquery.js"></script>
			<link rel="stylesheet" href="../templates/common.css" />
			<link rel="stylesheet" href="../templates/admin.css" />
			<link rel='stylesheet' href="../templates/fonts.css" type='text/css'>
		<?php }	?>
    </head>
    <body>
		<?php 
		if($this->getVar('isadmin') == 0)	{ 
			include $this->rootPath.'templates/infobar.php';
		} 
		?>
		<div id="bodyWrapper">
			<div id="headerBar">
				Entrepreneurs Club
			</div>
			<!-- The NavBar Starts Here. -->
