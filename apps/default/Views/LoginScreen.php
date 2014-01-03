<?php
/* LoginScreen View PHP */
namespace Views;

class LoginScreen {
	public function __construct() {
?>
    
	    <script type="text/javascript">
	    $("body, input, textarea, button").keypress(function(e){
	        if(e.which==13) { Logger.login()};
	    });
	    </script>

		<!-- login window -->
		<div id="loginWindow">
			
			<div id="header">
				<h1>
				rearend <b>manager </b> 2
				</h1>
			</div>
		
			<div id="document">
			
				<form id="loginForm">
				<input type="text" name="username" id="username" placeholder="username"><br/>
				<input type="password" name="password" id="password" placeholder="password"><br/>
				</form>	

				<button onclick="Logger.login()" style="margin-top: 0px;">login</button>
			</div>

		<div id="login-footer">
		compatible with chrome, safari, firefox (>= 4), IE9>
		</div>
		
		</div>
		<!-- end login window -->
	
		

<?php
	}
}
?>