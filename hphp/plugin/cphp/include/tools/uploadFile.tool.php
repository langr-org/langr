<?
#============================================================================================================================================================
# ��    �Q: UploadFile v 0.5.26
# ��    ��: �ļ��ς�
# ��    �ߣ�Arnold, arnold@addwe.com
# ����
#		include_once(FILE_PATH."include/tools/uploadFile.tool.php");
#		$UploadFile = new Tool_UploadFile;
#		$UploadFile->File			= $_FILES["image"];
#		$UploadFile->NewFilePath	= FILE_PATH."data/upload";
#		$UploadFile->NewFileName	= time();
#		$UploadFile->AcceptFileType	= "image";
#		$UploadFile->FileMaxSize	= 10000;
#		$UploadFile->ImageMinWidth  = 150;
#		$UploadFile->ImageMinHeight = 110;
#		$UploadFile->ImageMaxWidth  = 150;
#		$UploadFile->ImageMaxHeight = 110;
#		if (!$UploadFile->uploadFile()) {
#			echo $UploadFile->ErrMsg;
#		}
#============================================================================================================================================================
class Tool_UploadFile
{
	#=======================================================================================
	#  �ⲿ���ԣ���ͨ�^�ⲿָ��
	#=======================================================================================
	var $File = array();						// �ς����ļ�����������Ԫ��
											    // [name] =>	 �n����
												// [type] =>	 �ļ����
												// [tmp_name] => �ļ����R�rĿ��е����Q
												// [error] =>	 �e�`��̖
												// [size] =>     �ļ���С
	var $NewFilePath;							// �ļ��ς����·��
	var $NewFileName;							// �ļ��ς�����n���������������n����
	var $AcceptFileType;						// Ĭ�J���S�ς����ļ����;
	var $FileMaxSize;							// �ς��ļ������ߴ�
	var $ImageMinWidth;							// �DƬ����С����
	var $ImageMinHeight;						// �DƬ����С�߶�
	var $ImageMaxWidth;							// �DƬ����󌒶�
	var $ImageMaxHeight;						// �DƬ�����߶�


	#=======================================================================================
	#  �Ȳ����ԣ��Ȳ�ʹ�ã��o���ⲿָ��
	#=======================================================================================
	var $FileExtName;				// �ς��ļ��ĸ��n�����Ԅ�ͨ�^�Д��ļ���ͫ@��
    var $UploadIsImage = False;		// ���R�ς��ļ��Ƿ�DƬ������ǈDƬ�t�M�б��^�����ߵĲ�����Ĭ�J False���ļ���͙z��r������DƬ�t�xֵ True��
	var $ImgSource;					// �D����
	var $ErrMsg;					// �e�`��ʾ�YӍ

	#=======================================================================================
	#  �ς��ļ�
	#=======================================================================================
	function uploadFile(){
		if (!$this->checkFileArray()) {		// �z���ļ�����Ƿ�Ϸ�
			return False;
		}
		if (!$this->checkFileType()) {		// �z���ļ������Ƿ�Ϸ�
			return False;
		}
		if ($this->UploadIsImage) {			// ����ς����ļ��ǈDƬ���t�Д��������Ƿ����Ҫ��
			if (!$this->checkImageWidthHeight()) {
				return False;
			}
		}
		if (!$this->checkFileSize()) {		// �z���ļ��ߴ�
			return False;
		}
		if (!$this->fileCopy()) {			// �}�u�ļ�
			return False;
		}

		return True;
	}

	#=======================================================================================
	#  �z���ļ����
	#=======================================================================================
	function checkFileArray(){
		if (!isset($this->File['name'])) {
			$this->ErrMsg = "�e�`��Ոָ����Ҫ�ς����ļ���";
			return False;
		}
		if (!isset($this->File['type'])) {
			$this->ErrMsg = "�e�`���ς����ļ�����e�`��";
			return False;
		}
		if (!isset($this->File['tmp_name'])) {
			$this->ErrMsg = "�e�`��Ոָ���ς����ļ���";
			return False;
		}
		if (!isset($this->File['error'])) {
			$this->ErrMsg = "�e�`��@Ոָ����Ҫ�ς����ļ���";
			return False;
		}
		if (!isset($this->File['size'])) {
			$this->ErrMsg = "�e�`���ς����ļ���С�e�`��";
			return False;
		}
		if ($this->File['error'] != 0) {
			$this->ErrMsg = "�e�`���ς��ļ�ʧ�����e�`��̖�� ".$this->File['error']."��";
			return False;
		}

		return True;
	}	  

	#=======================================================================================
	#  �z���ļ�����
	#=======================================================================================
    function checkFileType(){
		## ��ʼ�� $this->AcceptFileType
		if (!isset($this->AcceptFileType)) {
			$this->ErrMsg = "�e�`��δָ�����S�ς����ļ���͡�";
			return False;			
		} elseif ("image" == $this->AcceptFileType) {
			$this->AcceptFileType = "jpg,gif,png";
		} elseif ("flash" == $this->AcceptFileType) {
			$this->AcceptFileType = "swf";
		} elseif ("all" == $this->AcceptFileType) {
			$this->AcceptFileType = "jpg,gif,png,swf,doc,txt,zip,csv";
		}
		## �Д��ļ���ͣ��Ԅӫ@ȡ�ļ����n��
		switch ($this->File['type']){ 
			case "image/pjpeg": 
				$this->FileExtName   = "jpg"; 
				$this->UploadIsImage = True;
			    break; 
			case "image/gif": 
			    $this->FileExtName   = "gif"; 
				$this->UploadIsImage = True;
			    break; 
			case "image/x-png": 
			    $this->FileExtName   = "png"; 
				$this->UploadIsImage = True;
				break; 
			case "application/x-shockwave-flash": 
			    $this->FileExtName = "swf"; 
			    break; 
			case "text/plain": 
			    $this->FileExtName = "txt"; 
			    break; 
			case "application/msword": 
			    $this->FileExtName = "doc"; 
			    break; 
			case "application/x-zip-compressed": 
			    $this->FileExtName = "zip"; 
			    break; 
			case "application/octet-stream":
			    $this->FileExtName = "csv"; 
			    break; 
			default:
				$this->ErrMsg = "�e�`���ς����ļ����δ֪��".$this->File['type'];
				return False;			
				break;			
		}
		## �Д��ļ�����Ƿ�Ϸ�
        $acceptFileTypeArray  = split(",",$this->AcceptFileType);
	    if (!in_array($this->FileExtName,$acceptFileTypeArray)){
	       $this->ErrMsg = "�e�`���ļ����ֻ���S�� ".$this->AcceptFileType;
		   return False;
	    }
	    return True;
	}

	#=======================================================================================
	#  �z��DƬ�ļ������c�߶�
	#=======================================================================================
	function checkImageWidthHeight(){
		if ("" == $this->ImgSource) $this->imgCreate();	// �����D����
		$imageWidth  = imagesx($this->ImgSource);
		$imageHeight = imagesy($this->ImgSource);
		
		if ((isset($this->ImageMinWidth))&&($this->ImageMinWidth > $imageWidth)){
	       $this->ErrMsg = "�e�`���DƬ���Ȳ���С� ".$this->ImageMinWidth." �DԪ.";
		   return False;
	    }

		if ((isset($this->ImageMinHeight))&&($this->ImageMinHeight > $imageHeight)){
	       $this->ErrMsg = "�e�`���DƬ�߶Ȳ���С� ".$this->ImageMinHeight." �DԪ.";
		   return False;
	    }

		if ((isset($this->ImageMaxWidth))&&($this->ImageMaxWidth < $imageWidth)){
	       $this->ErrMsg = "�e�`���DƬ���Ȳ��ܴ�� ".$this->ImageMaxWidth." �DԪ.";
		   return False;
	    }

		if ((isset($this->ImageMaxHeight))&&($this->ImageMaxHeight < $imageHeight)){
	       $this->ErrMsg = "�e�`���DƬ�߶Ȳ��ܴ�� ".$this->ImageMaxHeight." �DԪ.";
		   return False;
	    }

		return True;
    }

	#=======================================================================================
	#  �����D����
	#=======================================================================================
    function imgCreate(){
		switch ($this->File['type']){ 
			case "image/pjpeg": 
				$this->ImgSource = @ImageCreateFromJPEG($this->File['tmp_name']);
			    break; 
			case "image/gif": 
				$this->ImgSource = @ImageCreateFromGIF($this->File['tmp_name']);
			    break; 
			case "image/x-png": 
				$this->ImgSource = @ImageCreateFromPNG($this->File['tmp_name']);
			    break; 
		}
	 }

	#=======================================================================================
	# �ļ��ߴ�z��
	#=======================================================================================
    function checkFileSize(){
	    if ($this->File['size'] > $this->FileMaxSize){
		   $fileMaxSizeK = floor($this->FileMaxSize/1024);
	       $this->ErrMsg = "�e�`���ļ��ߴ粻�ܳ��^ ".$fileMaxSizeK."KB.";
		   return False;
	    }
	    return True;
    }

	#=======================================================================================
	# �ļ��}�u
	#=======================================================================================
    function fileCopy(){
		if (!isset($this->NewFilePath)) {
			$this->ErrMsg = "�e�`���]��ָ���ς����ļ����}�u·����";
			return False;
		}
		if (!isset($this->NewFileName)) {
			$this->ErrMsg = "�e�`���]��ָ���ς����ļ��ęn������";
			return False;
		}

		$newFileName = $this->NewFilePath."/".$this->NewFileName.".".$this->FileExtName;
		if (copy($this->File['tmp_name'],$newFileName)){
			return True;
		} else {
			$this->ErrMsg = "�e�`���ļ��ς�ʧ����";
			return False;
		}		  
	    return True;
    }
}
?>