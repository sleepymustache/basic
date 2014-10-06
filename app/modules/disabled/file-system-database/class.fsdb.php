<?php
namespace Module\FSDB;

/**
 * Implements a flat-file database that can be used when a real DB is overkill.
 *
 * A database that uses flat files for basic database functionality like select,
 * insert, update, and delete.
 *
 * ### Usage
 *
 * <code>
 *   $fruit = new stdClass();
 *   $fruit->name = "Apple";
 *   $fruit->color = "Red";
 *   $fruit->texture = "Crispy";
 *   $fruit->price = 0.50;
 *
 *   $db = new \Module\FSDB\Connection();
 *   $db->insert('fruit', $fruit);
 *   $data = $db->select('fruit', 'name', 'Banana');
 * </code>
 *
 * ### Changelog
 *
 * ## Version 1.0
 * * Added namespacing
 *
 * ## Version 0.8
 * * Added the date and changelog sections to documentation
 *
 * @todo select with =, >, <, !=, >=, <=
 *
 * @date August 13, 2014
 * @author Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @version 0.8
 * @license  http://opensource.org/licenses/MIT
 */
class Connection {
	/**
	 * string The directory where the data is stored
	 * @private
	 */
	private $directory;


	/**
	 * array Tables currently loaded in memory
	 * @private
	 */
	private $tables = array();

	/**
	 * __construct
	 *
	 * @param string $directory Directory where data is stored (optional)
	 */
	public function __construct($directory = '') {
		if ($directory == '') {
			$directory = getcwd() . "/data/";
		}
		if (is_dir($directory)) {
			$this->directory = $directory;
		} else {
			$oldumask = umask(0);
			if (@mkdir($directory, 0777)) {
				$this->directory = $directory;
			} else {
				throw new \Exception("FSDB: Cannot create data directory at:" . $directory);
			}
			umask($oldumask);
		}
	}

	/**
	 * __call
	 *
	 * Handles method calls, if the method exists in \Module\FSDB\Table, use that one.
	 *
	 * @param string $method Method called
	 * @param array $args   Arguments passed
	 * @return mixed Value.
	 */
	public function __call($method, $args) {
		if (method_exists("\Module\FSDB\_Table", $method)) {
			$tableName = $args[0];
			$filename = $args[0] . ".json";

			if (!isset($this->tables[$tableName])) {
				$this->tables[$tableName] = new _Table($this->directory . $filename);
			}

			array_shift($args);

			return call_user_func_array(array($this->tables[$tableName], $method), $args);

		} else {
			throw new \Exception("FSDB: Method does not exist: $method");
		}
	}
}

/**
 * Private class used by \Module\FSDB\Connection
 *
 * @date August 13, 2014
 * @author Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @version 0.8
 * @license  http://opensource.org/licenses/MIT
 * @internal
 */
class _Table {
	/**
	 * bool This flag gets set when the Database needs to be saved
	 * @private
	 */
	private $edited = false;


	/**
	 * bool This flag is set if the file is locked.
	 * @private
	 */
	private $locked = false;

	/**
	 * string The filename for this table
	 * @private
	 */
	private $file;


	/**
	 * int The handle for the opened file
	 * @private
	 */
	private $handle;


	/**
	 * array The rows for this table
	 * @private
	 */
	private $data = array();

	/**
	 * __construct
	 *
	 * @param mixed $file The file to open for this table.
	 */
	public function __construct($file) {
		if (!file_exists($file)) {
			$newFile = fopen($file, 'w');
			fclose($newFile);
		}

		$this->file = $file;

		$this->handle = fopen($this->file, "a+");

		if ($this->handle) {
			fseek($this->handle, 0);
			$this->getLock($file);
		} else {
			throw new \Exception('\FSDB\Table: Could not open file');
		}
	}

	/**
	 * __destruct
	 *
	 */
	public function __destruct() {
		if ($this->edited) {
			$this->save();
		}
		if ($this->locked) {
			flock($this->handle, LOCK_UN);
		}
		if ($this->handle) {
			fclose($this->handle);
		}
	}

	private function uuid($prefix = '') {
		$chars = md5(uniqid(mt_rand(), true));
		$uuid  = substr($chars,0,8) . '-';
		$uuid .= substr($chars,8,4) . '-';
		$uuid .= substr($chars,12,4) . '-';
		$uuid .= substr($chars,16,4) . '-';
		$uuid .= substr($chars,20,12);
		return $prefix . $uuid;
	}

	/**
	 * Gets a file lock for $this->handle, retries if it fails
	 *
	 * @private
	 */
	private function getLock($timeout) {
		if ($this->locked == false) {
			if (flock($this->handle, LOCK_EX)) {
				$this->locked = true;
				$this->load();
				return true;
			} else {
				if ($timeout < 10) {
					$this->getLock($timeout++);
				} else {
					throw new \Exception('\FSDB\Table: Could not lock file');
				}
			}
		}

	}

	/**
	 * Loads the table into $this->data, needs a file lock.
	 *
	 * @private
	 */
	private function load() {
		if ($this->locked == false) {
			throw new \Exception('\FSDB\Table: File in not locked yet');
		}

		$data = "";

		while (($buffer = fgets($this->handle)) !== false) {
			$data .= $buffer;
		}

		if (!feof($this->handle)) {
			throw new \Exception('\FSDB\Table: unexpected fgets() fail');
		}

		$this->data = json_decode($data, false);
	}

	/**
	 * save
	 *
	 * @private
	 * @return mixed Value.
	 */
	private function save() {
		// Clear the old file
		if ($this->handle) {
			fseek($this->handle, 0);
			ftruncate($this->handle, 0);

			// Write the new one
			if (fwrite($this->handle, json_encode($this->data))) {
				return true;
			} else {
				throw new \Exception("\FSDB\Connection: Cannot write data to:" . $this->file);
			}
		} else {
			throw new \Exception('\FSDB\Connection\: File is not opened, can not save.');
		}
	}

	/**
	 * Selects data from the table
	 *
	 * @param string $column The column to match
	 * @param mixed $search What to search for
	 * @return array An array of rows in the form of objects.
	 */
	public function select($column, $search = 0) {
		$results = array();

		if ($column == "*") {
			return $this->data;
		}

		if (count($this->data) == 0) {
			return array();
		}

		foreach($this->data as $row) {
			if ($row->$column == $search) {
				$results[] = $row;
			}
		}

		return $results;
	}

	/**
	 * Selects data from the table within a range
	 *
	 * @param string $column The column to match
	 * @param int $lower The lower range
	 * @param int $upper The upper range
	 * @return array An array of rows in the form of objects.
	 */
	public function selectRange($column, $lower, $upper) {
		$results = array();

		foreach($this->data as $row) {
			if ($row->$column > $lower && $row->column < $upper) {
				$results[] = $row;
			}
		}

		return $results;
	}

	/**
	 * Updates columns in the table.
	 *
	 * Currently, it overwrites the whole row instead of merging the row.
	 *
	 * @param string $column The column to match
	 * @param mixed $search What to search for
	 * @param object $data An object containing the row data.
	 * @return int How many rows were updated?
	 */
	public function update($column, $search = 0, $data = array()) {
		$updated = 0;

		foreach($this->data as $key => $value) {
			if ($this->data[$key]->$column == $search) {
				if (is_object($data)) {
					$this->data[$key] = (object) array_merge((array) $this->data[$key], (array) $data);
				} else {
					$this->data[$key]->$column = $data;
				}
				$updated++;
			}
		}

		if ($updated) {
			$this->edited = true;
		}

		return $updated;
	}

	/**
	 * Inserts a new row into the table
	 *
	 * @param object $data An object containing the row data.
	 * @return bool Was the operation successful?
	 */
	public function insert($data) {
		$data->id = $this->uuid();
		$this->data[] = $data;
		$this->edited = true;
		return true;
	}

	/**
	 * Deletes rows from the table
	 *
	 * @param string $column The column to match
	 * @param mixed $search What to search for
	 * @return int How many rows were deleted?
	 */
	public function delete($column, $search = 0) {
		$updated = 0;

		foreach($this->data as $key => $value) {
			if ($value->$column == $search) {
				unset($this->data[$key]);
				$updated++;
			}
		}

		if ($updated > 0) {
			$this->edited = true;
		}

		return $updated;
	}
}