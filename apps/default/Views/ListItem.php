<?php
/* ListItem View PHP */
namespace Views;

class ListItem {
	public function __construct($type, $id, $label, $secondary=null, $imgurl=null, $mediakind=null) {
		$this->type = $type;
		$this->imgurl = $imgurl;
		$this->id = $id;
		$this->label = substr($label, 0, (strlen($label) > 40) ? 40 : strlen($label));
		$this->label .= (strlen($label) > 40) ? '...' : '';
		$this->secondary = $secondary;
		$this->mediakind = $mediakind;

		if(isset($imgurl) && strlen($imgurl)>0):
		$li_style = "background-image:url('".$this->imgurl."');background-size: 20px;background-repeat:no-repeat;";
		else:
		$li_style = null;
		endif;

		?>

		<!-- list item -->

		<li data-type="<?=$this->type?>" data-id="<?=$this->id?>" <?= ($this->mediakind!=null) ? 'data-mediakind="'.$this->mediakind.'"' : ''; ?>>

			<!-- label -->
			<h6 style="<?=$li_style?>" class="icon-<?=$this->type?>"><?=$this->label?></h6>

			<!-- pubstate -->
			<?php if($this->secondary != null): ?>
				<div class="pubstate state-<?=$secondary?>">
				</div>
			<?php endif; ?>

		</li>
		<!-- end list item -->

		<?php
	}
}
?>
