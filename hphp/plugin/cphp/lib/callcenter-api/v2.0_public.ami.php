<?php
/**
 * 公共类
 *
 */
class ami
{
	/**
	 * AMI login
	 */
	function AMI_Login()
	{
		$strHost = CFG_AGI_HOST_PBX;
		$strUser = CFG_AGI_USER_PBX;
		$strSecret = CFG_AGI_PWD_PBX;
		$oSocket = @fsockopen($strHost, 5038, $errnum, $errdesc) or die("Connection to host failed");
		fputs($oSocket, "Action: login\r\n");
		fputs($oSocket, "Events: off\r\n");
		fputs($oSocket, "Username: $strUser\r\n");
		fputs($oSocket, "Secret: $strSecret\r\n\r\n");
		//sleep(0.5);
		$r = fgets($oSocket); 
		$r .= fgets($oSocket); 
		$r .= fgets($oSocket); 
		return $oSocket;
	}

	/**
	 *AMI logoff
	 */
	function AMI_Logoff($oSocket)
	{
		fputs($oSocket, "Action: Logoff\r\n\r\n");
		sleep(1);
		fgets($oSocket);
	}

	/**
	 * AMI action
	 */
	function AMI_Action($ami_action, $result = '')
	{
		$oSocket = $this->AMI_Login();
		foreach ($ami_action as $val) {
			fputs($oSocket, $val);	
		}
		fputs($oSocket, "\r\n");
		$this->AMI_Logoff($oSocket);
		$str_return = fgets($oSocket);
		if ( $result == 'result' ) {
			while ( ($r = fgets($oSocket)) != "\r\n" ) {
				$ret .= $r;
			}
			fclose($oSocket);
			return $ret;
		}
		$ar_str = split("\r\n", $str_return);
		$str_return_without_linefeeds = $ar_str[0];
		$str_return_without_linefeeds = substr($str_return_without_linefeeds, 10);
		fclose($oSocket);
		return $str_return_without_linefeeds;
	}

	/**
	 * 外呼
	 */
	function outCall($SrcExten,$Exten,$CallerId)
	{ 
		$ami_outCall = array("Action: outcall\r\n",
							 "SrcExten: ".$SrcExten."\r\n",
							 "Exten: ".$Exten."\r\n",
							 "CallerID: ".$CallerId."\r\n");
		return $this->AMI_Action($ami_outCall);
	}

	/**
	 *通话保持
	 */
	function keepcall($SrcExten) 
	{
		$ami_keepcall = array("Action: keepcall\r\n",
							  "SrcExten: ".$SrcExten."\r\n");
		return $this->AMI_Action($ami_keepcall);
	}

	/**
	 * 恢复通话
	 */
	function restorePark($SrcExten)
	{
		$ami_restorePark = array("Action: restorepark\r\n",
								 "SrcExten: ".$SrcExten."\r\n");
		return $this->AMI_Action($ami_restorePark);
	}

	/**
	 * 监听、强插
	 */
	function chanspy($SrcExten,$SpyExten,$SpyType)
	{
		$ami_chanspy = array("Action: chanspy\r\n",
							 "SrcExten: ".$SrcExten."\r\n",
							 "Exten: ".$SpyExten."\r\n",
							 "Type: ".$SpyType."\r\n");
		return $this->AMI_Action($ami_chanspy);
	}

	/**
	 * 通话质检
	 */
	function feedback($SrcExten) 
	{
		$ami_feedback = array("Action: feedback\r\n",
						      "SrcExten: ".$SrcExten."\r\n");
		return $this->AMI_Action($ami_feedback);
	}
  
	/**
	 * 通话转移
	 */
	function transfercall($SrcExten,$Exten)
	{
		$ami_transfercall = array("Action: transfercall\r\n",
								  "SrcExten: ".$SrcExten."\r\n",
								  "Exten: ".$Exten."\r\n");
		return $this->AMI_Action($ami_transfercall);
	}

	/**
	 * 通话转移
	 */
	function multiCall($SrcExten, $Exten)
	{
		$ami_multiCall = array("Action: threewaycalling\r\n",
							   "SrcExten: ".$SrcExten."\r\n",
							   "Exten: ".$Exten."\r\n");
		return $this->AMI_Action($ami_multiCall);
	}

	/**
	 * 强拆
	 */
	function hangupcall($Exten)
	{
		$ami_hangupcall = array("Action: hangup\r\n", "Exten: ".$Exten."\r\n");
		return $this->AMI_Action($ami_hangupcall);
	}

	/**
	 * 示忙
	 */
	function setdnd($SrcExten)
	{
		$ami_setdnd = array("Action: setdnd\r\n",
							"SrcExten: ".$SrcExten."\r\n");
		return $this->AMI_Action($ami_setdnd);
	}

	/**
	 * 取消示忙
	 */
	function deldnd($SrcExten)
	{
		$ami_deldnd = array("Action: deldnd\r\n",
							"SrcExten: ".$SrcExten."\r\n");
		return $this->AMI_Action($ami_deldnd);
	}

	/**
	 * 加入会议室
	 */
	function invitetomeeting($Exten, $ConfId, $CallerId)
	{
		$ami_invitetomeeting = array("Action: invitetomeeting\r\n",
								     "Exten: ".$Exten."\r\n",
								     "Conf: ".$ConfId."\r\n",
								     "CallerID: ".$CallerId."\r\n");
		return $this->AMI_Action($ami_invitetomeeting);
	}

	/**
	 * 会议室mute/unmute
	 */
	function meetmeadmin($Action,$ConfId,$UserId)
	{
		$ami_meetmemute = array("Action: ".$Action."\r\n",
								"Conf: ".$ConfId."\r\n",
								"Usernum: ".$UserId."\r\n");
		return $this->AMI_Action($ami_meetmemute);
	}

	/**
	 * 会议室mute/unmute
	 */
	function meetmelist($ConfId)
	{
		$ami_meetmelist = array("Action: MeetmeList\r\n",
								"Conf: ".$ConfId."\r\n");
		return $this->AMI_Action($ami_meetmelist);
	}


	/**
	 * 会议室mute/unmute
	 */
	function geterror($Error)
	{
		$str_return = '';
		switch ($Error) {
			case 'Device not exist':
				$str_return       = '您未绑定分机';
				break;
			case 'Device of spied not exist':
				$str_return       = '您监听的分机不存在';
				break;
			case 'Device is not Idle':
				$str_return       = '您的分机不处于空闲状态';
				break;
			case 'Device is not Inuse':
				$str_return       = '您的分机未建立通话';
				break;
			case 'Device of spied is not Inuse':
				$str_return       = '您监听的分机未建立通话';
				break;
			case 'Callee status error':
				$str_return       = '您拨的号码忙或者被示忙';
				break;
			case 'No channel needed to be restored':
				$str_return       = '没有需要恢复的通话';
				break;
			case 'DAHDI error':
				$str_return       = '系统未加载DAHDI驱动程序';
				break;
			case 'No conference':
				$str_return       = '三方会话资源不够!';
				break;
			default:
				$str_return	= '异常错误，请联系管理员!';
				break;
		}
		return $str_return;
	}

	/**
	 * NOTE: no use. 
	 */
	function getDnd($exten)
	{
		$ami = array("Action: command\r\n", "Command: database get DND $exten\r\n");
		return $this->AMI_Action($ami, 'result');
	}

	/**
	 * @fn
	 * @brief get DB.
	 * @param 
	 * @return 
	 * 	TODO: return
	 */
	function _getDB($family, $key) /* {{{ */
	{
		$ret = array();
		$ami = array("Action: command\r\n", "Command: database show $family $key\r\n");
		$ret['data'] = $this->AMI_Action($ami, 'result');
		$ret['ret'] = substr($ret['data'], 10, 7);
		return $ret;
	} /* }}} */

	/**
	 * @fn
	 * @brief set DB.
	 * @param 
	 * @return 
	 */
	function _setDB($family, $key, $value = null) /* {{{ */
	{
		if ( strlen($value) ) {
			$ami = array("Action: command\r\n", "Command: database put $family $key $value\r\n");
		} else {
			$ami = array("Action: command\r\n", "Command: database del $family $key\r\n");
		}
		return $this->AMI_Action($ami);
	} /* }}} */
}
?>
