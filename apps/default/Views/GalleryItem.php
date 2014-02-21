<?php
/* GalleryItem View PHP */

class Views_GalleryItem {
	public function __construct($item) {
		$imgUrl = ($item->kind != "vimeo/embedded" && $item->kind != "youtube/embedded") ? THUMBS.$item->imgUrl : $item->imgUrl;
		?>

		<!-- gallery item -->
		<div class="item" data-id="<?php echo $item->id; ?>">
			<div class="remove">-</div>
			<div class="itemimage" style="background-image: url('<?=$imgUrl?>')"></div>
			<?php
			if($item->kind == "vimeo/embedded" || $item->kind == "youtube/embedded"):
			?>
			<div class="video-play"></div>
			<?php endif; ?>
			<br>
			<small><?=$item->title?></small>
		</div>
		<!-- end gallery item -->

		<?php
	}
}
?>