<?php
namespace Module\Authentication;

require_once(DIRBASE . '/modules/enabled/db/class.record.php');

/**
 * Example of using the User module
 *
 * <code>
 * 	if (class_exists('User')) {
 * 		// Try to login
 * 		try {
 * 			$u = new \Module\Authentication\User();
 *
 * 			// check if the admin user exists
 * 			$uid = $u->authenticate('developer@envivent.com', 'test');
 *
 * 			echo "Authentication successful. User ID is ", $uid;
 * 		} catch (\Exception $e) {
 * 			// Admin uses doesn't exist. Let's create it.
 * 			$u->columns['role_id'] = 1;
 * 			$u->columns['email'] = 'developer@envivent.com';
 * 			$u->columns['password'] = $u->saltPassword('test');
 * 			$u->save();
 * 		}
 * 	}
 * </code>
 */
class User extends \Module\DB\Record {
	/**
	 * The name of the users table
	 * @var string
	 */
	public $table = 'users';

	/**
	 * The meta data for the table, e.g. fields and field data
	 * @var mixed
	 */
	public $metadata;

	/**
	 * The users Role ID
	 * @var int
	 */
	private $role;


	/**
	 * The salt for password encryption
	 * @var string
	 */
	private $salt = 'o_PXO=1-BCTkq>|>*}KmkM8CA-!x|J6Y/UyDJC#ph(*A6me>CJ1Uu8E7gye|Vek[';

	/**
	 * Authenticates a user
	 * @param  string $email The username
	 * @param  string $pass  The password
	 * @return int           The user_id
	 */
	public function authenticate($email, $pass) {
		$pass = crypt($pass, $this->salt);

		$query = $this->db->prepare("SELECT * FROM users WHERE email=:email AND password=:pass");
		$query->execute(array(
			':email' => $email,
			':pass' => $pass
		));
		$query->setFetchMode(\PDO::FETCH_ASSOC);

		if ($row = $query->fetch()) {
			$this->load($row['id']);
			return $row['id'];
		} else {
			throw new \Exception("Invalid user or password.");
		}
	}

	/**
	 * Gets the permission for their role
	 * @param  string $key What role do you want to query?
	 * @return string the value associated with the permission e.g. True, False, etc.
	 */
	public function getPermission($key) {
		return $this->role->getPermission($key);
	}

	/**
	 * Gets the data associated with the user
	 * @param  string $key The data you want to lookup
	 * @return mixed       The data associated with the key
	 */
	public function getUserData($key) {
		if (isset($this->columns[$key])) {
			return $this->columns[$key];
		} else {
			if (isset($this->metadata[$key])) {
				return $this->metadata[$key];
			}
		}
	}

	/**
	 * Loads the user
	 * @param  integer $id The user ID
	 * @return void
	 */
	public function load($id=0) {
		parent::load($id);

		// Load usermeta data
		$query = $this->db->query("SELECT * FROM usermeta where user_id={$this->columns['id']}");
		$query->setFetchMode(\PDO::FETCH_ASSOC);
		$query->execute();

		$metadata = $query->fetchAll();

		foreach ($metadata as $data) {
			$temp = new UserMeta($data['id']);
			$this->metadata[$temp->columns['key']] = $temp->columns['value'];
		}

		// Load the Role
		$this->role = new Role($this->columns['role_id']);
	}

	/**
	 * Sets user data by a key
	 * @param string $key   The key to store the data into
	 * @param mixed $value  The data to store at the key
	 */
	public function setUserData($key, $value) {
		if (isset($this->columns[$key])) {
			$this->columns[$key] = $value;
		} else {
			if (isset($this->metadata[$key])) {
				$this->metadata[$key] = $value;
			} else {
				$temp = new UserMeta();
				$temp->columns['key'] = $key;
				$temp->columns['value'] = $value;
				$temp->save();
			}
		}
	}

	/**
	 * Salts the password
	 * @param  string $pass The password
	 * @return string       The encrypted password
	 */
	public function saltPassword($pass) {
		return crypt($pass, $this->salt);
	}

	/**
	 * Saves the user
	 * @return void
	 */
	public function save() {
		$query = $this->db->prepare("SELECT * from users where email=:email");
		$query->setFetchMode(\PDO::FETCH_ASSOC);
		$query->execute(array(':email' => $this->columns['email']));

		if ($query->fetch()) {
			throw new \Exception('The user already exists.');
		} else {
			parent::save();
		}
	}

	/**
	 * Checks if a user is loaded
	 * @return boolean True, if loaded
	 */
	public function isLoaded() {
		if (isset($this->columns['id'])) {
			return true;
		}
	}

	/**
	 * Checks if a user is logged in
	 * @return boolean True, if logged in
	 */
	public function isLoggedIn() {
		if (isset($_SESSION['uid'])) {
			return $_SESSION['uid'];
		} else {
			return false;
		}
	}

	/**
	 * Checks if a user is an admin (role_id == 1)
	 * @return boolean True, if role_id ==  1
	 */
	public function isAdmin() {
		if (!$uid = $this->isLoggedIn()) {
			return false;
		}

		if (!$this->isLoaded()) {
			$this->load($uid);
		}

		if ($this->columns['role_id'] == 1) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Gets the name of the role of the user
	 * @return string The role
	 */
	public function getRole() {
		return $this->role->columns['name'];
	}
}

/**
 * The users meta data
 */
class UserMeta extends \Module\DB\Record {
	public $table = "usermeta";
}

/**
 * The users role
 */
class Role extends \Module\DB\Record {
	public $table = 'roles';
	private $_permissions;

	/**
	 * The the permission for the role
	 * @param  string $key The permission key
	 * @return mixed       The role data, e.g. True/False, etc.
	 */
	public function getPermission($key) {
		if (isset($this->_permissions[$key])) {
			return $this->_permissions[$key];
		} else {
			throw new \Exception("{$key} permission does not exist.");
		}
	}

	/**
	 * Loads the role
	 * @param  integer $id role_id
	 * @return void
	 */
	public function load($id=0) {
		parent::load($id);

		// Load permissions
		$query = $this->db->query("SELECT * FROM permissions where role_id={$this->columns['id']}");
		$query->setFetchMode(\PDO::FETCH_ASSOC);
		$query->execute();

		$permissions = $query->fetchAll();

		foreach ($permissions as $p) {
			$temp = new Permission($p['id']);
			$this->_permissions[$temp->columns['key']] = $temp->columns['value'];
		}
	}

	/**
	 * Sets a permission for this role
	 * @param string $key   The permission key
	 * @param void
	 */
	public function setPermission($key, $value) {
		if (isset($this->_permissions[$key])) {
			$this->_permissions[$key] = $value;
		} else {
			$temp = new Permission();
			$temp->columns['key'] = $key;
			$temp->columns['value'] = $value;
			$temp->save();
		}
	}
}

/**
 * A row in the permission table
 */
class Permission extends \Module\DB\Record {
	public $table = 'permissions';
}