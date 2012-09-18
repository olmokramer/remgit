<?php
/* CustomField Model PHP */
namespace Models;

class CustomField {

	//vars
	
	public $id;
	public $label;
	public $value;
	public $documentsId;
	public $fieldsId;
	public $fieldType;
	public $inputType;
	
	//getter
	
	public function __get($value) {
		return $this->value;
	}

}
?>