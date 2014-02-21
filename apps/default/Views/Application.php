<?php
/* Application View PHP */

class Views_Application {
	
	public function login() {
		new Views_LoginScreen();
	}

	public function run() {
		new Views_ApplicationContent();
	}
}
?>