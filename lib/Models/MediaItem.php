<?php
/* MediaItem Model PHP */
namespace Models;

class MediaItem {
	
	//vars
	
	public $id;
	public $kind;
	public $imgUrl;
	public $embedCode;
	public $title;
	public $caption;
	public $uuid;
	public $created;
	public $galleryItemId;
	public $galleryPosition;
	public $active;
	
	//getter
	
	public function __get($value) {
		return $this->$value;
	}
	
	//setter
		
	public function __set($var, $value) {
		$this->$var = $value;
	}
	
}
?>