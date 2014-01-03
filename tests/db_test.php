<?php
	if (file_exists('../modules/disabled/db/class.db.php')) {
		require_once('../modules/disabled/db/class.db.php');
	} else {
		require_once('../modules/enabled/db/class.db.php');
	}

	class TestOfDB extends UnitTestCase {
		// Check singleton pattern
		// Extend Record, check for load, save, delete methods
		// Check for columns as associative array
		// Check for correct meta data
		// Use alternative primary key
		// Check that form is correctly displayed
		// Check for load hook
		// Check for before/failure/success save hook
		// Check for before/failure/success delete hook
		// Check for form label filter
		// Check for form value filter
		// Check for form <li> buffer filter
		// Check form for LONG
		// Check form for FLOAT
		// Check form for STRING
		// Check form for VARSTRING
		// Check form for PASSWORD
		// Check form for DATETIME
		// Check form for TIMESTAMP
		// Check form for NEWDECIMAL
		// Check form for BLOB
		// Check filter for submit button value
		// test Create method
		// test Save method
		// test Update method
		// test delete method
	}