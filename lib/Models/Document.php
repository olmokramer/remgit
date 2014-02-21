<?php
/* Document Model PHP */

class Models_Document {
	public $id;
	public $title;
	public $coverUrl;
	public $position;
	public $menuItemsId;
	public $created;
	public $modified;
	public $published;
	public $pubstate;
	public $categories;
	public $customfields;
	public $galleries;

	public function __construct($docElements){
		$this->id = $docElements->native->id;
		$this->title = $docElements->native->title;
		$this->coverUrl = $docElements->native->coverUrl;
		$this->position = $docElements->native->position;
		$this->menuItemsId = $docElements->native->menuItems_id;
		$this->created = $docElements->native->created;
		$this->lastmodified = $docElements->native->lastmodified;
		$this->published = $docElements->native->published;
		$this->pubstate = $docElements->native->publishState;
		$this->categories = $docElements->categories;
		foreach($docElements->custom as $customfield) { $this->field = $customfield; }
		foreach($docElements->galleries as $gallery) { $this->gallery = $gallery; }
	}

	//getter
	public function __get($value) {
		if(isset($this->$value)) {
			return $this->$value;
		}
		else {
			if(in_array($value, array_keys($this->customfields))):
				return $this->customfields[$value]->value;
			endif;
			if(in_array($value, array_keys($this->galleries))):
				return $this->galleries[$value];
			endif;
		}
	}

	public function getCoverUrl($type='full') {
		if(substr($this->coverUrl, 0, 4) == "http") return $this->coverUrl;
		$coverUrl = ($type!='full') ? THUMBS.$this->coverUrl : UPLOADS.$this->coverUrl;
		return $coverUrl;
	}
	public function getMedia($galleryLabel) {
		if(isset($this->galleries[$galleryLabel])) {
			$gallery = $this->galleries[$galleryLabel];
			$media = array();
			foreach($gallery->media as $mediaItem) {
				$item = new stdClass;
				$item->id = $mediaItem->id;
				$item->embedCode = $mediaItem->embedCode;
				$item->title = $mediaItem->title;
				$item->caption = $mediaItem->caption;
				if(substr($mediaItem->imgUrl, 0, 4) == "http") {
					$item->url = $mediaItem->imgUrl;
					$item->thumb_url = $mediaItem->imgUrl;
				} else {
					$item->url = UPLOADS.$mediaItem->imgUrl;
					$item->thumb_url = THUMBS.$mediaItem->imgUrl;
				}
				$item->embedCode = $mediaItem->embedCode;
				$media[] = $item;
			}
			return $media;
		}
		return array();
	}

	public function getFirstAvailableImage() {
		if(count($this->galleries)>0) {
			$galleryLabels = array_keys($this->galleries);
			$galleryLabel = $galleryLabels[0];
			$media = $this->getMedia($galleryLabel);
			$image = (isset($media[0])) ? $media[0] : null;
			return $image;
		}
		return false;
	}

	//setter

	public function __set($var, $value) {
		switch($var) {
		default:
			$this->$var = $value;
			break;
		case 'field':
			$this->customfields[$this->tidy($value->label)] = $value;
			break;
		case 'gallery':
			$this->galleries[$value->label] = $value;
			break;
		}
	}

	public function tidy($var) {
		return trim(str_replace(array("'","\"", " "), array("", "", "_"), $var));
	}
}
?>
