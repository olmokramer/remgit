<?php
/* DB Config File */
namespace Config;

class DB {
private static $instance = null;

	private function __construct(){}

	public static function getInstance() {
		if(self::$instance == null) {
			try {
				define("DBHOST", "mysql3.greenhost.nl");
				define("DBNAME", "luukkramer_nl_rearend2");
				define("DBUSER", "luukkramer2");
				define("DBPASS", "MorirWov9");
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