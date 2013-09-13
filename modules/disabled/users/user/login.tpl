{{ #include header }}
	<h1>Login</h1>
	<form action="#" method="post">
		<fieldset>
			<legend>Please enter your user login information</legend>
			<ul>
				<li>
					<label for="txtEmail">Email:</label>
					<input type="text" id="txtEmail" name="txtEmail">
				</li>
				<li>
					<label for="pwdPassword">Password:</label>
					<input type="password" id="pwdPassword" name="pwdPassword">
				</li>
				<li>
					<p>{{ error }}</p>
				</li>
			</ul>
		</fieldset>
		<fieldset class="submit">
			<fieldset>
				<legend></legend>
				<ul>
					<li><input type="submit" value="Login"></li>
				</ul>
			</fieldset>
		</fieldset>
	</form>
{{ #include footer }}