<?php
/* Main Controller PHP */

//include Views
require('Views/ApplicationHeader.php');
require('Views/ApplicationContent.php');
require('Views/Application.php');
require('Views/LoginScreen.php');

class Controllers_Main {
	public function __construct() {	
		new Views_ApplicationHeader();
		$application = new Views_Application;
		
		if((!isset($_SESSION['app']['loginStatus'])) || ($_SESSION['app']['loginStatus'] != 1)) {
			$application->login();
		}
		else {
			$application->run();
		}
	}
}
?>