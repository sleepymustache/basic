<?php
/*******************************************************************************
 * Global settings                                                             *
 ******************************************************************************/

	// This is teamsite specific stuff, but doesn't hurt anything
	if (!@include_once('Webkit/init.php')) {
		require_once('teamsite.php');
	}

	$strCurrentDir = getcwd();
	$workarea = '\\htdocs\\';

	if (strpos($strCurrentDir, $workarea) > 0) {
		$strBack = '.';
	} else {
		$strBack = '';
	}

	if (strpos($strCurrentDir, $workarea) > 0) {
		while (strpos($strCurrentDir, $workarea) > 0) {
			$strCurrentDir = substr($strCurrentDir, 0, strrpos($strCurrentDir, '\\'));
			$strBack .= '/..';
		}
	} else {
		while (strpos($strCurrentDir, $workarea) > 0) {
			$strCurrentDir = substr($strCurrentDir, 0, strrpos($strCurrentDir, '\\'));
			$strBack .= '/..';
		}
	}

	define("ROOT", $strBack);

	// Debugging
	Debug::$enable_show = true;			// Show debug info on screen
	Debug::$enable_send = false;		// Send debug info via email
	Debug::$enable_log  = false;		// Log debug info to a db

	// Globals
	define('LIVE_URL', '****CHANGE_LIVE_SITE_URL****');

	// Server dependant variables (Stage vs Live) */
	if (strpos($_SERVER['SERVER_NAME'], LIVE_URL) !== false) {
		define("ENV", "LIVE");

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
	} else {
		define("ENV", "STAGE");

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