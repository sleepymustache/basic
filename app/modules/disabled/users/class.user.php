<?php
namespace Authentication;

/**
 * Example of using the User module
 *
 * @code
 *	if (class_exists('User')) {
 *		$u = new User();
 *
 *		// check if a user is logged in
 *		if (!$u->isLoggedIn()) {
 *			header('location: /user/login/');
 *		}
 *
 *		// check if a user is an admin
 *		if (!$u->isAdmin()) {
 *			echo "You must be an Administrator to see this page.";
 *			die();
 *		}
 *	}
 *	@endcode
 */
	error_reporting(E_ALL);
	ini_set("display_errors", 1);

	require_once(DIRBASE . '/modules/enabled/db/class.record.php');

	class User extends \DB\Record {
		public $table = 'users';
		public $metadata;
		private $role;

		private $salt = 'o_PXO=1-BCTkq>|>*}KmkM8CA-!x|J6Y/UyDJC#ph(*A6me>CJ1Uu8E7gye|Vek[';

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

		public function getPermission($key) {
			return $this->role->getPermission($key);
		}

		public function getUserData($key) {
			if (isset($this->columns[$key])) {
				return $this->columns[$key];
			} else {
				if (isset($this->metadata[$key])) {
					return $this->metadata[$key];
				}
			}
		}

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

		public function saltPassword($pass) {
			return crypt($pass, $this->salt);
		}

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

		public function isLoaded() {
			if (isset($this->columns['id'])) {
				return true;
			}
		}

		public function isLoggedIn() {
			if (isset($_SESSION['uid'])) {
				return $_SESSION['uid'];
			} else {
				return false;
			}
		}

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

		public function getRole() {
			return $this->role->columns['name'];
		}
	}

	class UserMeta extends \DB\Record {
		public $table = "usermeta";
	}

	class Role extends \DB\Record{
		public $table = 'roles';
		private $_permissions;

		public function getPermission($key) {
			if (isset($this->_permissions[$key])) {
				return $this->_permissions[$key];
			} else {
				throw new \Exception("{$key} permission does not exist.");
			}
		}

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

	class Permission extends \DB\Record{
		public $table = 'permissions';
	}

/*	// Create a new user if it doesn't exist
	try {
		$u = new Authentication\User();

		$u->columns['email'] = 'hi.i.am.jaime@gmail.com';
		$u->columns['password'] = $u->saltPassword('test');
		$u->columns['role_id'] = 1;
		$u->save();
	} catch (Exception $e) {
		echo $e->getMessage() . "<br />\n";
	}

	$u = new User();

	try {
		$u->authenticate('hi.i.am.jaime@gmail.com', 'test');

		if ($u->getPermission('add-user')) {
			echo "You may add users.";
		} else {
			echo "You may not add users.";
		}
	} catch (Exception $e) {
		echo $e->getMessage() . "<br />\n";
	}*/