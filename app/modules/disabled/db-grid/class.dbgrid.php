<?php
namespace Module\DB;

/**
 * Creates a grid view of a sql statement
 *
 * ### Usage
 *
 * <code>
 *   $dbg = new \Module\DB\Grid('users', 'SELECT * FROM users');
 *
 *   $dbg->exclude(array(
 *     'user_id',
 *     'password'
 *   ));
 *
 *   $dbg->mapFields(array(
 *     'name' => 'user_id'
 *   ));
 *
 *   $dbg->sortable(array(
 *     'name',
 *     'date'
 *   ));
 *
 *   $dbg->show();
 * </code>
 *
 * ### Changelog
 *
 * ## Version 1.2
 * * Added namespacing
 *
 * ## Version 1.1
 * * Added the date section to documentation
 *
 * ### Dependencies
 * * class.db.php
 * * class.hooks.php
 *
 * @date August 13, 2014
 * @author Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @version 1.2
 * @license  http://opensource.org/licenses/MIT
 */

class Grid {
	/**
	 * DB The DB object
	 */
	protected $db;

	/**
	 * string The name of our grid
	 */
	protected $name;

	/**
	 * string The SQL to run to make the grid
	 */
	protected $sql;

	/**
	 * string The column to sort by
	 */
	protected $sortBy = '';

	/**
	 * string Ascending? or Descending?
	 */
	protected $asc = true;

	/**
	 * int How many records per page?
	 */
	public $maxRecords = 10;

	/**
	 * int Which page are we on?
	 */
	public $page = 1;

	/**
	 * object The meta data for the fields
	 **/
	public $meta;

	/**
	 * array string fields that are sortable. Used when rendering column header links
	 */
	protected $canSortby;

	/**
	 * array string Which fields to exclude from the table
	 */
	protected $excluded;

	/**
	 * array associative An array of fields => id that gets passed to the hooks
	 */
	protected $map;

	/**
	 * string Querystring to pass into the sorting links in the th
	 */
	protected $querystring;

	/**
	 * Constructor
	 * @param string $name Give our DBForm a name, used for hooks and id
	 * @param string $sql  What SQL to run?
	 */
	public function __construct($name, $sql) {
		$this->db = \Module\DB\DB::getInstance();
		$this->sql = $sql;
		$this->name = $name;
	}

	/**
	 * What columns can be sorted?
	 * @param  array $array fields that are sortable, used when rendering the
	 *                      table headers
	 */
	public function sortable($array) {
		$this->canSortBy = $array;
	}

	/**
	 * Sets the Querystring to pass into the sorting links in the th
	 * @param string $query the querystring
	 */
	public function setQuerystring($query) {
		$this->querystring = $query;
	}

	/**
	 * Sorts the table data
	 * @param  string  $column The column to sort by
	 * @param  boolean $asc    Sort ascending?
	 */
	public function sort($column, $asc=true) {
		$this->sortBy = $column;
		$this->asc = $asc;
	}

	/**
	 * Which fields are excluded from the table
	 * @param  array $array a list of fields
	 */
	public function exclude($array) {
		$this->excluded = $array;
	}

	/**
	 * Checks to see if a column has been excluded
	 * @param  string  $str a column
	 * @return boolean      true if it has been excluded
	 */
	private function isExcluded($str) {
		if (!is_array($this->excluded)) {
			return false;
		}

		foreach ($this->excluded as $e) {
			if ($e == $str) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Maps IDs to fields for processing later
	 * @param  array $array  a list of fields => id that gets passed to the hooks
	 */
	public function mapFields($array) {
		$this->map = $array;
	}

	/**
	 * Returns how many pages of results we have
	 * @return int The number of pages in our resultset
	 */
	public function numberOfPages() {
		// Create SQL Statement
		$sql = $this->sql;

		$query = $this->db->prepare($sql);
		$query->execute();
		$query->setFetchMode(PDO::FETCH_OBJ);

		return ceil($query->rowCount() / $this->maxRecords);
	}

	/**
	 * Shows an editable form
	 */
	public function show() {
		// Create SQL Statement
		$sql = $this->sql;

		// Change the sort order
		if (!empty($this->sortBy)) {
			$asc = ($this->asc) ? "ASC" : "DESC";

			$sql = $sql . " ORDER BY `" . $this->sortBy . "` $asc";
		}

		// Limit records
		if ($this->maxRecords > 0) {
			$sql = $sql . " LIMIT " . ($this->page - 1) * $this->maxRecords . ", " . $this->maxRecords;
		}

		$query = $this->db->prepare($sql);
		$query->execute();
		$query->setFetchMode(\PDO::FETCH_OBJ);

		// Get column data
		for ($i = 0; $i <= $query->columnCount(); $i ++) {
			$meta = $query->getColumnMeta($i);
			if (isset($meta['name'])) {
				$this->meta[$meta['name']] = $meta;
			}
		}

		/**
		 * @ingroup hooks
		 */
		\Sleepy\Hook::addAction($this->name . "_beforeTable");
		/**
		 * @ingroup hooks
		 */
		\Sleepy\Hook::addAction("dbGrid_beforeTable");
		echo "<table id='{$this->name}_dbgrid' cellpadding='0' cellspacing='0'>\n<tr>";

		foreach ($this->meta as $meta) {
			/**
			 * @ingroup hooks
			 */
			$metaName = \Sleepy\Hook::addFilter($this->name . "_tableHeader", $meta['name']);
			/**
			 * @ingroup hooks
			 */
			$metaName = \Sleepy\Hook::addFilter("dbgrid_tableHeader", $metaName);
			if (!$this->isExcluded($meta['name'])) {
				if ($this->sortBy == $meta['name']) {
					if ($this->asc) {
						$asc = "&amp;asc=false";
						$class = "asc";
						$char = "&#x21E7;";
					} else {
						$asc = "&amp;asc=true";
						$class = "desc";
					}
				} else {
					$asc = "";
					$class = "";
				}
				echo "<th>";
				if ($this->canSortBy[$meta['name']]) {
					if (!empty($this->querystring)) {
						echo "<a class='{$class}' href='{$this->querystring}&amp;sort=" . urlencode($meta['name']) . $asc . "'>" .$metaName . "</a>";
					} else {
						echo "<a class='{$class}' href='?sort=" . urlencode($meta['name']) . $asc . "'>" .$metaName . "</a>";
					}
				} else {
					echo $metaName;
				}

				echo "</th>";
			}
		}

		echo "</tr>";

		foreach($query->fetchAll() as $row) {
			if (empty($rzebra)) {
					$rzebra = "zebra";
				} else {
					$rzebra = "";
				}
			echo "<tr class='{$rzebra}'>";
			foreach ($row as $key => $value) {
				$value = htmlentities($value);

				if (empty($czebra)) {
					$czebra = "zebra";
				} else {
					$czebra = "";
				}

				if (isset($this->map[$key])) {
					$kid = $this->map[$key];
					$id = $row->$kid;
				} else {
					$id = 0;
				}

				/**
				 * @ingroup hooks
				 */
				$value = \Sleepy\Hook::addFilter($this->name . "_tableColumn", array(
					$value,
					$key,
					$id
				));

				/**
				 * @ingroup hooks
				 */
				$value = \Sleepy\Hook::addFilter("dbgrid_tableColumn", array(
					$value,
					$key,
					$id
				));

				if (!$this->isExcluded($key)) {
					echo "<td class={$czebra}>{$value}</td>";
				}
			}
			echo "</tr>";
		}

		echo "</table>";
		\Sleepy\Hook::addAction($this->name . "_afterTable");
		\Sleepy\Hook::addAction("dbGrid_afterTable");

		if (isset($labels[$meta['name']])) {
			$label = $labels[$meta['name']];
		} else {
			$label = $meta['name'];
		}
	}
}