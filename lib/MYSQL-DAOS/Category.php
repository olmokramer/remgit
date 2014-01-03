<?php
/* Category DAO PHP */
namespace DAOS;

class Category {
	public function findAll() {
		$result = $this->selectAll();
		$cats = $this->parseResultToCats($result);
		return $cats;
	}
	
	public function findIdByLabel($label) {
		$cat = $this->selectByLabel($label);
		return $cat->id;
	}
	
	public function findByDocId($docId) {
		$result = $this->selectByDocId($docId);
		$cats = $this->parseResultToCats($result);
		return $cats;
	}
	
	public function findByMenuItemsId($menuItemsId) {
		$result = $this->selectByMenuItemsId($menuItemsId);
		$cats = $this->parseResultToCats($result);
		return $cats;
	}
	
	public function updateDocCategories($docId, $catIds) {
		$this->deleteDocCategories($docId);
		$this->insertDocCategories($docId, $catIds);
	}
	
	public function deleteDocCategories($docId) {
		$pdo = \Config\DB::getInstance();
		$sth = $pdo->prepare("DELETE FROM documents_categories WHERE documents_id = :documents_id");
		$sth->bindParam(":documents_id", $docId);
		$sth->execute();
		return true;
	}
	
	public function insertDocCategories($docId, $catIds) {
		$pdo = \Config\DB::getInstance();
		$sth = $pdo->prepare("INSERT INTO documents_categories(documents_id, categories_id) VALUES(:documents_id, :categories_id)");
		foreach($catIds as $catId) {
			$sth->bindParam(":documents_id", $docId);
			$sth->bindParam(":categories_id", $catId);
			$sth->execute();
		}
		return true;
	}
	
	private function selectByLabel($label) {
		$pdo = \Config\DB::getInstance();
		$sth = $pdo->prepare("SELECT * FROM categories WHERE label = :label LIMIT 1");
		$sth->bindParam(":label", $label);
		$sth->execute();
		$result = $sth->fetch(\PDO::FETCH_OBJ);
		return $result;
	}
	
	private function selectByDocId($docId) {
		$pdo = \Config\DB::getInstance();
		$sth = $pdo->prepare("SELECT documents_categories.categories_id as id, categories.label as label FROM documents_categories LEFT JOIN categories ON documents_categories.categories_id = categories.id WHERE documents_categories.documents_id = :documents_id");
		$sth->bindParam(":documents_id", $docId);
		$sth->execute();
		$result = $sth->fetchAll(\PDO::FETCH_OBJ);
		return $result;
	}
	
	private function selectAll() {
		$pdo = \Config\DB::getInstance();
		$sth = $pdo->prepare("SELECT id, label FROM categories ORDER BY label ASC");
		$sth->execute();
		$result = $sth->fetchAll(\PDO::FETCH_OBJ);
		return $result;
	}
	
	private function selectByMenuItemsId($menuItemsId) {
		$pdo = \Config\DB::getInstance();
		$sth = $pdo->prepare("SELECT id, label FROM categories WHERE menuItems_id = :menuItems_id ORDER BY label ASC");
		$sth->bindParam(":menuItems_id", $menuItemsId);
		$sth->execute();
		$result = $sth->fetchAll(\PDO::FETCH_OBJ);
		return $result;
	}
	
	private function parseResultToCats($result) {
		$cats = array();
		foreach($result as $catResult) {
			$cats[$catResult->id] = $catResult->label;
		}
		return $cats;
	}

}
?>