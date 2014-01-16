<?php
/* MediaMenu View PHP */
namespace Views;

class MediaMenu {
	public function __construct($batches) {
		?>

		<!-- media menu -->
		<ul>
			<?php new \Views\ListItem($type='library', null, 'All Media', null, null, 'all'); ?>
			<?php foreach($batches as $batch): ?>
			<?php new \Views\ListItem($type='image', null, 'uploaded ' . date("d/m/Y H:i", $batch->created), null, null, $batch->created); ?>
			<?php endforeach; ?>
			<?php /* new \Views\ListItem($type='library', null, 'Photos', null, null, 'photos'); ?>
			<?php new \Views\ListItem($type='library', null, 'Videos', null, null, 'videos'); */ ?>

		</ul>
		<!-- end media menu -->

		<?php
	}
}
?>