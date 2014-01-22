<?php
/* MediaMenu View PHP */
namespace Views;

class MediaMenu {
<<<<<<< HEAD
	public function __construct($batches) {
=======
	private $batches;
	public function __construct($batches) {
		$this->batches = $batches;
>>>>>>> refs/heads/dev
		?>

		<!-- media menu -->
		<ul>
			<?php new \Views\ListItem($type='library', null, 'All Media', null, null, 'all'); ?>
<<<<<<< HEAD
			<?php foreach($batches as $batch): ?>
			<?php new \Views\ListItem($type='image', null, 'uploaded ' . date("d/m/Y H:i", $batch->created), null, null, $batch->created); ?>
			<?php endforeach; ?>
			<?php /* new \Views\ListItem($type='library', null, 'Photos', null, null, 'photos'); ?>
			<?php new \Views\ListItem($type='library', null, 'Videos', null, null, 'videos'); */ ?>
=======
			<?php  new \Views\ListItem($type='library', null, 'Photos', null, null, 'photos'); ?>
			<?php new \Views\ListItem($type='library', null, 'Videos', null, null, 'videos');  ?>
			<?php foreach($batches as $batch): ?>
			<?php new \Views\ListItem($type='batch', $batch->created, date("d/m/Y H:i", $batch->created), null, null, 'batch');  ?>
			<?php endforeach; ?>
>>>>>>> refs/heads/dev

		</ul>
		<!-- end media menu -->

		<?php
	}
}
?>