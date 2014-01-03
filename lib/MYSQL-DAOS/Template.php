<?php
/* Template DAO PHP */
namespace DAOS;

class Template {

	public function getTemplates() {
		$templates = $this->selectAllTemplates();
		return $templates;
	}

	public function getTemplateFields($templateRef) {
		$templateFields = (is_numeric($templateRef)) ? $this->selectTemplateFieldsByTemplateId($templateRef) : $this->selectTemplateFieldsByTemplateLabel($templateRef);
		return $templateFields;
	}

	private function selectTemplateFieldsByTemplateId($id) {
		$pdo = \Config\DB::getInstance();
		$sth = $pdo->prepare("SELECT fields.id, fields.label, fields.kind, fields.fieldtype, fields.default, templates_fields.position FROM fields LEFT JOIN templates_fields ON fields.id = templates_fields.fields_id WHERE templates_fields.templates_id = :id ORDER BY templates_fields.position ASC");
		$sth->bindParam(":id", $id);
		$sth->execute();
		$result = $sth->fetchAll(\PDO::FETCH_OBJ);
		return $result;
	}

	private function selectAllTemplates() {
		$pdo = \Config\DB::getInstance();
		$sth = $pdo->prepare("SELECT * FROM templates");
		$sth->execute();
		$templates = $sth->fetchAll(\PDO::FETCH_OBJ);
		return $templates;
	}

	private function selectTemplateFieldsByTemplateLabel($label) {
		$templateResult = $this->selectTemplateByLabel($label);
		$result = $this->selectTemplateFieldsByTemplateId($templateResult->id);
		return $result;
	}

	private function selectTemplateByLabel($label) {
		$pdo = \Config\DB::getInstance();
		$sth = $pdo->prepare("SELECT * FROM templates WHERE label = :label");
		$sth->bindParam(":label", $label);
		$sth->execute();
		$result = $sth->fetchAll(\PDO::FETCH_OBJ);
		return $result;
	}
}
?>
