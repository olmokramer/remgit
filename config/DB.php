<?php
/* DB Config File */
namespace Config;

class DB {
private static $instance = null;

	private function __construct(){}

	public static function getInstance() {
		if(self::$instance == null) {
			try {
				define("DBHOST", "localhost");
				define("DBNAME", "abramdeswaan");
				define("DBUSER", "root");
				define("DBPASS", "sahag88sql");
				self::$instance = new \PDO('mysql:host='.DBHOST.';dbname='.DBNAME.';charset=UTF-8', DBUSER, DBPASS);
			} 
			catch(PDOException $e) {
				// Handle this properly
				throw $e;
			}
		}
		return self::$instance;
	}
}
?>