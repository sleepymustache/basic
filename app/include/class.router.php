<?php
namespace Sleepy;

/**
 * @page router1 Router Class
 * Class for basic routing functions.
 *
 * @section usage Usage
 * @code
 *   \Sleepy\Router::route('/user/{{ id }}', function ($route) {
 *       echo "Showing user ", $route->params['id'], "</br>";
 *   });
 * @endcode
 *
 * @section changelog Changelog
 * ## Version 0.4
 * * Simplified interface, thanks @cameff
 *
 * @todo  Document the class and add it to homepage
 * @todo  Write tests for the class if it's going to make it into core
 *
 * @date September 31, 2014
 * @author Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @version 0.4
 * @license  MIT
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
	 * Have we matched a route?
	 * @return boolean True, if we matched a route
	 */
	public static function hasRouted() {
		return self::$routeFound;
	}

	/**
	 * Get an array from a string based on Router::$delimeter
	 * @param  string $string a string to explode()
	 * @return array          an exploded string
	 */
	public static function getArray($string) {
		if (substr($string, strlen($string) - 1, 1) == self::$delimiter) {
			$string = substr($string, 0, strlen($string) - 1);
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
		$route = new Route(md5($pattern));
		array_push(self::$_routes, $route);
		$route->add($pattern, $func);
		return $route;
	}

	/**
	 * Starts parsing the Router::routes
	 * @return boolean true if a route was matched
	 */
	public static function start() {
		$currentPath = $_SERVER['REQUEST_URI'];

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
}

class Route {
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
		$filtered = \Sleepy\Hook::addFilter("route_parameters", $string);
		$filtered = \Sleepy\Hook::addFilter("route_parameter_" . $key, $filtered);
		$filtered = \Sleepy\Hook::addFilter("route_" . $this->name . "_parameters", $filtered);
		$filtered = \Sleepy\Hook::addFilter("route_" . $this->name . "_parameter_" . $key, $filtered);
		return $filtered;
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
	public function execute($depth=0) {
		$noMatch = false;

		if (count($this->_functions) < 1) {
			return;
		}

		// Shift a function off the queue
		$r = array_shift($this->_functions);
		$pattern = $r[0];
		$func = $r[1];

		// If we have already routed, then set the flag
		if (Router::hasRouted()) {
			$noMatch = true;
		} else {
			// Get array from string
			$pattern = Router::getArray($pattern);

			// If they are obviously different then stop the route
			if (count(Router::$parameters) == count($pattern) || $this->_hasWildcard($r[0])) {
				// Check for matches, stop if we have a problem
				foreach ($pattern as $idx => $value) {
					if ($this->_isPlaceholder($value)) {
						// If there is a placeholder, but no value. Wrong route.
						if (!array_key_exists($idx, Router::$parameters)) {
							$noMatch = true;
							break;
						}

						$key = $this->_cleanPlaceholder($value);
						$this->params[$key] = $this->_runFilters($key, Router::$parameters[$idx]);
						continue;
					}

					// If we are at a wildcard, we have a match!
					if ($value == "*") {
						$this->splat = implode(Router::$delimiter, array_slice(Router::$parameters, $idx));
						break;
					}

					// Make sure there are enough parameters when there is a wildcard.
					if (!array_key_exists($idx, Router::$parameters)) {
						$noMatch = true;
						break;
					}

					// If something doesn't match then stop the route.
					if ($value != Router::$parameters[$idx]) {
						$noMatch = true;
						break;
					}
				}
			} else {
				$noMatch = true;
			}
		}

		if ($noMatch) {
			\Sleepy\Hook::addAction("route_failed");
			\Sleepy\Hook::addAction("route_failed_" . $this->name);
		} else {
			// Call route_start actions
			\Sleepy\Hook::addAction("route_start");
			\Sleepy\Hook::addAction("route_start_" . $this->name);

			$func($this);
			Router::$routeFound = true;

			// Call route_end actions
			\Sleepy\Hook::addAction("route_end");
			\Sleepy\Hook::addAction("route_end_" . $this->name);
		}

		$this->execute();
	}
}