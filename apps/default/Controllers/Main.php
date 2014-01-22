<?php
/* Main Controller PHP */
namespace Controllers;

//include Views
require('Views/ApplicationHeader.php');
require('Views/ApplicationContent.php');
require('Views/Application.php');
require('Views/LoginScreen.php');

class Main {
	public function __construct() {	
		new \Views\ApplicationHeader();
		$application = new \Views\Application;
		
		if((!isset($_SESSION['app']['loginStatus'])) || ($_SESSION['app']['loginStatus'] != 1)) {
			$application->login();
		}
		else {
			$application->run();
		}
	}
}
?>