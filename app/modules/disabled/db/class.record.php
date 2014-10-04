<?php
namespace Module\DB;

require_once(dirname(__FILE__) . '/class.db.php');
require_once(dirname(__FILE__) . '/../../../include/class.debug.php');
/**
 * Base class that represents a record in a table.
 *
 * Extend this class and add a property $table that is equal to the table name
 * in your database. The columns will be loaded automatically and basic load(),
 * save(), and delete() methods are immediately available. Update the data by
 * changing the value of the columns like this:
 *
 * ### Usage
 *
 * <code>
 *   // load a record with id= 5 from a table called 'user'
 *   class user extends record {
 *     public $table = 'user';
 *   }
 *
 *   $u = new user();
 *   $u->load(5);
 *   $u->columns['first_name'] = 'Joe';
 * </code>
 *
 * You can then save the new information by calling the save method:
 *
 * <code>
 *   $u->save();
 * </code>
 *
 * You can also show a nice form to edit or add new records like this
 *
 * <code>
 *   $u->form(array(
 *     'first_name' => 'First Name: ',
 *     'last_name' => 'Last Name: ',
 *     'phone' => '(800) 555-5555'
 *   ));
 * </code>
 *
 * ### Changelog
 *
 * ## Version 1.2
 * * Added namespacing
 *
 * ## Version 1.1
 * * Added the date section to the documentation
 *
 * @section dependencies Dependencies
 * * class.hooks.php
 * * class.db.php
 *
 * @date June 16, 2014
 * @author Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @version  1.1
 * @license  http://opensource.org/licenses/MIT
 **/

class Record {
	/**
	 * PDO The PDO object
	 */
	protected $db;

	/**
	 * string The name of the table
	 */
	protected $table;

	/**
	 * string The primary key of the table
	 */
	protected $primaryKey = 'id';

	/**
	 * array The data of the record get loaded here
	 */
	public $columns;

	/**
	 * stdObject The record meta data gets loaded here
	 */
	public $meta;

	/**
	 * Initializes db and gets column information.
	 *
	 * @param string $id The id to load automatically
	 */
	public function __construct($id=0) {
		$this->db = DB::getInstance();

		$select = $this->db->query("SELECT * FROM `{$this->table}` LIMIT 1");

		for ($i = 0; $i <= $select->columnCount(); $i ++) {
			$meta = $select->getColumnMeta($i);
			if (isset($meta['name'])) {
				$this->meta[$meta['name']] = $meta;
			}
		}

		if ($id) {
			$this->load($id);
		}
	}

	/**
	 * Loads a record as an object.
	 *
	 * @param  integer $id id of a record to load
	 * @return bool True if loaded correctly
	 */
	public function load($id=0) {
		if ($this->table == '') {
			throw new \Exception('$this->table is not set.');
		}

		$query = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE {$this->primaryKey}=:{$this->primaryKey}");
		$query->execute(array(":{$this->primaryKey}" => $id));
		$query->setFetchMode(\PDO::FETCH_ASSOC);

		if ($this->columns = $query->fetch()) {
			return true;
		} else {
			throw new \Exception("{$this->table}: Record does not exist.");
		}
	}

	/**
	 * Saves the record to the database.
	 *
	 * @return  mixed Returns the Primary Key
	 */
	public function save() {
		$sql = "";
		$col = array();

		// If id is set, then update. Else, insert
		if ($new = !isset($this->columns[$this->primaryKey])) {
			$sql = "INSERT INTO {$this->table} SET";
		} else {
			$sql = "UPDATE {$this->table} SET";
		}

		// set up insert statement, don't update id
		foreach ($this->columns as $key => $value) {
			if ($key !== $this->primaryKey) {
				$sql .= " {$key}=:{$key},";
				$col[":{$key}"] = $value;
			} else {
				$col[":{$key}"] = $value;
			}
		}

		// remove the trailing comma
		$sql = substr($sql, 0, -1);

		if (!$new) {
			$sql = $sql . " WHERE {$this->primaryKey}=:{$this->primaryKey}";
		}

		$result = $this->db->prepare($sql);

		// save
		\Sleepy\Hook::addAction('recordBeforeSave');
		$result->execute($col);
		\Sleepy\Hook::addAction('recordAfterSave');

		if ($new) {
			if ($result->rowCount()) {
				$this->columns[$this->primaryKey] = $this->db->lastInsertId();
				return $this->columns[$this->primaryKey];
			} else {
				throw new Exception("{$this->table}: Record was not saved.");
			}
		} else {
			return $this->columns[$this->primaryKey];
		}
	}

	/**
	 * Deletes this record from the database
	 *
	 * @return bool True if delete is successful
	 */
	public function delete() {
		\Sleepy\Hook::addAction('recordBeforeDelete');
		$query = $this->db->prepare('DELETE FROM ' . $this->table . " WHERE {$this->primaryKey}=:{$this->primaryKey}");
		//\Sleepy\Debug::out($query->debugDumpParams());
		if ($query->execute(array(":{$this->primaryKey}" => $this->columns[$this->primaryKey]))) {
			\Sleepy\Hook::addAction('recordDeleteSucessful');
			return true;
		} else {
			\Sleepy\Hook::addAction('recordDeleteError');
			throw new \Exception("{$this->table}: Record was not deleted.");
		}
	}

	/**
	 * Shows an editable form
	 *
	 * @param  Array   $fields An array of columns => labels
	 * @param  string  $legend Customize the fieldset legend
	 * @param  boolean $submit Show the submit button?
	 * @return void            returns nothing
	 */
	public function form(Array $fields, $legend='table_name', $submit=true) {
		// Legend defaults to the table name
		if ($legend == 'table_name') {
			$legend = $this->table;
		}

		// If there are no fields sent, load all fields in the table.
		if (empty($fields)) {
			foreach ($this->meta as $meta) {
				$fields[$meta['name']] = $meta['name'];
			}
		}

		?>
		<fieldset>
			<legend><?php echo $legend;?></legend>
			<ul>
			<?php
				// Display each field
				foreach ($fields as $field => $label) {
					$class = "";
					$buffer = "";
					$meta = $this->meta[$field];

					// If the field is not in the database, then add a
					// "fake" field instead
					if (!is_array($meta)) {
						$meta['name'] = $field;
						$meta['flags'] = Array();
						$meta['native_type'] = 'VAR_STRING';
						$class .= 'not-binded ';
					}

					// Add classes for field flags
					foreach ($meta['flags'] as $flag) {
						if ($flag == 'not_null') {
							$class .= 'required ';
						}

						if ($flag == 'primary_key') {
							$class .= 'primary-key ';
						}
					}

					$label = \Sleepy\Hook::addFilter('dbForm_label', $label);

					?>
					<li>
						<?php
							// fix for tinyint so it works correctly. Damn PDO!
							if (!isset($meta['native_type'])) {
								if ($meta['len'] == 1) {
									$meta['native_type'] = 'BOOL';
								}
							}

							// Is this a password field?
							if (strpos(strtolower($label), 'password') > -1) {
								$meta['native_type'] = 'PASSWORD';
							}

							// Is this an email field?
							if (strpos(strtolower($label), 'email') > -1) {
								$class = "email ";
							}

							$value = htmlspecialchars($this->columns[$field]);

							switch ($meta['native_type']) {
							case 'LONG':
							case 'FLOAT':
								$class .= "digits ";
								$inputId = "txt_" . $meta['name'];

								$buffer .= "<label class=\"{$class}\" for=\"{$inputId}\">{$label}</label>";
								$buffer .= "<input class=\"{$class}\" type=\"text\" name=\"{$inputId}\" id=\"{$inputId}\" maxlength=\"{$meta['len']}\" value=\"{$value}\" />";
								break;
							case 'STRING':
							case 'VAR_STRING':
								$class .= "string ";
								$inputId = "txt_" . $meta['name'];
								$buffer .= "<label class=\"{$class}\" for=\"{$inputId}\">{$label}</label>";
								$buffer .= "<input class=\"{$class}\" type=\"text\" name=\"{$inputId}\" id=\"{$inputId}\" maxlength=\"{$meta['len']}\" value=\"{$value}\" />";
								break;
							case 'PASSWORD':
								$class .= "password ";
								$inputId = "pwd_" . $meta['name'];
								$buffer .= "<label class=\"{$class}\" for=\"{$inputId}\">{$label}</label>";
								$buffer .= "<input class=\"{$class}\" type=\"password\" name=\"{$inputId}\" id=\"{$inputId}\" maxlength=\"{$meta['len']}\" />";
								break;
							case 'DATETIME':
								$class .= "datetime ";
								$inputId = "txt_" . $meta['name'];
								$buffer .= "<label class=\"{$class}\" for=\"{$inputId}\">{$label}</label>";
								$buffer .= "<input class=\"{$class}\" type=\"text\" name=\"{$inputId}\" id=\"{$inputId}\" maxlength=\"{$meta['len']}\" value=\"{$value}\" />";
								break;
							case 'TIMESTAMP':
								$class .= "timestamp ";
								$inputId = "txt_" . $meta['name'];
								$buffer .= "<label class=\"{$class}\" for=\"{$inputId}\">{$label}</label>";
								$buffer .= "<input class=\"{$class}\" type=\"text\" name=\"{$inputId}\" id=\"{$inputId}\" maxlength=\"{$meta['len']}\" value=\"{$value}\" />";
								break;
							case 'NEWDECIMAL':
								$class .= "decimal ";
								$inputId = "txt_" . $meta['name'];
								$buffer .= "<label class=\"{$class}\" for=\"{$inputId}\">{$label}</label>";
								$buffer .= "<input class=\"{$class}\" type=\"text\" name=\"{$inputId}\" id=\"{$inputId}\" maxlength=\"{$meta['len']}\" value=\"{$value}\" />";
								break;
							case 'BOOL':
								$class .= "bool ";
								$inputId = "chk_" . $meta['name'];
								if ($value) {
									$checked = "checked=\"checked\"";
								}
								$buffer .= "<label class=\"{$class}\" for=\"{$inputId}\">{$label}</label>";
								$buffer .= "<input class=\"{$class}\" type=\"checkbox\" name=\"{$inputId}\" id=\"{$inputId}\" value=\"1\" {$checked} />";
								break;
							case 'BLOB':
								$class .= "string ";
								$inputId = "txt_" . $meta['name'];
								$buffer .= "<label class=\"{$class}\" for=\"{$inputId}\">{$label}</label>";
								$buffer .= "<textarea class=\"{$class}\" type=\"text\" name=\"{$inputId}\" id=\"{$inputId}\" maxlength=\"{$meta['len']}\">{$value}</textarea>";
								break;
							default:
								$buffer .= "No Handler for {$meta['native_type']} defined.";
							}

							// Add Hook filters to the buffer
							foreach(Array(
								'dbForm_list',
								$this->table . "_dbForm_list"
							) as $filterName) {
								$buffer = \Sleepy\Hook::addFilter($filterName, Array(
									$buffer,
									$meta['native_type'],
									$label,
									$value,
									$class,
									$inputId
								));
							}

							echo $buffer;
						?>
					</li>
					<?php
				}
				?>
			</ul>
		</fieldset>
		<?php
		if ($submit) {
			?>
			<fieldset class="submit">
				<legend></legend>
				<ul>
					<li><input type="submit" value="Save"></li>
				</ul>
			</fieldset>
			<?php
		}
	}
}