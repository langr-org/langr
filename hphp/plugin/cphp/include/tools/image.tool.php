<?php
/*
 �� �ܣ��D��̎�� (Ŀǰ�H���F�s�ԈD����)
 �� �ߣ�lalun, lalun@addcn.com
 �� ����
	include_once(FILE_PATH."include/tools/image.tool.php");
	$image = new tool_image;
	$image->srcFile = $fileName;
	if ( !$image->resizeTo(90, 90, $thumbName) ) {
		echo $image->ErrMsg;
	}
*/
class tool_image {
	var $srcFile;       // Դ�ļ���
	var $type,$ext;     // �D�����(GD ����)���Uչ��
	var $srcW, $srcH;   // Դ�ļ��L��
	var $ErrMsg;        // �e�`��Ϣ
	var $srcImg;        // ��Դ�ļ����ɵĈD���YԴ
	var $exif = array(  // ��֧�ֵĈDƬ���
		IMAGETYPE_GIF  => 'gif',
		IMAGETYPE_JPEG => 'jpg',
		IMAGETYPE_PNG  => 'png'
	);
	// ���ɿs�ԈD
	function resizeTo($w, $h, $destFile) {
		if ( !$this->readFile() ) return false;
		if ( !$thumb = $this->resize($this->srcImg, $w, $h) ) return false;
		if ( !$this->saveFile($thumb, $destFile) ) return false;
		return true;
	}
	// �����^��
	function makeAvatar($w, $h, $destFile) {
		if ( !$this->readFile() ) return false;
		if ( !$thumb = $this->resize($this->srcImg, $w, $h) ) return false;
		if ( !$this->saveAvatar($thumb, $destFile) ) return false;
		return true;
	}
	// �xȡԴ�D���ļ�
	function readFile(){
		if ( !isset($this->srcFile) ){
			$this->ErrMsg = "Ոָ��Դ�ļ�";
			return false;
		}
		if ( !file_exists($this->srcFile) ){
			$this->ErrMsg = "�Ҳ���Դ�ļ�";
			return false;
		}
		if ( !is_readable($this->srcFile) ){
			$this->ErrMsg = "�o���xȡԴ�ļ�";
			return false;
		}
		if ( !list($this->srcW, $this->srcH, $this->type) = @getimagesize($this->srcFile) ){
			$this->ErrMsg = "Դ�ļ�������Ч�ĈD���ļ�";
			return false;
		}
		if ( !array_key_exists($this->type, $this->exif) ) {
			$this->ErrMsg = "��֧�ֵĈD���ʽ";
			return false;
		}
		$this->ext = $this->exif[$this->type];
		$supportTypes = imagetypes();
		switch ($this->type) {
			case IMAGETYPE_GIF:
				if (!$supportTypes & IMAGETYPE_GIF) {
					$this->ErrMsg = "GD �첻֧�� GIF";
					return false;
				} else $this->srcImg = @ImageCreateFromGIF($this->srcFile); break;
			case IMAGETYPE_JPEG:
				if (!$supportTypes & IMAGETYPE_JPEG) {
					$this->ErrMsg = "GD �첻֧�� JPEG";
					return false;
				} else $this->srcImg = @ImageCreateFromJpeg($this->srcFile); break;
			case IMAGETYPE_PNG:
				if (!$supportTypes & IMAGETYPE_PNG) {
					$this->ErrMsg = "GD �첻֧�� PNG";
					return false;
				} else $this->srcImg = @ImageCreateFromPng($this->srcFile); break;
			default:
				$this->ErrMsg = "δ֪�ĈD���ʽ";
				return false;
		}
		if ( !is_resource($this->srcImg) ){
			$this->ErrMsg = "�����D���YԴʧ��";
			return false;
		} else {
			return true;
		}
	}
	// ���ɿs�ԈD (�Ȳ��{��)
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
				$this->ErrMsg = "���ɿs�ԈDʧ��";
				return false;
			} else {
				return $ni;
			}
		} else {
			return $image;
		}
	}
	// �����ļ����Ȳ��{�ã�
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
				$this->ErrMsg = "δ֪�D���ʽ";
				return false;
		}
		if ( !file_exists($filename) ){
			$this->ErrMsg = "�����ļ�ʧ��";
			return false;
		} else {
			return true;
		}
	}
	// �����^�񣨃Ȳ��{�ã�
	function saveAvatar($img, $filename) {
		if ( file_exists($filename) ) unlink($filename);
		imagegif($img, $filename);
		if ( !file_exists($filename) ){
			$this->ErrMsg = "�����ļ�ʧ��";
			return false;
		} else {
			return true;
		}
	}
}
?>
