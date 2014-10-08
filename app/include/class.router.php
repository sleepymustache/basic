<?php
namespace Sleepy;

/**
 * Implements routing functionality based on a URI.
 *
 * ### Usage
 *
 * <code>
 *   \Sleepy\Router::route('/user/{{ id }}/*', function ($route) {
 *       echo "The route uses the pattern: ", $route->pattern;
 *       echo "Route was matched using method: ", $route->method;
 *       echo "The wildcard matched: ", $route->splat;
 *       echo "Showing user ", $route->params['id'], "</br>";
 *   });
 * </code>
 *
 * ### Changelog
 *
 * ## Version 1.0
 * * With unit tests in place, we're ready to call this 1.0
 *
 * ## Version 0.4
 * * Simplified interface, thanks @cameff
 *
 * @todo  Document the class and add it to homepage
 *
 * @date October 07, 2014
 * @author Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @version 1.0
 * @license  http://opensource.org/licenses/MIT
 */
class Router {
	/**
	 * An array of routes
	 * @var array
	 * @private
	 */
	private static $_routes = array();

	/**
	 * Has a route been matched?
	 * @var boolean True, if we matched a route
	 */
	public static $routeFound = false;

	/**
	 * The delimiter for the route pattern
	 * @var string
	 */
	public static $delimiter = "/";

	/**
	 * If true, parse the querystring instead of the path
	 * @var boolean
	 */
	public static $querystring = false;

	/**
	 * An array of parameters, either from the path or querystring
	 * @var array
	 */
	public static $parameters = array();

	/**
	 * Gets an array from a string based on Router::$delimeter
	 * @param  string $string a string to explode()
	 * @return array          an exploded string
	 */
	public static function getArray($string) {
		if (substr($string, strlen($string) - 1, 1) == self::$delimiter) {
			$string = substr($string, 0, strlen($string) - 1);
		}

		if (substr($string, 0, 1) != self::$delimiter) {
			$string = self::$delimiter . $string;
		}

		return explode(self::$delimiter, $string);;
	}

	/**
	 * Creates a new route
	 * @param  string   $pattern A pattern to match
	 * @param  function $func    A callback function
	 * @return object            \Sleepy\Route()
	 */
	public static function route($pattern, $func) {
		if (is_array($pattern)) {
			$route = new _Route(md5($pattern[0]));
		} else {
			$route = new _Route(md5($pattern));
		}

		array_push(self::$_routes, $route);
		$route->add($pattern, $func);
		return $route;
	}

	/**
	 * Starts parsing the Router::routes
	 * @return boolean true if a route was matched
	 */
	public static function start($currentPath='') {
		self::$routeFound = false;

		if ($currentPath == '') {
			$currentPath = $_SERVER['REQUEST_URI'];
		}

		if (self::$querystring) {
			$currentPath = str_replace("/?q=", "", $currentPath);
		}

		// Get all parameters
		self::$parameters = self::getArray($currentPath);

		foreach (self::$_routes as $route) {
			$route->method = $_SERVER['REQUEST_METHOD'];
			$route->execute();
		}

		return self::$routeFound;
	}

	public static function reset() {
		self::$_routes = array();
	}
}

/**
 * Private class used by the Router class
 *
 * ### Usage
 *
 * This class is private and should not be instatiated outside of the Router
 * class
 *
 * ### Changelog
 *
 * ## Version 0.4
 * * Bug fixes
 *
 * @todo  Write tests for the class
 *
 * @date September 31, 2014
 * @author Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @version 0.4
 * @license  http://opensource.org/licenses/MIT
 * @internal
 */
class _Route {
	/**
	 * A list of (pattern ,callbacks)
	 * @var array
	 */
	private $_functions = array();

	/**
	 * The name of the route, MD5 hash of pattern by default
	 * @var string
	 */
	public $name;

	/**
	 * A hash of matched placeholder
	 * @var array
	 */
	public $params;

	/**
	 * The method that was matched
	 * @var string
	 */
	public $method;

	/**
	 * Returns the string matched with a wildcard
	 * @var string
	 */
	public $splat;

	/**
	 * Cleans the handlebars from placeholders
	 * @param  string $placeholder The full placeholder
	 * @return string              The stripped placeholder
	 */
	private function _cleanPlaceholder($placeholder) {
		$key = str_replace("{{", "", $placeholder);
		$key = str_replace("}}", "", $key);
		return trim($key);
	}

	/**
	 * Is this $string a placeholder
	 * @param  [type]  $string The possible placeholder
	 * @return boolean         True, if it is a placeholder
	 */
	private function _isPlaceholder($string) {
		return substr($string, 0,2) == "{{" && substr($string, strlen($string) - 2, 2) == "}}";
	}

	/**
	 * Run all the filters for a placeholder
	 * @param  string $key    The placeholder
	 * @param  string $string The string to parse
	 * @return string         The parsed string
	 */
	private function _runFilters($key, $string) {
		if (class_exists("\Sleepy\Hook")) {
			$string = \Sleepy\Hook::addFilter("route_parameters", $string);
			$string = \Sleepy\Hook::addFilter("route_parameter_" . $key, $string);
			$string = \Sleepy\Hook::addFilter("route_" . $this->name . "_parameters", $string);
			$string = \Sleepy\Hook::addFilter("route_" . $this->name . "_parameter_" . $key, $string);
		}

		return $string;
	}

	/**
	 * Does the pattern have a wildcard?
	 * @return boolean True, if there is a wildcard
	 */
	private function _hasWildcard($pattern) {
		if (strlen($pattern) < 1) {
			return false;
		} else {
			return strpos($pattern, "*");
		}
	}

	/**
	 * Store the variables found in the route
	 * @param  string $key   The placeholder
	 * @param  string $value The value
	 * @return boolean       True, if we succeeded
	 */
	private function _storeVariable($key, $value) {
		if ($value == "") {
			return false;
		}

		$key = $this->_cleanPlaceholder($key);
		$value = $this->_runFilters($key, $value);

		// Check for multiple variables, they should match.
		if (isset($this->params[$key])) {
			if ($value != $this->params[$key]) {
				return false;
			}
		}

		$this->params[$key] = $value;
		return true;
	}

		/**
	 * Creates a new route
	 * @param string $name Optional.
	 */
	public function __construct($name='') {
		$this->name = $name;
	}

	/**
	 * if URL matches pattern do $func
	 * @param  string   $pattern a pattern with {{ placeholders }}
	 * @param  function $func    Executes if pattern matches; func($variables)
	 */
	public function add($pattern, $func) {
		// If we have an array of patterns match those individually
		if (is_array($pattern)) {
			foreach ($pattern as $p) {
				$this->add($p, $func);
			}
		} else {
			array_push($this->_functions, array($pattern, $func));
		}

		return $this;
	}

	/**
	 * Executes the call back functions
	 */
	public function execute() {
		$noMatch = false;

		if (count($this->_functions) < 1) {
			return;
		}

		// Shift a function off the queue
		$r = array_shift($this->_functions);
		$rawPattern = $r[0];
		$func = $r[1];

		if (Router::$routeFound) {
			$noMatch = true;
		} else {
			// Get array from string
			$pattern = Router::getArray($rawPattern);

			// If they are obviously different then stop the route
			if (count(Router::$parameters) == count($pattern) || $this->_hasWildcard($rawPattern)) {
				// Check for matches, stop if we have a problem
				foreach ($pattern as $idx => $value) {
					// Store the variable
					if ($this->_isPlaceholder($value)) {
						if (!$this->_storeVariable($value, Router::$parameters[$idx])) {
							$noMatch = true;
							break;
						}

						continue;
					}

					// If we are at a wildcard, we have a match!
					if ($value == "*") {
						$this->splat = implode(Router::$delimiter, array_slice(Router::$parameters, $idx));
						break;
					}

					// If something doesn't match then stop the route.
					if (!isset(Router::$parameters[$idx]) || $value != Router::$parameters[$idx]) {
						$noMatch = true;
						break;
					}
				}
			} else {
				$noMatch = true;
			}
		}

		if ($noMatch) {
			if (class_exists("\Sleepy\Hook")) {
				\Sleepy\Hook::addAction("route_failed");
				\Sleepy\Hook::addAction("route_failed_" . $this->name);
			}
		} else {
			// Call route_start actions
			if (class_exists("\Sleepy\Hook")) {
				\Sleepy\Hook::addAction("route_start");
				\Sleepy\Hook::addAction("route_start_" . $this->name);
			}

			$this->pattern = $rawPattern;
			Router::$routeFound = true;
			$func($this);

			// Call route_end actions
			if (class_exists("\Sleepy\Hook")) {
				\Sleepy\Hook::addAction("route_end");
				\Sleepy\Hook::addAction("route_end_" . $this->name);
			}
		}

		$this->execute();
	}
}