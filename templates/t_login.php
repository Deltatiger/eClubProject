<!-- Page for User Login -->
<div id="mBody">
	<form action="login.php" method="POST">
		Username : <input type="text" name="username" /> <br />
		Password : <input type="text" name="password" /> <br />
		
		<input type="submit" value="Register" name="login" /> 
		
		<p class="message">
			<?php
				$this->printVar('message');
			?>
		</p>
	</form>
</div>