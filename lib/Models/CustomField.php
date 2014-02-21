<?php
/* CustomField Model PHP */

class Models_CustomField {

	//vars
	public $id;
	public $label;
	public $value;
	public $documentsId;
	public $fieldsId;
	public $fieldType;
	public $inputType;
	public $default;

	//getter

	public function __get($value) {
		return $this->value;
	}

}
?>
