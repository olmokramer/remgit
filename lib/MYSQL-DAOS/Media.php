<?php
/* Media DAO PHP */
namespace DAOS;

class Media {

	public function findAllMedia($options=null) {
		$result = $this->selectAllMedia($options);
		$media = $this->parseResultToMedia($result);
		return $media;
	}
	
	public function findOrphans($options=null) {
		$result = $this->selectOrphans();
		$media = $this->parseResultToMedia($result);
		return $media;
	}
	
	public function findMostRecent($options=null) {
		$result = $this->selectMostRecent($options);
		$media = $this->parseResultToMedia($result);
		return $media;
	}
	
	public function findMediaById($id) {
		$mediaItem = $this->selectMediaById($id);
		return $mediaItem;
	}
	
	public function findGalleriesByDocId($docId) {
		$result = $this->selectGalleriesByDocId($docId);
		$galleries = $this->parseResultToGalleries($result);
		return $galleries;
	}
	
	public function findMediaByGalleryId($galleryId) {
		$result = $this->selectMediaByGalleryId($galleryId);
		$media = $this->parseResultToMedia($result);
		return $media;	
	}
	
	public function findNewVimeoItems($galleryId) {
		$result = $this->selectVimeoItems($galleryId);
		$resultAPI = $this->selectVimeoItemsAPI($galleryId);
		$newItems = array_diff($resultAPI, $result);
		return $newItems;
	}
	
	public function create($fileName, $kind="image/jpeg") {
		$pdo = \Config\DB::getInstance();
		$sth = $pdo->prepare("INSERT INTO media(kind, imgUrl, title, created) VALUES(:kind, :imgUrl, :title, UNIX_TIMESTAMP())");
		$sth->bindParam(":imgUrl", $fileName);
		$sth->bindParam(":title", $fileName);
		$sth->bindParam(":kind", $kind);
		$sth->execute();
		return true;
	}
	
	public function update($id, $vars) {
		$pdo = \Config\DB::getInstance();
		$sth = $pdo->prepare("UPDATE media SET title = :title, caption = :caption WHERE id = :id LIMIT 1");
		$sth->bindParam(":title", $vars['title']);
		$sth->bindParam(":caption", $vars['caption']);
		$sth->bindParam(":id", $id);
		$sth->execute();
		return true;
	}
	
	public function delete($id) {
		$item = $this->findMediaById($id);
		
		$pdo = \Config\DB::getInstance();
		$sth = $pdo->prepare("DELETE FROM media WHERE id = :id");
		$sth->bindParam(":id", $id);
		
		unlink(UPLOADS_PATH.$item->imgUrl);
		unlink(THUMBS_PATH.$item->imgUrl);

		$sth2 = $pdo->prepare("DELETE FROM galleries_media WHERE media_id = :id");
		$sth2->bindParam(":id", $id);
		
		$sth->execute();
		$sth2->execute();
		return true;
	}
	
	public function removeMediaFromGallery($galleryId, $mediaId) {
		$pdo = \Config\DB::getInstance();
		$sth = $pdo->prepare("DELETE FROM galleries_media WHERE galleries_id = :galleries_id AND media_id = :media_id LIMIT 1");
		$sth->bindParam(":galleries_id", $galleryId);
		$sth->bindParam(":media_id", $mediaId);
		$sth->execute();		
	}
	
	public function addMediaToGallery($galleryId, $mediaId) {
		$pdo = \Config\DB::getInstance();
		$sth = $pdo->prepare("INSERT INTO galleries_media(galleries_id, media_id) VALUES(:galleries_id, :media_id)");
		$sth->bindParam(":galleries_id", $galleryId);
		$sth->bindParam(":media_id", $mediaId);
		$sth->execute();
	}
	
	public function updateOrder($id, $items) {
		$pdo = \Config\DB::getInstance();
		$key = 0;
		foreach($items as $item) {
			$sth = $pdo->prepare("UPDATE galleries_media SET position = :position WHERE media_id = :media_id AND galleries_id = :gall_id LIMIT 1");
			$sth->bindParam(":position", $key);
			$sth->bindParam(":media_id", $item);
			$sth->bindParam(":gall_id", $id);
			$sth->execute();
			echo $item.",";
			$key++;
		}
	}
	
	private function selectAllMedia($options) {
		$pdo = \Config\DB::getInstance();
		$options = $this->parseOptions($options);
		$sth = $pdo->prepare("SELECT id, kind, imgUrl, embedCode, title FROM media".$options->order.$options->limit);
		$sth->execute();
		$result = $sth->fetchAll(\PDO::FETCH_OBJ);
		return $result;	
	}
	
	private function selectOrphans($options) {
		$pdo = \Config\DB::getInstance();
		$options = $this->parseOptions($options);
		$sth = $pdo->prepare("SELECT id, kind, imgUrl, embedCode, title FROM media left join galleries_media on media.id = galleries_media.media_id where galleries_media.id IS NULL".$options->order.$options->limit);
		$sth->execute();
		$result = $sth->fetchAll(\PDO::FETCH_OBJ);
		return $result;	
	}
	
	private function selectMostRecent($options) {
		$pdo = \Config\DB::getInstance();
		$sth = $pdo->prepare("SELECT created FROM media ORDER BY created DESC LIMIT 1");
		$sth->execute();
		$last = $sth->fetch();
		$options .= "&conditions={media.created EQUALS ".$last->created."}";
		$options = $this->parseOptions($options);
		$sth2 = $pdo->prepare("SELECT id, kind, imgUrl, embedCode, title FROM media".$options->where.$options->order.$options->limit);
		$sth2->execute();
		$result = $sth2->fetchAll(\PDO::FETCH_OBJ);
		return $result;	

	}
	
	private function selectMediaById($id) {
		$pdo = \Config\DB::getInstance();
		$sth = $pdo->prepare("SELECT id, kind, imgUrl, embedCode, caption, title FROM media WHERE id = :id");
		$sth->bindParam(":id", $id);
		$sth->execute();
		$result = $sth->fetch(\PDO::FETCH_OBJ);
		return $result;	
	}
		
	private function selectGalleriesByDocId($docId) {
		$pdo = \Config\DB::getInstance();
		$sth = $pdo->prepare("SELECT id, label FROM galleries WHERE documents_id = :doc_id");
		$sth->bindParam(":doc_id", $docId);
		$sth->execute();
		$result = $sth->fetchAll(\PDO::FETCH_OBJ);
		return $result;
	}
	
	private function selectMediaByGalleryId($galleryId) {
		$pdo = \Config\DB::getInstance();
		$sth = $pdo->prepare("SELECT media.id, media.kind, media.imgUrl, media.embedCode, media.title, media.caption, galleries_media.position FROM media LEFT JOIN galleries_media ON media.id = galleries_media.media_id WHERE galleries_media.galleries_id = :galleries_id ORDER BY galleries_media.position ASC");
		$sth->bindParam(":galleries_id", $galleryId);
		$sth->execute();
		$result = $sth->fetchAll(\PDO::FETCH_OBJ);
		return $result;		
	}
	
	private function selectVimeoItems($galleryId) {
		$pdo = \Config\DB::getInstance();
		$sth = $pdo->prepare("SELECT media.*, galleries_media.position FROM media LEFT JOIN galleries_media ON media.id = galleries_media_id WHERE galleries_media.galleries_id = :galleries_id AND media.kind = vimeo/embedded ORDER BY galleries_media.position");
		$sth->bindParam(":galleries_id", $galleryId);
		$sth->execute();
		$result = $sth->fetchAll(\PDO::FETCJ_OBJ);
		return $result;
	}
	
	private function selectVimeoItemsAPI($galleryId) {
		$pdo = \Config\DB::getInstance();
		$content = file_get_contents("http://vimeo.com/api/v2/user/".$galleryId."/videos.php");
		if ($content) {
			$videos = unserialize($content);
		}
		return $videos;
	}
	
	private function parseResultToGalleries($result) {
		$galleries = array();
		foreach($result as $galleryResult) {
			$galleries[] = $this->parseGalleryResultToGallery($galleryResult);
		}
		return $galleries;
	}
	
	private function parseGalleryResultToGallery($galleryResult) {
		$gallery = new \Models\Gallery;
		$gallery->id = $galleryResult->id;
		$gallery->label = $galleryResult->label;
		$gallery->media = $this->findMediaByGalleryId($galleryResult->id);
		return $gallery;
	}
	
	private function parseResultToMedia($result) {
		$media = array();
		foreach($result as $mediaResult) {
			$media[] = $mediaResult;
		}
		return $media;
	}

	private function parseOptions($optionsString) {
		$optionsElements = $this->parseStringToOptionsElements($optionsString);
		$options = $this->parseOptionsElementsToOptions($optionsElements);
		$optionsObj = $this->parseOptionsToObject($options);
		return $optionsObj;
	}
	
	private function parseStringToOptionsElements($optionsString) {
		$optionsElements = ($optionsString != null) ? explode("&", $optionsString) : null;
		return $optionsElements;
	}

	private function parseOptionsElementsToOptions($optionsElements) {
		$options = array();
		if(count($optionsElements)>0) {
			foreach($optionsElements as $optionsElement) {
				$option = list($label, $value) = explode("=", $optionsElement);
				$options[$label] = $value;
			}
		}
		return $options;
	}
	
	private function parseOptionsToObject($options) {
		$conditions = (isset($options['conditions'])) ? $options['conditions'] : null;
		$orderBy = (isset($options['orderBy'])) ? $options['orderBy'] : 'media.created';
		$orderType = (isset($options['orderType'])) ? $options['orderType'] : 'DESC';
		$limit = (isset($options['limit'])) ? $options['limit'] : null;
		$optionsObj = new \stdClass;
		$optionsObj->order = $this->parseOrder($orderBy, $orderType);
		$optionsObj->limit = $this->parseLimit($limit);
		$optionsObj->conditions = $this->parseConditions($limit);
		return $optionsObj;
	}

	private function parseOrder($orderBy, $orderType) {
		return " ORDER BY " . $orderBy. " " . $orderType . " ";
	}
	
	private function parseLimit($limit) {
		return ($limit != null) ? " LIMIT " . $limit . " " : null;
	}
	
	private function parseConditions($conditions) {
		$conditions = str_replace(array("{", "}", "EQUALS"), array("","","="), $conditions);
		$conditions = explode("&", $conditions);
		$output = null;
		if(count($conditions)>0) {
			foreach($conditions as $condition) { 
				$output .= $condition . " ";
			}
		}
		else {
			$output = "1=1";
		}
		return " WHERE ".$output;
	}
}
?>