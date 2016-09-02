<?
#=====================================================================================================
# 功    能: Sms 發送簡訊處理
# 使用舉例：
#  include_once("sms.tool.php");
#  $Sms=new Tool_Sms();
#  $Sms->mobile		= "0928320695";	# 電話
#  $Sms->message	= "這是艾德網給您的發的測試短訊！";	# 簡訊內容(繁體)
#  if ($Sms->send()){echo "OK";}
# 特别说明：参数$sendServer为发送平台选择，"addwe"选择艾德网发送平台（和中华国际合作），"twsms"选择台湾简讯平台
#======================================================================================================

class Tool_Sms{ 
	//var $sendServer		= "addwe";			# 发送平台
	var $sendServer		= "twsms";			# 发送平台
	var $twsmsusername	= "betcity";			# 帳號
	var $twsmspassword	= "qaz2278okm";		# 密碼
	var $type		= "now";			# 發送型態
	var $encoding	= "big5";			# 簡訊內容編碼
	var $popup		= "";				# 使用 POPUP 顯示
	var $mo			= "";				# 使用雙向簡訊
	var $vldtime	= "";				# 簡訊有效期限
	var $dlvtime	= "";				# 預約時間
	var $APCode		= '1';					# 发简讯参数(必填)
	var $addweusername	= 'card';				# 帐号(必填)
	var $addwepassword	= '29993432';			# 密码(必填)

	
	/******************************************************************************* 
	 功能：發送簡訊
	*******************************************************************************/
	function send(){
		if ( 'twsms' == $this->sendServer ) {
			//-------------------------
			//台湾简讯平台
			//-------------------------
			$this->message = urlencode($this->message);
			$msg = "username=".$this->twsmsusername."&password=".$this->twsmspassword."&type=".$this->type."&encoding=".$this->encoding."&popup=".$this->popup."&mo=".$this->mo."&mobile=".$this->mobile."&message=".$this->message."&vldtime=".$this->vldtime."&dlvtime=".$this->dlvtime;
			$num = strlen($msg);		

			// 打開 API 閘道
			$fp = fsockopen ("api.twsms.com", 80);
			if ($fp) {
				$MSGData .= "POST /send.php HTTP/1.1\r\n";
				$MSGData .= "Host: api.twsms.com\r\n";
				$MSGData .= "Content-Length: ".$num."\r\n";
				$MSGData .= "Content-Type: application/x-www-form-urlencoded\r\n";
				$MSGData .= "Connection: Close\r\n\r\n";
				$MSGData .= $msg."\r\n";
				fputs ($fp, $MSGData);

				// 取出回傳值
				while (!feof($fp)) $Tmp[]=fgets ($fp,128); 

				// 關閉閘道
				fclose ($fp);

				// 顯示回傳值
				$CheckRes = split("=",$Tmp[9]);

				if (intval($CheckRes[1]) <= 0){
					$smsStatus	=	0;
					$this->result = $CheckRes[1];
					return False;
				}
				else {
					$this->result	=	1;
					return True;
				}
			}
			else {
				$this->result = "f";
				$smsStatus	=	-1;
				return False;
			}
		} elseif( 'addwe' == $this->sendServer ) {
			//-----------------------
			//艾德网发送平台
			//-----------------------
			$msg = "APCode=".$this->APCode."&username=".$this->addweusername."&password=".$this->addwepassword."&mobile=".$this->mobile."&message=".$this->message."&type=".$this->type."&CallBack=0918813560";
			$num = strlen($msg);

			// 打开 API 闸道
			$fp = fsockopen ("sms.addwe.com.tw", 80);
			if ($fp) {
				$MSGData .= "POST /api.php HTTP/1.1\r\n";
				$MSGData .= "Host: sms.addwe.com.tw\r\n";
				$MSGData .= "Content-Length: ".$num."\r\n";
				$MSGData .= "Content-Type: application/x-www-form-urlencoded\r\n";
				$MSGData .= "Connection: Close\r\n\r\n";
				$MSGData .= $msg."\r\n";
				fputs ($fp, $MSGData);
				
				// 取出回传值
				while (!feof($fp)) $Tmp[]=fgets ($fp); 
				
				// 关闭闸道
				fclose ($fp);
				
				// 显示回传值
				$find = 'message=';
				for ($i=0;$i<count($Tmp);$i++) {
					if (is_int(strpos($Tmp[$i],$find))) $k = $i;
				}
				$this->result = substr(strstr($Tmp[$k],$find),strlen($find),1);
				//$this->array = $Tmp;

				if ( $this->result == '1' ) {
					return True; 
				}else{
					return False;
				}
			}
			else {
				return False;
			}
		}
	}
}
?>
