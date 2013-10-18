<?php
	if (file_exists('../modules/disabled/file-system-database/class.fsdb.php')) {
		require_once('../modules/disabled/file-system-database/class.fsdb.php');
	} else {
		require_once('../modules/enabled/file-system-database/class.fsdb.php');
	}

	class TestOfFSDB extends UnitTestCase {
		// Create new object when no db exists
		// create new object when db exists
		// Load object
		// Load multiple objects
		// Update single object
		// Update multiple objects
		// delete single object
		// delete multiple object
	}