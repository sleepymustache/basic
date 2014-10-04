<?php
require_once('class.fsdb.php');

/**
 * Tests the \Module\FSDB\Connection() class
 *
 * @internal
 * @date August 13, 2014
 * @author Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @version 1.8
 * @license  http://opensource.org/licenses/MIT
 */
class TestOfFSDB extends UnitTestCase {
	function setup() {
		$this->db = new \Module\FSDB\Connection();
	}

	function tearDown() {
		unlink(getcwd() . '/data/fruit.json');
		rmdir(getcwd() . '/data/');
	}

	// Create new object when no db exists
	function testCreateNewFDBDTable() {
		$apple = new stdClass();
		$apple->name = "Apple";
		$apple->color = "Red";
		$apple->texture = "Crispy";
		$apple->price = 0.50;

		$this->db->insert('fruit', $apple);

		$results = $this->db->delete('fruit', 'color', 'Red');
		$this->assertTrue($results > 0);
		unset($this->db);
	}

	// create new object when db exists
	function testCreateNewDocument() {
		// Create a banana document
		$banana = new stdClass();
		$banana->name = "Banana";
		$banana->color = "Yellow";
		$banana->texture = "Mushy";
		$banana->price = 0.29;

		// Create a pear document
		$pear = new stdClass();
		$pear->name = "pear";
		$pear->color = "Yellow";
		$pear->texture = "Mushy";
		$pear->price = 1.29;

		// Add both to the DB
		$this->db->insert('fruit', $banana);
		$this->db->insert('fruit', $pear);

		// get a list of fruits
		$results = $this->db->select('fruit', 'color', 'Yellow');
		$this->assertTrue(is_array($results));
		$this->assertEqual($results[0]->name, "Banana");

		// Delete all yellow fruits
		$results = $this->db->delete('fruit', 'color', 'Yellow');
		$this->assertTrue($results > 1);
		unset($this->db);
	}
}