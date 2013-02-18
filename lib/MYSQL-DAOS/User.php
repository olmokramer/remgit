<?php
/* User DAO PHP */
namespace DAOS;

class User {
	private static $instance = null;
	private function __construct(){}
	
	public static function getInstance() {
		if(self::$instance == null) {
			self::$instance = new \DAOS\User();
		}
		return self::$instance;
	}
	
	public function findAllUsers() {
		$usersResult = $this->selectAllUsers();
		$users = $this->parseUsersResultToUsers($usersResult);
		return $users;
	}
	
	private function selectAllUsers() {
		$db = \Config\DB::getInstance();
		$sth = $db->prepare("SELECT * FROM users");
		$sth->execute();
		$result = $sth->fetchAll();
		return $result;	
	}
	
	private function parseUsersResultToUsers($usersResult) {
		$users = array();
		foreach($usersResult as $userResult) {
			$users[] = $this->parseUserResultToUser($userResult);
		}
		return $users;
	}
	
	private function parseUserResultToUser($userResult) {
		$user = new \Models\User;
		$user->setId($userResult['id']);
		$user->setUsername($userResult['username']);
		$user->setPassword($userResult['digesta1']);
		return $user;
	}
}
?>