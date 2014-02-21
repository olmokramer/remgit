<?php
/* Gallery Controller PHP */

include(DAOS_ROOT.'Media.php');

class Controllers_Gallery {
	
	public function updateOrder($galleryId, $items) {
		$this->dao = new DAOS_Media;
		$doc = $this->dao->updateOrder($galleryId, $items);
	}
}
?>