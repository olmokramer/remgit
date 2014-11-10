<?php
/* MediaBrowser_Media View PHP */
namespace Views;

class MediaBrowser_Media {
	public function __construct($media, $activeMedia, $options) {
		$this->media = $media;
		$this->activeMedia = $activeMedia;

		/* options */
		$num_records = count($media);
		$optionsarray = explode("&", $options);
		$options = array();

		foreach($optionsarray as $option):
		list($label, $value) = explode("=", $option);
		$options[$label] = $value;
		endforeach;

		list($limitstart, $limitend) = explode(",", $options['limit']);
		$rows_requested = $limitend;
		$max_row_num = $limitstart+$limitend;
		/* end options */

		foreach($this->media as $item):
		$imgUrl = ($item->kind != "vimeo/embedded") ? THUMBS.$item->imgUrl : $item->imgUrl;
		if(is_array($this->activeMedia)):
		$className = (!in_array($item->id, $this->activeMedia)) ? "mediaItem" : "mediaItemInactive";
		?>

		<div class="<?=$className?>" data-id="<?=$item->id?>" style="background-image:url('<?=$imgUrl?>')" data-id="<?=$item->id?>" data-cleanurl="<?=$item->imgUrl?>">
			<?php if(($item->kind == "vimeo/embedded" || $item->kind == "youtube/embedded")) : ?>
			<div class="video-play"></div>
			<?php endif; ?>
			<small><?=$item->title?></small>
		</div>

		<?php else: ?>

		<div class="<?=$className?>" data-id="<?=$item->id?>" style="background-image:url('<?=$imgUrl?>')" data-id="<?=$item->id?>" data-cleanurl="<?=$item->imgUrl?>">
			<?php if(($item->kind == "vimeo/embedded" || $item->kind == "youtube/embedded")) : ?>
			<div class="video-play"></div>
			<?php endif; ?>
			<small><?=$item->title?></small>
		</div>

		<?php endif;
		endforeach;

		if($num_records == $rows_requested): ?>
			<div class="showmore-browser" data-limitstart="<?=$limitstart+50?>" data-limitend="<?=50?>">
			show 50 more
			</div>
		<?php endif;
	}
}
?>
