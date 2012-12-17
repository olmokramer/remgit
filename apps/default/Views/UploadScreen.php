<?php
/* UploadScreen View PHP */
namespace Views;

class UploadScreen {
	public function __construct() {
		?>
		
		<!-- upload screen -->
		<div id="uploadScreen">
			
			<!-- header -->
			<div class="header">
				<h3>upload media</h3>
			</div>
			
			<!-- footer -->
			<div class="footer">
				<button onclick="$('#uploadScreen').remove()">Done</button>
			</div>
			
			<!-- main div -->
			<div id="media-tabs">

			<!-- uploader -->
			<div id="html5_uploader" style="width: auto; height:120px;">
				You browser doesn't support native upload. Try Firefox 3 or Safari 4.
			</div>
		
			<script type="text/javascript">
		
			$(function() {
			
			
				// Setup html5 version
				$("#html5_uploader").pluploadQueue({
					// General settings
					runtimes : 'html5',
					url : 'plugins/plupload/upload.php',
					max_file_size : '20mb',
					chunk_size : '2mb',
					unique_names : false,
					preinit : attachCallbacks,
	
					filters : [
						{title : "Image files", extensions : "jpg,gif,png,jpeg"},
/* 						{title : "Zip files", extensions : "zip"} */
					],
			
					// Resize images on clientside if we can
					resize : {width : <?=MAX_UPLOADS_PX?>, height : <?=MAX_UPLOADS_PX?>, quality : 90}
				})
	
			});
	
	        var uploader = $('#uploader').pluploadQueue();
			
			// added redirect function after uploaded
			function attachCallbacks(uploader) {
			uploader.bind('FileUploaded', function(Up, File, Response) {
			    if( (uploader.total.uploaded + 1) == uploader.files.length)
			         {
						Media.showList('limit=0,50', Media.currentMediaKind);
						console.log(Response);
			          }
			    })
			}
			</script>


			</div>
			<!-- end main div -->
			
		</div>
		<!-- end upload screen -->
		<?php
	}
}
?>