<?
#============================================================================================================================================================
# 名    稱: UploadFile v 0.5.26
# 功    能: 文件上傳
# 作    者：Arnold, arnold@addwe.com
# 例：
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
	#  外部屬性，可通過外部指定
	#=======================================================================================
	var $File = array();						// 上傳的文件，包括以下元素
											    // [name] =>	 檔案名
												// [type] =>	 文件類型
												// [tmp_name] => 文件在臨時目錄中的名稱
												// [error] =>	 錯誤編號
												// [size] =>     文件大小
	var $NewFilePath;							// 文件上傳後的路徑
	var $NewFileName;							// 文件上傳後的新檔案名，（不帶副檔名）
	var $AcceptFileType;						// 默認充許上傳的文件類型;
	var $FileMaxSize;							// 上傳文件的最大尺寸
	var $ImageMinWidth;							// 圖片的最小寬度
	var $ImageMinHeight;						// 圖片的最小高度
	var $ImageMaxWidth;							// 圖片的最大寬度
	var $ImageMaxHeight;						// 圖片的最大高度


	#=======================================================================================
	#  內部屬性，內部使用，無需外部指定
	#=======================================================================================
	var $FileExtName;				// 上傳文件的副檔名，自動通過判斷文件類型獲得
    var $UploadIsImage = False;		// 標識上傳文件是否圖片，如果是圖片則進行比較寬、高的操作，默認 False，文件類型檢查時如果爲圖片則賦值 True。
	var $ImgSource;					// 圖型流
	var $ErrMsg;					// 錯誤提示資訊

	#=======================================================================================
	#  上傳文件
	#=======================================================================================
	function uploadFile(){
		if (!$this->checkFileArray()) {		// 檢查文件陣列是否合法
			return False;
		}
		if (!$this->checkFileType()) {		// 檢查文件屬性是否合法
			return False;
		}
		if ($this->UploadIsImage) {			// 如果上傳的文件是圖片，則判斷寬、高是否符合要求
			if (!$this->checkImageWidthHeight()) {
				return False;
			}
		}
		if (!$this->checkFileSize()) {		// 檢查文件尺寸
			return False;
		}
		if (!$this->fileCopy()) {			// 複製文件
			return False;
		}

		return True;
	}

	#=======================================================================================
	#  檢查文件陣列
	#=======================================================================================
	function checkFileArray(){
		if (!isset($this->File['name'])) {
			$this->ErrMsg = "錯誤：請指定需要上傳的文件。";
			return False;
		}
		if (!isset($this->File['type'])) {
			$this->ErrMsg = "錯誤：上傳的文件類型錯誤。";
			return False;
		}
		if (!isset($this->File['tmp_name'])) {
			$this->ErrMsg = "錯誤：請指定上傳的文件。";
			return False;
		}
		if (!isset($this->File['error'])) {
			$this->ErrMsg = "錯誤：@請指定需要上傳的文件。";
			return False;
		}
		if (!isset($this->File['size'])) {
			$this->ErrMsg = "錯誤：上傳的文件大小錯誤。";
			return False;
		}
		if ($this->File['error'] != 0) {
			$this->ErrMsg = "錯誤：上傳文件失敗，錯誤編號爲 ".$this->File['error']."。";
			return False;
		}

		return True;
	}	  

	#=======================================================================================
	#  檢查文件屬性
	#=======================================================================================
    function checkFileType(){
		## 初始化 $this->AcceptFileType
		if (!isset($this->AcceptFileType)) {
			$this->ErrMsg = "錯誤：未指定充許上傳的文件類型。";
			return False;			
		} elseif ("image" == $this->AcceptFileType) {
			$this->AcceptFileType = "jpg,gif,png";
		} elseif ("flash" == $this->AcceptFileType) {
			$this->AcceptFileType = "swf";
		} elseif ("all" == $this->AcceptFileType) {
			$this->AcceptFileType = "jpg,gif,png,swf,doc,txt,zip,csv";
		}
		## 判斷文件類型，自動獲取文件副檔名
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
				$this->ErrMsg = "錯誤：上傳的文件類型未知。".$this->File['type'];
				return False;			
				break;			
		}
		## 判斷文件類型是否合法
        $acceptFileTypeArray  = split(",",$this->AcceptFileType);
	    if (!in_array($this->FileExtName,$acceptFileTypeArray)){
	       $this->ErrMsg = "錯誤：文件類型只充許爲 ".$this->AcceptFileType;
		   return False;
	    }
	    return True;
	}

	#=======================================================================================
	#  檢查圖片文件寬度與高度
	#=======================================================================================
	function checkImageWidthHeight(){
		if ("" == $this->ImgSource) $this->imgCreate();	// 建立圖形流
		$imageWidth  = imagesx($this->ImgSource);
		$imageHeight = imagesy($this->ImgSource);
		
		if ((isset($this->ImageMinWidth))&&($this->ImageMinWidth > $imageWidth)){
	       $this->ErrMsg = "錯誤：圖片寬度不能小於 ".$this->ImageMinWidth." 圖元.";
		   return False;
	    }

		if ((isset($this->ImageMinHeight))&&($this->ImageMinHeight > $imageHeight)){
	       $this->ErrMsg = "錯誤：圖片高度不能小於 ".$this->ImageMinHeight." 圖元.";
		   return False;
	    }

		if ((isset($this->ImageMaxWidth))&&($this->ImageMaxWidth < $imageWidth)){
	       $this->ErrMsg = "錯誤：圖片寬度不能大於 ".$this->ImageMaxWidth." 圖元.";
		   return False;
	    }

		if ((isset($this->ImageMaxHeight))&&($this->ImageMaxHeight < $imageHeight)){
	       $this->ErrMsg = "錯誤：圖片高度不能大於 ".$this->ImageMaxHeight." 圖元.";
		   return False;
	    }

		return True;
    }

	#=======================================================================================
	#  建立圖形流
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
	# 文件尺寸檢查
	#=======================================================================================
    function checkFileSize(){
	    if ($this->File['size'] > $this->FileMaxSize){
		   $fileMaxSizeK = floor($this->FileMaxSize/1024);
	       $this->ErrMsg = "錯誤：文件尺寸不能超過 ".$fileMaxSizeK."KB.";
		   return False;
	    }
	    return True;
    }

	#=======================================================================================
	# 文件複製
	#=======================================================================================
    function fileCopy(){
		if (!isset($this->NewFilePath)) {
			$this->ErrMsg = "錯誤：沒有指定上傳後文件的複製路徑。";
			return False;
		}
		if (!isset($this->NewFileName)) {
			$this->ErrMsg = "錯誤：沒有指定上傳後文件的檔案名。";
			return False;
		}

		$newFileName = $this->NewFilePath."/".$this->NewFileName.".".$this->FileExtName;
		if (copy($this->File['tmp_name'],$newFileName)){
			return True;
		} else {
			$this->ErrMsg = "錯誤：文件上傳失敗。";
			return False;
		}		  
	    return True;
    }
}
?>