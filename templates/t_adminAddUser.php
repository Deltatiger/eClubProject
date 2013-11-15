<!-- Page for Add New User -->
<div id="mBody">
	<form action="adminAddUser.php" method="POST">
		Username : <input type="text" name="newUsername" /> <br />
		Password : <input type="text" name="newUserPass" /> <br />
		Initial Balance : <input type="text" name="newUserInitBal" /> <br />
		
		<input type="submit" value="Register" name="addUser" /> 
		
		<p class="message">
			<?php
				$this->printVar('message');
			?>
		</p>
	</form>
</div>