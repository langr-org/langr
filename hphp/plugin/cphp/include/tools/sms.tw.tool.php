<?
#=====================================================================================================
# ��    ��: Sms �l�ͺ�Ӎ̎��
# ʹ���e����
#  include_once("sms.tool.php");
#  $Sms=new Tool_Sms();
#  $Sms->mobile		= "0928320695";	# �Ԓ
#  $Sms->message	= "�@�ǰ��¾W�o���İl�Ĝyԇ��Ӎ��";	# ��Ӎ����(���w)
#  if ($Sms->send()){echo "OK";}
# �ر�˵��������$sendServerΪ����ƽ̨ѡ��"addwe"ѡ�񰬵�������ƽ̨�����л����ʺ�������"twsms"ѡ��̨���Ѷƽ̨
#======================================================================================================

class Tool_Sms{ 
	//var $sendServer		= "addwe";			# ����ƽ̨
	var $sendServer		= "twsms";			# ����ƽ̨
	var $twsmsusername	= "betcity";			# ��̖
	var $twsmspassword	= "qaz2278okm";		# �ܴa
	var $type		= "now";			# �l���͑B
	var $encoding	= "big5";			# ��Ӎ���ݾ��a
	var $popup		= "";				# ʹ�� POPUP �@ʾ
	var $mo			= "";				# ʹ���p��Ӎ
	var $vldtime	= "";				# ��Ӎ��Ч����
	var $dlvtime	= "";				# �A�s�r�g
	var $APCode		= '1';					# ����Ѷ����(����)
	var $addweusername	= 'card';				# �ʺ�(����)
	var $addwepassword	= '29993432';			# ����(����)

	
	/******************************************************************************* 
	 ���ܣ��l�ͺ�Ӎ
	*******************************************************************************/
	function send(){
		if ( 'twsms' == $this->sendServer ) {
			//-------------------------
			//̨���Ѷƽ̨
			//-------------------------
			$this->message = urlencode($this->message);
			$msg = "username=".$this->twsmsusername."&password=".$this->twsmspassword."&type=".$this->type."&encoding=".$this->encoding."&popup=".$this->popup."&mo=".$this->mo."&mobile=".$this->mobile."&message=".$this->message."&vldtime=".$this->vldtime."&dlvtime=".$this->dlvtime;
			$num = strlen($msg);		

			// ���_ API �l��
			$fp = fsockopen ("api.twsms.com", 80);
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
			//����������ƽ̨
			//-----------------------
			$msg = "APCode=".$this->APCode."&username=".$this->addweusername."&password=".$this->addwepassword."&mobile=".$this->mobile."&message=".$this->message."&type=".$this->type."&CallBack=0918813560";
			$num = strlen($msg);

			// �� API բ��
			$fp = fsockopen ("sms.addwe.com.tw", 80);
			if ($fp) {
				$MSGData .= "POST /api.php HTTP/1.1\r\n";
				$MSGData .= "Host: sms.addwe.com.tw\r\n";
				$MSGData .= "Content-Length: ".$num."\r\n";
				$MSGData .= "Content-Type: application/x-www-form-urlencoded\r\n";
				$MSGData .= "Connection: Close\r\n\r\n";
				$MSGData .= $msg."\r\n";
				fputs ($fp, $MSGData);
				
				// ȡ���ش�ֵ
				while (!feof($fp)) $Tmp[]=fgets ($fp); 
				
				// �ر�բ��
				fclose ($fp);
				
				// ��ʾ�ش�ֵ
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
