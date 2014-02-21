<?php
/* Field Model PHP */

class Models_Field {

	public $id;
	public $label;
	public $kind;
	public $fieldtype;
	public $inputtype;
	
	public function __construct($fieldResult) {
		$this->id = $fieldResult->id;
		$this->label = $fieldResult->label;
		$this->kind = $fieldResult->kind;
		$this->fieldtype = $fieldResult->fieldtype;
		$this->inputtype = $fieldResult->inputtype;		
	}
	
	public function __set($var, $value) {
		switch($var) {
		default:
			$this->$var = $value;
			break;
		}
	}
	
	public function __get($var) {
		switch($var) {
		default:
			return $this->$var;
			break;	
		}
	}
}
