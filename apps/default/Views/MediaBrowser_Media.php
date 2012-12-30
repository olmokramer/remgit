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

		<div class="<?=$className?>" data-id="<?=$item->id?>">
			<img src="<?=$imgUrl?>" data-cleanurl="<?=$item->imgUrl?>">
		</div>
		
		<?php else: ?>
		
		<div class="mediaItem" data-id="<?=$item->id?>">
			<img src="<?=$imgUrl?>" data-id="<?=$item->id?>" data-cleanurl="<?=$item->imgUrl?>">
		</div>
		
		<?php endif;
		endforeach;
		
		if($num_records == $rows_requested): ?>
			<div class="showmore-browser" data-limitstart="<?=$limitstart+50?>" data-limitend="<?=50?>">
			show 50 more...
			</div>
		<?php endif;	
	}
}
?>