<?php
/* GalleryItem View PHP */
namespace Views;

class GalleryItem {
	public function __construct($item) {
		$imgUrl = ($item->kind != "vimeo/embedded" && $item->kind != "youtube/embedded") ? THUMBS.$item->imgUrl : $item->imgUrl;
		?>
		
		<!-- gallery item -->
		<div class="item" data-id="<?php echo $item->id; ?>">
			<div class="remove">-</div>
			<img src="<?=$imgUrl?>">
		</div>
		<!-- end gallery item -->
		
		<?php
	}
}
?>