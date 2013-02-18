<?php
/* Gallery Controller PHP */
namespace Controllers;

include(DAOS_ROOT.'Media.php');

class Gallery {
	
	public function updateOrder($galleryId, $items) {
		$this->dao = new \DAOS\Media;
		$doc = $this->dao->updateOrder($galleryId, $items);
	}
}
?>