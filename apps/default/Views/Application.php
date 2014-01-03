<?php
/* Application View PHP */
namespace Views;

class Application {
	
	public function login() {
		new \Views\LoginScreen();
	}

	public function run() {
		new \Views\ApplicationContent();
	}
}
?>