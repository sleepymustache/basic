<?php
	define("ENV", "LIVE");

	require_once('../include/class.debug.php');
	require_once('../include/global.php');
	require_once('simpletest/autorun.php');

	class AllTests extends TestSuite {
		function __construct() {
			parent::__construct();
			$this->TestSuite('All Tests');
			$this->collect(
				dirname(__file_),
				new SimplePatternCollector('/_test.php/')
			);
		}
	}