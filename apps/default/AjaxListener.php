<?php
session_start();

//include files
include("../../config/settings.php");
include("Utils.php");
include("settings.php");

//
if (get_magic_quotes_gpc()) {
    function stripslashes_gpc(&$value) {
        $value = stripslashes($value);
    }
    array_walk_recursive($_GET, 'stripslashes_gpc');
    array_walk_recursive($_POST, 'stripslashes_gpc');
    array_walk_recursive($_COOKIE, 'stripslashes_gpc');
    array_walk_recursive($_REQUEST, 'stripslashes_gpc');
}

$listener = new \AjaxListener;
$listener->vars = ($_SERVER['REQUEST_METHOD'] == "POST") ? $_POST : $_GET;
$action = ($_SERVER['REQUEST_METHOD'] == "POST") ? strip_tags($_POST['action']) : strip_tags($_GET['action']);
$listener->$action();

class AjaxListener {
	
	private $vars;
	private $controller;
	
	public function __set($var, $value) {
		$this->$var = $value;
	}
	
	public function showMainMenu() {
		include(CONTROLLERS_ROOT."MainMenu.php");
		$this->controller = new \Controllers\MainMenu;
		$this->controller->show();
	}
	public function showMediaMenu() {
		include(CONTROLLERS_ROOT."MainMenu.php");
		$this->controller = new \Controllers\MainMenu;
		$this->controller->showMediaMenu();
	}
	public function showDocList() {
		include(CONTROLLERS_ROOT."Document.php");
		$this->controller = new \Controllers\Document;
		$this->controller->showList(strip_tags($this->vars['type']), strip_tags($this->vars['id']), "ignoreUnpublished=1");
	}
	public function showDoc() {
		include(CONTROLLERS_ROOT."Document.php");
		$this->controller = new \Controllers\Document;
		$this->controller->show(strip_tags($this->vars['id']));
	}
	public function createDoc() {
		include(CONTROLLERS_ROOT."Document.php");
		$this->controller = new \Controllers\Document;
		$this->controller->create($this->vars['menuItemsId']);
	}
	public function saveDoc() {
		include(CONTROLLERS_ROOT."Document.php");
		$this->controller = new \Controllers\Document;
		$categories = (isset($this->vars['categories'])) ? $this->vars['categories'] : array();
		$this->controller->update($this->vars['id'], $this->vars['fields'], $categories, $this->vars['pubdate'], $this->vars['pubstate'], $this->vars['coverImage']);
	}
	public function deleteDoc() {
		include(CONTROLLERS_ROOT."Document.php");
		$this->controller = new \Controllers\Document;
		$this->controller->delete($this->vars['id']);
	}
	public function sortDocs() {
		include(CONTROLLERS_ROOT."Document.php");
		$this->controller = new \Controllers\Document;
		$this->controller->updateOrder($this->vars['menuItemId'], $this->vars['docIds']);
	}
	public function sortGallery() {
		include(CONTROLLERS_ROOT."Gallery.php");
		$this->controller = new \Controllers\Gallery;
		$this->controller->updateOrder($this->vars['gall_id'], $this->vars['items']);
	}
	public function showMediaBrowser() {
		include(CONTROLLERS_ROOT."Media.php");
		$this->controller = new \Controllers\Media;
		$activeMedia = (isset($this->vars['activeMedia'])) ? $this->vars['activeMedia'] : array();
		$this->controller->showBrowser($activeMedia, $this->vars['mediaKind'], $this->vars['options']);
	}
	public function appendToMediaBrowser() {
		include(CONTROLLERS_ROOT."Media.php");
		$this->controller = new \Controllers\Media;
		$activeMedia = (isset($this->vars['activeMedia'])) ? $this->vars['activeMedia'] : array();
		$this->controller->appendToBrowser($activeMedia, $this->vars['options']);
	}
	public function appendToImagePicker() {
		include(CONTROLLERS_ROOT."Media.php");
		$this->controller = new \Controllers\Media;
		$this->controller->appendToPicker($this->vars['options']);
	}
	public function removeMediaFromGallery() {
		include(CONTROLLERS_ROOT."Document.php");
		$this->controller = new \Controllers\Document;
		$this->controller->removeMediaFromGallery($this->vars['galleryId'], $this->vars['mediaIds']);
	}
	public function addMediaToGallery() {
		include(CONTROLLERS_ROOT."Document.php");
		$this->controller = new \Controllers\Document;
		$this->controller->addMediaToGallery($this->vars['galleryId'], $this->vars['selectedMedia']);
	}
	public function showMediaList() {
		include(CONTROLLERS_ROOT."Media.php");
		$this->controller = new \Controllers\Media;
		$this->controller->showList($this->vars['options'], $this->vars['kind'], 0);
	}
	public function appendtoMediaList() {
		include(CONTROLLERS_ROOT."Media.php");
		$this->controller = new \Controllers\Media;
		$this->controller->showList($this->vars['options'], 1);
	}
	public function showMediaItem() {
		include(CONTROLLERS_ROOT."Media.php");
		$this->controller = new \Controllers\Media;
		$this->controller->show($this->vars['id']);
	}
	public function saveMediaItem() {
		include(CONTROLLERS_ROOT."Media.php");
		$this->controller = new \Controllers\Media;
		$this->controller->update($this->vars);
	}
	public function deleteMediaItem() {
		include(CONTROLLERS_ROOT."Media.php");
		$this->controller = new \Controllers\Media;
		$this->controller->delete($this->vars['id']);
	}
	public function showUploadScreen() {
		include(CONTROLLERS_ROOT."Media.php");
		$this->controller = new \Controllers\Media;
		$this->controller->showUploadScreen();
	}
	public function showAddVideoStream() {
		include(CONTROLLERS_ROOT."Media.php");
		$this->controller = new \Controllers\Media;
		$this->controller->showAddVideoStream();
	}
	public function addVideoStream() {
		include(CONTROLLERS_ROOT."Media.php");
		$this->controller = new \Controllers\Media;
		$this->controller->addVideoStream($this->vars['vimeoUserId']);
	}
	public function refreshVideoStreams() {
		include(CONTROLLERS_ROOT."Media.php");
		$this->controller = new \Controllers\Media;
		$this->controller->refreshVideoStreams();
	}
	public function saveSingleVideo() {
		include(CONTROLLERS_ROOT."Media.php");
		$this->controller = new \Controllers\Media;
		$this->controller->saveSingleVideo($this->vars['url']);
	}
	public function login() {
		include(CONTROLLERS_ROOT.'Logger.php');
		$loginController = new \Controllers\Logger;
		$loginController->login($this->vars);
	}
	public function logout() {
		include(CONTROLLERS_ROOT.'Logger.php');
		$loginController = new \Controllers\Logger;
		$loginController->logout();
		break;
	}

}
?>