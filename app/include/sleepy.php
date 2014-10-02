<?php
// If we are not setup yet, forward the user to the setup page
if (!@include_once('global.php')) {
	header('Location: /app/setup/');
	die();
}

require_once('class.sm.php');

\Sleepy\SM::initialize();