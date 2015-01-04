<?php
//settings
include(dirname(dirname(__FILE__))."/config/settings.php");

//DAOS
include(DAOS_ROOT."Category.php");
include(DAOS_ROOT."Document.php");
include(DAOS_ROOT."Field.php");
include(DAOS_ROOT."Media.php");
include(DAOS_ROOT."MenuItem.php");
include(DAOS_ROOT."User.php");

//Models
include(MODELS_ROOT."CustomField.php");
include(MODELS_ROOT."Document.php");
include(MODELS_ROOT."Gallery.php");
include(MODELS_ROOT."Field.php");
include(MODELS_ROOT."MediaItem.php");
include(MODELS_ROOT."MenuItem.php");
include(MODELS_ROOT."TemplateField.php");
include(MODELS_ROOT."User.php");

class BackendController {
	private $docDAO;

	public function __construct() {
		$this->docDAO = new \DAOS\Document();
		$this->mediaDAO = new \DAOS\Media();
	}

	public function getDocuments($label, $options=null) {
		$documents = $this->docDAO->getDocuments($label, $options);
		return $documents;
	}

	public function countDocuments($label, $options=null) {
		$count = $this->docDAO->countDocuments($label, $options);
		return $count;
	}

	public function searchDocuments($label, $options=null) {
		$documents = $this->docDAO->searchDocuments($label, $options);
		return $documents;
	}

	public function getRelatedDocuments($id, $options=null) {
		$documents = $this->docDAO->getRelatedDocuments($id, $options);
		return $documents;
	}

	public function getDocument($id) {
		$document = $this->docDAO->getDocument($id);
		return $document;
	}

	public function getRandomImage() {
		$image = $this->mediaDAO->findRandomImage();
		return $image;
	}
}

/*
$options = array(
	"orderBy" => "title",
	"orderType" => "ASC",
	"limit" => "0,10",
	"cat" => "bio,contact",
	"query" => array(
		array(
			"value" => "RIVA",
			"wildcard" => true
		),
		array(
			"value" => "test",
			"wildcard" => false
		),
		...,
		"matchAll" => true,
		"searchFields" => array(
			[0] => title,
			[1] => opdracht,
			[2] => locatie,
			...
		)
	),
);
*/
?>
