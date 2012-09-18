<?php
/* MenuItem Model PHP */
namespace Models;

class MenuItem {
	
	//vars
	
	public $id;
	public $title;
	public $position;
	public $pubstate;
	public $fields;
	
	//getter
	
	public function __get($value) {
		return $this->$value;
	}
	
	//setter
	
	public function __set($var, $value) {
		switch($var) {
		default:
			$this->$var = $value;
			break;
		case 'field':
			$this->fields[] = $value;
			break;
		}
	}	
}
?>