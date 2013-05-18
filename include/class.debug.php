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
 * @section changelog Changelog
 * * Changed to a static class pattern
 *
 * @author Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @version 1.5
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
		$this->emailBuffer = array();
		//$this->emailBuffer[] = "Date: " . date(DATE_ATOM, mktime(date("G"), date("i"), 0, date("m"), date("d"), date("Y")));
		$this->emailBuffer[] = "Filename: " . $_SERVER["SCRIPT_FILENAME"];
		$this->emailBuffer[] = "";
		$this->emailTo = "hi.i.am.jaime@gmail.com";
		$this->emailFrom = "hi.i.am.jaime@gmail.com";
		//$this->emailSubject = date(DATE_ATOM, mktime(date("G"), date("i"), 0, date("m"), date("d"), date("Y")));
		$this->emailCC = "";
		$this->emailBCC = "";
	}

	public function __destruct() {
		if (self::$enable_send) {
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
			mail(self::$emailTo, self::$emailSubject, implode("<br />\n", self::$emailBuffer), implode("\n", $headers));
		}
	}

	/**
	* Return instance or create initial instance
	*
	* @return object
	*/
	private function initialize() {
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
			if (!is_object($this->dbPDO)) {
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
	private function show($var) {
		if (!self::$enable_show) {
			return false;
		}
		echo "<pre>\n";
		if (is_array($var) || is_object($var)) {
			print_r($var);
		} else {
			echo $var;
		}
		echo "</pre>\n";
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
	private function send($var) {
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
	public function out($var) {
		self::initialize();

		if (self::$enable_send) {
			self::send($var);
		}
		if (self::$enable_log) {
			self::log($var);
		}
		if (self::$enable_show) {
			self::show($var);
		}
	}

	/**
	 * Sets all the enabled flags to false
	 *
	 * @return void
	 */
	public function disable() {
		self::$enable_send = false;
		self::$enable_log = false;
		self::$enable_show = false;
	}
}