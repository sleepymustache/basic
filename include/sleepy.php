<?php
	// Performance benchmarking
	require_once('class.performance.php');
	Performance::start('template');

	// Enable sessions
	session_start();

	include_once('class.debug.php');
	include_once('global.php');
	include_once('class.hooks.php');
	include_once('class.template.php');

	class Sleepy {
		public function __construct() {
			Hook::addAction('sleepy_preprocess');

			// Send the encoding ahead of time to speed up rendering
			header('Content-Type: text/html; charset=utf-8');
		}

		public function __destruct() {
			Hook::addAction('sleepy_postprocess');
		}
	}

	$_sleepy = new Sleepy();