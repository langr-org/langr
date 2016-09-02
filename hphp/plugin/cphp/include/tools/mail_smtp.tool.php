<?php
/*==========================================================================
*名称:邮件发送类:smtp
*功能:发送包含文字,图片,HTML,附件的邮件
*例:
*$mail 			= new Tool_Smtp("210.64.24.30");
*
*$file 			= "as.txt";//多个文件用","隔开,并且文件名中不能有","
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
*邮件服务器初始化
=========================================*/	
	Function Tool_Smtp($server="smtp.china.com",$port=25,$time_out=20) 	{
		$this->hostname=$server; 
		$this->port=$port; 
		$this->timeout=$time_out; 
		return true; 
	} 

/*========================================
*邮件测试开关
=========================================*/	
	function setDebug($de){
		$this->debug = $de;
	}

/*========================================
*输出测试内容
=========================================*/	
	Function outdebug($message) 
	{ 
		echo htmlspecialchars($message)."<br>\n"; 
	} 
	
/*========================================
*向邮件服务器发送命令
=========================================*/		
	function command($command,$return_lenth=1,$return_code='2') 	{ 
		if ($this->connection==0) { 
			$this->err_str="錯誤：沒有連結到任何伺服器，請檢查網路連結。"; 
			return false; 
		} 
		
		if ($this->debug) 
			$this->outdebug(">>> $command"); 
		
		if (!fputs($this->connection,"$command \r\n")) 	{ 
			$this->err_str="錯誤：無法發送指令".$command; 
			return false; 
		}	else 	{ 
			$resp=fgets($this->connection,256); 
			if($this->debug) 
			$this->outdebug("$resp"); 
		
		if (substr($resp,0,$return_lenth)!=$return_code)	{ 
			$this->err_str="錯誤：".$command." 指令伺服器返回無效:".$resp; 
			return false; 
		}	else 
		return true; 
		} 
	} 
	
/*========================================
*与SMTP邮件服务器进行联接
=========================================*/		
	Function open() { 
		if($this->hostname=="") {
			$this->err_str="錯誤：無效的伺服器名稱!"; 
			return false; 
		} 
	
		if ($this->debug) 
			echo "$this->hostname,$this->port,&$err_no, &$err_str, $this->timeout<BR>"; 
		
		if (!$this->connection=fsockopen($this->hostname,$this->port,&$err_no, &$err_str, $this->timeout)) { 
			$this->err_str="錯誤：連結 SMTP 伺服器夫敗，錯誤資訊：".$err_str."錯誤編號：".$err_no; 
			return false; 
		} else { 
			$resp=fgets($this->connection,256); 
			if($this->debug) 
				$this->outdebug("$resp"); 
			
			if (substr($resp,0,1)!="2") {
				$this->err_str="錯誤：伺服器返回無效的資訊：".$resp."請檢查SMTP伺服器是否正確。"; 
				return false; 
			} 
			return true; 
		} 
	} 
	
/*========================================
*关闭与邮件服务器的联接
=========================================*/		
	Function Close() { 
		if($this->connection!=0) { 
			fclose($this->connection); 
			$this->connection=0; 
		} 
	} 
	
/*========================================
*设定邮件头部信息
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
*解析包含图片的邮件
*注意:此图片尽量是网络上可用的图片
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
			// 把图片数据读出来保存到一个数据； 
	
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
*邮件正文处理
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
				}// 对于是否 text 正文 、 html正文 有没有，须有不同的 MIME 头 
	
				if (!$hava_att) 
					$this->body.="This is a multi-part message in MIME format.\r\n\r\n"; 
					// 正文标识，如果是已经有附件的编码，则在正文 中不需要这一句 
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
				}//正文编码，有或没有　text 部分，编成不同的格式。 
	
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
			} else{ // 有或没有图片，以上是有图片的处理，下面是没有图片的处理 
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
				}//正文编码，有或没有　text 部分，编成不同的格式。 
	
			}// end else 
		
		}else{ // 如果没有　html 正文，只有　text 正文　
			$this->body.="Content-Type: text/plain; \tcharset=\"$this->charset\"\r\n"; 
			$this->body.="Content-Transfer-Encoding: base64\r\n"; 
			$this->body.="\r\n".chunk_split(base64_encode(StripSlashes($text_body)))."\r\n"; 
		} 
	}// end function default 
	
/*========================================
*邮件附件处理
=========================================*/		
	Function Buildbody($text_body=null,$html_body=null,$att=null) { 
		$this->body="MIME-Version: 1.0\r\n"; 
		if (null==$att or (@count($att)==0)) {//如果没有附件，查看正文的类型　； 
			$encode_level=0; 
			$this->build_content($encode_level,$text_body,$html_body); 
		}else{ //如果有附件 
			$bound_level=0; 
			$this->body.="Content-Type: multipart/mixed;\tboundary=\""; 
			$bound_level++; 
	
			$this->body.=$this->bound_begin.$bound_level.$this->bound_end."\"\r\n\r\n"; 
			$this->body.="This is a multi-part message in MIME format.\r\n\r\n"; 
			$this->body.="--".$this->bound_begin.$bound_level.$this->bound_end."\r\n"; 
			$this->build_content($bound_level,$text_body,$html_body,true);// 编入正文部分 
			
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
*邮件发送
*
*注意:附件的形式以","分开的单个或多个文件,如
*$file = "as.txt" 或 $file = "as.txt,用PHP发送附件.txt"
*如果有$html_body的话,会覆盖$text_body的内容
=========================================*/		
	function send($from_name,$to_name,$from_mail,$to_mail,$subject,$text_body=false,$html_body=false,$att=false) { 
		if (empty($from_mail) or empty($to_mail)) { 
			$this->err_str="錯誤：沒有指定正確的郵件地址，發件人：".$from_mail."收件人：".$to_mail; 
			return false; 
		} 
	
		if (gettype($to_mail)!="array") 
			$to_mail=split(",",$to_mail);//如果不是数组，转换成数组，哪怕只有一个发送对象; 
		if (gettype($to_name)!="array") 
			$to_name=split(",",$to_name);//如果不是数组，转换成数组，哪怕只有一个发送对象; 
	
		$this->Buildbody($text_body,$html_body,$att); 
		// 所有信件的内容是一样的，可以只编一次，而对于不同的收信人，需要不同的　Head 
	
		if (!$this->open()) 
			return false; 
		if (!$this->command("HELO $this->hostname",3,"250")) 
			return false; 
			// 与服务器建立链接 
	
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
			// 准备发送邮件 
			if ($this->debug) 
				$this->outdebug("sending subject;"); 
			if (!fputs($this->connection,$this->subject)) {
				$this->err_str="錯誤：發送郵件頭資訊時出錯！";return false;
			} 
			if ($this->debug) 
				$this->outdebug("sending body;"); 
			if (!fputs($this->connection,$this->body)) {
				$this->err_str="錯誤：發送正文時出錯！";return false;
			} 
			if (!fputs($this->connection,".\r\n")) {
				$this->err_str="錯誤：發送正文時出錯！";return false;
			}//正文发送完毕，退出； 
			$resp=fgets($this->connection,256); 
			if($this->debug) 
				$this->outdebug("$resp"); 
			if (substr($resp,0,1)!="2") { 
				$this->err_str="錯誤：發送完成，伺服器沒有回應！！"; 
				return false; 
			} 
			// 发送邮件 
		} 
		if (!$this->command("QUIT",3,"221")) 
			return false; 
		$this->close(); 
		return true; 
	} 
}
?> 