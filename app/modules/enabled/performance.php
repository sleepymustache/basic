<?php
namespace Module\Performance;

/**
 * Starts the timer when the framework loads.
 * @return void
 * @internal
 */
function start_timer() {
	define('STARTTIME', microtime(true));
}

/**
 * When everything is done, append the time and memory usage to the file
 * @return void
 * @internal
 */
function stop_timer() {
	$stop_timer = microtime(true) - STARTTIME;
	echo "\n<!-- Generated in $stop_timer seconds using " .
		(memory_get_peak_usage() / 1024) .
		" kb memory, by sleepyMUSTACHE -->";
}

\Sleepy\Hook::doAction('sleepy_preprocess',  '\Module\Performance\start_timer');
\Sleepy\Hook::doAction('sleepy_postprocess', '\Module\Performance\stop_timer');
