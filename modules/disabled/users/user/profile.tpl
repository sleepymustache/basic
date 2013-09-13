{{ #include header }}
	<h1>User Profile</h1>
	<form action="#" method="post">
		<fieldset>
			<ul>
				<li>
					<label for="txtEmail">Email:</label>
					<input type="text" disabled="disabled" id="txtEmail" name="txtEmail" value="{{ email }}">
				</li>
				<li>
					<label for="txtRole">Role:</label>
					<input type="text" disabled="disabled" id="txtRole" name="txtRole" value="{{ role }}">
				</li>
				<li>
					<label for="pwdPassword">Password:</label>
					<input type="password" id="pwdPassword" name="pwdPassword">
				</li>
				<li>
					<label for="pwdConfirm">Confirm:</label>
					<input type="password" id="pwdConfirm" name="pwdConfirm">
				</li>
			</ul>
		</fieldset>
		<fieldset class="submit">
			<ul>
				<li><input type="submit" value="Save"></li>
			</ul>
		</fieldset>
	</form>
{{ #include footer }}