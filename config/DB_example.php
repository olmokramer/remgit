<?php
/* DB Config File */
namespace Config;

class DB {
	private static $instance = null;

	public static function getInstance() {
		if(self::$instance == null) {
			try {
				define("DBHOST", "<DBHOST>");
				define("DBNAME", "<DBNAME>");
				define("DBUSER", "<DBUSER>");
				define("DBPASS", "<DBPASS>");
				self::$instance = new \PDO('mysql:host='.DBHOST.';dbname='.DBNAME.';charset=UTF8', DBUSER, DBPASS);
			} catch(PDOException $e) {
				throw $e;
			}
		}
		return self::$instance;
	}
}
?>
