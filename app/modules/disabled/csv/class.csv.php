<?php
namespace CSV;

/**
 * @page csv1 CSV Class
 *
 * Creates a Comma Separated Value file.
 *
 * @section usage Usage
 * @code
 *   // loads a existing CSV file
 *   $c = new \CSV\Document('presidents.csv');
 *
 *   $c->add(array(
 *     'George',
 *     'Washington'
 *   ));
 *
 *   $c->save();
 * @endcode
 *
 * @section changelog Changelog
 * ##Version 1.8
 * * Added namespacing
 * * Renamed remove method to delete
 * * Added a search method
 * ##Version 1.6
 * * Updated documentation
 * ##Version 1.5
 * * check if we can remove the header before doing it... teamsite issue.
 * * suppress flock warnings... teamsite issue
 *
 * @date August 13, 2014
 * @author Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @version 1.8
 * @license  MIT
 */
class Document {
	/**
	 * string The filename of the CSV file
	 */
	public $filename = '';


	/**
	 * string Define what character to use as a delimiter
	 */
	public $delimiter = ',';


	/**
	 * mixed The data gets stored here while "in utero"
	 */
	public $data;

	/**
	 * Constructor
	 *
	 * @param string $filename
	 *   (optional) if set, loads the file
	 */
	public function __construct($filename='') {
		if ($filename != '') {
			$this->load($filename);
		}
	}

	/**
	 * Loads an existing CSV file into $this->data
	 *
	 * @param string $filename
	 *   (optional) Name of file. If it is not set, then it uses
	 *   $this->filename instead.
	 * @return bool
	 *   Returns true if successful.
	 */
	public function load($filename='') {
		if ($filename != '') {
			$this->filename = $filename;
		} else {
			if ($this->filename == '') {
				throw new \Exception('CSV::load - Cannot save without a filename.');
			}
		}

		$this->data = array();

		// If the file does not exist, create it.
		if (!file_exists($this->filename)) {
			file_put_contents($this->filename, '');
		}

		if (($handle = @fopen($this->filename, "r+")) !== FALSE) {
			flock($handle, LOCK_EX);
			while (($data = fgetcsv($handle, 0, $this->delimiter)) !== FALSE) {
				$this->data[] = $data;
				$num = count($data);
			}
			flock($handle, LOCK_UN);
			fclose($handle);
			$numberOfFields = count($data);
		} else {
			throw new \Exception('CSV::load - Cannot open file for writing');
		}

		return true;
	}

	/**
	 * Saves the CSV file.
	 *
	 * @param string $filename
	 *   (optional) Name of file. If it is not set, then it uses
	 *   $this->filename instead.
	 * @return bool
	 *   Returns true if successful.
	 */
	public function save($filename='') {
		if ($filename != '') {
			$this->filename = $filename;
		} else {
			if ($this->filename == '') {
				throw new \Exception('CSV::save - Cannot save without a filename.');
			}
		}

		// make the file if it doesn't exist.
		if (!file_exists($this->filename)) {
			file_put_contents($this->filename, '');
		}

		if ($handle = @fopen($this->filename, 'r+')) {
			@flock($handle, LOCK_EX);
			foreach ($this->data as $row) {
				fputcsv($handle, $row);
			}
			@flock($handle, LOCK_UN);
			fclose($handle);
		} else {
			throw new \Exception("CSV::save - Cannot open file ({$filename}) for writing");
		}

		return true;
	}

	/**
	 * Adds a new line to the CSV file
	 *
	 * @param mixed $array
	 *   An array of values.
	 * @return bool
	 *   Returns true if successful.
	 */
	public function add($array) {
		if (!is_array($array)) {
			throw new \Exception('CSV::add - Parameter must be an array.');
		}

		if (count($array) == count($array, COUNT_RECURSIVE)) {
			$this->data[] = $array;
		} else {
			foreach ($array as $record) {
				$this->data[] = $record;
			}
		}

		return true;
	}

	/**
	 * Deletes a line from the CSV file
	 *
	 * @param int $id
	 *   The array key to remove from $this->data.
	 * @return bool
	 *   Returns true if successful.
	 */
	public function delete($id) {
		if (isset($this->data[$id])) {
			unset($this->data[$id]);
			return true;
		} else {
			throw new \Exception('CSV::delete - Row does not exist. Data not removed.');
		}
	}

	/**
	 * Search through the CSV for a string and return an associative array of
	 * array_index => array()
	 *
	 * @param  string  $string        The string to search for
	 * @param  boolean $partial       If true, return partial matches
	 * @param  boolean $caseSensitive If true, search is case sensitive
	 * @return array                  An array of matching data
	 */
	public function search($string, $partial=false, $caseSensitive=true) {
		$matches = array();

		if (!$caseSensitive) {
			$string = strtolower($value);
		}

		foreach ($this->data as $id => $row) {
			foreach ($row as $value) {
				if (!$caseSensitive) {
					$value = strtolower($value);
				}

				$match = strpos($value, $string);

				if (!$partial) {
					if ($match === 0) {
						$matches[$id] = $row;
					}
				} else {
					if ($match !== false) {
						$matches[$id] = $row;
					}
				}
			}
		}

		return $matches;
	}

	/**
	 * Instead of saving the file, it outputs to default output with headers.
	 */
	public function show() {
		ob_end_clean();

		// Name the file
		$filename = date('YmdHis') . ".csv";

		// Write the CSV headers
		if (function_exists('header_remove')) {
			header_remove();
		}

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"$filename\";" );
		header("Content-Transfer-Encoding: binary");

		// output of csv to default output (screen) instead of a file
		$this->filename = "php://output";

		$this->save();
	}
}