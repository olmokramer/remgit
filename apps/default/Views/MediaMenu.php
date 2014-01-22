<?php
/* MediaMenu View PHP */
namespace Views;

class MediaMenu {
	private $batches;
	public function __construct($batches) {
		$this->batches = $batches;
		?>

		<!-- media menu -->
		<ul>
			<?php new \Views\ListItem($type='library', null, 'All Media', null, null, 'all'); ?>
			<?php  new \Views\ListItem($type='library', null, 'Photos', null, null, 'photos'); ?>
			<?php new \Views\ListItem($type='library', null, 'Videos', null, null, 'videos');  ?>
			<?php foreach($batches as $batch): ?>
			<?php new \Views\ListItem($type='batch', $batch->created, date("d/m/Y H:i", $batch->created), null, null, 'batch');  ?>
			<?php endforeach; ?>

		</ul>
		<!-- end media menu -->

		<?php
	}
}
?>