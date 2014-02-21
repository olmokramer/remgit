<?php
/* Gallery Model PHP */

class Models_Gallery {

	//vars
	
	public $id;
	public $label;
	public $media;
	public $kind;
	
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