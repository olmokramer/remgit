<?php
/* TemplateField Model PHP */

class Models_TemplateField {
	
	//vars
	
	private $id;
	private $kind;
	private $templateId;
	private $fieldsId;
	private $fieldType;
	private $label;
	
	
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