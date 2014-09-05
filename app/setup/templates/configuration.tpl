/*******************************************************************************
 * Global settings
 ******************************************************************************/

// Comma separated URLs that define the environments
define('LIVE_URL',  '{{ liveURL }}');
define('STAGE_URL', '{{ stageURL }}');

/**
 * Checks if the current site matches a URL
 * @param  string  $str The URL to match with current site
 * @return boolean      true if there was a match
 */
function isENV($str) {
	foreach (explode("," , $str) as $url) {
		if (strpos(strtolower($_SERVER['SERVER_NAME']), strtolower(trim($url))) !== false)
			return true;
	}

	return false;
}

// Server dependant variables (Dev/Stage/Live)
if (isENV(STAGE_URL)) {
	define("ENV", "STAGE");

	// Base Directory/URL
	define("URLBASE", "{{ stageURLBASE }}");
	define("DIRBASE", "{{ stageDIRBASE }}");

	// DB Credentials
	define ("DBHOST", "{{ stageDBHOST }}");
	define ("DBUSER", "{{ stageDBUSER }}");
	define ("DBPASS", "{{ stageDBPASS }}");
	define ("DBNAME", "{{ stageDBNAME }}");

	// Email information
	define('EMAIL_FROM', "{{ stageFROM }}");
	define('EMAIL_TO',   "{{ stageTO }}");
	define('EMAIL_CC',   "{{ stageCC }}");
	define('EMAIL_BCC',  "{{ stageBCC }}");

	// Analytics
	define('GA_ACCOUNT', "{{ stageANALYTICS }}");
} elseif (isENV(LIVE_URL)) {
	define("ENV", "LIVE");

	// Base Directory/URL
	define("URLBASE", "{{ liveURLBASE }}");
	define("DIRBASE", "{{ liveDIRBASE }}");

	// DB Credentials
	define ("DBHOST", "{{ liveDBHOST }}");
	define ("DBUSER", "{{ liveDBUSER }}");
	define ("DBPASS", "{{ liveDBPASS }}");
	define ("DBNAME", "{{ liveDBNAME }}");

	// Email information
	define('EMAIL_FROM', "{{ liveFROM }}");
	define('EMAIL_TO',   "{{ liveTO }}");
	define('EMAIL_CC',   "{{ liveCC }}");
	define('EMAIL_BCC',  "{{ liveBCC }}");

	// Analytics
	define('GA_ACCOUNT', "{{ liveANALYTICS }}");
} else {
	define("ENV", "DEV");

	// Base Directory/URL
	define("URLBASE", "{{ devURLBASE }}");
	define("DIRBASE", "{{ devDIRBASE }}");

	// DB Credentials
	define ("DBHOST", "{{ devDBHOST }}");
	define ("DBUSER", "{{ devDBUSER }}");
	define ("DBPASS", "{{ devDBPASS }}");
	define ("DBNAME", "{{ devDBNAME }}");

	// Email information
	define('EMAIL_FROM', "{{ devFROM }}");
	define('EMAIL_TO',   "{{ devTO }}");
	define('EMAIL_CC',   "{{ devCC }}");
	define('EMAIL_BCC',  "{{ devBCC }}");

	// Analytics
	define('GA_ACCOUNT', "{{ stageANALYTICS }}");
}

require_once('class.debug.php');

// Set Debugging
\Sleepy\Debug::$enable_show = true;				// Show debug info on screen
\Sleepy\Debug::$enable_send = false;			// Send debug info via email
\Sleepy\Debug::$enable_log  = false;			// Log debug info to a db