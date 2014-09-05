<?php
require_once('class.db.php');
require_once('class.record.php');

class Log extends \DB\Record {
	public $table = 'log';
}

class TestOfDB extends UnitTestCase {
	function setup() {
		$this->log = new Log();
	}

	// Make sure DB is setup
	function testDBsetup() {

	}

	// Check singleton pattern
	function testSingleton() {
		// Assert that the class is abstract
		$abstractClass = new ReflectionClass('\DB\DB');
		$this->assertTrue($abstractClass->isAbstract());
	}

	// Extend \DB\Record, check for load, save, delete methods
	function testRecord() {
		$this->assertTrue(method_exists($this->log, 'load'));
		$this->assertTrue(method_exists($this->log, 'save'));
		$this->assertTrue(method_exists($this->log, 'delete'));
		$this->assertTrue(method_exists($this->log, 'form'));
	}

	function testCRUD() {
		// Create
		$this->log->columns['message'] = "Testing, testing, 123.";
		$id = $this->log->save();
		unset($this->log);

		// Read
		$this->log = new Log($id);
		$this->assertEqual($this->log->columns['id'], $id);

		// Update
		$this->log->columns['message'] = "Testing";
		$this->log->save();
		unset($this->log);

		// Read
		$this->log = new Log($id);
		$this->assertEqual($this->log->columns['message'], "Testing");
		unset($this->log);

		// Delete
		$this->log = new Log($id);
		$this->assertTrue($this->log->delete());
	}

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