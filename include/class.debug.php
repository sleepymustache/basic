<?php
/**
 * Class for custom debugging functions.
 *
 * This class can send emails, log to a database, or display on screen debug
 * information. You can set the enabled flags to enable the debug functions or
 * set them to false to quiet them down. This way you can leave them as a part
 * of your code with little overhead. For email and database logging, don't
 * forget to setup the public properties.
 *
 * @section usage Usage
 * @code
 *   // Turn debugging to screen on
 *   Debug::$enable_show = true;
 *   Debug::out("This will goto the screen because $enable_show == true");
 *
 *   // Turn off debugging to screen
 *   Debug::$enable_show = false;
 * @endcode
 *
 * @section changelog Changelog
 * ## Version 1.7
 * * Added the date section to the documentation
 *
 * ## Version 1.6
 * * Updated defaults to coinside with globals
 *
 * @date June 16, 2014
 * @author Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @version 1.6
 * @copyright  GPL 3 http://cuttingedgecode.com
 */
class Debug {
	/**
	 * object Debug The single instance is stored here.
	 * @private
	 */
	private static $instance = NULL;

	/**
	 * bool Enable output to screen
	 */
	public static $enable_show = false;

	/**
	 * bool Enabled logging to a database
	 */
	public static $enable_log = false;

	/**
	 * bool Enabled logging via email
	 */
	public static $enable_send = false;

	/**
	 * string Email address to send email to.
	 */
	public static $emailTo;

	/**
	 * string Email address cc send email to.
	 */
	public static $emailCC;

	/**
	 * string Email address bcc send email to.
	 */
	public static $emailBCC;

	/**
	 * string Email address to send email from.
	 */
	public static $emailFrom;

	/**
	 * string The subject of the email.
	 */
	public static $emailSubject;

	/**
	 * string[] The body of the email.
	 */
	public static $emailBuffer;

	/**
	 * string Database Host
	 */
	public static $dbHost;

	/**
	 * string Database Name
	 */
	public static $dbName;

	/**
	 * string Database User Name
	 */
	public static $dbUser;

	/**
	 * string Database Password
	 */
	public static $dbPass;

	/**
	 * string Database Table to use for logging
	 */
	public static $dbTable;

	/**
	 * PDO Database object
	 */
	private static $dbPDO;

	private function __clone() {}

	private function __construct() {
		// Setup email defaults
		Debug::$emailBuffer = array();
		Debug::$emailBuffer[] = "Date: " . date(DATE_ATOM, mktime(date("G"), date("i"), 0, date("m"), date("d"), date("Y")));
		Debug::$emailBuffer[] = "Server IP: " . $_SERVER['SERVER_ADDR'];
		Debug::$emailBuffer[] = "Client IP: " . $_SERVER['REMOTE_ADDR'];
		Debug::$emailBuffer[] = "Filename: " . $_SERVER["SCRIPT_FILENAME"];
		Debug::$emailBuffer[] = "---";
		Debug::$emailTo = EMAIL_TO;
		Debug::$emailFrom = EMAIL_FROM;
		Debug::$emailSubject = date(DATE_ATOM, mktime(date("G"), date("i"), 0, date("m"), date("d"), date("Y")));
		Debug::$emailCC = EMAIL_CC;
		Debug::$emailBCC = EMAIL_BCC;

		// Setup logging defaults
		Debug::$dbHost  = DBHOST;
		Debug::$dbName  = DBNAME;
		Debug::$dbUser  = DBUSER;
		Debug::$dbPass  = DBPASS;
		Debug::$dbTable = "log";
	}

	public function __destruct() {
		if (self::$enable_send) {
			self::sendEmail();
		}
	}

	/**
	* Return instance or create initial instance
	*
	* @return object
	*/
	private static function initialize() {
		if (!self::$instance) {
			self::$instance = new Debug();
		}
		return self::$instance;
	}

	/**
	 * sets the Exception Handler
	 */
	public function setHandler() {
		self::initialize();
		set_exception_handler(array('Debug', 'exceptionHandler'));
	}

	/**
	 * Exception Handler
	 */
	public function exceptionHandler($e) {
		if (headers_sent()) {
			echo "Error: " , $e->getMessage(), "\n";
		} else {
			$_SESSION['exception'] = $e->getMessage() . "<br />" . str_replace("\n", "<br />", $e->getTraceAsString()) . "";
			header('Location: /error/');
		}
	}

	/**
	 * Writes to a database log table.  The table should be called log, or set
	 * $this->dbTable. It should contain 2 columns: 'datetime, message'
	 *
	 * @param  mixed $var
	 *   Anything you want to log
	 * @return bool
	 */
	private function log($var) {
		if (!self::$enable_log) {
			return false;
		}

		if (is_array($var) || is_object($var)) {
			$buffer = print_r($var, true);
		} else {
			$buffer = $var;
		}

		try {
			// MySQL with PDO_MYSQL
			if (!is_object(self::$dbPDO)) {
				self::$dbPDO = new PDO("mysql:host=" . self::$dbHost . ";dbname=" . self::$dbName, self::$dbUser, self::$dbPass);
				self::$dbPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
			$query = self::$dbPDO->prepare("INSERT INTO " . self::$dbTable . " (datetime, message) values (:datetime, :message)");
			$query->bindParam(':datetime', date(DATE_ATOM, mktime(date("G"), date("i"), 0, date("m"), date("d"), date("Y"))));
			$query->bindParam(':message', $buffer);
			$query->execute();
		} catch(PDOException $e) {
			self::show($e->getMessage());
			return false;
		}
		return true;
	}

	/**
	 * Displays debug information on screen
	 *
	 * @param mixed $var
	 *   Anything you want to log
	 * @return bool
	 */
	private static function show($var) {
		if (!self::$enable_show) {
			return false;
		}
		echo "<pre>";
		if (is_array($var) || is_object($var)) {
			print_r($var);
		} else {
			echo $var;
		}
		echo "</pre>";
		return true;
	}

	/**
	 * Iterates a buffer that gets emailed on __destruct()
	 *
	 * @param mixed $var
	 *   Anything you want to log
	 * @return bool
	 * @private
	 */
	private static function send($var) {
		if (!self::$enable_send) {
			return false;
		}

		if (is_array($var) || is_object($var)) {
			self::$emailBuffer[] = print_r($var, true);
		} else {
			self::$emailBuffer[] = $var;
		}

		return true;
	}

	/**
	 * Determines what output methods are enabled and passes $var to it.
	 *
	 * @param  mixed $var Anything you want to log
	 * @return void
	 */
	public static function out($var) {
		$result = true;

		self::initialize();

		if (self::$enable_send) {
			$result = $result && self::send($var);
		}
		if (self::$enable_log) {
			$result = $result && self::log($var);
		}
		if (self::$enable_show) {
			$result = $result && self::show($var);
		}

		if (!self::$enable_show &&
			!self::$enable_send &&
			!self::$enable_log) {
			$result = false;
		}

		return $result;
	}

	/**
	 * Sets all the enabled flags to false
	 *
	 * @return void
	 */
	public static function disable() {
		self::$enable_send = false;
		self::$enable_log = false;
		self::$enable_show = false;
	}

	/**
	 * Sends the email.
	 *
	 * @return bool true if sent successfully
	 */
	public static function sendEmail() {
		if (!self::$enable_send) {
			return false;
		}

		$headers = array();
		$headers[] = 'From: ' . self::$emailFrom;
		$headers[] = "MIME-Version: 1.0";
		$headers[] = 'Content-type: text/html; charset=iso-8859-1';
		if (self::$emailCC != '') {
			$headers[] = 'Cc: ' . self::$emailCC;
		}
		if (self::$emailBCC != '') {
			$headers[] = 'Bcc: ' . self::$emailBCC;
		}
		return mail(self::$emailTo, self::$emailSubject, implode("<br />\n", self::$emailBuffer), implode("\n", $headers));
	}
}