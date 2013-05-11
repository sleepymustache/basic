<?php
/**
 * @page csv1 CSV Class
 *
 * Creates a Comma Separated Value file.
 *
 * @section usage Usage
 * @code
 * 	$c = new CSV();
 * 	$data = array(
 * 		'George',
 * 		'Washington'
 * 	);
 * 	$c->add($data);
 * 	$c->save('presidents.csv');
 * @endcode
 *
 * @section changelog Changelog
 * * added show() to the class, this method outputs the csv file to the
 *   browser directly, good for when you don't have write permissions.
 *
 * @author Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @version 1.2
 * @copyright  GPL 3 http://cuttingedgecode.com
 */
class CSV {
	/**
	 * string The filename of the CSV file
	 */
	public $filename = '';


	/**
	 * string Define what character to use as a delimeter
	 */
	public $delimiter = ',';


	/**
	 * mixed The data gets stored here while "in utero"
	 */
	public $data;

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
				throw new Exception('Cannot save without a filename.');
			}
		}
		if (($handle = fopen($this->filename, "r")) !== FALSE) {
			flock($handle, LOCK_EX);
			while (($data = fgetcsv($handle, 0, $this->delimiter)) !== FALSE) {
				$this->data[] = $data;
				$num = count($data);
			}
			flock($handle, LOCK_UN);
			fclose($handle);
			$numberOfFields = count($data);
		} else {
			throw new Exception('Cannot open file for writing');
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
				throw new Exception('Cannot save without a filename.');
			}
		}
		if ($handle = fopen($this->filename, 'r+')) {
			flock($handle, LOCK_EX);
			foreach ($this->data as $row) {
				fputcsv($handle, $row);
			}
			fclose($handle);
			flock($handle, LOCK_UN);
		} else {
			throw new Exception('Cannot open file for writing');
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
		$this->data[] = $array;
		return true;
	}

	/**
	 * Removes a line from the CSV file
	 *
	 * @param int $id
	 *   The array key to remove from $this->data.
	 * @return bool
	 *   Returns true if successful.
	 */
	public function remove($id) {
		if (isset($this->data[$id])) {
			unset($this->data[$id]);
			return true;
		} else {
			throw new Exception('Row does not exist. Data not removed.');
		}
	}

	/**
	 * Instead of saving the file, it outputs to default output with headers.
	 */
	public function show() {
		// Name the file
		$filename = date('YmdHis') . ".csv";

		// Write the CSV headers
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