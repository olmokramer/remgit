<?php
/* Document DAO PHP */

class DAOS_Document {

	//public

	public function getDocuments($menuItemsRef, $options=null) {
		$documents = (is_numeric($menuItemsRef)) ? $this->findByMenuItemsId($menuItemsRef, $options) : $this->findByMenuItemsLabel($menuItemsRef, $options);
		return $documents;
	}

	public function getRelatedDocuments($id, $options=null) {
		$documents = $this->findByCategoryMatchCount($id, $options);
		return $documents;
	}

	public function searchDocuments($menuItemsRef, $options=null) {
		$documents = (is_numeric($menuItemsRef)) ? $this->searchByMenuItemsId($menuItemsRef, $options) : $this->searchByMenuItemsLabel($menuItemsRef, $options);
		return $documents;
	}

	public function countDocuments($menuItemsRef, $options=null) {
		$count = (is_numeric($menuItemsRef)) ? $this->countByMenuItemsId($menuItemsRef, $options) : $this->countByMenuItemsLabel($menuItemsRef, $options);
		return $count;
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

	private function searchByMenuItemsId($menuItemsId, $options=null) {
		$result = $this->selectSearchByMenuItemsId($menuItemsId, $options);
		$documents = $this->parseResultToDocs($result);
		return $documents;
	}

	private function searchByMenuItemsLabel($menuItemsLabel, $options=null) {
		$result = $this->selectSearchByMenuItemsLabel($menuItemsLabel, $options);
		$documents = $this->parseResultToDocs($result);
		return $documents;
	}

	private function countByMenuItemsId($menuItemsId, $options=null) {
		$count = $this->selectCountByMenuItemsId($menuItemsId, $options);
		return $count;
	}

	private function countByMenuItemsLabel($menuItemsLabel, $options=null) {
		$count = $this->selectCountByMenuItemsLabel($menuItemsLabel, $options);
		return $count;
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
		return $document;
	}

	public function findByCategory($value, $options=null) {
		$result = (is_numeric($value)) ? $this->selectByCategoryId($value, $options) : $this->selectByCategoryLabel($value, $options);
		$docs = $this->parseResultToDocs($result);
		return $docs;
	}

	//public

	public function create($menuItemsId) {
		$templateId = $this->getTemplatesIdByMenuItemsId($menuItemsId);
		$templateDAO = new DAOS_Template;
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
		$pdo = DB::getInstance();
		$title = 'Untitled Document';
		$sth = $pdo->prepare("INSERT INTO documents(title, menuItems_id, created, published) VALUES(:title, :menuItems_id, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
		$sth->bindParam(":title", $title);
		$sth->bindParam(":menuItems_id", $menuItemsId);
		$sth->execute();
		return $pdo->lastInsertId();
	}

	private function deleteDoc($id) {
		$pdo = DB::getInstance();
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
		$pdo = DB::getInstance();
		$mediaDAO = new DAOS_Media;
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
		$pdo = DB::getInstance();
		$table = ($fieldtype == 'single') ? 'documents_fields_singleline' : 'documents_fields_multiline';
		$sth = $pdo->prepare("INSERT INTO ".$table."(documents_id, fields_id) VALUES(:documents_id, :fields_id)");
		$sth->bindParam(":documents_id", $docId);
		$sth->bindParam(":fields_id", $templateField->id);
		$sth->execute();
		return true;
	}

	private function insertGalleryInstance($templateField, $docId) {
		$pdo = DB::getInstance();
		$sth = $pdo->prepare("INSERT INTO galleries(label, kind, documents_id) VALUES(:label, :kind, :documents_id)");
		$sth->bindParam(":label", $templateField->label);
		$sth->bindParam(":kind", $templateField->kind);
		$sth->bindParam(":documents_id", $docId);
		$sth->execute();
		return true;
	}

	private function getTemplatesIdByMenuItemsId($menuItemsId) {
		$pdo = DB::getInstance();
		$sth = $pdo->prepare("SELECT id FROM templates WHERE menuItems_id = :menuItems_id LIMIT 1");
		$sth->bindParam(":menuItems_id", $menuItemsId);
		$sth->execute();
		$result = $sth->fetchAll(PDO::FETCH_OBJ);
		return $result[0]->id;
	}

	private function parseDocumentElements($docResult) {
		$mediaDAO = new DAOS_Media;
		$categoryDAO = new DAOS_Category;
		$docElements = new stdClass();
		$docElements->native = $docResult;
		$docElements->custom = $this->findCustomFieldsByDocId($docResult->id);
		$docElements->categories = $categoryDAO->findByDocId($docResult->id);
		$docElements->galleries = $mediaDAO->findGalleriesByDocId($docResult->id);
		return $docElements;
	}

	private function findCustomFieldsByDocId($docId) {
		$pdo = DB::getInstance();
		$sth = $pdo->prepare("(SELECT templates_fields.position as a, documents_fields_singleline.id, documents_fields_singleline.value, fields.label, fields.fieldtype, fields.inputtype, fields.default FROM documents_fields_singleline LEFT JOIN fields ON documents_fields_singleline.fields_id = fields.id LEFT JOIN templates_fields ON documents_fields_singleline.fields_id = templates_fields.fields_id WHERE documents_fields_singleline.documents_id = :doc_id)
UNION ALL (SELECT templates_fields.position as bx, documents_fields_multiline.id, documents_fields_multiline.value, fields.label, fields.fieldtype, fields.inputtype, fields.default FROM documents_fields_multiline LEFT JOIN fields ON documents_fields_multiline.fields_id = fields.id LEFT JOIN templates_fields ON documents_fields_multiline.fields_id = templates_fields.fields_id WHERE documents_fields_multiline.documents_id = :doc_id) ORDER BY a ASC");
		$sth->bindParam(":doc_id", $docId);
		$sth->execute();
		$customFields = $sth->fetchAll(PDO::FETCH_OBJ);
		return $customFields;
	}

	private function findGalleriesByDocId($docId) {
		$pdo = DB::getInstance();
		$sth = $pdo->prepare("SELECT * FROM galleries WHERE documents_id = :doc_id");
		$sth->bindParam(":doc_id", $docId);
		$sth->execute();
		$galleries = $sth->fetchAll(PDO::FETCH_OBJ);
		return $galleries;
	}

	private function selectByMenuItemsId($menuItemsId, $options) {
		$pdo = DB::getInstance();
		$options = $this->parseOptions($options);
		$sth = $pdo->prepare("SELECT * FROM documents WHERE menuItems_id = :menuItems_id ".$options->conditions.$options->order.$options->limit);
		$sth->bindParam(":menuItems_id", $menuItemsId);
		$sth->execute();
		$result = $sth->fetchAll(PDO::FETCH_OBJ);
		return $result;
	}

	private function selectByMenuItemsLabel($menuItemsLabel, $options) {
		$pdo = DB::getInstance();
		$options = $this->parseOptions($options);
		$sth = $pdo->prepare("SELECT documents.* FROM documents LEFT JOIN menuItems ON documents.menuItems_id = menuItems.id ".$options->joins." WHERE menuItems.label = :label ".$options->conditions.$options->order.$options->limit);
		$sth->bindParam(":label", $menuItemsLabel);
		$sth->execute();
		$result = $sth->fetchAll(PDO::FETCH_OBJ);
		return $result;
	}

	private function selectSearchByMenuItemsId($menuItemsId, $options) {
		$pdo = DB::getInstance();
		$options = $this->parseOptions($options);
		$sth = $pdo->prepare("SELECT documents.* FROM documents LEFT JOIN documents_fields_singleline ON documents.id = documents_fields_singleline.documents_id LEFT JOIN documents_fields_multiline ON documents.id = documents_fields_multiline.documents_id LEFT JOIN menuItems ON documents.menuItems_id = menuItems.id WHERE menuItems.id = :menuItems_id ".$options->conditions." GROUP BY documents.id ".$options->order.$options->limit);
		$sth->bindParam(':menuItems_id', $menuItemsId);
		$sth->execute();
		$result = $sth->fetchAll(PDO::FETCH_OBJ);
		$sth2 = $pdo->prepare("SELECT COUNT(documents.id) FROM documents LEFT JOIN documents_fields_singleline ON documents.id = documents_fields_singleline.documents_id LEFT JOIN documents_fields_multiline ON documents.id = documents_fields_multiline.documents_id LEFT JOIN menuItems ON documents.menuItems_id = menuItems.id WHERE menuItems.id = :menuItems_id ".$options->conditions." GROUP BY documents.id ".$options->order);
		$sth->bindParam(':menuItems_id', $menuItemsId);
		$sth2->execute();
		$count = $sth2->rowCount();
		$result["count"] = $count;
		return $result;
	}

	private function selectSearchByMenuItemsLabel($menuItemsLabel, $options) {
		$pdo = DB::getInstance();
		$options = $this->parseOptions($options);
		$sth = $pdo->prepare("SELECT documents.* FROM documents LEFT JOIN documents_fields_singleline ON documents.id = documents_fields_singleline.documents_id LEFT JOIN documents_fields_multiline ON documents.id = documents_fields_multiline.documents_id LEFT JOIN menuItems ON documents.menuItems_id = menuItems.id WHERE menuItems.label = :menuItems_label ".$options->conditions." GROUP BY documents.id ".$options->order.$options->limit);
		$sth->bindParam(':menuItems_label', $menuItemsLabel);
		$sth->execute();
		$result = $sth->fetchAll(PDO::FETCH_OBJ);
		$sth2 = $pdo->prepare("SELECT COUNT(documents.id) FROM documents LEFT JOIN documents_fields_singleline ON documents.id = documents_fields_singleline.documents_id LEFT JOIN documents_fields_multiline ON documents.id = documents_fields_multiline.documents_id LEFT JOIN menuItems ON documents.menuItems_id = menuItems.id WHERE menuItems.label = :menuItems_label ".$options->conditions." GROUP BY documents.id ".$options->order);
		$sth2->bindParam(':menuItems_label', $menuItemsLabel);
		$sth2->execute();
		$count = $sth2->rowCount();
		$result["count"] = $count;
		return $result;
	}

	private function selectCountByMenuItemsId($menuItemsId, $options) {
		$pdo = DB::getInstance();
		$options = $this->parseOptions($options);
		$sth = $pdo->prepare("SELECT COUNT(id) FROM documents WHERE menuItems_id = :menuItems_id ".$options->conditions);
		$sth->bindParam(":menuItems_id", $menuItemsId);
		$sth->execute();
		$result = $sth->fetchAll();
		return $result[0][0];
	}

	private function selectCountByMenuItemsLabel($menuItemsLabel, $options) {
		$pdo = DB::getInstance();
		$options = $this->parseOptions($options);
		$sth = $pdo->prepare("SELECT COUNT(documents.id) FROM documents LEFT JOIN menuItems ON documents.menuItems_id = menuItems.id ".$options->joins." WHERE menuItems.label = :label ".$options->conditions);
		$sth->bindParam(":label", $menuItemsLabel);
		$sth->execute();
		$result = $sth->fetchAll();
		return $result[0][0];
	}

	private function selectByCategoryMatchCount($id, $options) {
		$pdo = DB::getInstance();
		$options = $this->parseOptions($options);
		$sth = $pdo->prepare("SELECT (SELECT menuItems_id FROM documents WHERE id = :documents_id LIMIT 1) AS menuItemsId, documents.*, count(*) AS matches FROM documents_categories LEFT JOIN documents ON documents_categories.documents_id = documents.id WHERE categories_id IN (SELECT categories_id FROM documents_categories WHERE documents_id = :documents_id) AND documents_id != :documents_id AND menuItems_id = menuItems_id GROUP BY documents_id ORDER BY matches DESC, RAND() ".$options->limit);
		$sth->bindParam(":documents_id", $id);
		$sth->execute();
		$result = $sth->fetchAll(PDO::FETCH_OBJ);
		return $result;
	}

	private function selectById($id) {
		$pdo = DB::getInstance();
		$sth = $pdo->prepare("SELECT * FROM documents WHERE id = :id LIMIT 1");
		$sth->bindParam(":id", $id);
		$sth->execute();
		$result = $sth->fetch(PDO::FETCH_OBJ);
		return $result;
	}

	private function selectCategoryIdByCategoryLabel($catLabel) {
		$pdo = DB::getInstance();
		$sth = $pdo->prepare("SELECT id FROM categories WHERE label = :label LIMIT 1");
		$sth->bindParam(":label", $catLabel);
		$sth->execute();
		$result = $sth->fetch(PDO::FETCH_OBJ);
		return $result->id;
	}

	private function selectByCategoryLabel($catLabel, $options) {
		$id = $this->selectCategoryIdByCategoryLabel($catLabel);
		$result = $this->selectByCategoryId($id, $options);
		return $result;
	}

	private function selectByCategoryId($id, $options) {
		$pdo = DB::getInstance();
		$options = $this->parseOptions($options);
		$sth = $pdo->prepare("SELECT documents.* FROM documents LEFT JOIN documents_categories ON documents.id = documents_categories.documents_id WHERE documents_categories.categories_id = :categories_id ".$options->conditions.$options->order.$options->limit);
		$sth->bindParam(":categories_id", $id);
		$sth->execute();
		$result = $sth->fetchAll(PDO::FETCH_OBJ);
		return $result;
	}

	private function updateDoc($doc) {
		$pdo = DB::getInstance();
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
		$pdo = DB::getInstance();
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
		$pdo = DB::getInstance();
		$table = ($customField->fieldType == "single" ? "documents_fields_singleline" : "documents_fields_multiline");
		$sth = $pdo->prepare("UPDATE ".$table." SET value = :value WHERE id = :id LIMIT 1");
		$sth->bindParam(":value", $customField->value);
		$sth->bindParam(":id", $customField->id);
		$sth->execute();
		return true;
	}

	private function parseResultToDocs($result) {
		$docs = array();
		foreach($result as $key => $docResult) {
			if($key !== "count") {
				$docElements = $this->parseDocumentElements($docResult);
				$docs[] = $this->parseDocElementsToDoc($docElements);
			}
		}
		if(isset($result["count"])) {
			$docs["count"] = $result["count"];
		}
		return $docs;
	}

	private function parseDocElementsToDoc($docElements) {
		$doc = new Models_Document($docElements);
		return $doc;
	}

	private function parseOptions($options) {
		$options = (is_array($options)) ? $options : $this->parseOptionsStringToOptions($options);
		$optionsObj = $this->parseOptionsToObject($options);
		return $optionsObj;
	}

	private function parseOptionsStringToOptions($optionsString) {
		$optionElements = ($optionsString != null) ? explode("&", $optionsString) : null;
		$options = $this->parseOptionsElementsToOptions($optionElements);
		return $options;
	}

	private function parseOptionsElementsToOptions($optionElements) {
		$options = array();
		if(count($optionElements)>0) {
			foreach($optionElements as $optionElement) {
				$option = list($label, $value) = explode("=", $optionElement);
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
		$query = (isset($options['query'])) ? $options['query'] : null;
		$ignoreUnpublished = (isset($options['ignoreUnpublished'])) ? $options['ignoreUnpublished'] : null;
		$optionsObj = new stdClass();
		$optionsObj->joins = ($cats != null) ? $this->joinTable('categories', $cats) : null;
		$optionsObj->conditions = $this->parseConditions($cats, $query, $ignoreUnpublished);
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

	private function parseConditions($cats=null, $query=null, $ignoreUnpublished=null) {
		$cats = array_filter(explode(",",$cats));
		$conditions_return = '';
		if(is_array($cats) && count($cats) > 0) {
			$conditions = $this->parseCatConditions($cats);
			$conditions_return .= " AND (" . implode(" OR ", $conditions) . ") ";
		}
		if(is_array($query) && count($query) > 0) {
			$conditions = $this->parseQueryConditions($query);
			$queryDelimiter = (isset($query['matchAll']) && !$query['matchAll']) ? ' OR ' : ' AND '; //defaults to matchAll=true
			$conditions_return .= " AND (" . implode($queryDelimiter, $conditions) . ") ";
		}
		$conditions_return .= ($ignoreUnpublished == "1") ? null : " AND documents.publishState !='0' ";
		return $conditions_return;
	}

	private function parseCatConditions($cats) {
		$conditions = array();
		foreach($cats as $cat) {
			$categoryDAO = new DAOS_Category;
			$catId = $categoryDAO->findIdByLabel($cat);
			$conditions[] = "documents_categories.categories_id = '".$catId."'";
		}
		return $conditions;
	}

	private function parseQueryConditions($query) {
		$conditions = array();
		$searchFields = (isset($query['searchFields'])) ? $query['searchFields'] : null;
		unset($query['matchAll'], $query['searchFields']);

		foreach($query as $i => $item) {
			if($searchFields != null) {
				$condition = '(';
				foreach($searchFields as $j => $fieldLabel) {
					$condition .= ($j > 0) ? ' OR ' : '';
					$condition .= $this->parseQueryItem($item, $fieldLabel);
				}
				$condition .= ')';
			} else {
				$condition = $this->parseQueryItem($item, $item['label']);
			}
			$conditions[] = $condition;
		}
		return $conditions;
	}

	private function parseQueryItem($queryItem, $label) {
		$fieldDAO = new DAOS_Field;
		$wildcard = (isset($queryItem['wildcard']) && !$queryItem['wildcard']) ? '' : '%'; //defaults to wildcard=true
		$wildcard_operator = (isset($queryItem['wildcard']) && !$queryItem['wildcard']) ? ' = ' : ' LIKE '; //defaults to wildcard=true
		switch($label) {
			case 'title':
				$condition = 'documents.title' . $wildcard_operator . '\'' . $wildcard . $queryItem['value'] . $wildcard . '\'';
				break;
			default:
				$fieldTemplate = $fieldDAO->findFieldByLabel($label);
				$table = 'documents_fields_' . $fieldTemplate->fieldtype . 'line';
				$condition = '(' . $table . '.value' . $wildcard_operator . '\'' . $wildcard . $queryItem['value'] . $wildcard . '\'' . ' AND ' . $table . '.fields_id = ' . $fieldTemplate->id . ')';
				break;
		}
		return $condition;
	}

	private function parseOrder($orderBy, $orderType) {
		return " ORDER BY " . $orderBy. " " . $orderType . " ";
	}

	private function parseLimit($limit) {
		return ($limit != null) ? " LIMIT " . $limit . " " : null;
	}
}
?>
