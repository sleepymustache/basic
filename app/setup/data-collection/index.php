<?php
	session_start();

	define('DIRBASE', $_SERVER['DOCUMENT_ROOT'] . '/app/setup/');

	require_once($_SERVER['DOCUMENT_ROOT'] . '/app/include/class.template.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/app/modules/disabled/form-builder/class.formbuilder.php');

	// What step are we on?
	if (!isset($_GET['step'])) {
		$step = 1;
	} else {
		$step = $_GET['step'];
	}

	switch(@$_GET['step']) {
	case 2:
		$environment = "Staging";
		break;
	case 3:
		$environment = "Live";
		break;
	default:
		$environment = "Development";
	}

	// The form data
	$json = '{
		"id": "' . strtolower($environment) . '",
		"action": "./?step=' . $step . '",
		"method": "POST",
		"fieldsets": [
			{
				"legend": "Paths",
				"fields": [
					{
						"name": "txtDomain",
						"label": "Domain*",
						"type": "text",
						"value": "",
						"rules": {
							"required": true
						}
					}, {
						"name": "txtURL",
						"label": "Base URL*",
						"type": "text",
						"value": "\\",
						"rules": {
							"required": true
						}
					}, {
						"name": "txtPath",
						"label": "Base Directory*",
						"type": "text",
						"value": "' . $_SERVER['DOCUMENT_ROOT'] . '\\app\\",
						"rules": {
							"required": true
						}
					}
				]
			}, {
				"legend": "Database",
				"fields": [
					{
						"name": "txtHost",
						"label": "Host",
						"type": "text",
						"value": "localhost"
					}, {
						"name": "txtName",
						"label": "Name",
						"type": "text",
						"value": ""
					}, {
						"name": "txtUser",
						"label": "User",
						"type": "text",
						"value": ""
					}, {
						"name": "txtPass",
						"label": "Password",
						"dataMap": "Password",
						"type": "password"
					}
				]
			}, {
				"legend": "Emails",
				"fields": [
					{
						"name": "txtEmailFrom",
						"label": "From",
						"type": "text",
						"placeholder": "example@example.com",
						"value": "",
						"rules": {
							"email": true
						}
					}, {
						"name": "txtEmailTo",
						"label": "To",
						"type": "text",
						"placeholder": "example@example.com",
						"value": "",
						"rules": {
							"email": true
						}
					}, {
						"name": "txtEmailCC",
						"label": "CC",
						"type": "text",
						"placeholder": "example@example.com",
						"value": "",
						"rules": {
							"email": true
						}
					}, {
						"name": "txtEmailBCC",
						"label": "BCC",
						"type": "text",
						"placeholder": "example@example.com",
						"value": "",
						"rules": {
							"email": true
						}
					}
				]
			}, {
				"fields": [
					{
						"name": "btnSubmit",
						"label": "",
						"value": "Continue",
						"type": "submit",
						"class": "submit"
					}
				]
			}
		]
	}';

	$Form = new \FormBuilder\Form($json);

	if ($Form->submitted()) {
		// Validate the form
		$passed = $Form->validate();

		if ($passed === true) {
			$_SESSION['steps'][$step] = $_POST;

			$step = $step + 1;

			if ($step == 4) {
				header('Location: /app/setup/verify/');
				die();
				var_dump($_SESSION['steps']);

				echo "PROCESS DATA";
				echo "Confirmation Page";
				echo "TEST CONNECTIONs";
				echo "CREATE SETUP FILE";
				echo "DELETE setup folder";
				die();
				// We filled out all 3 forms
			} else {
				header("Location: /app/setup/data-collection/?step={$step}");
			}
		}
	}

	$page = new \Sleepy\Template('setup');

	// SEO
	$page->bind('title', 'Sleepy Mustache');
	$page->bind('description', 'This is the description');
	$page->bind('keywords', 'blog, sleepy mustache, framework');

	// Content
	$page->bind('header', 'sleepy<span>MUSTACHE</span>');
	$page->bind('heading', $environment . ' Setup - Step ' . $step . ' of 3');
	$page->bindStart();
?>
	<p>
		Please enter the database, paths, and email information below.
	</p>
<?php
	$page->bindStop('help');
	$page->bind('form', $Form->render());

	$page->show();