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
		<?php } else { ?>
			<script src="../templates/jquery.min.js"></script>
			<script src="../templates/adminJquery.js"></script>
		<?php }	?>
        <link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,700,400italic' type='text/css'>
    </head>
    <body>
        <!-- The NavBar Starts Here. -->
