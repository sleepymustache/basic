<?php
/*******************************************************************************
 * Global settings                                                             *
 ******************************************************************************/

	// This is teamsite specific stuff, but doesn't hurt anything
	if (!@include_once('Webkit/init.php')) {
		require_once('teamsite.php');
	}

	$strCurrentDir = getcwd();

	if (strpos($strCurrentDir, '\\htdocs\\') > 0) {
		$strBack = '.';
	}

	if (strpos($strCurrentDir, '\\htdocs\\') > 0) {
		while (strpos($strCurrentDir, '\\htdocs\\') > 0) {
			$strCurrentDir = substr($strCurrentDir, 0, strrpos($strCurrentDir, '\\'));
			$strBack .= '/..';
		}
	} else {
		while (strpos($strCurrentDir, '/htdocs/') > 0) {
			$strCurrentDir = substr($strCurrentDir, 0, strrpos($strCurrentDir, '/'));
			$strBack .= '/..';
		}
	}

	define("ROOT", $strBack);

	// Debugging
	Debug::$enable_show = true;
	Debug::setHandler();

	// Globals
	define('LIVE_URL', 'xofigo.com');

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
		define('EMAIL_TO', '');
		define('EMAIL_CC', '');
		define('EMAIL_BCC', '');

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
		define('EMAIL_TO', '');
		define('EMAIL_CC', '');
		define('EMAIL_BCC', '');

		// Analytics
		define('GA_ACCOUNT', '');
	}