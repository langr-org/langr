<?
#============================================================================================================================================================
# 名    稱: Email v 0.07.09
# 功    能: Email 發送處理，支援多人轉發、附件、HTML郵件
# 作    者：Arnold, arnold@addwe.com
# 使用舉例：
#  include_once("mail.tool.php");
#  $Email=new Tool_Email();
#  $Email->setTo("gzdkj@163.net");
#  $Email->setFrom("gzdkj@163.net");
#  $Email->mailCC="arnold@addcn.com";
#  $Email->mailImg="f_4.jpg";
#  $Email->mailAttachments="f_4.jpg";
#  $Email->setSubject("測試！");
#  $Email->setText("測試！");
#  $Email->setHTML("<b><a href=http://love.idv.to>測試！</a></b><img border='0' src='f_4.jpg'>");
#  if ($Email->send()){echo "OK";}
#============================================================================================================================================================

class Tool_Email{ 
      var $mailTo                =  "";                         // 郵件發送地址列表 
      var $mailCC                =  "";                         // 抄送郵件地址列表 
      var $mailBCC               =  "";                         // 匿名郵件地址列表 
      var $mailFrom              =  "";                         // 發送人郵件地址 
      var $mailSubject           =  "";                         // 郵件主題 
      var $mailText              =  "";                         // 純文本郵件內容 
      var $mailHTML              =  "";                         // Html 文本郵件內容 
      var $mailImg               =  "";                         // 郵件圖片
      var $mailAttachments       =  "";                         // 郵件附件列表 

	  /* ----------------------------------------------------------------------------------------------------
		   功能：設置 Email 發送地址，並對Email地址格式進行檢查
				 $inAddress    : 要發送的 Email 地址列表。
	  -------------------------------------------------------------------------------------------------------*/
      function setTo($inAddress){ 
        $addressArray = explode( ",",$inAddress); 
        for($i=0;$i<count($addressArray);$i++){ 
            if($this->checkEmail($addressArray[$i])==false) return false; 
        } 
        $this->mailTo = implode($addressArray, ","); 
        return true; 
	  } 

	  /* ----------------------------------------------------------------------------------------------------
		   功能：設置 Email 抄送地址，並對Email地址格式進行檢查
				 $inAddress    : 要抄送的 Email 地址列表。
	  -------------------------------------------------------------------------------------------------------*/
      function setCC($inAddress){ 
        $addressArray = explode( ",",$inAddress); 
        for($i=0;$i<count($addressArray);$i++){ 
            if($this->checkEmail($addressArray[$i])==false) return false; 
        } 
        $this->mailCC = implode($addressArray, ","); 
        return true; 
	  } 

	  /* ----------------------------------------------------------------------------------------------------
		   功能：設置 Email 匿名發送郵件地址列表，並對Email地址格式進行檢查
				 $inAddress    : 要匿名發送的 Email 地址列表。
	  -------------------------------------------------------------------------------------------------------*/
      function setBCC($inAddress){ 
        $addressArray = explode( ",",$inAddress); 
        for($i=0;$i<count($addressArray);$i++){ 
            if($this->checkEmail($addressArray[$i])==false) return false; 
        } 
        $this->mailBCC = implode($addressArray, ","); 
        return true; 
      } 

	  /* ----------------------------------------------------------------------------------------------------
		   功能：設置 Email 發送人郵件地址，並對Email地址格式進行檢查
				 $inAddress    : 發送人郵件地址。
	  -------------------------------------------------------------------------------------------------------*/
      function setFrom($inAddress){ 
        if($this->checkEmail($inAddress)){ 
            $this->mailFrom = $inAddress; 
            return true; 
        } 
        return false; 
      } 

	  /* ----------------------------------------------------------------------------------------------------
		   功能：設置 Email 郵件主題
				 $inSubject    : 郵件主題。
	  -------------------------------------------------------------------------------------------------------*/
      function setSubject($inSubject){ 
        if(strlen(trim($inSubject)) > 0){ 
            $this->mailSubject = eregi_replace( "\n", "",$inSubject); 
            return true; 
        } 
        return false; 
      } 

	  /* ----------------------------------------------------------------------------------------------------
		   功能：設置 Email 純文本格式郵件內容
				 $inText    : 純文本格式郵件內容。
	  -------------------------------------------------------------------------------------------------------*/
      function setText($inText){ 
        if(strlen(trim($inText)) > 0){ 
            $this->mailText = $inText; 
            return true; 
        } 
        return false; 
      } 

	  /* ----------------------------------------------------------------------------------------------------
		   功能：設置 Email HTML格式郵件內容
				 $inHTML    : HTML格式郵件內容。
	  -------------------------------------------------------------------------------------------------------*/
      function setHTML($inHTML){ 
        if(strlen(trim($inHTML)) > 0){ 
            $this->mailHTML = $inHTML; 
            return true; 
        } 
        return false; 
      } 

	  /* ----------------------------------------------------------------------------------------------------
		   功能：設置 Email 郵件中的圖片
				 $images    : 郵件中的圖片。
	  -------------------------------------------------------------------------------------------------------*/
      function setHtmlImages($images){ 
        if(strlen(trim($images)) > 0){ 
            $this->mailImg = $images; 
            return true; 
        }         
        return false; 
      } 

	  /* ----------------------------------------------------------------------------------------------------
		   功能：設置 Email 郵件中的附件
				 $inAttachments    : 郵件中的附件。
	  -------------------------------------------------------------------------------------------------------*/
      function setAttachments($inAttachments){ 
        if(strlen(trim($inAttachments)) > 0){ 
            $this->mailAttachments = $inAttachments; 
            return true; 
        }         
        return false; 
      } 

	  /* ----------------------------------------------------------------------------------------------------
		   功能：檢查 Email 格式正確性
				 $inAddress    : Email地址。
	  -------------------------------------------------------------------------------------------------------*/
      function checkEmail($inAddress){ 
        $check_result=(ereg("^[^@ ]+@([a-zA-Z0-9\-]+\.)+([a-zA-Z0-9\-]{2}|net|com|gov|mil|org|edu|int)\$",$inAddress)); 
        if (!$check_result) {
        	$this->err_num="402";
        } 
        return $check_result;
      } 

	  /* ----------------------------------------------------------------------------------------------------
		   功能：獲取一個隨機的分界字串
				 $offset    : 偏移量。
	  -------------------------------------------------------------------------------------------------------*/
      function getRandomBoundary($offset = 0){ 
        srand(time()+$offset); // 設置亂數種子
        return ( "----".(md5(rand()))); // 獲取一個 4 位元到32位元的字串
      } 

	  /* ----------------------------------------------------------------------------------------------------
		   功能：獲取文件類型
				 $inFileName    : 檔案名。
	  -------------------------------------------------------------------------------------------------------*/
	  function getContentType($inFileName){ 
        $inFileName = basename($inFileName); // 傳回不含路徑的檔案名稱
        if (strrchr($inFileName, ".") == false){ // 檢查是否有副檔名
           return  "application/octet-stream"; 
        } 
        $extension = strrchr($inFileName, "."); // 根據副檔名得到文件類型
        switch($extension){ 
            case  ".gif":    return  "image/gif"; 
            case  ".gz":     return  "application/x-gzip"; 
            case  ".htm":    return  "text/html";
            case  ".php":    return  "text/html";
            case  ".shtml":  return  "text/html";   
            case  ".html":   return  "text/html"; 
            case  ".jpg":    return  "image/jpeg"; 
            case  ".tar":    return  "application/x-tar"; 
            case  ".txt":    return  "text/plain"; 
            case  ".zip":    return  "application/zip"; 
            default:         return  "application/octet-stream"; 
        } 
        return  "application/octet-stream"; 
      } 

	  /* ----------------------------------------------------------------------------------------------------
		   功能：格式化文本頭部資訊
	  -------------------------------------------------------------------------------------------------------*/
      function formatTextHeader(){ 
        $outTextHeader =  ""; 
        $outTextHeader .=  "Content-Type: text/plain; charset=".CHAR_SET."\n"; 
        $outTextHeader .=  "Content-Transfer-Encoding: 7bit\n\n"; 
        $outTextHeader .= $this->mailText. "\n"; 
        return $outTextHeader; 
      } 

	  /* ----------------------------------------------------------------------------------------------------
		   功能：格式化HTML頭部資訊
	  -------------------------------------------------------------------------------------------------------*/
	  function formatHTMLHeader(){ 
        $outHTMLHeader =  ""; 
        $outHTMLHeader .=  "Content-Type: text/html; charset=".CHAR_SET."\n";                                  
        $outHTMLHeader .=  "Content-Transfer-Encoding: 7bit\n\n"; 
        $outHTMLHeader .= $this->mailHTML. "\n"; 
        return $outHTMLHeader; 
      } 

	  /* ----------------------------------------------------------------------------------------------------
		   功能：格式化圖片頭部資訊
				 $inFileLocation    : 文件位置。
	  -------------------------------------------------------------------------------------------------------*/
      function formatImgHeader($inFileLocation){ 
        $outImgHeader =  ""; 
        $contentType = $this->getContentType($inFileLocation); // 根據文件副檔名獲取文件類型
        // 格式化頭部資訊
        $outImgHeader .=  "Content-Type: ".$contentType. ";\n"; 
        $outImgHeader .=  ' name="'.basename($inFileLocation). '"'. "\n"; 
        $outImgHeader .=  "Content-Transfer-Encoding: base64 \n";
        $outImgHeader .=  "Content-ID:<".basename($inFileLocation).">\n\n";
        exec( "uuencode -m $inFileLocation nothing_out",$returnArray); 
        for ($i=1;$i<(count($returnArray));$i++){ // 增加每一行返回的值
            $outImgHeader .= $returnArray[$i]. "\n"; 
        } 
        $outImgHeader .=  "\n"; 
        return $outImgHeader; 
      } 

	  /* ----------------------------------------------------------------------------------------------------
		   功能：格式化附件頭部資訊
				 $inFileLocation    : 文件位置。
	  -------------------------------------------------------------------------------------------------------*/
      function formatAttachmentHeader($inFileLocation){ 
        $outAttachmentHeader =  ""; 
        $contentType = $this->getContentType($inFileLocation); // 根據文件副檔名獲取文件類型
        if (ereg( "text",$contentType)){ // 如果內容類型是 TEXT 的標準 7 位元編碼
           $outAttachmentHeader .=  "Content-Type: ".$contentType. ";\n"; 
           $outAttachmentHeader .=  ' name="'.basename($inFileLocation). '"'. "\n"; 
           $outAttachmentHeader .=  "Content-Transfer-Encoding: 7bit\n"; 
           $outAttachmentHeader .=  "Content-Disposition: attachment;\n";
           $outAttachmentHeader .=  ' filename="'.basename($inFileLocation). '"'. "\n\n"; 
           $textFile = fopen($inFileLocation, "r"); 
           while (!feof($textFile)){ 
             $outAttachmentHeader .= fgets($textFile,1000); 
           } 
           fclose($textFile); 
           $outAttachmentHeader .=  "\n"; 
        } else{ // 非 TEXT 文件，64 位元編碼
            $outAttachmentHeader .=  "Content-Type: ".$contentType. ";\n"; 
            $outAttachmentHeader .=  ' name="'.basename($inFileLocation). '"'. "\n"; 
            $outAttachmentHeader .=  "Content-Transfer-Encoding: base64\n"; 
            $outAttachmentHeader .=  "Content-Disposition: attachment;\n"; 
            $outAttachmentHeader .=  ' filename="'.basename($inFileLocation). '"'. "\n\n";
            exec( "uuencode -m $inFileLocation nothing_out",$returnArray); 
            for ($i = 1; $i<(count($returnArray)); $i++){  
                $outAttachmentHeader .= $returnArray[$i]. "\n"; 
            } 
        }     
        return $outAttachmentHeader; 
      } 

	  /******************************************************************************* 
		   功能：發送 E-mail
	  *******************************************************************************/ 
      function send(){ 
        $mailHeader =  ""; // 初始化郵件頭部資訊
        if ($this->mailCC !=  "") $mailHeader .=  "CC: ".$this->mailCC. "\n"; // 郵件頭部資訊中增加抄送郵件地址列表
        if ($this->mailBCC !=  "") $mailHeader .=  "BCC: ".$this->mailBCC. "\n"; // 郵件頭部資訊中增加匿名郵件地址列表 
        if ($this->mailFrom !=  "") $mailHeader .=  "FROM: ".$this->mailFrom. "\n"; // 郵件頭部資訊中增加發送人郵件地址

        // 僅純文本
        if ($this->mailText !=  "" && $this->mailHTML ==  "" && $this->mailAttachments ==  ""){ 
           $mail_result=@mail($this->mailTo,$this->mailSubject,$this->mailText,$mailHeader); 
           if ($mail_result) {
	          $this->err_num="400";  
	       } else {
	          $this->err_num="401";  
	       }
	       return $mail_result;
        }
		
        // HTML 與 文本
        else if ($this->mailText !=  "" && $this->mailHTML !=  "" && $this->mailAttachments ==  ""){ 
             $bodyBoundary = $this->getRandomBoundary(); // 獲取一個隨機的分界字串
             $textHeader = $this->formatTextHeader(); // 格式化文本頭部資訊
             $htmlHeader = $this->formatHTMLHeader(); // 格式化HTML頭部資訊
             $mailHeader .=  "MIME-Version: 1.0\n"; // 設置 MIME 版本
             $mailHeader .=  "Content-Type: multipart/alternative;\n"; // 建立主內容頭部資訊與分界字串
             $mailHeader .=  ' boundary="'.$bodyBoundary. '"'; 
             $mailHeader .=  "\n\n\n"; 
             $mailHeader .=  "--".$bodyBoundary. "\n"; // 增加主體內容和分界字串
             $mailHeader .= $textHeader; 
             $mailHeader .=  "--".$bodyBoundary. "\n"; 
             $mailHeader .= $htmlHeader; // 增加 HTML 和結尾分界字串
             if ($this->mailImg!="")
             { 
                 $ImgArray = explode( ",",$this->mailImg); // 增加包含圖片的 HTML 內容
                 for ($i=0;$i<count($ImgArray);$i++){ 
                 $mailHeader .=  "\n--".$bodyBoundary. "\n"; 
                 $mailHeader .= $this->formatImgHeader($ImgArray[$i]); 
              } 
            } 
            $mailHeader .=  "\n--".$bodyBoundary. "--";
            $mail_result=@mail($this->mailTo,$this->mailSubject, "",$mailHeader); // 發送郵件
            if ($mail_result) {
	           $this->err_num="400";  
  	        } else {
	           $this->err_num="401";  
     	    }
            return $mail_result;
        } 

		// HTML 與 TEXT 與 附件
        else if($this->mailText !=  "" && $this->mailHTML !=  "" && $this->mailAttachments !=  ""){ 
             $attachmentBoundary = $this->getRandomBoundary(); 
             $mailHeader .=  "Content-Type: multipart/mixed;\n"; 
             $mailHeader .=  ' boundary="'.$attachmentBoundary. '"'. "\n\n"; 
             $mailHeader .=  "This is a multi-part message in MIME format.\n"; 
             $mailHeader .=  "--".$attachmentBoundary. "\n"; 
             
             // TEXT 與 HTML
             $bodyBoundary = $this->getRandomBoundary(1); 
             $textHeader = $this->formatTextHeader(); 
             $htmlHeader = $this->formatHTMLHeader(); 
             $mailHeader .=  "MIME-Version: 1.0\n"; 
             $mailHeader .=  "Content-Type: multipart/alternative;\n"; 
             $mailHeader .=  ' boundary="'.$bodyBoundary. '"'; 
             $mailHeader .=  "\n\n\n"; 
             $mailHeader .=  "--".$bodyBoundary. "\n"; 
             $mailHeader .= $textHeader; 
             $mailHeader .=  "--".$bodyBoundary. "\n"; 
             $mailHeader .= $htmlHeader; 
             if($this->mailImg!="")
			 { 
               $ImgArray = explode( ",",$this->mailImg); 
               for ($i=0;$i<count($ImgArray);$i++){ 
                   $mailHeader .=  "\n--".$bodyBoundary. "\n"; 
                   $mailHeader .= $this->formatImgHeader($ImgArray[$i]); 
               } 
             } 
             $mailHeader .=  "\n--".$bodyBoundary. "--"; 
             $attachmentArray = explode( ",",$this->mailAttachments); 
             for ($i=0;$i<count($attachmentArray);$i++){ 
                 $mailHeader .=  "\n--".$attachmentBoundary. "\n"; 
                 $mailHeader .= $this->formatAttachmentHeader($attachmentArray[$i]); 
             } 
             $mailHeader .=  "--".$attachmentBoundary. "--";
             $mail_result=@mail($this->mailTo,$this->mailSubject, "",$mailHeader); 
             if ($mail_result) {
	            $this->err_num="400";  
             } else {
	    	    $this->err_num="401";  
	         }
             return $mail_result;
        }
        return false; 
      } 
}
?>