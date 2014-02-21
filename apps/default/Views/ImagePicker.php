<?php
/* ImagePicker View PHP */

class Views_ImagePicker {
	public function __construct($media, $activeMedia, $options) {		
		?>
		
		<!-- image picker -->
		<div id="imagePicker">
		
			<div id="imagepicker-media">
				<?php new Views_MediaBrowser_Media($media, $activeMedia, $options); ?>
			</div>

		</div>
		<!-- end image picker -->
		
		<?php
	}
}
?>