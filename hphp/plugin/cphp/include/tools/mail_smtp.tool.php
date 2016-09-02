<?php
/*==========================================================================
*����:�ʼ�������:smtp
*����:���Ͱ�������,ͼƬ,HTML,�������ʼ�
*��:
*$mail 			= new Tool_Smtp("210.64.24.30");
*
*$file 			= "as.txt";//����ļ���","����,�����ļ����в�����","
*$html_body = "<html><body><font color='red'>ok,i'm here</font><img src='http://www.tom.com/img/image/tom_skype_020060228165740.gif' /></body></html>";
*$text_body = false;
*$from_name	="tw";
*$to_name		="my";
*$from_mail	="service@twbbs.net.tw";
*$to_mail 	= "tazen_007@56.com";
*$subject 	= "test";
*$mail->send($from_name,$to_name,$from_mail,$to_mail,$subject,$text_body,$html_body,$file);
==============================================================================*/
class Tool_Smtp { 
	var $hostname=""; 
	var $port=25; 
	var $connection=0; 
	var $debug=0; 
	
	var $timeout=30; 
	var $err_str; 
	var $err_no; 
	
	var $autocode=true; 
	var $charset="GB2312"; 
	var $subject=""; 
	var $body=""; 
	var $attach=""; 
	var $temp_text_body; 
	var $temp_html_body; 
	var $temp_body_images; 
	
	var $bound_begin=" "; 
	var $bound_end=" "; 
/*========================================
*�ʼ���������ʼ��
=========================================*/	
	Function Tool_Smtp($server="smtp.china.com",$port=25,$time_out=20) 	{
		$this->hostname=$server; 
		$this->port=$port; 
		$this->timeout=$time_out; 
		return true; 
	} 

/*========================================
*�ʼ����Կ���
=========================================*/	
	function setDebug($de){
		$this->debug = $de;
	}

/*========================================
*�����������
=========================================*/	
	Function outdebug($message) 
	{ 
		echo htmlspecialchars($message)."<br>\n"; 
	} 
	
/*========================================
*���ʼ���������������
=========================================*/		
	function command($command,$return_lenth=1,$return_code='2') 	{ 
		if ($this->connection==0) { 
			$this->err_str="�e�`���]���B�Y���κ��ŷ�����Ո�z��W·�B�Y��"; 
			return false; 
		} 
		
		if ($this->debug) 
			$this->outdebug(">>> $command"); 
		
		if (!fputs($this->connection,"$command \r\n")) 	{ 
			$this->err_str="�e�`���o���l��ָ��".$command; 
			return false; 
		}	else 	{ 
			$resp=fgets($this->connection,256); 
			if($this->debug) 
			$this->outdebug("$resp"); 
		
		if (substr($resp,0,$return_lenth)!=$return_code)	{ 
			$this->err_str="�e�`��".$command." ָ���ŷ������؟oЧ:".$resp; 
			return false; 
		}	else 
		return true; 
		} 
	} 
	
/*========================================
*��SMTP�ʼ���������������
=========================================*/		
	Function open() { 
		if($this->hostname=="") {
			$this->err_str="�e�`���oЧ���ŷ������Q!"; 
			return false; 
		} 
	
		if ($this->debug) 
			echo "$this->hostname,$this->port,&$err_no, &$err_str, $this->timeout<BR>"; 
		
		if (!$this->connection=fsockopen($this->hostname,$this->port,&$err_no, &$err_str, $this->timeout)) { 
			$this->err_str="�e�`���B�Y SMTP �ŷ����򔡣��e�`�YӍ��".$err_str."�e�`��̖��".$err_no; 
			return false; 
		} else { 
			$resp=fgets($this->connection,256); 
			if($this->debug) 
				$this->outdebug("$resp"); 
			
			if (substr($resp,0,1)!="2") {
				$this->err_str="�e�`���ŷ������؟oЧ���YӍ��".$resp."Ո�z��SMTP�ŷ����Ƿ����_��"; 
				return false; 
			} 
			return true; 
		} 
	} 
	
/*========================================
*�ر����ʼ�������������
=========================================*/		
	Function Close() { 
		if($this->connection!=0) { 
			fclose($this->connection); 
			$this->connection=0; 
		} 
	} 
	
/*========================================
*�趨�ʼ�ͷ����Ϣ
=========================================*/	
	Function Buildhead($from_name,$to_name,$from_mail,$to_mail,$subject) { 
		if (empty($from_name)) 
			$from_name=$from_mail; 
	
		if (empty($to_name)) 
			$to_name=$to_mail; 
	
		$this->subject="From: =?$this->charset?B?".base64_encode($from_name)."?=<$from_mail>\r\n"; 
		$this->subject.="To: =?$this->charset?B?".base64_encode($to_name)."?=<$to_mail>\r\n"; 
		$subject=ereg_replace("\n","",$subject); 
		$this->subject.="Subject: =?$this->charset?B?".base64_encode($subject)."?=\r\n"; 
	
		if ($this->debug) 
			echo nl2br(htmlspecialchars($this->subject)); 
		return true; 
	} 
	
/*========================================
*��������ͼƬ���ʼ�
*ע��:��ͼƬ�����������Ͽ��õ�ͼƬ
=========================================*/		
	Function parse_html_body($html_body=null) { 
		$passed=""; 
		$image_count=0; 
		$this->temp_body_images=array(); 
		while (eregi("\<*img([^\>]+)src<:space:>*=<:space:>*([^ ]+)",$html_body,$reg)) { 
			$pos=@strpos($html_body,$reg[0]); 
			$passed.=substr($html_body,0,$pos); 
			$html_body=substr($html_body,$pos+strlen($reg[0])); 
			$image_tag=$reg[2]; 
			$image_att=$reg[1]; 
			$tag_len=strlen($image_tag); 
	
			if ($image_tag[0]=="'" or $image_tag[0]=='"') 
				$image_tag=substr($image_tag,1); 
			if (substr($image_tag,strlen($imgage_tag)-1,1)=="'" or substr($image_tag,strlen($imgage_tag)-1,1)=='"') 
				$image_tag=substr($image_tag,0,strlen($imgage_tag)-1); 
				//echo $image_tag."<br>"; 
			$cid=md5(uniqid(rand())); 
			$cid=substr($cid,0,15)."@unigenius.com"; 
			$passed.="<img ".$image_att."src=\"cid:".$cid."\""; 
			$end_pos=@strpos($html_body,'>'); 
			$passed.=substr($html_body,0,$end_pos); 
			$html_body=substr($html_body,$end_pos); 
			// ��ͼƬ���ݶ��������浽һ�����ݣ� 
	
			$img_file_con=fopen($image_tag,"r"); 
			unset($image_data); 
			while ($tem_buffer=AddSlashes(fread($img_file_con,16777216))) 
				$image_data.=$tem_buffer; 
			fclose($img_file_con); 
			$image_exe_name=substr($image_tag,strrpos($image_tag,'.')+1,3); 
			switch (strtolower($image_exe_name)) { 
				case "jpg": 
				case "jpeg": 
							$content_type="image/jpeg"; 
							break; 
				case "gif": 
							$content_type="image/gif"; 
							break; 
				case "png": 
							$content_type="image/x-png"; 
							break; 
				case "tif": 
							$content_type="image/tif"; 
							break; 
				default: 
							$content_type="image/"; 
							break; 
				} 
	
				$this->temp_body_images[$image_count][name]=basename($image_tag); 
				$this->temp_body_images[$image_count][type]=$content_type; 
				$this->temp_body_images[$image_count][cid]=$cid; 
				$this->temp_body_images[$image_count][data]=$image_data; 
				$image_count++; 
			} 
			$this->temp_html_body=$passed.$html_body; 
			return true; 
	
	} 

/*========================================
*�ʼ����Ĵ���
=========================================*/		
	function build_content($bound_level=0,$text_body,$html_body,$hava_att=false) { 
		if ($html_body) { 
			
			if (eregi("\<*img<:space:>+src<:space:>*=<:space:>*([^ ]+)",$html_body,$reg)) { 
				$bound_level++; 
				
				if ($text_body) { 
					$this->body.="Content-Type: multipart/related; type=\"multipart/alternative\"; \tboundary=\""; 
					$this->body.=$this->bound_begin.$bound_level.$this->bound_end."\"\r\n\r\n"; 
					} else { 
						$this->body.="Content-Type: multipart/related; \tboundary=\""; 
						$this->body.=$this->bound_begin.$bound_level.$this->bound_end."\"\r\n\r\n"; 
				}// �����Ƿ� text ���� �� html���� ��û�У����в�ͬ�� MIME ͷ 
	
				if (!$hava_att) 
					$this->body.="This is a multi-part message in MIME format.\r\n\r\n"; 
					// ���ı�ʶ��������Ѿ��и����ı��룬�������� �в���Ҫ��һ�� 
				$this->body.="--".$this->bound_begin.$bound_level.$this->bound_end."\r\n"; 
				$this->parse_html_body($html_body); 
				
				if ($text_body) { 
					$this->body.="Content-Type: multipart/alternative; \tboundary=\""; 
					$bound_level++; 
					$this->body.=$this->bound_begin.$bound_level.$this->bound_end."\"\r\n\r\n"; 
					$this->body.="--".$this->bound_begin.$bound_level.$this->bound_end."\r\n"; 
					$this->body.="Content-Type: text/plain;\r\n"; 
					$this->body.="\tcharset=\"$this->charset\"\r\n"; 
					$this->body.="Content-Transfer-Encoding: base64\r\n"; 
					$this->body.="\r\n".chunk_split(base64_encode(StripSlashes($text_body)))."\r\n"; 
					$this->body.="--".$this->bound_begin.$bound_level.$this->bound_end."\r\n"; 
					$this->body.="Content-Type: text/html;\r\n"; 
					$this->body.="\tcharset=\"$this->charset\"\r\n"; 
					$this->body.="Content-Transfer-Encoding: base64\r\n"; 
					$this->body.="\r\n".chunk_split(base64_encode(StripSlashes($this->temp_html_body)))."\r\n"; 
					$this->body.="--".$this->bound_begin.$bound_level.$this->bound_end."--\r\n\r\n"; 
					$bound_level--; 
				} else {
					$this->body.="--".$this->bound_begin.$bound_level.$this->bound_end."\r\n"; 
					$this->body.="Content-Type: text/html;\r\n"; 
					$this->body.="\tcharset=\"$this->charset\"\r\n"; 
					$this->body.="Content-Transfer-Encoding: base64\r\n"; 
					$this->body.="\r\n".chunk_split(base64_encode(StripSlashes($this->temp_html_body)))."\r\n"; 
				}//���ı��룬�л�û�С�text ���֣���ɲ�ͬ�ĸ�ʽ�� 
	
			for ($i=0;$i<count($this->temp_body_images);$i++) { 
				$this->body.="--".$this->bound_begin.$bound_level.$this->bound_end."\r\n"; 
				$this->body.="Content-Type:".$this->temp_body_images[$i][type]."; name=\""; 
				$this->body.=$this->temp_body_images[$i][name]."\"\r\n"; 
				$this->body.="Content-Transfer-Encoding: base64\r\n"; 
				$this->body.="Content-ID: <".$this->temp_body_images[$i][cid].">\r\n"; 
				$this->body.="\r\n".chunk_split(base64_encode(StripSlashes($this->temp_body_images[$i][data])))."\r\n"; 
			} 
			$this->body.="--".$this->bound_begin.$bound_level.$this->bound_end."--\r\n\r\n"; 
			$bound_level--; 
			} else{ // �л�û��ͼƬ����������ͼƬ�Ĵ���������û��ͼƬ�Ĵ��� 
				$this->temp_html_body=$html_body; 
			
				if ($text_body) { 
				$bound_level++; 
				$this->body.="Content-Type: multipart/alternative;  \tboundary=\""; 
				$this->body.=$this->bound_begin.$bound_level.$this->bound_end."\"\r\n\r\n"; 
	
				if (!$hava_att) $this->body.="\r\nThis is a multi-part message in MIME format.\r\n\r\n"; 
				$this->body.="--".$this->bound_begin.$bound_level.$this->bound_end."\r\n"; 
				$this->body.="Content-Type: text/plain;\r\n"; 
				$this->body.="\tcharset=\"$this->charset\"\r\n"; 
				$this->body.="Content-Transfer-Encoding: base64\r\n"; 
				$this->body.="\r\n".chunk_split(base64_encode(StripSlashes($text_body)))."\r\n"; 
				$this->body.="--".$this->bound_begin.$bound_level.$this->bound_end."\r\n"; 
				$this->body.="Content-Type: text/html;\r\n"; 
				$this->body.="\tcharset=\"$this->charset\"\r\n"; 
				$this->body.="Content-Transfer-Encoding: base64\r\n"; 
				$this->body.="\r\n".chunk_split(base64_encode(StripSlashes($this->temp_html_body)))."\r\n"; 
				$this->body.="--".$this->bound_begin.$bound_level.$this->bound_end."--\r\n\r\n"; 
				$bound_level--; 
				} else {  
					$this->body.="Content-Type: text/html;\r\n"; 
					$this->body.="\tcharset=\"$this->charset\"\r\n"; 
					$this->body.="Content-Transfer-Encoding: base64\r\n"; 
					$this->body.="\r\n".chunk_split(base64_encode(StripSlashes($this->temp_html_body)))."\r\n"; 
				}//���ı��룬�л�û�С�text ���֣���ɲ�ͬ�ĸ�ʽ�� 
	
			}// end else 
		
		}else{ // ���û�С�html ���ģ�ֻ�С�text ���ġ�
			$this->body.="Content-Type: text/plain; \tcharset=\"$this->charset\"\r\n"; 
			$this->body.="Content-Transfer-Encoding: base64\r\n"; 
			$this->body.="\r\n".chunk_split(base64_encode(StripSlashes($text_body)))."\r\n"; 
		} 
	}// end function default 
	
/*========================================
*�ʼ���������
=========================================*/		
	Function Buildbody($text_body=null,$html_body=null,$att=null) { 
		$this->body="MIME-Version: 1.0\r\n"; 
		if (null==$att or (@count($att)==0)) {//���û�и������鿴���ĵ����͡��� 
			$encode_level=0; 
			$this->build_content($encode_level,$text_body,$html_body); 
		}else{ //����и��� 
			$bound_level=0; 
			$this->body.="Content-Type: multipart/mixed;\tboundary=\""; 
			$bound_level++; 
	
			$this->body.=$this->bound_begin.$bound_level.$this->bound_end."\"\r\n\r\n"; 
			$this->body.="This is a multi-part message in MIME format.\r\n\r\n"; 
			$this->body.="--".$this->bound_begin.$bound_level.$this->bound_end."\r\n"; 
			$this->build_content($bound_level,$text_body,$html_body,true);// �������Ĳ��� 
			
			$att = explode(",",$att);
			$num=count($att); 
			for ($i=0;$i<$num;$i++) { 
				
				$file_name		=	$att[$i]; 
				$file_source	=	dirname($_SERVER['PATH_TRANSLATED'])."/".$att[$i]; 
				$file_type		=	filetype($att[$i]); 
				$file_size		=	filesize($att[$i]); 
	      
				if (file_exists($file_source)) { 
					$file_data=addslashes(fread($fp=fopen($file_source,"r"), filesize($file_source))); 
					$file_data=chunk_split(base64_encode(StripSlashes($file_data))); 
					$this->body.="--".$this->bound_begin.$bound_level.$this->bound_end."\r\n"; 
					$this->body.="Content-Type: $file_type;\r\n\tname=\"$file_name\"\r\nContent-Transfer-Encoding: base64\r\n"; 
					$this->body.="Content-Disposition: attachment;\r\n\tfilename=\"$file_name\"\r\n\r\n"; 
					$this->body.=$file_data."\r\n"; 
				} 
			}//end for 
	
			$this->body.="--".$this->bound_begin.$bound_level.$this->bound_end."--\r\n\r\n"; 
		}// end else 
	
		if ($this->debug) 
			echo nl2br(htmlspecialchars($this->body)); 
	
		return true; 
	} 
	
/*========================================
*�ʼ�����
*
*ע��:��������ʽ��","�ֿ��ĵ��������ļ�,��
*$file = "as.txt" �� $file = "as.txt,��PHP���͸���.txt"
*�����$html_body�Ļ�,�Ḳ��$text_body������
=========================================*/		
	function send($from_name,$to_name,$from_mail,$to_mail,$subject,$text_body=false,$html_body=false,$att=false) { 
		if (empty($from_mail) or empty($to_mail)) { 
			$this->err_str="�e�`���]��ָ�����_���]����ַ���l���ˣ�".$from_mail."�ռ��ˣ�".$to_mail; 
			return false; 
		} 
	
		if (gettype($to_mail)!="array") 
			$to_mail=split(",",$to_mail);//����������飬ת�������飬����ֻ��һ�����Ͷ���; 
		if (gettype($to_name)!="array") 
			$to_name=split(",",$to_name);//����������飬ת�������飬����ֻ��һ�����Ͷ���; 
	
		$this->Buildbody($text_body,$html_body,$att); 
		// �����ż���������һ���ģ�����ֻ��һ�Σ������ڲ�ͬ�������ˣ���Ҫ��ͬ�ġ�Head 
	
		if (!$this->open()) 
			return false; 
		if (!$this->command("HELO $this->hostname",3,"250")) 
			return false; 
			// ��������������� 
	
		for ($i=0;$i<count($to_mail);$i++) { 
			$this->Buildhead($from_name,$to_name[$i],$from_mail,$to_mail[$i],$subject); 
			if (!$this->command("RSET",3,"250")) 
				return false; 
			if (!$this->command("MAIL FROM:".$from_mail,3,"250")) 
				return false; 
			if (!$this->command("RCPT TO:".$to_mail[$i],3,"250")) 
				return false; 
			if (!$this->command("DATA",3,"354")) 
				return false; 
			// ׼�������ʼ� 
			if ($this->debug) 
				$this->outdebug("sending subject;"); 
			if (!fputs($this->connection,$this->subject)) {
				$this->err_str="�e�`���l���]���^�YӍ�r���e��";return false;
			} 
			if ($this->debug) 
				$this->outdebug("sending body;"); 
			if (!fputs($this->connection,$this->body)) {
				$this->err_str="�e�`���l�����ĕr���e��";return false;
			} 
			if (!fputs($this->connection,".\r\n")) {
				$this->err_str="�e�`���l�����ĕr���e��";return false;
			}//���ķ�����ϣ��˳��� 
			$resp=fgets($this->connection,256); 
			if($this->debug) 
				$this->outdebug("$resp"); 
			if (substr($resp,0,1)!="2") { 
				$this->err_str="�e�`���l����ɣ��ŷ����]�лؑ�����"; 
				return false; 
			} 
			// �����ʼ� 
		} 
		if (!$this->command("QUIT",3,"221")) 
			return false; 
		$this->close(); 
		return true; 
	} 
}
?> 