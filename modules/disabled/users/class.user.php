<?php
	require_once('modules/enabled/db/class.record.php');

	class User extends Record {
		public $table = 'users';
		public $metadata;
		private $role;

		private $salt = 'o_PXO=1-BCTkq>|>*}KmkM8CA-!x|J6Y/UyDJC#ph(*A6me>CJ1Uu8E7gye|Vek[';

		public function authenticate($email, $pass) {
			$pass = crypt($pass, $this->salt);

			$query = $this->db->prepare("SELECT * FROM users WHERE email=:email");
			$query->execute(array(':email' => $email));
			$query->setFetchMode(PDO::FETCH_ASSOC);

			if ($row = $query->fetch()) {
				$this->load($row['id']);
			} else {
				throw new Exception("{$this->table}: Record does not exist.");
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
			$query->setFetchMode(PDO::FETCH_ASSOC);
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
	}

	class UserMeta extends Record {
		public $table = "usermeta";
	}

	class Role extends Record{
		public $table = 'roles';
		private $_permissions;

		public function getPermission($key) {
			if (isset($this->_permissions[$key])) {
				return $this->_permissions[$key];
			} else {
				throw new Exception("{$key} permission does not exist.");
			}
		}

		public function load($id=0) {
			parent::load($id);

			// Load permissions
			$query = $this->db->query("SELECT * FROM permissions where role_id={$this->columns['id']}");
			$query->setFetchMode(PDO::FETCH_ASSOC);
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

	class Permission extends Record{
		public $table = 'permissions';
	}

	$u = new User();
	$u->authenticate('hi.i.am.jaime@gmail.com', 'test');
	Debug::out($u);
	if ($u->getPermission('add-user')) {
		echo "You may add users.";
	} else {
		echo "You may not add users.";
	}