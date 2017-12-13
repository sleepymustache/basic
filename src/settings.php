<?php
/*******************************************************************************
 * Global settings
 ******************************************************************************/

// Comma separated URLs that define the environments
define('LIVE_URL',  '');
define('STAGE_URL', '');

// Server dependant variables (Stage/Dev/Live)
if (\Sleepy\SM::isENV(STAGE_URL)) {
	define('ENV', 'STAGE');

	// Base Directory/URL
	define('URLBASE', '/');
	define('DIRBASE', realpath(dirname(__FILE__)) . '/app/');

	// DB Credentials
	define ("DBHOST", $WHG_DB_HOST);
	define ("DBUSER", $WHG_DB_USER);
	define ("DBPASS", $WHG_DB_PASSWD);
	define ("DBNAME", $WHG_DB_REPLDB);

	// Email information
	define('EMAIL_FROM', '');
	define('EMAIL_TO',   '');
	define('EMAIL_CC',   '');
	define('EMAIL_BCC',  '');

	// Analytics
	define('GA_ACCOUNT', '');
} elseif (\Sleepy\SM::isENV(LIVE_URL)) {
	define('ENV', 'LIVE');

	// Base Directory/URL
	define('URLBASE', '/');
	define('DIRBASE', realpath(dirname(__FILE__)) . '/app/');

	// DB Credentials
	define ("DBHOST", $WHG_DB_HOST);
	define ("DBUSER", $WHG_DB_USER);
	define ("DBPASS", $WHG_DB_PASSWD);
	define ("DBNAME", $WHG_DB_REPLDB);

	// Email information
	define('EMAIL_FROM', '');
	define('EMAIL_TO',   '');
	define('EMAIL_CC',   '');
	define('EMAIL_BCC',  '');

	// Analytics
	define('GA_ACCOUNT', '');
} else {
	define('ENV', 'DEV');

	// Base Directory/URL
	define('URLBASE', '/');
	define('DIRBASE', realpath(dirname(__FILE__)) . '/app/');

	// DB Credentials
	define ("DBHOST", $WHG_DB_HOST);
	define ("DBUSER", $WHG_DB_USER);
	define ("DBPASS", $WHG_DB_PASSWD);
	define ("DBNAME", $WHG_DB_REPLDB);

	// Email information
	define('EMAIL_FROM', '');
	define('EMAIL_TO',   '');
	define('EMAIL_CC',   '');
	define('EMAIL_BCC',  '');

	// Analytics
	define('GA_ACCOUNT', '');
}

// Set Debugging
if (class_exists('\Sleepy\Debug')) {
	\Sleepy\Debug::$enable_console = true;	// Show debug info in console
	\Sleepy\Debug::$enable_show    = false;	// Show debug info on screen
	\Sleepy\Debug::$enable_send    = false;	// Send debug info via email
	\Sleepy\Debug::$enable_log     = false;	// Log debug info to a db
}
