<?php
namespace Sleepy;

/**
 * This class enables the pre and post-process hooks as well as some basic
 * framework functionality
 *
 * @section usage Usage
 * @code
 *   \Sleepy\SM::initialize();
 * @endcode
 *
 * @section changelog Changelog
 * ## Version 1.0
 * * Initial commit
 *
 * @date September 26, 2014
 * @author Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @version 1.0
 * @license http://opensource.org/licenses/MIT
 */
class SM {
	private static $_instance;

	public static $is_initialized = false;

	private function __clone() {}

	/**
	 * The constructor is private to ensure we only have one sleepy_preprocess
	 * and sleepy_postprocess hooks.
	 */
	private function __construct() {
		// Enable sessions
		session_start();

		require_once('class.hooks.php');
		require_once('class.template.php');

		\Sleepy\Hook::addAction('sleepy_preprocess');

		// Send the encoding ahead of time to speed up rendering
		header('Content-Type: text/html; charset=utf-8');
	}

	public function __destruct() {
		\Sleepy\Hook::addAction('sleepy_postprocess');
	}

	/**
	 * Initialized the SM class
	 */
	public static function initialize() {
		if (!self::$is_initialized) {
			self::$is_initialized = true;
			self::$_instance = new SM;
		}
	}

	/**
	 * Checks if we are in the live environment
	 * @return boolean True if we are live
	 */
	public static function isLive() {
		return (ENV == "LIVE");
	}

	/**
	 * Checks if we are in the staging environment
	 * @return boolean True if we are in the staging environment
	 */
	public static function isStage() {
		return (ENV == "STAGE");
	}

	/**
	 * Checks if we are in the development environment
	 * @return boolean True if we are in in the development environment
	 */
	public static function isDev() {
		return (ENV != "LIVE" && ENV != "STAGE");
	}
}