<?php
/* MediaList View PHP */
namespace Views;

class MediaList {
	public function __construct($media, $options, $append=0) {
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
		
		<?php if($append == 0): ?><ul><?php endif; ?>
		
		<!-- media items -->
		<?php
		foreach($this->media as $item):
		new \Views\ListItem($type='mediaItem', $id=$item->id, $label=$item->title, null, THUMBS.$item->imgUrl);
		endforeach;
		?>
		<!-- end media items -->
				
		<?php if($append == 1): ?></ul><?php endif; ?>
		
		<?php if($num_records == $rows_requested): ?>
		<div class="showmore" data-limitstart="<?php echo $limitstart+50; ?>" data-limitend="<?php echo 50; ?>">show 50 more…</div>
		<?php endif;
	}
}
?>