<?
/**
 * 台灣手機簡訊發送工具類
 * 說明：
 * 本工具類支持 twsms、簡訊王 兩家簡訊平台，默認是使用 twsms
 * 使用舉例：
 * $s = loadClass('tool', 'twSms', $this)
 * $s->Mobile = '0928320695';
 * $s->Message = '這是一條測試簡訊！';
 * if ($s->send()){echo "OK";}
 * 
 * @author Arnold 2007/05/11
 * @package Tool
 */
class Tool_TwSms{ 
	/**
	 * 簡訊服務廠商
	 * twsms ：台灣簡訊（默認值），用戶接收時顯示門號 0961295275
	 * smsking : 簡訊王，用戶接收時顯示門號 0954000501
	 * 
	 * @var string
	 */
	var $Server		= 'twsms';
	/**
	 * 手機門號，例：0928320695
	 *
	 * @var string
	 */
	var $Mobile		= '';
	/**
	 * 簡訊內容，不能超過140字節
	 *
	 * @var string
	 */
	var $Message    = '';
	/**
	 * 是否自動將簡訊內字中文編碼由 CFG_TEMPLATE_LANGUAGE 轉換為簡訊廠商支持的編碼 BIG5
	 *
	 * @var int
	 */
	var $AutoCharSet    = true;
	/**
	 * 錯誤編號
	 *
	 * @var int
	 */
	var $ErrNo    = 0;
	/**
	 * 錯誤資訊
	 *
	 * @var string
	 */
	var $ErrMsg    = '';
	/**
	 * 錯誤資訊列表
	 *
	 * @var array
	 */
	var $ErrArr    = array();	
	/**
	 * 發送簡訊操作
	 *
	 * @return bool 發送成功返回 True，發送失敗返回 False；
	 */
	function send(){
		if (!$this->check()) return False;
		switch ($this->Server){
			case 'twsms':
				if ($this->sendTwSms()){
					return True;
				}
				break;
			case 'smsking':
				if ($this->sendSmsKing()){
					return True;
				}
				break;
		}
		return False;
	}
	/**
	 * 簡訊參數檢查
	 *
	 * @return bool 成功返回 True，失敗返回 False；
	 */
	function check(){
		$this->ErrArr[1]	= '錯誤：手機或簡訊內容不能為空！';
		$this->ErrArr[2]	= '錯誤：簡訊內容錯誤過140字節！';
		$this->ErrArr[3]	= '錯誤：手機門號格式錯誤！';
		$this->ErrArr[101]	= '錯誤：TwSms 簡訊發送接口連結失敗！';
		$this->ErrArr[102]	= '錯誤：TwSms 簡訊發送失敗，請檢查 ErrNo 資訊！';
		$this->ErrArr[201]	= '錯誤：SmsKing 簡訊發送接口連結失敗！';
		$this->ErrArr[202]	= '錯誤：SmsKing 簡訊發送失敗，請檢查 ErrNo 資訊！';
		if ((empty($this->Mobile))||(empty($this->Message))){
			$this->ErrNo  = 1;
			$this->ErrMsg = $this->ErrArr[$this->ErrNo];
			return False;
		}
		if (strlen($this->Message)>140){
			$this->ErrNo  = 2;
			$this->ErrMsg = $this->ErrArr[$this->ErrNo];
			return False;
		}
		if ((strlen($this->Mobile)!=10) || (substr($this->Mobile,0,2)!='09') || (!eregi("^([0-9]*)$",$this->Mobile))){
			$this->ErrNo  = 3;
			$this->ErrMsg = $this->ErrArr[$this->ErrNo];
			return False;	
		}
		if ($this->AutoCharSet) $this->Message = ICONV(CFG_TEMPLATE_LANGUAGE, 'big5', $this->Message);
		return True;
	}
	/**
	 * TwSms發送簡訊操作
	 *
	 * @return bool 發送成功返回 True，發送失敗返回 False；
	 */
	function sendTwSms(){
		$username = ''; // TwSms 用戶名，請與主管獲取
		$password = '';	 // TwSms 用戶密碼，請與主管獲取
		$this->Message = urlencode($this->Message); // url編碼
		$msg = "username=$username&password=$password&type=now&encoding=big5&popup=&mo=&mobile=".$this->Mobile."&message=".$this->Message."&vldtime=&dlvtime=";
		$num = strlen($msg);		
		$fp = @fsockopen ("api.twsms.com", 80);
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
				$this->ErrNo = abs($CheckRes[1]);
				$this->ErrMsg = $this->ErrArr[102];
			} else {
				return True;
			}
		} else {
			$this->ErrNo = 101;
			$this->ErrMsg = $this->ErrArr[$this->ErrNo];
		}
		return False;
	}
	/**
	 * SmsKing 發送簡訊操作
	 *
	 * @return bool 發送成功返回 True，發送失敗返回 False；
	 */
	function sendSmsKing(){
		$gid = '';	// SmsKing 群組序號，請與主管獲取
		$id = '';	// SmsKing 用戶名，請與主管獲取
		$password = ''; // SmsKing 用戶密碼，請與主管獲取
		$mname    = '';	 // SmsKing 發訊者名稱，請營運人員定義，例：8591、Twbbs等，11碼內
		$this->Message = urlencode($this->Message); // url編碼
		$msg = "gid=$gid&id=$id&password=$password&tel=".$this->Mobile."&msg=".$this->Message."&mname=$mname";
		$num = strlen($msg);		
		$fp = @fsockopen ("api.message.com.tw", 80);
		if ($fp) {
			$MSGData .= "POST /send.php HTTP/1.1\r\n";
			$MSGData .= "Host: api.message.com.tw\r\n";
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
			$CheckRes = split("=",$Tmp[12]);
			if (intval($CheckRes[1]) < 0){
				$this->ErrNo = abs($CheckRes[1]);
				$this->ErrMsg = $this->ErrArr[202];
			} else {
				return True;
			}
		} else {
			$this->ErrNo = 201;
			$this->ErrMsg = $this->ErrArr[$this->ErrNo];
		}
		return False;
	}
}
?>