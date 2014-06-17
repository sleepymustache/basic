<?php
/**
 * @page db1 DB Class
 *
 * Singleton database class
 *
 * This abstract class is to ensure that only one PDO instance is used. You get
 * a instance of the database with:
 *
 * @section usage Usage
 * @code
 *   DB::$dbhost = 'localhost';
 *   DB::$dbname = 'db';
 *   DB::$dbuser = 'username';
 *   DB::$dbpass = 'itsmeopenup';
 *   $db = DB::getInstance();
 * @endcode
 *
 * @section changelog Changelog
 * ## Version 1.1
 * * Added the date section to the documentation
 *
 * @date June 16, 2014
 * @author Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @version 1.1
 * @copyright  GPL 3 http://cuttingedgecode.com
 */
abstract class DB {

	/**
	 * PDO The single instance is stored here.
	 * @private
	 */
	private static $instance = NULL;

	/**
	 * string The database host
	 */
	public static $dbhost = DBHOST;

	/**
	 * string The database name
	 */
	public static $dbname = DBNAME;

	/**
	 * string The database user
	 */
	public static $dbuser = DBUSER;

	/**
	 * string The database password
	 */
	public static $dbpass = DBPASS;

	/**
	* Sets constructor to private, prevents new instances
	*/
	private function __construct() {}

	/**
	* Sets __clone to private, prevents clones
	*/
	private function __clone(){}

	/**
	* Gets the DB instance or creates an initial connection
	*
	* @return object (PDO)
	*/
	public static function getInstance() {
		if (!self::$instance) {
			self::$instance = new PDO("mysql:host=" . self::$dbhost . ";dbname=" . self::$dbname, self::$dbuser, self::$dbpass);
			self::$instance-> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		return self::$instance;
	}
}

