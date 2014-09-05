<?php
	session_start();

	define('DIRBASE', $_SERVER['DOCUMENT_ROOT'] . '/app/setup/');

	require_once($_SERVER['DOCUMENT_ROOT'] . '/app/include/class.template.php');

	$page = new \Sleepy\Template('setup');

	// SEO
	$page->bind('title', 'Sleepy Mustache');
	$page->bind('description', 'This is the description');
	$page->bind('keywords', 'blog, sleepy mustache, framework');

	// Content
	$page->bind('header', 'sleepy<span>MUSTACHE</span>');
	$page->bind('heading', 'Completing setup');
	$page->bindStart();
?>
	<p>
		Please verify that your settings are correct before continuing. To
		change these settings, please use the browser back button. To modify
		settings after the wizard is completed, edit the settings file located
		at <em>/include/global.php</em>.
	</p>
<?php
	$page->bindStop('help');
	$page->bindStart();

	foreach ($_SESSION['steps'] as $env) {
		?>
		<h2><?php echo ucfirst($env['frmID']) ?> Environment Settings</h2>
		<table>
			<tr>
				<td><strong>Database Host:</strong></td>
				<td><?php echo (strlen($env['txtHost']) > 0) ? $env['txtHost'] : "N/A" ?></td>
			</tr>
			<tr>
				<td><strong>Database Name:</strong></td>
				<td><?php echo (strlen($env['txtName']) > 0) ? $env['txtName'] : "N/A" ?></td>
			</tr>
			<tr>
				<td><strong>Database User:</strong></td>
				<td><?php echo (strlen($env['txtUser']) > 0) ? $env['txtUser'] : "N/A" ?></td>
			</tr>
			<tr>
				<td><strong>Database Pass:</strong></td>
				<td><?php echo (strlen($env['txtPass']) > 0) ? $env['txtPass'] : "N/A" ?></td>
			</tr>
			<tr>
				<td><strong>Domain:</strong></td>
				<td><?php echo (strlen($env['txtDomain']) > 0) ? $env['txtDomain'] : "N/A" ?></td>
			</tr>
			<tr>
				<td><strong>URL:</strong></td>
				<td><?php echo (strlen($env['txtURL']) > 0) ? $env['txtURL'] : "N/A" ?></td>
			</tr>
			<tr>
				<td><strong>Path:</strong></td>
				<td><?php echo (strlen($env['txtPath']) > 0) ? $env['txtPath'] : "N/A" ?></td>
			</tr>
			<tr>
				<td><strong>Email To:</strong></td>
				<td><?php echo (strlen($env['txtEmailTo']) > 0) ? $env['txtEmailTo'] : "N/A" ?></td>
			</tr>
			<tr>
				<td><strong>Email From:</strong></td>
				<td><?php echo (strlen($env['txtEmailFrom']) > 0) ? $env['txtEmailFrom'] : "N/A" ?></td>
			</tr>
			<tr>
				<td><strong>Email CC:</strong></td>
				<td><?php echo (strlen($env['txtEmailCC']) > 0) ? $env['txtEmailCC'] : "N/A" ?></td>
			</tr>
			<tr>
				<td><strong>Email BCC:</strong></td>
				<td><?php echo (strlen($env['txtEmailBCC']) > 0) ? $env['txtEmailBCC'] : "N/A" ?></td>
			</tr>
		</table>
		<?php
	}
?>
	<p>
		<a href="/app/setup/finish/">Finish</a>
	</p>
<?php
	$page->bindStop('form');
	$page->show();