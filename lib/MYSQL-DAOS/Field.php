<?php
/* Fields DAO PHP */

class DAOS_Field {

	public function findAllFields() {
		$result = $this->selectAllFields();
		$fields = $this->parseResultsToFields($result);
		return $fields;
	}

	public function findFieldByLabel($label) {
		$field = $this->selectFieldByLabel($label);
		return $field;
	}

	private function selectAllFields() {
		$db = DB::getInstance();
		$sth = $db->prepare("SELECT * FROM fields");
		$sth->execute();
		$result = $sth->fetchAll(PDO::FETCH_OBJ);
		return $result;
	}

	private function selectFieldByLabel($label) {
		$db = DB::getInstance();
		$sth = $db->prepare("SELECT * FROM fields WHERE label = :label LIMIT 1");
		$sth->bindParam(":label", $label);
		$sth->execute();
		$result = $sth->fetchAll(PDO::FETCH_OBJ);
		return $result[0];
	}

	private function parseResultsToFields($result) {
		$fields = array();
		foreach($result as $fieldResult) {
			$fields[] = Models_Field($result);
		}
		return $fields;
	}
}
?>
