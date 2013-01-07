<?php
/* UploadScreen View PHP */
namespace Views;

class AddVideoStream {
	public function __construct() {
		?>
		
		<!-- add video stream screen -->
		<div id="addVideoStream">
			
			<!-- header -->
			<div class="header">
				<h3>Add Video Stream</h3>
			</div>
			
			<!-- footer -->
			<div class="footer">
				<button onclick="$('#addVideoStream').remove()">Done</button>
			</div>
			
			<!-- main div -->
			<div id="media-tabs">
			
            <p>enter vimeo user id:</p><br>
            
			<form method="POST" name="videoStream">
            	
            	<input id="streamId" type="text" name="streamId" />
            
            </form>
                
            <button onclick="Media.addVideoStream()">Add Stream</button>

			</div>
			<!-- end main div -->
			
		</div>
		<!-- end add video stream screen -->
		<?php
	}
}
?>