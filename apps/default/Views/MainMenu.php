<?php
/* MainMenu View PHP */

class Views_MainMenu {
	public function __construct($menuItems, $categories=array()) {
		$this->items = $menuItems;
		$this->cats = $categories;
		?>
		<ul>
		
			<!-- 	items -->
			<?php
			foreach($this->items as $item):
			new Views_ListItem($type='folder', $id=$item->id, $label=$item->label);
			endforeach;
			?>
					
			<!-- 	categories -->
			<?php
			foreach($this->cats as $catid => $label):
			new Views_ListItem($type='cat', $id=$catid, $label=$label);
			endforeach;
			?>
		
		</ul>
		<?php
	}
}
?>