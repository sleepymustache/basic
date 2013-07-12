<?php
/**
 * @page hooks1 Hook Class
 * Adds Hooks and Filters
 *
 * Adds Hooks and filters into your project. You attach modules to those
 * hooks and filters by adding php files into the "/modules/" director.
 *
 * @section usage Usage
 * @code
 * 	// add a hook point
 * 	$content = Hook::addFilter('update_content', $_POST['content']);
 *
 * 	// Add a module to the hook point--in /modules/<moduleName.php>
 * 	function clean_html ($html) {
 * 		$c = htmlentities(trim($html), ENT_NOQUOTES, "UTF-8", false);
 * 		return $c;
 * 	}
 *
 * 	Hook::applyFilter("update_content", "clean_html");
 * @endcode
 *
 * @section changelog Changelog
 *
 * Version 1.0
 *
 * * static class pattern fixes
 * * multiple module directories
 * * crawls subdirectories of module directories
 *
 * @author Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @version 1.0
 * @copyright  GPL 3 http://cuttingedgecode.com
 */

/**
 * This class stores the filters. it has properties to store the name of the
 * filter as well the functions that should run when the filters are stored.
 * the filters property is an array. The key is the name of the
 * function and value is the arguments. Currently we do not make any use of the
 * arguments.
 *
 * @param string $name name of the filter
 * @return object
 */
class Filter {
	// This is the name of the filter
	public $name;

	/**
	 * array a list of functions
	 */
	public $functions;

	/**
	 * Constructor
	 * @param string $name The name of the filter
	 */
	public function __construct($name) {
		$this->name = $name;
	}

	/**
	 * Adds a function to this filter
	 * @param string $function The function to call
	 * @param array $args An array of parameters
	 */
	public function add($function, $args) {
		$this->functions[$function] = $args;
	}
}

/**
 * This class is the main hook class. Call it using class::method.
 * @code
 * Hook::addFilter('fitername', $variable);
 * @endcode
 * @return void
 */
class Hook {

	/**
	 * bool Has this been initialized?
	 * @private
	 */
	private static $initialized = false;

	/**
	 * array string An array of filters
	 * @private
	 */
	private static $filters = array();

	/**
	 * array string The directories where the modules are stored
	 */
	public static $directories;

	/**
	 * Private constructor ensure there are no instances
	 * @private
	 */
	private function __construct() {}

	/**
	* Return instance or create initial instance
	*
	* @private
	* @static
	* @return object
	*/
	private static function initialize() {
		if (!self::$initialized) {
			// For teamsite, replace the path below with correct branch
			if (strlen(ROOT) > 0) {
				self::$directories[] = ROOT . "/modules/enabled";
				self::$directories[] = "/var/www/webs/bybz25/htdocs/iw-mount/default/main/MarketingSites/BSP/Xofigo/LicensedFacilitiesDB/USA/LicensedFacilitiesDB/WORKAREA/htdocs/modules";
			}

			self::$directories[] = $_SERVER['DOCUMENT_ROOT'] . "/modules/enabled/";
			self::$initialized = true;
			self::load();
		}
	}

	/**
	 * Loads all the modules
	 *
	 * @private
	 * @static
	 * @return void
	 */
	private static function load() {
		$all = "";

		// get all subdirectories
		foreach (self::$directories as $directory) {
			$add = glob($directory . '/*' , GLOB_ONLYDIR);

			if (is_array($all)) {
				$all = array_merge($all, $add);
			} else {
				$all = $add;
			}
		}

		$all = array_merge($all, self::$directories);

		// include all php files
		foreach ($all as $directory) {
			$files = glob($directory . "/*.php");

			if (is_array($files)) {
				foreach($files as $file) {
					require_once($file);
				}
			}
		}
	}

	/**
	 * Add a new filter to a filter-type hook point
	 *
	 * @param  string $name     [description]
	 * @param  string $function [description]
	 * @param  int $args     [description]
	 * @static
	 * @return void
	 */
	public static function applyFilter($name, $function, $args='1') {
		self::initialize();

		if (!isset(self::$filters[$name])) {
			self::$filters[$name] = new Filter ($name);
		}

		// add the function to the filter
		self::$filters[$name]->add($function, $args);
	}

	/**
	 * Adds a new filter-type hook point
	 *
	 * @param mixed $name  [description]
	 * @param string $value [description]
	 * @static
	 * @return void
	 */
	public static function addFilter($name, $value='') {
		self::initialize();

		if (!isset(self::$filters[$name])) {
			if (is_array($value)) {
				return $value[0];
			} else {
				return $value;
			}
		}

		foreach (self::$filters[$name]->functions as $function => $args) {
			if (is_array($value)) {
				$returned = call_user_func_array($function, $value);
			} else {
				$returned = call_user_func($function, $value);
			}
		}

		return $returned;
	}

	/**
	 * Adds a new function to a action-type hook point
	 *
	 * @param  string $name     Name of filter
	 * @param  string $function Function to call
	 * @static
	 * @return void
	 */
	public static function doAction($name, $function) {
		self::applyFilter($name, $function);
	}

	/**
	 * Adds a new action-type hook point
	 *
	 * @param string $name [description]
	 * @static
	 * @return void
	 */
	public static function addAction($name) {
		self::addFilter($name);
	}
}