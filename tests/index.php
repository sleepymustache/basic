<?php
	define("ENV", "LIVE");
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
	
	require_once('../include/class.debug.php');
	require_once('../include/global.php');
	require_once('simpletest/autorun.php');

	class AllTests extends TestSuite {
		function __construct() {
			parent::__construct();
			$this->TestSuite('All Tests');
			$this->collect(
				dirname(__file__),
				new SimplePatternCollector('/_test.php/')
			);
		}
	}