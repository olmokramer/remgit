<?php
/* MediaList View PHP */

class Views_MediaList {
	public function __construct($media, $options, $kind, $append=0) {
	    $uuid = uniqid();
		$this->media = $media;

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
		?>


		<!-- media items -->
		<?php
		switch($kind):
			case "all":
			default:
				foreach($this->media as $item):
					new Views_ListItem($type='mediaItem', $id=$item->id, $label=$item->title, null, ($item->kind != "vimeo/embedded" && $item->kind != "youtube/embedded") ? THUMBS.$item->imgUrl : $item->imgUrl);
				endforeach;
			break;
			case "videos":
				foreach($this->media as $item):
					new Views_ListItem($type='mediaItem', $id=$item->id, $label=$item->title, null, $item->imgUrl);
				endforeach;
			break;
			case "photos":
				foreach($this->media as $item):
					new Views_ListItem($type='mediaItem', $id=$item->id, $label=$item->title, null, THUMBS.$item->imgUrl);
				endforeach;
			break;
		endswitch;

		?>
		<!-- end media items -->


		<?php if(count($this->media) >= $rows_requested): ?>
		<div class="showmore" data-limitstart="<?php echo $limitstart+50; ?>" data-limitend="<?php echo 50; ?>">show <?=$limitstart+50;?> to <?=$limitstart+100;?></div>
		<?php endif;
	}
}
?>
