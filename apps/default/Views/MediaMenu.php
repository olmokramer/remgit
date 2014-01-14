<?php
/* MediaMenu View PHP */
namespace Views;

class MediaMenu {
	public function __construct() {
		?>

		<!-- media menu -->
		<ul>
			<?php new \Views\ListItem($type='library', null, 'All Media', null, null, 'all'); ?>
			<?php /* new \Views\ListItem($type='library', null, 'Photos', null, null, 'photos'); ?>
			<?php new \Views\ListItem($type='library', null, 'Videos', null, null, 'videos'); */ ?>

		</ul>
		<!-- end media menu -->

		<?php
	}
}
?>