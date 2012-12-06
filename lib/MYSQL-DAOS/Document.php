<?php
/* Document DAO PHP */
namespace DAOS;

class Document {

	//public
	
	public function getDocuments($menuItemsRef, $options=null) {
		$documents = (is_numeric($menuItemsRef)) ? $this->findByMenuItemsId($menuItemsRef, $options) : $this->findByMenuItemsLabel($menuItemsRef, $options);
		return $documents;
	}
	
	public function getRelatedDocuments($id, $options=null) {
		$documents = $this->findByCategoryMatchCount($id, $options);
		return $documents;
	}
	
		public function getDocument($id) {
		$document = $this->findById($id);
		return $document;
	}
	
	//semi private

	private function findByMenuItemsId($menuItemsId, $options=null) {
		$result = $this->selectByMenuItemsId($menuItemsId, $options);
		$documents = $this->parseResultToDocs($result);
		return $documents;
	}

	private function findByMenuItemsLabel($menuItemsLabel, $options=null) {
		$result = $this->selectByMenuItemsLabel($menuItemsLabel, $options);
		$documents = $this->parseResultToDocs($result);
		return $documents;
	}
	
	private function findByCategoryMatchCount($id, $options=null) {
		$result = $this->selectByCategoryMatchCount($id, $options);
		$documents = $this->parseResultToDocs($result);
		return $documents;
	}
	
	private function findById($id) {
		$docResult = $this->selectById($id);
		$docElements = $this->parseDocumentElements($docResult);
		$document = $this->parseDocElementsToDoc($docElements);
		return $document;	
	}

	public function findByCategory($value, $options=null) {
		$result = (is_numeric($value)) ? $this->selectByCategoryId($value, $options) : $this->selectByCategoryLabel($value, $options);
		$docs = $this->parseResultToDocs($result);
		return $docs;
	}
	
	//public
	
	public function create($menuItemsId) {
		$templateId = $menuItemsId;
		$templateDAO = new \DAOS\Template;
		$docId = $this->insertDoc($menuItemsId);
		$templateFields = $templateDAO->getTemplateFields($templateId);
		$this->createCustomFields($templateFields, $docId);
		return $docId;
	}
	
	public function update($doc) {
		$this->updateDoc($doc);
		$this->updateCustomFields($doc->customfields);
	}
	
	public function updateOrder($menuItemsId, $docIds) {
		$position = 0;
		foreach($docIds as $docId) {
			$this->updateDocPosition($menuItemsId, $docId, $position);
			$position++;
		}
	} 
	
	public function delete($id) {
		$this->deleteDoc($id);
		$this->deleteCustomFields($id);
	}
	
	//private
	
	private function insertDoc($menuItemsId) {
		$pdo = \Config\DB::getInstance();
		$title = 'Untitled Document';
		$sth = $pdo->prepare("INSERT INTO documents(title, menuItems_id, created, published) VALUES(:title, :menuItems_id, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
		$sth->bindParam(":title", $title);
		$sth->bindParam(":menuItems_id", $menuItemsId);
		$sth->execute();
		return $pdo->lastInsertId();
	}
	
	private function deleteDoc($id) {
		$pdo = \Config\DB::getInstance();
		$sth = $pdo->prepare("DELETE FROM documents WHERE id = :id LIMIT 1");
		$sth->bindParam(":id", $id);
		$sth->execute();
		return true;
	}
	
	private function createCustomFields($templateFields, $docId) {
		foreach($templateFields as $templateField) {
			switch($templateField->fieldtype) {
			case "single":
				$this->insertFieldInstance('single', $templateField, $docId);
				break;
			case "multi":
				$this->insertFieldInstance('multi', $templateField, $docId);
				break;
			case "gallery":
				$this->insertGalleryInstance($templateField, $docId);
				break;
			}
		}
	}
	
	private function deleteCustomFields($docId) {
		$pdo = \Config\DB::getInstance();
		$mediaDAO = new \DAOS\Media;
		$galleries = $mediaDAO->findGalleriesByDocId($docId);
		
		foreach($galleries as $gallery) {
			$sth = $pdo->prepare("DELETE FROM galleries_media WHERE galleries_id = :galleries_id");
			$sth->bindParam(":galleries_id", $gallery->id);
			$sth->execute();	
			
			$sth2 = $pdo->prepare("DELETE FROM galleries WHERE id = :id");
			$sth2->bindParam(":id", $gallery->id);
			$sth2->execute();	
		}
		
		$tables = array('documents_fields_multiline', 'documents_fields_singleline');
		foreach($tables as $table) {
			$sth = $pdo->prepare("DELETE FROM ".$table." WHERE documents_id = :documents_id");
			$sth->bindParam(":documents_id", $docId);
			$sth->execute();	
		}
		return true;
	}
	
	private function insertFieldInstance($fieldtype, $templateField, $docId) {
		$pdo = \Config\DB::getInstance();
		$table = ($fieldtype == 'single') ? 'documents_fields_singleline' : 'documents_fields_multiline';
		$sth = $pdo->prepare("INSERT INTO ".$table."(documents_id, fields_id) VALUES(:documents_id, :fields_id)");
		$sth->bindParam(":documents_id", $docId);
		$sth->bindParam(":fields_id", $templateField->id);
		$sth->execute();
		return true;
	}
	
	private function insertGalleryInstance($templateField, $docId) {
		$pdo = \Config\DB::getInstance();
		$sth = $pdo->prepare("INSERT INTO galleries(label, documents_id) VALUES(:label, :documents_id)");
		$sth->bindParam(":label", $templateField->label);
		$sth->bindParam(":documents_id", $docId);
		$sth->execute();
		return true;
	}
	
	private function parseDocumentElements($docResult) {
		$mediaDAO = new \DAOS\Media;
		$categoryDAO = new \DAOS\Category;
		$docElements = new \stdClass();
		$docElements->native = $docResult;
		$docElements->custom = $this->findCustomFieldsByDocId($docResult->id);
		$docElements->categories = $categoryDAO->findByDocId($docResult->id);
		$docElements->galleries = $mediaDAO->findGalleriesByDocId($docResult->id);
		return $docElements;
	}
	
	private function findCustomFieldsByDocId($docId) {
		$pdo = \Config\DB::getInstance();
		$sth = $pdo->prepare("(SELECT templates_fields.position as a, documents_fields_singleline.id, documents_fields_singleline.value, fields.label, fields.fieldtype, fields.inputtype FROM documents_fields_singleline LEFT JOIN fields ON documents_fields_singleline.fields_id = fields.id LEFT JOIN templates_fields ON documents_fields_singleline.fields_id = templates_fields.fields_id WHERE documents_fields_singleline.documents_id = :doc_id)
UNION ALL (SELECT templates_fields.position as bx, documents_fields_multiline.id, documents_fields_multiline.value, fields.label, fields.fieldtype, fields.inputtype FROM documents_fields_multiline LEFT JOIN fields ON documents_fields_multiline.fields_id = fields.id LEFT JOIN templates_fields ON documents_fields_multiline.fields_id = templates_fields.fields_id WHERE documents_fields_multiline.documents_id = :doc_id) ORDER BY a ASC");
		$sth->bindParam(":doc_id", $docId);
		$sth->execute();
		$customFields = $sth->fetchAll(\PDO::FETCH_OBJ);
		return $customFields;
	}
	
	private function findGalleriesByDocId($docId) {
		$pdo = \Config\DB::getInstance();
		$sth = $pdo->prepare("SELECT * FROM galleries WHERE documents_id = :doc_id");
		$sth->bindParam(":doc_id", $docId);
		$sth->execute();
		$galleries = $sth->fetchAll(\PDO::FETCH_OBJ);
		return $galleries;
	}
		
	private function selectByMenuItemsId($menuItemsId, $options) {
		$pdo = \Config\DB::getInstance();
		$options = $this->parseOptions($options);
		$sth = $pdo->prepare("SELECT * FROM documents WHERE menuItems_id = :menuItems_id ".$options->conditions.$options->order.$options->limit);
		$sth->bindParam(":menuItems_id", $menuItemsId);
		$sth->execute();
		$result = $sth->fetchAll(\PDO::FETCH_OBJ);
		return $result;
	}
	
	private function selectByMenuItemsLabel($menuItemsLabel, $options) {
		$pdo = \Config\DB::getInstance();
		$options = $this->parseOptions($options);
		$sth = $pdo->prepare("SELECT documents.* FROM documents LEFT JOIN menuItems ON documents.menuItems_id = menuItems.id ".$options->joins." WHERE menuItems.label = :label ".$options->conditions.$options->order.$options->limit);
		$sth->bindParam(":label", $menuItemsLabel);
		$sth->execute();
		$result = $sth->fetchAll(\PDO::FETCH_OBJ);
		return $result;
	}
	
	private function selectByCategoryMatchCount($id, $options) {
		$pdo = \Config\DB::getInstance();
		$options = $this->parseOptions($options);
		$sth = $pdo->prepare("SELECT (SELECT menuItems_id FROM documents WHERE id = :documents_id LIMIT 1) AS menuItemsId, documents.*, count(*) AS matches FROM documents_categories LEFT JOIN documents ON documents_categories.documents_id = documents.id WHERE categories_id IN (SELECT categories_id FROM documents_categories WHERE documents_id = :documents_id) AND documents_id != :documents_id AND menuItems_id = menuItems_id GROUP BY documents_id ORDER BY matches DESC, RAND() ".$options->limit);
		$sth->bindParam(":documents_id", $id);
		$sth->execute();
		$result = $sth->fetchAll(\PDO::FETCH_OBJ);
		return $result;		
	}

	private function selectById($id) {
		$pdo = \Config\DB::getInstance();
		$sth = $pdo->prepare("SELECT * FROM documents WHERE id = :id LIMIT 1");
		$sth->bindParam(":id", $id);
		$sth->execute();
		$result = $sth->fetch(\PDO::FETCH_OBJ);
		return $result;		
	}
	
	private function selectCategoryIdByCategoryLabel($catLabel) {
		$pdo = \Config\DB::getInstance();
		$sth = $pdo->prepare("SELECT id FROM categories WHERE label = :label LIMIT 1");
		$sth->bindParam(":label", $catLabel);
		$sth->execute();
		$result = $sth->fetch(\PDO::FETCH_OBJ);
		return $result->id;
	}
	
	private function selectByCategoryLabel($catLabel, $options) {
		$id = $this->selectCategoryIdByCategoryLabel($catLabel);
		$result = $this->selectByCategoryId($id, $options);
		return $result;
	}
	
	private function selectByCategoryId($id, $options) {
		$pdo = \Config\DB::getInstance();
		$options = $this->parseOptions($options);
		$sth = $pdo->prepare("SELECT documents.* FROM documents LEFT JOIN documents_categories ON documents.id = documents_categories.documents_id WHERE documents_categories.categories_id = :categories_id ".$options->conditions.$options->order.$options->limit);
		$sth->bindParam(":categories_id", $id);
		$sth->execute();
		$result = $sth->fetchAll(\PDO::FETCH_OBJ);
		return $result;
	}
	
	private function updateDoc($doc) {
		$pdo = \Config\DB::getInstance();
		$sth = $pdo->prepare("UPDATE documents SET title = :title, published = :published, lastmodified = UNIX_TIMESTAMP(), publishState = :publishState, coverUrl = :coverUrl WHERE id = :id LIMIT 1");
		$sth->bindParam(":title", $doc->title);
		$sth->bindParam(":published", $doc->published);
		$sth->bindParam(":publishState", $doc->publishState);
		$sth->bindParam(":id", $doc->id);
		$sth->bindParam(":coverUrl", $doc->coverImage);
		$sth->execute();
		return true;
	}
	
	private function updateDocPosition($menuItemsId, $docId, $position) {
		$pdo = \Config\DB::getInstance();
		$sth = $pdo->prepare("UPDATE documents SET position = :position WHERE id = :id LIMIT 1");
		$sth->bindParam(":position", $position);
		$sth->bindParam(":id", $docId);
		$sth->execute();
		return true;
	}
	
	private function updateCustomFields($customfields) {
		foreach($customfields as $customField) {
			$this->updateCustomField($customField);
		}
		return true;
	}
	
	private function updateCustomField($customField) {
		$pdo = \Config\DB::getInstance();
		$table = ($customField->fieldType == "single" ? "documents_fields_singleline" : "documents_fields_multiline");
		$sth = $pdo->prepare("UPDATE ".$table." SET value = :value WHERE id = :id LIMIT 1");
		$sth->bindParam(":value", $customField->value);
		$sth->bindParam(":id", $customField->id);
		$sth->execute();
		return true;
	}
	
	private function parseResultToDocs($result) {
		$docs = array();
		foreach($result as $docResult) {
			$docElements = $this->parseDocumentElements($docResult);
			$docs[] = $this->parseDocElementsToDoc($docElements);
		}
		return $docs;
	}
	
	private function parseDocElementsToDoc($docElements) {
		$doc = new \Models\Document($docElements);
		return $doc;
	}
	
	private function parseOptions($optionsString) {
		$optionsElements = $this->parseStringToOptionsElements($optionsString);
		$options = $this->parseOptionsElementsToOptions($optionsElements);
		$optionsObj = $this->parseOptionsToObject($options);
		return $optionsObj;
	}
	
	private function parseStringToOptionsElements($optionsString) {
		$optionsElements = ($optionsString != null) ? explode("&", $optionsString) : null;
		return $optionsElements;
	}

	private function parseOptionsElementsToOptions($optionsElements) {
		$options = array();
		if(count($optionsElements)>0) {
			foreach($optionsElements as $optionsElement) {
				$option = list($label, $value) = explode("=", $optionsElement);
				$options[$label] = $value;
			}
		}
		return $options;
	}
	
	private function parseOptionsToObject($options) {
		$orderBy = (isset($options['orderBy'])) ? $options['orderBy'] : 'documents.position';
		$orderType = (isset($options['orderType'])) ? $options['orderType'] : 'ASC';
		$limit = (isset($options['limit'])) ? $options['limit'] : null;
		$cats = (isset($options['cat'])) ? $options['cat'] : null;
		$ignoreUnpublished = (isset($options['ignoreUnpublished'])) ? $options['ignoreUnpublished'] : null;
		$optionsObj = new \stdClass();
		$optionsObj->joins = ($cats != null) ? $this->joinTable('categories', $cats) : null;
		$optionsObj->conditions = $this->parseConditions($cats, null, $ignoreUnpublished);
		$optionsObj->order = $this->parseOrder($orderBy, $orderType);
		$optionsObj->limit = $this->parseLimit($limit);
		return $optionsObj;
	}

	private function joinTable($tableName) {
		switch($tableName) {
		case 'categories':
			$join = "LEFT JOIN documents_categories ON documents.id = documents_categories.documents_id";
			break;
		}
		return $join;
	}
	
	private function parseConditions($cats=null, $searchstring=null, $ignoreUnpublished=null) {
		$cats = array_filter(explode(",",$cats));
		$conditions_return = null;
		if(is_array($cats) && count($cats)>0) {
			$conditions = array();
			foreach($cats as $cat) {
				$categoryDAO = new \DAOS\Category;
				$catId = $categoryDAO->findIdByLabel($cat);
				$conditions[] = "documents_categories.categories_id = '".$catId."'";
			}
			$conditions_return = " AND (" . implode(" OR ", $conditions) . ") ";

		}
		$conditions_return .= ($ignoreUnpublished == "1") ? null : " AND documents.publishState !='0' ";
		return $conditions_return;
	}
	
	private function parseOrder($orderBy, $orderType) {
		return " ORDER BY " . $orderBy. " " . $orderType . " ";
	}
	
	private function parseLimit($limit) {
		return ($limit != null) ? " LIMIT " . $limit . " " : null;
	}
}
?>