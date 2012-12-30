<?php
/* Application Header View PHP */
namespace Views;

class ApplicationHeader {
	public function __construct() {
		?>

		<!DOCTYPE html>
		<html>
		<head>
		
		<title>Rearend CMS - Website Management The Easy Way</title>
		
		<!-- meta tags -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		
		<!-- css files -->
		<link rel="stylesheet" href="css/style.css">
		<link rel="stylesheet" href="css/jquery.tagsinput.css">
		<link rel="stylesheet" href="css/jquery.cleditor.css">
		<link rel="stylesheet" href="css/jquery.gritter.css">
		<link rel="stylesheet" href="plugins/plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.css" type="text/css" media="screen" />
		
		<!-- jquery files -->
		<script src="js/jquery.js"></script>
		<script src="js/jqueryui.js"></script>

		<!-- jquery plugins -->
		<script src="js/jquery.cookie.js"></script>
		<script src="js/jquery.tagsinput.js"></script>
		<script src="js/jquery.cleditor.js"></script>
		<script src="js/jquery.gritter.js"></script>	
		<script src="js/jquery.gritter.js"></script>
		<script type="text/javascript" src="plugins/plupload/js/plupload.js"></script>
		<script type="text/javascript" src="plugins/plupload/js/plupload.html4.js"></script>
		<script type="text/javascript" src="plugins/plupload/js/plupload.html5.js"></script>
		<script type="text/javascript" src="plugins/plupload/js/jquery.plupload.queue/jquery.plupload.queue.js"></script>   
		
		<!-- javascript controller files -->
		<script src="Controllers/Main.js"></script>
		<script src="Controllers/Document.js"></script>
		<script src="Controllers/Gallery.js"></script>
		<script src="Controllers/Logger.js"></script>
		<script src="Controllers/MainMenu.js"></script>
		<script src="Controllers/Media.js"></script>
			
		</head>

		<?php
	}
}
?>