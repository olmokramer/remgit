<?php
/* Fields DAO PHP */
namespace DAOS;

class Field {
	
	public function findAllFields() {
		$fields = $this->selectAllFields();
		$fields = $this->parseResultToFields($result);
		return $fields;
	}
	
	private function selectAllFields() {
		$db = DB::getInstance();
		$sth = $db->prepare("SELECT * FROM fields");
		$sth->execute();
		$result = $sth->fetchAll(PDO::FETCH_OBJ);
		return $result;	
	}
	
	private function parseResultToFields($result) {
		foreach($result as $fieldResult) {
			new \Models\Field($fieldResult);
		}
		return true;
	}
	
}