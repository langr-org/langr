<?
#============================================================================================================================================================
# ��    �Q: Email v 0.07.09
# ��    ��: Email �l��̎��֧Ԯ�����D�l��������HTML�]��
# ��    �ߣ�Arnold, arnold@addwe.com
# ʹ���e����
#  include_once("mail.tool.php");
#  $Email=new Tool_Email();
#  $Email->setTo("gzdkj@163.net");
#  $Email->setFrom("gzdkj@163.net");
#  $Email->mailCC="arnold@addcn.com";
#  $Email->mailImg="f_4.jpg";
#  $Email->mailAttachments="f_4.jpg";
#  $Email->setSubject("�yԇ��");
#  $Email->setText("�yԇ��");
#  $Email->setHTML("<b><a href=http://love.idv.to>�yԇ��</a></b><img border='0' src='f_4.jpg'>");
#  if ($Email->send()){echo "OK";}
#============================================================================================================================================================

class Tool_Email{ 
      var $mailTo                =  "";                         // �]���l�͵�ַ�б� 
      var $mailCC                =  "";                         // �����]����ַ�б� 
      var $mailBCC               =  "";                         // �����]����ַ�б� 
      var $mailFrom              =  "";                         // �l�����]����ַ 
      var $mailSubject           =  "";                         // �]�����} 
      var $mailText              =  "";                         // ���ı��]������ 
      var $mailHTML              =  "";                         // Html �ı��]������ 
      var $mailImg               =  "";                         // �]���DƬ
      var $mailAttachments       =  "";                         // �]�������б� 

	  /* ----------------------------------------------------------------------------------------------------
		   ���ܣ��O�� Email �l�͵�ַ���K��Email��ַ��ʽ�M�Йz��
				 $inAddress    : Ҫ�l�͵� Email ��ַ�б�
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
		   ���ܣ��O�� Email ���͵�ַ���K��Email��ַ��ʽ�M�Йz��
				 $inAddress    : Ҫ���͵� Email ��ַ�б�
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
		   ���ܣ��O�� Email �����l���]����ַ�б��K��Email��ַ��ʽ�M�Йz��
				 $inAddress    : Ҫ�����l�͵� Email ��ַ�б�
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
		   ���ܣ��O�� Email �l�����]����ַ���K��Email��ַ��ʽ�M�Йz��
				 $inAddress    : �l�����]����ַ��
	  -------------------------------------------------------------------------------------------------------*/
      function setFrom($inAddress){ 
        if($this->checkEmail($inAddress)){ 
            $this->mailFrom = $inAddress; 
            return true; 
        } 
        return false; 
      } 

	  /* ----------------------------------------------------------------------------------------------------
		   ���ܣ��O�� Email �]�����}
				 $inSubject    : �]�����}��
	  -------------------------------------------------------------------------------------------------------*/
      function setSubject($inSubject){ 
        if(strlen(trim($inSubject)) > 0){ 
            $this->mailSubject = eregi_replace( "\n", "",$inSubject); 
            return true; 
        } 
        return false; 
      } 

	  /* ----------------------------------------------------------------------------------------------------
		   ���ܣ��O�� Email ���ı���ʽ�]������
				 $inText    : ���ı���ʽ�]�����ݡ�
	  -------------------------------------------------------------------------------------------------------*/
      function setText($inText){ 
        if(strlen(trim($inText)) > 0){ 
            $this->mailText = $inText; 
            return true; 
        } 
        return false; 
      } 

	  /* ----------------------------------------------------------------------------------------------------
		   ���ܣ��O�� Email HTML��ʽ�]������
				 $inHTML    : HTML��ʽ�]�����ݡ�
	  -------------------------------------------------------------------------------------------------------*/
      function setHTML($inHTML){ 
        if(strlen(trim($inHTML)) > 0){ 
            $this->mailHTML = $inHTML; 
            return true; 
        } 
        return false; 
      } 

	  /* ----------------------------------------------------------------------------------------------------
		   ���ܣ��O�� Email �]���еĈDƬ
				 $images    : �]���еĈDƬ��
	  -------------------------------------------------------------------------------------------------------*/
      function setHtmlImages($images){ 
        if(strlen(trim($images)) > 0){ 
            $this->mailImg = $images; 
            return true; 
        }         
        return false; 
      } 

	  /* ----------------------------------------------------------------------------------------------------
		   ���ܣ��O�� Email �]���еĸ���
				 $inAttachments    : �]���еĸ�����
	  -------------------------------------------------------------------------------------------------------*/
      function setAttachments($inAttachments){ 
        if(strlen(trim($inAttachments)) > 0){ 
            $this->mailAttachments = $inAttachments; 
            return true; 
        }         
        return false; 
      } 

	  /* ----------------------------------------------------------------------------------------------------
		   ���ܣ��z�� Email ��ʽ���_��
				 $inAddress    : Email��ַ��
	  -------------------------------------------------------------------------------------------------------*/
      function checkEmail($inAddress){ 
        $check_result=(ereg("^[^@ ]+@([a-zA-Z0-9\-]+\.)+([a-zA-Z0-9\-]{2}|net|com|gov|mil|org|edu|int)\$",$inAddress)); 
        if (!$check_result) {
        	$this->err_num="402";
        } 
        return $check_result;
      } 

	  /* ----------------------------------------------------------------------------------------------------
		   ���ܣ��@ȡһ���S�C�ķֽ��ִ�
				 $offset    : ƫ������
	  -------------------------------------------------------------------------------------------------------*/
      function getRandomBoundary($offset = 0){ 
        srand(time()+$offset); // �O�Áy���N��
        return ( "----".(md5(rand()))); // �@ȡһ�� 4 λԪ��32λԪ���ִ�
      } 

	  /* ----------------------------------------------------------------------------------------------------
		   ���ܣ��@ȡ�ļ����
				 $inFileName    : �n������
	  -------------------------------------------------------------------------------------------------------*/
	  function getContentType($inFileName){ 
        $inFileName = basename($inFileName); // ���ز���·���ęn�����Q
        if (strrchr($inFileName, ".") == false){ // �z���Ƿ��и��n��
           return  "application/octet-stream"; 
        } 
        $extension = strrchr($inFileName, "."); // �������n���õ��ļ����
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
		   ���ܣ���ʽ���ı��^���YӍ
	  -------------------------------------------------------------------------------------------------------*/
      function formatTextHeader(){ 
        $outTextHeader =  ""; 
        $outTextHeader .=  "Content-Type: text/plain; charset=".CHAR_SET."\n"; 
        $outTextHeader .=  "Content-Transfer-Encoding: 7bit\n\n"; 
        $outTextHeader .= $this->mailText. "\n"; 
        return $outTextHeader; 
      } 

	  /* ----------------------------------------------------------------------------------------------------
		   ���ܣ���ʽ��HTML�^���YӍ
	  -------------------------------------------------------------------------------------------------------*/
	  function formatHTMLHeader(){ 
        $outHTMLHeader =  ""; 
        $outHTMLHeader .=  "Content-Type: text/html; charset=".CHAR_SET."\n";                                  
        $outHTMLHeader .=  "Content-Transfer-Encoding: 7bit\n\n"; 
        $outHTMLHeader .= $this->mailHTML. "\n"; 
        return $outHTMLHeader; 
      } 

	  /* ----------------------------------------------------------------------------------------------------
		   ���ܣ���ʽ���DƬ�^���YӍ
				 $inFileLocation    : �ļ�λ�á�
	  -------------------------------------------------------------------------------------------------------*/
      function formatImgHeader($inFileLocation){ 
        $outImgHeader =  ""; 
        $contentType = $this->getContentType($inFileLocation); // �����ļ����n���@ȡ�ļ����
        // ��ʽ���^���YӍ
        $outImgHeader .=  "Content-Type: ".$contentType. ";\n"; 
        $outImgHeader .=  ' name="'.basename($inFileLocation). '"'. "\n"; 
        $outImgHeader .=  "Content-Transfer-Encoding: base64 \n";
        $outImgHeader .=  "Content-ID:<".basename($inFileLocation).">\n\n";
        exec( "uuencode -m $inFileLocation nothing_out",$returnArray); 
        for ($i=1;$i<(count($returnArray));$i++){ // ����ÿһ�з��ص�ֵ
            $outImgHeader .= $returnArray[$i]. "\n"; 
        } 
        $outImgHeader .=  "\n"; 
        return $outImgHeader; 
      } 

	  /* ----------------------------------------------------------------------------------------------------
		   ���ܣ���ʽ�������^���YӍ
				 $inFileLocation    : �ļ�λ�á�
	  -------------------------------------------------------------------------------------------------------*/
      function formatAttachmentHeader($inFileLocation){ 
        $outAttachmentHeader =  ""; 
        $contentType = $this->getContentType($inFileLocation); // �����ļ����n���@ȡ�ļ����
        if (ereg( "text",$contentType)){ // ������������ TEXT �Ę˜� 7 λԪ���a
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
        } else{ // �� TEXT �ļ���64 λԪ���a
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
		   ���ܣ��l�� E-mail
	  *******************************************************************************/ 
      function send(){ 
        $mailHeader =  ""; // ��ʼ���]���^���YӍ
        if ($this->mailCC !=  "") $mailHeader .=  "CC: ".$this->mailCC. "\n"; // �]���^���YӍ�����ӳ����]����ַ�б�
        if ($this->mailBCC !=  "") $mailHeader .=  "BCC: ".$this->mailBCC. "\n"; // �]���^���YӍ�����������]����ַ�б� 
        if ($this->mailFrom !=  "") $mailHeader .=  "FROM: ".$this->mailFrom. "\n"; // �]���^���YӍ�����Ӱl�����]����ַ

        // �H���ı�
        if ($this->mailText !=  "" && $this->mailHTML ==  "" && $this->mailAttachments ==  ""){ 
           $mail_result=@mail($this->mailTo,$this->mailSubject,$this->mailText,$mailHeader); 
           if ($mail_result) {
	          $this->err_num="400";  
	       } else {
	          $this->err_num="401";  
	       }
	       return $mail_result;
        }
		
        // HTML �c �ı�
        else if ($this->mailText !=  "" && $this->mailHTML !=  "" && $this->mailAttachments ==  ""){ 
             $bodyBoundary = $this->getRandomBoundary(); // �@ȡһ���S�C�ķֽ��ִ�
             $textHeader = $this->formatTextHeader(); // ��ʽ���ı��^���YӍ
             $htmlHeader = $this->formatHTMLHeader(); // ��ʽ��HTML�^���YӍ
             $mailHeader .=  "MIME-Version: 1.0\n"; // �O�� MIME �汾
             $mailHeader .=  "Content-Type: multipart/alternative;\n"; // �����������^���YӍ�c�ֽ��ִ�
             $mailHeader .=  ' boundary="'.$bodyBoundary. '"'; 
             $mailHeader .=  "\n\n\n"; 
             $mailHeader .=  "--".$bodyBoundary. "\n"; // �������w���ݺͷֽ��ִ�
             $mailHeader .= $textHeader; 
             $mailHeader .=  "--".$bodyBoundary. "\n"; 
             $mailHeader .= $htmlHeader; // ���� HTML �ͽYβ�ֽ��ִ�
             if ($this->mailImg!="")
             { 
                 $ImgArray = explode( ",",$this->mailImg); // ���Ӱ����DƬ�� HTML ����
                 for ($i=0;$i<count($ImgArray);$i++){ 
                 $mailHeader .=  "\n--".$bodyBoundary. "\n"; 
                 $mailHeader .= $this->formatImgHeader($ImgArray[$i]); 
              } 
            } 
            $mailHeader .=  "\n--".$bodyBoundary. "--";
            $mail_result=@mail($this->mailTo,$this->mailSubject, "",$mailHeader); // �l���]��
            if ($mail_result) {
	           $this->err_num="400";  
  	        } else {
	           $this->err_num="401";  
     	    }
            return $mail_result;
        } 

		// HTML �c TEXT �c ����
        else if($this->mailText !=  "" && $this->mailHTML !=  "" && $this->mailAttachments !=  ""){ 
             $attachmentBoundary = $this->getRandomBoundary(); 
             $mailHeader .=  "Content-Type: multipart/mixed;\n"; 
             $mailHeader .=  ' boundary="'.$attachmentBoundary. '"'. "\n\n"; 
             $mailHeader .=  "This is a multi-part message in MIME format.\n"; 
             $mailHeader .=  "--".$attachmentBoundary. "\n"; 
             
             // TEXT �c HTML
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