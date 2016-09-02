<?php
/*
 功 能：圖像處理 (目前僅實現縮略圖功能)
 作 者：lalun, lalun@addcn.com
 用 例：
	include_once(FILE_PATH."include/tools/image.tool.php");
	$image = new tool_image;
	$image->srcFile = $fileName;
	if ( !$image->resizeTo(90, 90, $thumbName) ) {
		echo $image->ErrMsg;
	}
*/
class tool_image {
	var $srcFile;       // 源文件名
	var $type,$ext;     // 圖像類型(GD 常量)，擴展名
	var $srcW, $srcH;   // 源文件長寬
	var $ErrMsg;        // 錯誤消息
	var $srcImg;        // 由源文件生成的圖像資源
	var $exif = array(  // 已支持的圖片類型
		IMAGETYPE_GIF  => 'gif',
		IMAGETYPE_JPEG => 'jpg',
		IMAGETYPE_PNG  => 'png'
	);
	// 生成縮略圖
	function resizeTo($w, $h, $destFile) {
		if ( !$this->readFile() ) return false;
		if ( !$thumb = $this->resize($this->srcImg, $w, $h) ) return false;
		if ( !$this->saveFile($thumb, $destFile) ) return false;
		return true;
	}
	// 生成頭像
	function makeAvatar($w, $h, $destFile) {
		if ( !$this->readFile() ) return false;
		if ( !$thumb = $this->resize($this->srcImg, $w, $h) ) return false;
		if ( !$this->saveAvatar($thumb, $destFile) ) return false;
		return true;
	}
	// 讀取源圖像文件
	function readFile(){
		if ( !isset($this->srcFile) ){
			$this->ErrMsg = "請指定源文件";
			return false;
		}
		if ( !file_exists($this->srcFile) ){
			$this->ErrMsg = "找不到源文件";
			return false;
		}
		if ( !is_readable($this->srcFile) ){
			$this->ErrMsg = "無法讀取源文件";
			return false;
		}
		if ( !list($this->srcW, $this->srcH, $this->type) = @getimagesize($this->srcFile) ){
			$this->ErrMsg = "源文件不是有效的圖像文件";
			return false;
		}
		if ( !array_key_exists($this->type, $this->exif) ) {
			$this->ErrMsg = "不支持的圖像格式";
			return false;
		}
		$this->ext = $this->exif[$this->type];
		$supportTypes = imagetypes();
		switch ($this->type) {
			case IMAGETYPE_GIF:
				if (!$supportTypes & IMAGETYPE_GIF) {
					$this->ErrMsg = "GD 庫不支持 GIF";
					return false;
				} else $this->srcImg = @ImageCreateFromGIF($this->srcFile); break;
			case IMAGETYPE_JPEG:
				if (!$supportTypes & IMAGETYPE_JPEG) {
					$this->ErrMsg = "GD 庫不支持 JPEG";
					return false;
				} else $this->srcImg = @ImageCreateFromJpeg($this->srcFile); break;
			case IMAGETYPE_PNG:
				if (!$supportTypes & IMAGETYPE_PNG) {
					$this->ErrMsg = "GD 庫不支持 PNG";
					return false;
				} else $this->srcImg = @ImageCreateFromPng($this->srcFile); break;
			default:
				$this->ErrMsg = "未知的圖像格式";
				return false;
		}
		if ( !is_resource($this->srcImg) ){
			$this->ErrMsg = "建立圖像資源失敗";
			return false;
		} else {
			return true;
		}
	}
	// 生成縮略圖 (內部調用)
	function resize($image, $width, $height){
		$srcW = imagesx($image);
		$srcH = imagesy($image);
		$toWH  = $width/$height;
		$srcWH = $srcW/$srcH;
		if ($toWH <= $srcWH) {
			$height = $width/$srcWH;
		} else {
			$width  = $height*$srcWH;
		}
		if ($srcW>$width || $srcH>$height) {
			if (function_exists("imagecreatetruecolor")) {
				@$ni = ImageCreateTrueColor($width, $height);
				if ($ni) {
					ImageCopyResampled($ni,$image,0,0,0,0,$width,$height,$srcW,$srcH);
				} else {
					$ni=ImageCreate($width,$height);
					ImageCopyResized($ni,$image,0,0,0,0,$width,$height,$srcW,$srcH);
				}
			} else {
				$ni=ImageCreate($width,$height);
				ImageCopyResized($ni,$image,0,0,0,0,$width,$height,$srcW,$srcH);
			}
			if ( !is_resource($ni) ){
				$this->ErrMsg = "生成縮略圖失敗";
				return false;
			} else {
				return $ni;
			}
		} else {
			return $image;
		}
	}
	// 保存文件（內部調用）
	function saveFile($img, $filename) {
		if ( file_exists($filename) ) unlink($filename);
		switch ($this->type) {
			case IMAGETYPE_GIF:
				imagegif($img, $filename); break;
			case IMAGETYPE_JPEG:
				imagejpeg($img, $filename); break;
			case IMAGETYPE_PNG:
				imagepng($img, $filename); break;
			default:
				$this->ErrMsg = "未知圖像格式";
				return false;
		}
		if ( !file_exists($filename) ){
			$this->ErrMsg = "保存文件失敗";
			return false;
		} else {
			return true;
		}
	}
	// 保存頭像（內部調用）
	function saveAvatar($img, $filename) {
		if ( file_exists($filename) ) unlink($filename);
		imagegif($img, $filename);
		if ( !file_exists($filename) ){
			$this->ErrMsg = "保存文件失敗";
			return false;
		} else {
			return true;
		}
	}
}
?>
