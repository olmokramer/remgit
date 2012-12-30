/* Logger Controller JS */
var Logger = {
	
	/*
	function login
	*/
	
	login: function() {
		$.ajax({
			type: "POST",
			url: "AjaxListener.php",
			data: "action=login&"+$('#loginForm').serialize(),
			success: function(data){
				if(data == 'true') {
					document.location.reload();
				}
				else {
					$('#loginWindow').effect("shake", { times:3 }, 75);
					//console.log(data);
				}
			}
		})
	},
	
	/*
	function logout
	*/
	
	logout: function() {
		$('body').load(
			'AjaxListener.php',
			{action: 'logout'},
			function() {
				document.location.reload();
			}
		);
	}
}