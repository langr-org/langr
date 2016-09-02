<?
/**
 * ̨���֙C��Ӎ�l�͹����
 * �f����
 * �������֧�� twsms����Ӎ�� �ɼҺ�Ӎƽ̨��Ĭ�J��ʹ�� twsms
 * ʹ���e����
 * $s = loadClass('tool', 'twSms', $this)
 * $s->Mobile = '0928320695';
 * $s->Message = '�@��һ�l�yԇ��Ӎ��';
 * if ($s->send()){echo "OK";}
 * 
 * @author Arnold 2007/05/11
 * @package Tool
 */
class Tool_TwSms{ 
	/**
	 * ��Ӎ���ՏS��
	 * twsms ��̨����Ӎ��Ĭ�Jֵ�����Ñ����Օr�@ʾ�T̖ 0961295275
	 * smsking : ��Ӎ�����Ñ����Օr�@ʾ�T̖ 0954000501
	 * 
	 * @var string
	 */
	var $Server		= 'twsms';
	/**
	 * �֙C�T̖������0928320695
	 *
	 * @var string
	 */
	var $Mobile		= '';
	/**
	 * ��Ӎ���ݣ����ܳ��^140�ֹ�
	 *
	 * @var string
	 */
	var $Message    = '';
	/**
	 * �Ƿ��Ԅӌ���Ӎ�������ľ��a�� CFG_TEMPLATE_LANGUAGE �D�Q�麆Ӎ�S��֧�ֵľ��a BIG5
	 *
	 * @var int
	 */
	var $AutoCharSet    = true;
	/**
	 * �e�`��̖
	 *
	 * @var int
	 */
	var $ErrNo    = 0;
	/**
	 * �e�`�YӍ
	 *
	 * @var string
	 */
	var $ErrMsg    = '';
	/**
	 * �e�`�YӍ�б�
	 *
	 * @var array
	 */
	var $ErrArr    = array();	
	/**
	 * �l�ͺ�Ӎ����
	 *
	 * @return bool �l�ͳɹ����� True���l��ʧ������ False��
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
	 * ��Ӎ�����z��
	 *
	 * @return bool �ɹ����� True��ʧ������ False��
	 */
	function check(){
		$this->ErrArr[1]	= '�e�`���֙C��Ӎ���ݲ��ܞ�գ�';
		$this->ErrArr[2]	= '�e�`����Ӎ�����e�`�^140�ֹ���';
		$this->ErrArr[3]	= '�e�`���֙C�T̖��ʽ�e�`��';
		$this->ErrArr[101]	= '�e�`��TwSms ��Ӎ�l�ͽӿ��B�Yʧ����';
		$this->ErrArr[102]	= '�e�`��TwSms ��Ӎ�l��ʧ����Ո�z�� ErrNo �YӍ��';
		$this->ErrArr[201]	= '�e�`��SmsKing ��Ӎ�l�ͽӿ��B�Yʧ����';
		$this->ErrArr[202]	= '�e�`��SmsKing ��Ӎ�l��ʧ����Ո�z�� ErrNo �YӍ��';
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
	 * TwSms�l�ͺ�Ӎ����
	 *
	 * @return bool �l�ͳɹ����� True���l��ʧ������ False��
	 */
	function sendTwSms(){
		$username = ''; // TwSms �Ñ�����Ո�c���ܫ@ȡ
		$password = '';	 // TwSms �Ñ��ܴa��Ո�c���ܫ@ȡ
		$this->Message = urlencode($this->Message); // url���a
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
			// ȡ���؂�ֵ
			while (!feof($fp)) $Tmp[]=fgets ($fp,128); 
			// �P�]�l��
			fclose ($fp);
			// �@ʾ�؂�ֵ
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
	 * SmsKing �l�ͺ�Ӎ����
	 *
	 * @return bool �l�ͳɹ����� True���l��ʧ������ False��
	 */
	function sendSmsKing(){
		$gid = '';	// SmsKing Ⱥ�M��̖��Ո�c���ܫ@ȡ
		$id = '';	// SmsKing �Ñ�����Ո�c���ܫ@ȡ
		$password = ''; // SmsKing �Ñ��ܴa��Ո�c���ܫ@ȡ
		$mname    = '';	 // SmsKing �lӍ�����Q��Ո�I�\�ˆT���x������8591��Twbbs�ȣ�11�a��
		$this->Message = urlencode($this->Message); // url���a
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
			// ȡ���؂�ֵ
			while (!feof($fp)) $Tmp[]=fgets ($fp,128); 
			// �P�]�l��
			fclose ($fp);
			// �@ʾ�؂�ֵ
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