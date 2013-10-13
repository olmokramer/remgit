<?php
/* DB Config File */
namespace Config;

class DB {
private static $instance = null;

	private function __construct(){}

	public static function getInstance() {
		if(self::$instance == null) {
			try {
				define("DBHOST", "localhost:3306");
				define("DBNAME", "rearend_manager");
				define("DBUSER", "root");
				define("DBPASS", "");
				self::$instance = new \PDO('mysql:host='.DBHOST.';dbname='.DBNAME.';charset=UTF8', DBUSER, DBPASS);
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
