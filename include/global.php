<?php
/*******************************************************************************
 * Global settings                                                             *
 ******************************************************************************/

	// This is teamsite specific stuff, but doesn't hurt anything
	if (!@include_once('Webkit/init.php')) {
		require_once('teamsite.php');
	}

	// Debugging
	Debug::$enable_show = true;			// Show debug info on screen
	Debug::$enable_send = false;		// Send debug info via email
	Debug::$enable_log  = false;		// Log debug info to a db

	// Globals
	define('LIVE_URL',  '****CHANGE_URL****');
	define('STAGE_URL', '****CHANGE_URL****');

	// Server dependant variables (Dev/Stage/Live)
	if (strpos($_SERVER['SERVER_NAME'], STAGE_URL) !== false) {
		define("ENV", "LIVE");

		// Base Directory/URL
		define("URLBASE", "/");
		define("DIRBASE", $_SERVER['DOCUMENT_ROOT']);

		// DB credentials
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
	} elseif (strpos($_SERVER['SERVER_NAME'], LIVE_URL)) {
		define("ENV", "STAGE");

		// Base Directory/URL
		define("URLBASE", "/");
		define("DIRBASE", $_SERVER['DOCUMENT_ROOT']);

		// DB Credentials
		define ("DBHOST", $WHG_DB_HOST);
		define ("DBUSER", $WHG_DB_USER);
		define ("DBPASS", $WHG_DB_PASSWD);
		define ("DBNAME", $WHG_DB_REPLDB);

		// Email information
		define('EMAIL_FROM', 'from@mailinator.com');
		define('EMAIL_TO',   'jaime@mailinator.com');
		define('EMAIL_CC',   '');
		define('EMAIL_BCC',  '');

		// Analytics
		define('GA_ACCOUNT', '');
	} else {
		define("ENV", "DEV");

		// Base Directory/URL
		define("URLBASE", "/");
		define("DIRBASE", $_SERVER['DOCUMENT_ROOT']);

		// DB credentials
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