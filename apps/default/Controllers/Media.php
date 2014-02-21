<?php
/* Media Controller PHP */

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

class Controllers_Media {
	public function showBrowser($activeMedia, $mediaKind, $options="limit=0,50") {
		$mediaDAO = new DAOS_Media;
		$media = $mediaDAO->findMediaByKind($mediaKind, $options);
		new Views_MediaBrowser($media, $activeMedia, $options);
	}

	public function showPicker($activeMedia, $options="limit=0,50") {
		$mediaDAO = new DAOS_Media;
		$media = $mediaDAO->findAllMedia($options);
		new Views_ImagePicker($media, $activeMedia, $options);
	}

	public function appendToBrowser($activeMedia, $options="limit=0,50") {
		$mediaDAO = new DAOS_Media;
		$media = $mediaDAO->findAllMedia($options);
		new Views_MediaBrowser_Media($media, $activeMedia, $options);
	}

	public function appendToPicker($options="limit=0,50") {
		$mediaDAO = new DAOS_Media;
		$media = $mediaDAO->findAllMedia($options);
		new Views_MediaBrowser_Media($media, $activeMedia=array(), $options);
	}

	public function showList($options="limit=0,50", $kind="all", $append=0) {
		$mediaDAO = new DAOS_Media;
		switch($kind) {
			default:
			case "all":
				$media = $mediaDAO->findAllMedia($options);
				break;
			case "videos":
					$media = $mediaDAO->findAllVideos($options);
					break;
			case "photos":
					$media = $mediaDAO->findAllImages($options);
					break;
			case "batch":
					$date = preg_replace("/^.*id=/", "", $options);
					$media = $mediaDAO->findMediaByCreationDate($date, $options);
					break;

		}
		new Views_MediaList($media, $options, $kind, $append);
	}

	public function show($id) {
		$mediaDAO = new DAOS_Media;
		$mediaItem = $mediaDAO->findMediaById($id);
		new Views_MediaItem($mediaItem);
	}

	public function update($vars) {
		$mediaDAO = new DAOS_Media;
		$mediaItem = $mediaDAO->update($vars['id'], $vars);
	}

	public function delete($id) {
		$mediaDAO = new DAOS_Media;
		$mediaItem = $mediaDAO->delete($id);
	}

	public function deleteBatch($batchId) {
		$mediaDAO = new DAOS_Media;
		$mediaItems = $mediaDAO->findMediaByCreationDate($batchId, "limit=99999999");
		foreach($mediaItems as $mediaItem):
		$mediaDAO->delete($mediaItem->id);
		endforeach;
	}

	public function create($fileName, $mediaKind, $created) {
		$mediaDAO = new DAOS_Media;
		$id = $mediaDAO->create($fileName, $mediaKind, null, null, $created);
		echo $id;
	}

	public function uploadImage($vars) {
		$fileName =  uniqid() . "_" . strip_tags($vars['name']);
		$created = strip_tags($vars['creationdate']);
		$mimeType = strip_tags($vars['type']);
		$data = $this->getFileData(strip_tags($vars['data']));
		$this->writeFile($data, $fileName);
		$this->create($fileName, $mimeType, $created);
		$this->generateThumbnail(UPLOADS_PATH.$fileName, $fileName);
	}

	public function showUploadScreen() {
		new Views_UploadScreen();
	}

	public function showAddVideoStream() {
		new Views_UploadScreen;
	}

	public function addVideoStream($vimeoUserId) {
		$mediaDAO = new DAOS_Media;
		$mediaDAO->addVideoStream($vimeoUserId);
	}

	public function refreshVideoStreams() {
		$mediaDAO = new DAOS_Media;
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

	private function getFileData($str) {
    	list($type, $data) = explode(',', $str);
    	return $data;
	}

	private function writeFile($data, $fileName) {
	    $file = fopen(UPLOADS_PATH . $fileName, "w") or die("can't open file");
	    fwrite($file, base64_decode($data));
	    fclose($file);
	}

	private function saveYoutubeVideo($url) {
		$mediaDAO = new DAOS_Media;
		$pos = strrpos($url, "/") + 1;
		$id = substr(stristr(substr($url, $pos), "="), 1);
		$embedCode = '<iframe width="560" height="315" src="http://www.youtube.com/embed/'.$id.'" frameborder="0" allowfullscreen></iframe>';
		$thumb = 'http://img.youtube.com/vi/'.$id.'/default.jpg';

		$mediaDAO->create($thumb, "youtube/embedded", $embedCode, "Youtube video (".$id.")");
	}

	private function saveVimeoVideo($url) {
		$mediaDAO = new DAOS_Media;
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

	private function generateThumbnail($targetFile, $fileName) {
		$imgsize = getimagesize($targetFile);
			switch(strtolower(substr($targetFile, -3))){
			    case "jpg":
			    case "peg":
			        $image = imagecreatefromjpeg($targetFile);
			    break;
			    case "png":
			        $image = imagecreatefrompng($targetFile);
			    break;
			    case "gif":
			        $image = imagecreatefromgif($targetFile);
			    break;
			    default:
			        exit;
			    break;
			}

			$width = MAX_THUMBS_PX; //New width of image
			$height = $imgsize[1]/$imgsize[0]*$width; //This maintains proportions

			$src_w = $imgsize[0];
			$src_h = $imgsize[1];

			$picture = imagecreatetruecolor($width, $height);
			imagealphablending($picture, false);
			imagesavealpha($picture, true);
			$bool = imagecopyresampled($picture, $image, 0, 0, 0, 0, $width, $height, $src_w, $src_h);

			if($bool){
			    switch(strtolower(substr($targetFile, -3))){
			        case "jpg":
			        case "peg":
			            header("Content-Type: image/jpeg");
						$mediaType = "image/jpeg";
			            $bool2 = imagejpeg($picture,THUMBS_PATH.$fileName,90);
			        break;
			        case "png":
			            header("Content-Type: image/png");
						$mediaType = "image/png";
			            imagepng($picture,THUMBS_PATH.$fileName);
			        break;
			        case "gif":
			            header("Content-Type: image/gif");
						$mediaType = "image/gif";
			            imagegif($picture,THUMBS_PATH.$fileName);
			        break;
			    }
			}

			imagedestroy($picture);
			imagedestroy($image);
	}
}
?>