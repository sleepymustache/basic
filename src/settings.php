<?php
/**
 * Setting File
 *
 * PHP version 7.0.0
 *
 * @category Settings
 * @package  Sleepy\Core
 * @author   Jaime Rodriguez <hi.i.am.jaime@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @version  GIT: 1.0.0
 * @link     http://sleepymustache.com
 */

use Sleepy\Core\Debug;
use Sleepy\Core\SM;

// Comma separated URLs that define the environments
SM::$live_urls  = [ 'example.com' ];
SM::$stage_urls = [ 'stage.example.com' ];

// Server dependant variables (Dev/Stage/Live)
if (SM::isStage()) {

    // Base Directory/URL
    define('URLBASE', '/');
    define('DIRBASE', $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'app');

    // DB Credentials
    define("DBHOST", '');
    define("DBUSER", '');
    define("DBPASS", '');
    define("DBNAME", '');

    // Email information
    define('EMAIL_FROM', '');
    define('EMAIL_TO',   '');
    define('EMAIL_CC',   '');
    define('EMAIL_BCC',  '');

    // Analytics
    define('GA_ACCOUNT', '');

    // Set Debugging
    Debug::$enableShow    = false;   // Show debug info on screen
    Debug::$enableSend    = false;   // Send debug info via email
    Debug::$enableLog     = false;   // Log debug info to a db
    Debug::$enableConsole = false;   // Show debug info in the console

} elseif (SM::isLive()) {

    // Base Directory/URL
    define('URLBASE', '/');
    define('DIRBASE', $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'app');

    // DB Credentials
    define("DBHOST", '');
    define("DBUSER", '');
    define("DBPASS", '');
    define("DBNAME", '');

    // Email information
    define('EMAIL_FROM', '');
    define('EMAIL_TO',   '');
    define('EMAIL_CC',   '');
    define('EMAIL_BCC',  '');

    // Analytics
    define('GA_ACCOUNT', '');

    // Set Debugging
    Debug::$enableShow    = false;   // Show debug info on screen
    Debug::$enableSend    = false;   // Send debug info via email
    Debug::$enableLog     = false;   // Log debug info to a db
    Debug::$enableConsole = false;   // Show debug info in the console

} else {

    // Base Directory/URL
    define('URLBASE', '/');
    define('DIRBASE', $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'app');

    // DB Credentials
    define("DBHOST", '');
    define("DBUSER", '');
    define("DBPASS", '');
    define("DBNAME", '');

    // Email information
    define('EMAIL_FROM', '');
    define('EMAIL_TO',   '');
    define('EMAIL_CC',   '');
    define('EMAIL_BCC',  '');

    // Analytics
    define('GA_ACCOUNT', '');

    // Set Debugging
    Debug::$enableShow    = false;   // Show debug info on screen
    Debug::$enableSend    = false;   // Send debug info via email
    Debug::$enableLog     = false;   // Log debug info to a db
    Debug::$enableConsole = true;    // Show debug info in the console
}
