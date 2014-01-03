<?php
/* MediaBrowser View PHP */
namespace Views;

class MediaBrowser {
	public function __construct($media, $activeMedia, $options) {		
		?>
		
		<!-- media browser -->
		<div id="mediaBrowser">
			
			<div class="header">
			<h3>media browser</h3>
			</div>
		
			<div class="footer">
				<button id="button-addtogallery">add to gallery</button>
				<button id="button-browsercancel">cancel</button>
			</div>
		
			<div id="media-tabs">
				<?php new \Views\MediaBrowser_Media($media, $activeMedia, $options); ?>
			</div>

		</div>
		<!-- end media browser -->
		
		<?php
	}
}
?>