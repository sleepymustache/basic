<?php
	/**
	 * This page collects all the test suites from sleepyMUSTACHE and runs them
	 * all. As a new feature, you can add a file with the pattern
	 * [name]_test.php in any module, and it will be added automagically to the
	 * testing suite.
	 */

	if (!include_once('../include/global.php')) {
		header('Location: ../setup/');
		die();
	}

	require_once(dirname(__FILE__) . '/simpletest/autorun.php');

	class AllTests extends TestSuite {
		function __construct() {
			//parent::__construct();
			$this->TestSuite('All Tests');

			$directories = array(
				'./',
				'../include',
				'../modules/disabled',
				'../modules/enabled'
			);

			$all = "";

			// get all subdirectories
			foreach ($directories as $directory) {
				$add = glob($directory . '/*' , GLOB_ONLYDIR);

				if (is_array($all)) {
					$all = array_merge($all, $add);
				} else {
					$all = $add;
				}
			}

			$all = array_merge($all, $directories);

			foreach ($all as $directory) {
				$this->collect(
					$directory,
					new SimplePatternCollector('/_test.php/i')
				);
			}
		}
	}