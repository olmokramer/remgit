<?php
/* Logger Controller PHP */
namespace Controllers;

include(DAOS_ROOT.'User.php');
include(MODELS_ROOT.'User.php');

class Logger {
		private $valid_users;

		public function login($vars) {

			$input_user = trim(strip_tags($vars['username']));
			$input_password = trim(strip_tags($vars['password']));
			$input_sharedkey = SHARED_KEY;
			$valid_user = $this->validateUser($input_user, $this->valid_users);
			if($valid_user) {
				$valid_credentials = $this->validatePasswordByUsername($input_password, $valid_user, $input_sharedkey);
			}

			$_SESSION['app']['loginStatus'] = ($valid_credentials == 'true') ? 1 : 0;
			echo $valid_credentials;
		}

		public function logout() {
			session_destroy();
		}
	private function findAllUsers() {
		$userDAO = \DAOS\User::getInstance();
		$users = $userDAO->findAllUsers();

		$valid_users = array();
		foreach($users as $valid_user) {
			$valid_users[$valid_user->getUsername()] = $valid_user->getPassword();
		}
		$this->valid_users = $valid_users;
		return $valid_users;
	}

	private function findPasswordByUsername($username) {
		$users = $this->valid_users;
		$password = $users[$username];
		return $password;
	}

	private function validateUser($username) {
		$valid_users =  $this->findAllUsers();
		$valid_user = (array_key_exists($username, $valid_users)) ? $username : false;
		return $valid_user;
	}

	private function validatePasswordByUsername($input_password, $valid_user, $input_sharedkey) {
		$valid_password = $this->findPasswordByUsername($valid_user);
		$encrypted_password = md5($valid_user . ':' . $input_sharedkey . ':' .$input_password);
		$valid_credentials = ($valid_password == $encrypted_password) ? 'true' : 'false';
		return $valid_credentials;
	}

}
?>
