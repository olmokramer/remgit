<?php
/* Media Controller PHP */
namespace Controllers;

//include backend files
include(DAOS_ROOT.'Media.php');
include(MODELS_ROOT.'MediaItem.php');

//include views
include(VIEWS_ROOT.'MediaBrowser.php');
include(VIEWS_ROOT.'MediaBrowser_Media.php');
include(VIEWS_ROOT.'MediaList.php');
include(VIEWS_ROOT.'MediaItem.php');
require_once(VIEWS_ROOT.'ListItem.php');
include(VIEWS_ROOT.'UploadScreen.php');
include(VIEWS_ROOT.'AddVideoStream.php');

class Media {
	public function showBrowser($activeMedia, $mediaKind, $options="limit=0,50") {
		$mediaDAO = new \DAOS\Media;
		$media = $mediaDAO->findMediaByKind($mediaKind, $options);
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
	
	public function create($fileName, $mediaKind) {
		$mediaDAO = new \DAOS\Media;
		$mediaDAO->create($fileName, $mediaKind);
	}
	
	public function showUploadScreen() {
		new \Views\UploadScreen();
	}
	
	public function showAddVideoStream() {
		new \Views\AddVideoStream();
	}
	
	public function addVideoStream($vimeoUserId) {
		$mediaDAO = new \DAOS\Media;
		$mediaDAO->addVideoStream($vimeoUserId);
	}
	
	public function refreshVideoStreams() {
		$mediaDAO = new \DAOS\Media;
		$mediaDAO->refreshVideoStreams();
	}
	
	public function saveSingleVideo($url) {
		
		$pos1 = preg_match("/youtube/i", $url);
		$pos2 = preg_match("/vimeo/i", $url);
		
		if($pos1) {
			echo "this is a youtube video!";
			$this->saveYoutubeVideo($url);
		}
		
		elseif($pos2) {
			echo "this is a vimeo video!";
			$this->saveVimeoVideo($url);
		}
				
	}
	
	private function saveYoutubeVideo($url) {
		$mediaDAO = new \DAOS\Media;
		$pos = strrpos($url, "/") + 1;
		$id = substr(stristr(substr($url, $pos), "="), 1);
		$embedCode = '<iframe width="560" height="315" src="http://www.youtube.com/embed/'.$id.'" frameborder="0" allowfullscreen></iframe>';
		$thumb = 'http://img.youtube.com/vi/'.$id.'/default.jpg';
		
		$mediaDAO->create($thumb, "youtube/embedded", $embedCode, "Youtube video (".$id.")");		
	}

	private function saveVimeoVideo($url) {
		$mediaDAO = new \DAOS\Media;
		$pos = strrpos($url, "/") + 1;
		$id = substr($url, $pos);

		$content = file_get_contents("http://vimeo.com/api/v2/video/".$id.".php");
		if ($content) {
			$item = unserialize($content);
			$id = $item[0]['id'];
			$title = $item[0]['title'];
			$thumb = $item[0]['thumbnail_small'];
			$embedCode = '<iframe src="http://player.vimeo.com/video/'.$id.'?title=0&amp;byline=0&amp;portrait=0&amp;color=ffffff" width="500" height="213" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
			
			$mediaDAO->create($thumb, "vimeo/embedded", $embedCode, $title);								
		}
		else {
			echo "no contents found";
		}


		exit();
		$embedCode = '';
		$thumb = "";
		$mediaDAO->create($thumb, "youtube/embedded", $embedCode, "Youtube video (".$id.")");		
	}
}
?>