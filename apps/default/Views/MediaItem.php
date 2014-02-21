<?php
/* MediaItem View PHP */

class Views_MediaItem {
	public function __construct($mediaItem) {
		$this->m = $mediaItem;
		?>
		
		<!-- media item -->
		<div id="mediaitem-container">
			
			<div style="text-align:center;">
            <?php
			switch($this->m->kind):
			default:
				break;
			default:
				?>
				<img src="<?php echo UPLOADS.$this->m->imgUrl; ?>" class="mediaItemFull">
                <?php
				break;
			case "vimeo/embedded":
			case "youtube/embedded":
				echo $this->m->embedCode;
				break;
			endswitch;
			?>
			</div>
			
			<div class="properties-head">title</div><br>
				<ul class="properties-list">
				<li>
				<input id="media-title" type="text" placeholder="title" value="<?=htmlspecialchars($this->m->title)?>">
				</li>
				</ul>
			
			<div class="properties-head">caption</div><br>
				<ul class="properties-list">
				<li>
				<textarea id="media-caption" placeholder="caption"><?=$this->m->caption?></textarea>
				</li>
				</ul>
				</div>
			
		</div>
		<!-- end media item -->
		
		<?php
	}
}
?>