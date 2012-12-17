<?php
/* Media Controller PHP */
namespace Controllers;

//include backend files
include(DAOS_ROOT.'Media.php');

//include views
include(VIEWS_ROOT.'MediaBrowser.php');
include(VIEWS_ROOT.'MediaBrowser_Media.php');
include(VIEWS_ROOT.'MediaList.php');
include(VIEWS_ROOT.'MediaItem.php');
include(VIEWS_ROOT.'ListItem.php');
include(VIEWS_ROOT.'UploadScreen.php');

class Media {
	public function showBrowser($activeMedia, $options="limit=0,50") {
		$mediaDAO = new \DAOS\Media;
		$media = $mediaDAO->findAllMedia($options);
		new \Views\MediaBrowser($media, $activeMedia, $options);
	}

	public function showPicker($activeMedia, $options="limit=0,50") {
		$mediaDAO = new \DAOS\Media;
		$media = $mediaDAO->findAllMedia($options);
		new \Views\ImagePicker($media, $activeMedia, $options);
	}
		
	public function appendToBrowser($activeMedia, $options="limit=0,50") {
		$mediaDAO = new \DAOS\Media;
		$media = $mediaDAO->findAllMedia($options);
		new \Views\MediaBrowser_Media($media, $activeMedia, $options);
	}

	public function appendToPicker($options="limit=0,50") {
		$mediaDAO = new \DAOS\Media;
		$media = $mediaDAO->findAllMedia($options);
		new \Views\MediaBrowser_Media($media, $activeMedia=array(), $options);
	}
	
	public function showList($options="limit=0,50", $kind="all", $append=0) {
		$mediaDAO = new \DAOS\Media;
		$media = $mediaDAO->findAllMedia($options);
		new \Views\MediaList($media, $options, $kind, $append);
	}
	
	public function show($id) {
		$mediaDAO = new \DAOS\Media;
		$mediaItem = $mediaDAO->findMediaById($id);
		new \Views\MediaItem($mediaItem);	
	}
	
	public function update($vars) {
		$mediaDAO = new \DAOS\Media;
		$mediaItem = $mediaDAO->update($vars['id'], $vars);
	}
	
	public function delete($id) {
		$mediaDAO = new \DAOS\Media;
		$mediaItem = $mediaDAO->delete($id);
	}
	
	public function create($fileName) {
		$mediaDAO = new \DAOS\Media;
		$mediaDAO->create($fileName);
	}
	
	public function showUploadScreen() {
		new \Views\UploadScreen();
	}
}
?>