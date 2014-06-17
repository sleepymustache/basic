<?php
/**
 * @page perf1 Performance Class
 *
 * A Performance class that aids in timing scripts for optimization.
 *
 * @section usage Usage
 * @code
 *   Performance::start('forloop');
 *
 *   for ($i = 0; $i < 100; $i++) {
 *     echo $i * $i;
 *   }
 *
 *   echo "Execution time: " . Performance::stop('forloop');
 * @endcode
 *
 * @section changelog Changelog
 *   ## Version 1.1
 *   * Added the date section to documentation
 *
 * @date June 16, 2014
 * @author Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @version 1.1
 * @copyright  GPL 3 http://cuttingedgecode.com
 */
class Performance {
	private static $timers;

	public static function start($label) {
		Performance::$timers[$label] = microtime();
	}

	public static function stop($label) {
		return microtime() - Performance::$timers[$label];
	}
}
