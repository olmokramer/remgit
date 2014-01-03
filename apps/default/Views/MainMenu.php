<?php
/* MainMenu View PHP */
namespace Views;

class MainMenu {
	public function __construct($menuItems, $categories) {
		$this->items = $menuItems;
		$this->cats = $categories;
		?>
		<ul>
		
			<!-- 	items -->
			<?php
			foreach($this->items as $item):
			new \Views\ListItem($type='folder', $id=$item->id, $label=$item->label);
			endforeach;
			?>
					
			<!-- 	categories -->
			<?php
			foreach($this->cats as $catid => $label):
			new \Views\ListItem($type='cat', $id=$catid, $label=$label);
			endforeach;
			?>
		
		</ul>
		<?php
	}
}
?>