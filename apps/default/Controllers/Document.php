<?php
/* Document Controller PHP */

//include backend files
include(DAOS_ROOT.'Document.php');
include(DAOS_ROOT.'Category.php');
include(DAOS_ROOT.'Media.php');
include(DAOS_ROOT.'Template.php');
include(MODELS_ROOT.'Document.php');
include(MODELS_ROOT.'Gallery.php');

//include views
include(VIEWS_ROOT.'DocumentList.php');
include(VIEWS_ROOT.'ListItem.php');
include(VIEWS_ROOT.'Document.php');
include(VIEWS_ROOT.'GalleryItem.php');

class Controllers_Document {
	
	//functions
	
	public function showList($type, $value, $options=null) {
		$this->dao = new DAOS_Document;
		switch($type) {
		case 'folder':
			$docs = $this->dao->getDocuments($value, $options);
			break;
		case 'cat':
			$docs = $this->dao->findByCategory($value, $options);
			break;
		}
		new Views_DocumentList($docs);
	}
		
	public function show($id) {
		$this->dao = new DAOS_Document;
		$this->dao2 = new DAOS_Category;
		$doc = $this->dao->getDocument($id);
		$categories = $this->dao2->findByMenuItemsId($doc->menuItemsId);
		new \Views\Document($doc, $categories);
	}
	
	public function create($menuItemsId) {
		$this->dao = new DAOS_Document;
		$id = $this->dao->create($menuItemsId);
		echo $id;
	}
	
	public function update($id, $fields, $categories, $pubdate, $pubstate, $coverImage) {
		$this->dao = new DAOS_Document;
		$this->dao2 = new DAOS_Category;
		$doc = $this->parseVarsToDoc($id, $fields, $pubdate, $pubstate, $coverImage);
		$this->dao->update($doc);
		$this->dao2->updateDocCategories($id, $categories);
	}
	
	public function updateOrder($menuItemsId, $docIds) {
		$this->dao = new DAOS_Document;
		$doc = $this->dao->updateOrder($menuItemsId, $docIds);
	}
	
	public function delete($id) {
		$this->dao = new DAOS_Document;
		$this->dao->delete($id);
	}

	public function removeMediaFromGallery($galleryId, $mediaIds) {
		$this->dao = new DAOS_Media;
		foreach($mediaIds as $mediaId) {
			$this->dao->removeMediaFromGallery($galleryId, $mediaId);
		}
	}
	
	public function addMediaToGallery($galleryId, $mediaIds=null) {
		$this->dao = new DAOS_Media;
		if(is_array($mediaIds)) {
			foreach($mediaIds as $mediaId) {
				$this->dao->addMediaToGallery($galleryId, $mediaId);
				$mediaItem = $this->dao->findMediaById($mediaId);
				new \Views\GalleryItem($mediaItem);
			}
		}
	}
	
	private function parseVarsToDoc($id, $fields, $pubdate, $pubstate, $coverImage) {
		$doc = new Models_Document;
		$doc->id = $id;
		$doc->coverImage = $coverImage;
		$doc->modified = time();
		$doc->publishState = $pubstate;
		$doc->published = strtotime($pubdate);
		foreach($fields as $field) {
			switch($field['kind']) {
			case "native":
				$doc->$field['label'] = $field['value'];
				break;
			case "custom":
				$doc->customfields[] = (object) $field;
				break;
			}
		}
		return $doc;
	}
	
}
?>