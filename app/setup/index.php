<?php
	define('URLBASE', '/app/setup/');
	define('DIRBASE', $_SERVER['DOCUMENT_ROOT'] . URLBASE);

	require_once(DIRBASE . '../include/class.template.php');

	$page = new \Sleepy\Template('setup');
	$page->bind('title', 'sleepyMUSTACHE - Setup');
	$page->bind('header', 'Setup');
	$page->bind('heading', 'Introduction');
	$page->bindStart();
?>
	<p>
		Your install of SleepyMUSTACHE is almost complete. This wizard will
		walk you through the rest of the installation process and should be
		complete in under five minutes.
	</p>
	<p>
		The next three pages will get information about your development,
		staging, and live environments, including your database connection,
		your server setup (e.g. Domain, paths, etc.), and email information
		(e.g. To, From, CC, BCC).
	</p>
	<p>
		Please note, the only required settings are the domain, base URL, and
		base directory. All settings can be modified in the future by editing
		<code>/app/include/global.php</code>.
	</p>
	<p>
		<a href="data-collection/">Continue &raquo;</a>
	</p>
<?php
	$page->bindStop('form');
	$page->show();