<!-- Page for User Login -->
<div id="mBody">
	<div id="loginHolder">
		<form action="login.php" method="POST">
			<div class="formLabel">
				<label for="username">Username : </label>
			</div>
			<div class="formElement">
				<input type="text" name="username" id="username" class="inputType1"/>
			</div>
			<div class="clearDiv"></div>
			<div class="formLabel">
				<label for="password">Password : </label>
			</div>
			<div class="formElement">
				<input type="password" name="password" id="password" class="inputType1"/> <br />
			</div>
			<div class="clearDiv"></div>
			<p class="message">
				<?php
					$this->printVar('message');
				?>
			</p>
			<div class="centerDiv">
				<input type="submit" value="Register" name="login" class="buttonType1"/> 
			</div>
		</form>
	</div>
</div>