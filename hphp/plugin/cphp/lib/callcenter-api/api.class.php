<?php
/**
 * @file api2.class.php
 * @brief asterisk manager interface function api.
 * 应用层功能扩展, 可供内部或外部直接调用.
 * 
 * Copyright (C) 2011 WiseTalk.cn
 * All rights reserved.
 * 
 * @package index
 * @author Langr <hua@langr.org> 2011/12/16 11:27
 * 
 * $Id: api.class.php 536 2012-04-20 01:42:42Z huangh $
 */

/*
session_start();
 */
include(dirname(__FILE__).'/api.config.php');
include(dirname(__FILE__).'/v2.0_public.ami.php');

function c($fStr, $fFrom = 'gb2312', $fTo = 'utf-8') {
	return $fStr;
	/*$fStr = iconv($fFrom, $fTo, $fStr);
	return $fStr;*/
}

class api extends ami
{
	public $linkid = NULL;
	public $config = array();
	/**
	 * @WARNNING!!! 当对外提供接口时, 出错号不应随意改变.
	 */
	const E_OK = 0;
	const E_ARGS = 1;
	const E_LOGOFF = 2;
	const E_DATA_INVALID = 3;
	const E_NOOP = 4;
	const E_IP_DENY = 5;
	const E_DATA_EMPTY = 100;
	const E_OP_FAIL = 103;
	const E_KEEP_NOOP = 104;
	const E_UNKEEP_NOOP = 105;
	const E_EXTEN_NO_EXIST = 801;
	const E_EXTEN_NO_CALLING = 802;
	const E_EXTEN_OFFLINE = 803;
	const E_EXTEN_BUSY = 804;
	const E_NUMBER_ERROR = 805;
	const E_SYS = 999;
	const E_SYS_8 = 998;
	const E_SYS_7 = 997;
	const E_API_NO_EXIST = 900;
	const E_KEY_NO_EXIST = 901;
	const E_CHECKSUM = 902;

	function __construct() /* {{{ */
	{
		global $api_config;
		$this->config = $api_config;
		$this->config['allow'] = strlen($api_config['allow']) ? $api_config['allow'] : '0';
		$this->config['key'] = md5(getenv('REMOTE_ADDR').'@'.$api_config['key']);
	} /* }}} */

	function __destruct() /* {{{ */
	{
		if ( $_this->linkid ) {
			/* ... */
		}
	} /* }}} */

	/**
	 * @fn
	 * @brief 
	 * @param 
	 * @return 
	 */
	function showIndex(&$args = array()) /* {{{ */
	{
		return self::showLogin($args);
	} /* }}} */

	/**
	 * @fn
	 * @brief Login
	 * 	远程调用时的同步登陆, 用于同步用户数据或鉴权.
	 * 	!!!此设计已经取消, 不需要分机登陆到接口, 只需要调用接口的主机校检鉴权, 但设计可以保留.
	 * 	SESSIOIN 用来保存数据并不可靠...
	 * @param $args['srcno'] 登陆的分机号.
	 * @return 
	 */
	function showLogin(&$args = array()) /* {{{ */
	{
		if ( API_DEBUG != 'debug' && getenv('REMOTE_ADDR') != '127.0.0.1' ) {
			return self::_return(self::_error(self::E_IP_DENY), $args['method'], $args['return']);
		}
		$_SESSION['userinfo'][$args['srcno']] = $args;
		return self::_return(self::_error(self::E_OK), $args['method'], $args['return']);
	} /* }}} */

	/**
	 * @fn
	 * @brief checksum
	 * 	远程调用时的鉴权, 如果IP为信任IP, 则直接返回 true.
	 * 	为了提高鉴权校验效率, 一个陌生的IP, 只有第一次请求时需要鉴权, 通过后下次为信任.
	 * 	!!!高效率鉴权只在有正常传送COOKIE的情况有效.
	 * @param 
	 * @param $args['chksum'] checksum = md5(action+srcno+dstno+md5(client_ip+'@'+key)).
	 * @return true, success; false, failure.
	 */
	function doCheckSum(&$args = array()) /* {{{ */
	{
		if ( API_DEBUG == 'debug' ) {
			return true;	/* debug */
		}
		
		$allow = $_COOKIE['wisetalk_api_ip'];
		/* 上次刚刚校验过, 此次pass */
		if ( !empty($allow) && $allow == md5(getenv('REMOTE_ADDR')) ) {
			return true;
		}
		/* 信任的IP地址, pass */
		if ( $this->config['allow'] == '*' || strpos($this->config['allow'], getenv('REMOTE_ADDR')) !== false ) {
			return true;
		}

		$sum = md5($args['action'].$args['srcno'].$args['dstno'].$this->config['key']);
		if ( $sum != $args['chksum'] ) {
			return false;
		}

		setcookie('wisetalk_api_ip', md5(getenv('REMOTE_ADDR')), 0, '/');
		return true;
	} /* }}} */

	/**
	 * @fn
	 * @brief 会议室列表.
	 *!@param $args['method']
	 *!@param $args['return']
	 * @param $args['srcno'] confno 房间号
	 * @param $args['dstno'] user number 用户号
	 * @param $args['type'] meetaction 操作: mute, unmute, kick, ...
	 * @return 
	 */
	function showMeetList(&$args = array()) /* {{{ */
	{
		$_this = & self::_getInstance();
		if ( $_this == false ) {
			return self::_return(self::_error(self::E_LOGOFF), $args['method'], $args['return']);
		}

		//$confno = $_SESSION['confuser'];
		$confno = $args['srcno'];
		if ( empty($confno) ) {
			return $_this->_return($_this->_error(self::E_ARGS), $args['method'], $args['return']);
		}
		if ( $_GET['meetaction'] == "mute" ) {
				$Action="MeetmeMute";
		} else if ( $_GET['meetaction'] == "unmute" ) {
			$Action="MeetmeUnmute";
		} else if ( $_GET['meetaction'] == "kick" ) {
			$Action="MeetmeKick";
		}
		$_this->meetmeadmin($Action,$confno,$_GET['user_id']);
		sleep(1);
		$res_list=$_this->meetmelist($confno);
		if ( $res_list == "Success" ) {
			/* TODO: 从memcached取数据并显示 */
			$res = 'todo';
		} else {
			return $_this->_return($_this->_error(self::E_OP_FAIL), $args['method'], $args['return']);
		}
		
		/* ... */

		$ret = $_this->_error(self::E_OK);
		$ret['ret'] = $res;
		return $_this->_return($ret, $args['method'], $args['return']);
	} /* }}} */
	
	/**
	 * @fn
	 * @brief 示忙/闲.
	 * 	取消type参数, 示忙/示闲函数分开.
	 * @param $args['method']
	 * @param $args['return']
	 * @param $args['srcno'] 需要示忙的分机.
	 *!@param $args['return'] 返回数据格式: ajax(默认), xml.
	 * @return 
	 */
	function showAgentBusy(&$args = array()) /* {{{ */
	{
		$_this = & self::_getInstance();
		if ( $_this == false ) {
			return self::_return(self::_error(self::E_LOGOFF), $args['method'], $args['return']);
		}

		$exten = strstr($_SESSION['userinfo'][$exten]['dial'], 'ZAP') ? $_SESSION['userinfo'][$exten]['dial'] : '';
		$exten = empty($exten) ? $args['srcno'] : $exten;

		/* 示忙 */
		$res = $_this->setdnd($exten);
		$_SESSION['userinfo'][$exten]['stat'] = 1;

		$ret = $_this->_error(self::E_OK);
		$ret['ret']['stat'] = $_SESSION['userinfo'][$exten]['stat'];
		return $_this->_return($ret, $args['method'], $args['return']);
	} /* }}} */

	/**
	 * @fn
	 * @brief 示闲.
	 * @param $args['method']
	 * @param $args['return']
	 * @param $args['srcno'] 需要示闲的分机.
	 *!@param $args['return'] 返回数据格式: ajax(默认), xml.
	 * @return 
	 */
	function showAgentIdle(&$args = array()) /* {{{ */
	{
		$_this = & self::_getInstance();
		if ( $_this == false ) {
			return self::_return(self::_error(self::E_LOGOFF), $args['method'], $args['return']);
		}

		$exten = strstr($_SESSION['userinfo'][$exten]['dial'], 'ZAP') ? $_SESSION['userinfo'][$exten]['dial'] : '';
		$exten = empty($exten) ? $args['srcno'] : $exten;

		/* 取消示忙 */
		$res = $_this->deldnd($exten);
		$_SESSION['userinfo'][$exten]['stat'] = 0;

		$ret = $_this->_error(self::E_OK);
		$ret['ret']['stat'] = $_SESSION['userinfo'][$exten]['stat'];
		return $_this->_return($ret, $args['method'], $args['return']);
	} /* }}} */

	/**
	 * @fn
	 * @brief 通话回复, 与点击拔号同类.
	 * @param $args['method']
	 * @param $args['return']
	 * @param $args['srcno'] 主叫号
	 * @param $args['dstno'] 被叫号
	 * @return 
	 */
	function showCallReply(&$args = array()) /* {{{ */
	{
		$_this = & self::_getInstance();
		if ( $_this == false ) {
			return self::_return(self::_error(self::E_LOGOFF), $args['method'], $args['return']);
		}

		$clid = $args['dstno'];
		if ( strlen($clid) < 3 ) {
			return $_this->_return(self::_error(self::E_NUMBER_ERROR), $args['method'], $args['return']);
		}
		
		$CallerId = "clickcall <".$args['srcno'].">";
		$_res = $_this->outCall($args['srcno'], $clid, $CallerId);
		if ( $_res != "Success" ) {
			$err = $_this->geterror($_res);
			return $_this->_return(self::_error(self::E_EXTEN_BUSY, c($err)), $args['method'], $args['return']);
		}

		return $_this->_return(self::_error(self::E_OK), $args['method'], $args['return']);
	} /* }}} */

	/**
	 * @fn
	 * @brief 点击拔号.
	 * @param $args['method']
	 * @param $args['return']
	 * @param $args['srcno'] 主叫号
	 * @param $args['dstno'] 被叫号
	 * @param $args['type'] 外呼id, 外呼时用, 没有就不带.
	 * @return 
	 */
	function showOutCall(&$args = array()) /* {{{ */
	{
		$_this = & self::_getInstance();
		if ( $_this == false ) {
			return self::_return(self::_error(self::E_LOGOFF), $args['method'], $args['return']);
		}

		$exten = $args['srcno'];
		$clid = $args['dstno'];
		$lid = $args['type'];
		if ( strlen($args['dstno']) < 3 ) {
			return $_this->_return(self::_error(self::E_NUMBER_ERROR, $args['dstno']), $args['method'], $args['return']);
		}
		
		$strCallerId = "clickcall <".$args['srcno'].">";
		$_res = $_this->outCall($exten, $clid, $strCallerId);
		if ( $_res != "Success" ) {
			$err = $_this->geterror($_res);
			return $_this->_return($_this->_error(self::E_OP_FAIL, c($err)), $args['method'], $args['return']);
		}

		return $_this->_return(self::_error(self::E_OK), $args['method'], $args['return']);
	} /* }}} */

	/**
	 * @fn
	 * @brief 加入会议室.
	 * 	被邀请者拔打邀请者房间号.
	 * @param $args['method']
	 * @param $args['return']
	 * @param $args['srcno'] 邀请者会议室房间号
	 * @param $args['dstno'] 被邀请者分机号
	 * @return 
	 */
	function showInMeeting(&$args = array()) /* {{{ */
	{
		$_this = & self::_getInstance();
		if ( $_this == false ) {
			return self::_return(self::_error(self::E_LOGOFF), $args['method'], $args['return']);
		}

		$clid = trim($args['dstno']);
		$CallerId = "clickcall <".$args['srcno'].">";
		$_res = $_this->invitetomeeting($clid, trim($args['srcno']), $CallerId);
		if ( $_res != "Success" ) {
			$err = $_this->geterror($_res);
			return $_this->_return($_this->_error(self::E_OP_FAIL, c($err)), $args['method'], $args['return']);
		}
		return $_this->_return(self::_error(self::E_OK), $args['method'], $args['return']);
	} /* }}} */
	
	/**
	 * @fn
	 * @brief 通话保持.
	 * 	取消type参数, 保持/恢复函数分开.
	 * 	只需要传递工号, 不需要处理分机号.
	 * @param $args['method']
	 * @param $args['return']
	 * @param $args['srcno'] 需要保持的电话号
	 * @return 
	 */
	function showCallKeep(&$args = array()) /* {{{ */
	{
		$_this = & self::_getInstance();
		if ( $_this == false ) {
			return self::_return(self::_error(self::E_LOGOFF), $args['method'], $args['return']);
		}

		$exten = $args['srcno'];
		/* 保持 */
		$_res = $_this->keepcall($exten);
		if ( $_res != 'Success' ) {
			$err = $_this->geterror($_res);
			$ret = self::_error(self::E_OP_FAIL, c($err));
		} else {
			$_SESSION['userinfo'][$exten]['keep'] = 1;
			$ret = self::_error(self::E_OK);
			$ret['ret']['keep'] = 1;
		}
		return $_this->_return($ret, $args['method'], $args['return']);
	} /* }}} */
	
	/**
	 * @fn
	 * @brief 通话恢复.
	 * 	只需要传递工号, 不需要处理分机号.
	 * @param $args['method']
	 * @param $args['return']
	 * @param $args['srcno'] 需要恢复的电话号
	 * @return 
	 */
	function showCallRestore(&$args = array()) /* {{{ */
	{
		$_this = & self::_getInstance();
		if ( $_this == false ) {
			return self::_return(self::_error(self::E_LOGOFF), $args['method'], $args['return']);
		}

		$exten = $args['srcno'];
		/* 恢复 */
		$_res = $_this->restorePark($exten);
		if ( $_res != 'Success' ) {
			$err = $_this->geterror($_res);
			$ret = self::_error(self::E_OP_FAIL, c($err));
		} else {
			$ret = self::_error(self::E_OK);
			$ret['ret']['keep'] = 0;
			$_SESSION['userinfo'][$exten]['keep'] = 0;
		}
		return $_this->_return($ret, $args['method'], $args['return']);
	} /* }}} */

	/**
	 * @fn
	 * @brief 监听电话.
	 * @param $args['method']
	 * @param $args['srcno'] 监听者分机号
	 * @param $args['dstno'] 被监听者分机号
	 * @param $args['return']
	 * @return 
	 */
	function showCallMonitor(&$args = array()) /* {{{ */
	{
		$_this = & self::_getInstance();
		if ( $_this == false ) {
			return self::_return(self::_error(self::E_LOGOFF), $args['method'], $args['return']);
		}

		$monitor = $args['srcno'];
		$monitornum = $args['dstno'];

		$spytype = 'monitor';
		$_res = $_this->chanspy($args['srcno'], $monitornum, $spytype);
		if ( $_res != "Success" ) {
			$err = $_this->geterror($_res);
			return $_this->_return($_this->_error(self::E_OP_FAIL, c($err)), $args['method'], $args['return']);
		}

		return $_this->_return(self::_error(self::E_OK), $args['method'], $args['return']);
	} /* }}} */

	/**
	 * @fn
	 * @brief 强插.
	 * @param $args['method']
	 * @param $args['return']
	 * @param $args['srcno'] 强插者分机号
	 * @param $args['dstno'] 被强插者分机号
	 * @return 
	 */
	function showCallWhisper(&$args = array()) /* {{{ */
	{
		$_this = & self::_getInstance();
		if ( $_this == false ) {
			return self::_return(self::_error(self::E_LOGOFF), $args['method'], $args['return']);
		}

		$monitor = $args['srcno'];
		$monitored = $args['dstno'];
		
		$spytype = 'whisper';
		$_res = $_this->chanspy($monitor, $monitored, $spytype);
		if ( $_res != "Success" ) {
			$err = $_this->geterror($_res);
			return $_this->_return($_this->_error(self::E_OP_FAIL, c($err)), $args['method'], $args['return']);
		}

		return $_this->_return(self::_error(self::E_OK), $args['method'], $args['return']);
	} /* }}} */

	/**
	 * @fn
	 * @brief 强行挂机.
	 * @param $args['method']
	 * @param $args['return']
	 * @param $args['srcno']
	 * @return 
	 */
	function showCallHangup(&$args = array()) /* {{{ */
	{
		$_this = & self::_getInstance();
		if ( $_this == false ) {
			return self::_return(self::_error(self::E_LOGOFF), $args['method'], $args['return']);
		}

		$_res = $_this->hangupcall($args['srcno']);
		if ( $_res != "Success" ) {
			$err = $_this->geterror($_res);
			return $_this->_return($_this->_error(self::E_OP_FAIL, c($err)), $args['method'], $args['return']);
		}

		return $_this->_return(self::_error(self::E_OK), $args['method'], $args['return']);
	} /* }}} */

	/**
	 * @fn
	 * @brief 通话质检...通话服务评分.
	 * @param $args['method']
	 * @param $args['return']
	 * @param $args['srcno']
	 * @return 
	 */
	function showCallFeedBack(&$args = array()) /* {{{ */
	{
		$_this = & self::_getInstance();
		if ( $_this == false ) {
			return self::_return(self::_error(self::E_LOGOFF), $args['method'], $args['return']);
		}

		$exten = $args['srcno'];
                $_res = $_this->feedback($exten);
		if ( $_res != "Success" ) {
			$err = $_this->geterror($_res);
			$ret = self::_error(self::E_OP_FAIL, c($err));
		} else {
			$ret = self::_error(self::E_OK);
		}
		//$ret['ret'] = $_res;
		return $_this->_return($ret, $args['method'], $args['return']);
	} /* }}} */

	/**
	 * @fn
	 * @brief 通话转移(后转).
	 * @param $args['method']
	 * @param $args['return']
	 * @param $args['srcno'] 转出分机
	 * @param $args['dstno'] 转入分机
	 * @return 
	 */
	function showCallTransfer(&$args = array()) /* {{{ */
	{
		$_this = & self::_getInstance();
		if ( $_this == false ) {
			return self::_return(self::_error(self::E_LOGOFF), $args['method'], $args['return']);
		}
		if ( !is_numeric($args['dstno']) || !is_numeric($args['srcno']) ) {
			return $_this->_return(self::_error(self::E_EXTEN_NO_EXIST), $args['method'], $args['return']);
		}
	
		$_res = $_this->transfercall($args['srcno'], $args['dstno']);
		if ( $_res != "Success" ) {
			$err = $_this->geterror($_res);
			return $_this->_return($_this->_error(self::E_OP_FAIL, c($err)), $args['method'], $args['return']);
		}
		return $_this->_return(self::_error(self::E_OK), $args['method'], $args['return']);
	} /* }}} */

	/**
	 * @fn
	 * @brief 三方通话, 多方通话.
	 * @param $args['method']
	 * @param $args['return']
	 * @param $args['srcno'] 邀请者分机号.
	 * @param $args['dstno'] 被邀请者分机号.
	 * @return 
	 */
	function showMultiCall(&$args = array()) /* {{{ */
	{
		$_this = & self::_getInstance();
		if ( $_this == false ) {
			return self::_return(self::_error(self::E_LOGOFF), $args['method'], $args['return']);
		}
		if ( $args['dstno'] == 'null' ) {
			return self::_return(self::_error(self::E_EXTEN_NO_EXIST), $args['method'], $args['return']);
		}
		$args['srcno'] = empty($args['srcno']) ? $_SESSION['userinfo']['extension'] : $args['srcno'];

		$_res = $_this->multiCall($args['srcno'], $args['dstno']);
		if ( $_res != "Success" ) {
			$err = $_this->geterror($_res);
			return $_this->_return($_this->_error(self::E_OP_FAIL, c($err)), $args['method'], $args['return']);
		}
		$ret = self::_error(self::E_OK);
		//$ret['ret'] = $rs;
		return $_this->_return($ret, $args['method'], $args['return']);
	} /* }}} */
	
	/**
	 * @fn
	 * @brief set asterisk DB.
	 * @param $args['method']
	 * @param $args['return']
	 * @param $args['srcno'] key.
	 * @param $args['dstno'] value.
	 *!@param $args['type'] DB name.
	 *!@param $args['return'] 返回数据格式: ajax(默认), xml.
	 * @return 
	 */
	function showSetDB(&$args = array()) /* {{{ */
	{
		$_this = & self::_getInstance();
		if ( $_this == false ) {
			return self::_return(self::_error(self::E_LOGOFF), $args['method'], $args['return']);
		}

		if ( empty($args['srcno']) || empty($args['type']) ) {
			return self::_return(self::_error(self::E_ARGS), $args['method'], $args['return']);
		}

		$re = $_this->_setDB($args['type'], $args['srcno'], $args['dstno']);
		$ret = self::_error(self::E_OK);
		$ret['ret'] = $re['data'];
		return $_this->_return($ret, $args['method'], $args['return']);
	} /* }}} */

	/**
	 * @fn
	 * @brief get asterisk DB.
	 * @param $args['method']
	 * @param $args['return']
	 * @param $args['srcno'] key.
	 *!@param $args['type'] DB name.
	 *!@param $args['return'] 返回数据格式: ajax(默认), xml.
	 * @return 
	 */
	function showGetDB(&$args = array()) /* {{{ */
	{
		$_this = & self::_getInstance();
		if ( $_this == false ) {
			return self::_return(self::_error(self::E_LOGOFF), $args['method'], $args['return']);
		}

		if ( empty($args['srcno']) || empty($args['type']) ) {
			return self::_return(self::_error(self::E_ARGS), $args['method'], $args['return']);
		}

		$re = $_this->_getDB($args['type'], $args['srcno']);
		$ret = self::_error(self::E_OK);
		$ret['ret'] = $re['data'];
		return $_this->_return($ret, $args['method'], $args['return']);
	} /* }}} */

	/**
	 * @fn
	 * @brief 设置分机随行, 通话(前)转移: 无条件转 CF, 遇忙转 CFB, 无应答转 CFU.
	 * @param $args['method']
	 * @param $args['return']
	 * @param $args['srcno'] 需要转移的分机.
	 * @param $args['dstno'] 转出分机, 为空则会删除分机转移数据.
	 *!@param $args['type'] 转移类型: CF(默认), CFB, CFU. (type: '', 'B', 'U')
	 *!@param $args['return'] 返回数据格式: ajax(默认), xml.
	 * @return 
	 */
	function showSetTransfer(&$args = array()) /* {{{ */
	{
		$_this = & self::_getInstance();
		if ( $_this == false ) {
			return self::_return(self::_error(self::E_LOGOFF), $args['method'], $args['return']);
		}

		if ( empty($args['srcno']) ) {
			return self::_return(self::_error(self::E_ARGS), $args['method'], $args['return']);
		} else if ( empty($args['type']) ) {
			//$args['type'] = 'CF';
		}
		$args['type'] = 'CF'.$args['type'];
		if ( $args['srcno'] == $args['dstno'] ) {
			return self::_return(self::_error(self::E_ARGS, '转移分机不能为自己.'), $args['method'], $args['return']);
		}

		$re = $_this->_setDB($args['type'], $args['srcno'], $args['dstno']);
		$ret = self::_error(self::E_OK);
		$ret['ret'] = $re;
		return $_this->_return($ret, $args['method'], $args['return']);
	} /* }}} */

	/**
	 * @fn
	 * @brief 工号座席绑定...
	 * @param $args['method']
	 * @param $args['return']
	 * @return 
	 */
	function showSeat4JobNum(&$args = array()) /* {{{ */
	{
		$_this = & self::_getInstance();
		if ( $_this == false ) {
			return self::_return(self::_error(self::E_LOGOFF), $args['method'], $args['return']);
		}

		return $_this->_return(self::_error(self::E_OK), $args['method'], $args['return']);
	} /* }}} */

	/**
	 * @fn
	 * @brief 取弹屏数据, 根据 type 设置长连接时间, 
	 * 	取到一条数据时就立即返回, 多条数据时返回最后一条.
	 * @param $args['method']
	 * @param $args['return']
	 * @param $args['srcno'] 要弹屏的分机.
	 *!@param $args['type'] 长轮询, 连接(最长返回)时间 = 4 * (type - 1); default: type = 0, null 为默认(20s); type = 1, 没数据则立即返回.
	 * @return 
	 * 	$ret['srcno'] 本机, 
	 * 	$ret['dstno'] 对方电话, 
	 * 	$ret['type'] 类型: 'to' 去电, 'from' 来电, 'inv' 内部分机互打.
	 */
	function showPopWindow(&$args = array()) /* {{{ */
	{
		$_this = & self::_getInstance($args['action']);
		if ( $_this == false ) {
			return self::_return(self::_error(self::E_LOGOFF), $args['method'], $args['return']);
		}

		if ( API_DEBUG == 'debug' ) {
			$_this->wlog('pop.log', $_SERVER["REQUEST_URI"]);
		}
		$long_c = (isset($args['type']) && $args['type'] >= 1) ? $args['type'] : 6;
		$long_t = 4;
		$re = array();
		for ( $i = 0; $i < $long_c; ) {
			$i++;
			$re = $_this->_doPopWindow($args['srcno']);
			if ( count($re) ) {
				break;
			}
			if ( $i < $long_c ) {
				sleep($long_t);
			}
		}

		if ( count($re) ) { 
			$ret = self::_error(self::E_OK);
			$ret['ret'] = $re;
		} else {
			$ret = self::_error(self::E_DATA_EMPTY);
		}
		return $_this->_return($ret, $args['method'], $args['return']);
	} /* }}} */

	/**
	 * @fn
	 * @brief 取弹屏数据.
	 * @param $srcno
	 * @return array()
	 */
	function test_doPopWindow($srcno) /* {{{ */
	{
		$ret = array();

		if ( ($r = rand(100, 200)) % 19 <= 9 ) {
			$ret['srcno'] = $srcno;
			$ret['dstno'] = '15989526'.$r;
			$ret['type'] = $r > 150 ? 'from' : 'to';
		}

		return $ret;
	} /* }}} */

	/**
	 * @fn
	 * @brief test 取弹屏数据.
	 * @param $srcno
	 * @return array()
	 */
	function _doPopWindow($srcno) /* {{{ */
	{
		$ret = array();
		$exten_len = 5;

		$srcno = trim($srcno);
		if ( !$srcno ) {
			return $ret;
		}
		$db = mysql_connect(CFG_AGI_HOST_PBX, 'root', 'hlzx20110928');
		if ( !$db ) {
			return $ret;
		}
		$res = mysql_query("select * from proxyman.newevents where src='$srcno' or dst='$srcno' order by date desc limit 1");
		if ( !$res ) {
			return $ret;
		}
		$res = mysql_fetch_assoc($res);
		if ( !$res ) {
			return $ret;
		}
		$ret['srcno'] = $srcno;
		if ( $res['src'] == $srcno ) {
			$ret['dstno'] = $res['dst'];
			$ret['type'] = strlen($res['dst']) < $exten_len ? 'inv' : 'to';
		} else {
			$ret['dstno'] = $res['src'];
			$ret['type'] = strlen($res['src']) < $exten_len ? 'inv' : 'from';
		}
		$res = mysql_query("delete from proxyman.newevents where src='$srcno' or dst='$srcno'");

		return $ret;
	} /* }}} */

	/**
	 * @fn
	 * @brief 
	 * @param $args['method']
	 * @param $args['return']
	 * @return 
	 */
	function __showXXX(&$args = array()) /* {{{ */
	{
		$_this = & self::_getInstance();
		if ( $_this == false ) {
			return self::_return(self::_error(self::E_LOGOFF), $args['method'], $args['return']);
		}

		return $_this->_return(self::_error(self::E_OK), $args['method'], $args['return']);
	} /* }}} */

	/**
	 * @brief Returns a singleton instance.
	 * 	没有用户数据信息时返回 false.
	 * @return object ok, false 无用户数据.
	 * @access public
	 * @static
	 */
	function &_getInstance($action = null) /* {{{ */
	{
		/*if ( self::doCheckLogin() != true ) {
			return false;
		}*/

		static $instance = NULL;
		if ( !$instance ) {
			$instance = & new api();
		}
		if ( $action != 'PopWindow' && !$instance->linkid ) {
			//$instance->linkid = $instance->AMI_Login();
		}
		return $instance;
	} /* }}} */

	/**
	 * @brief Returns
	 * 	defalut: $method == 'ret', $retrun = array
	 * @param $data data array.
	 * @param $method GET/POST=>'show', 'soap', 内部调用=>'ret'
	 * @param $return 'json', 'xml', 'array'
	 * @return 
	 * @access public
	 */
	function _return($data = array(), $method = 'ret', $return = 'array') /* {{{ */
	{
		$ret = NULL;
		if ( !is_array($data) ) {
			$data = self::_error(self::E_DATA_INVALID);
		}

		if ( $return == 'json' ) {
			$ret = json_encode($data);
			$ret = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $ret);
		} else if ( $return == 'xml' ) {
			$ret = self::arraytoxml($data);
		} else {
			/*( $return == 'array' )*/
			$ret = $data;
		}

		if ( $method == 'show' ) {
			echo $ret;
			exit;
		} else if ( $method == 'soap' ) {
			;
		} else {
			/*( $method == 'ret' )*/
			return $ret;
		}
		return ;
	} /* }}} */

	/**
	 * @brief error info
	 * 	error($errno) 外部或应用层调用
	 * 	_error($errno) api内部调用
	 * @param $errno 接口出错号.
	 * @return string 返回出错号的文字解释. 
	 */
	function error($errno = 0, $addmsg = NULL) /* {{{ */
	{
		$ret = self::_error($errno, $addmsg, false);
		return $ret['errmsg'];
	} /* }}} */

	/**
	 * @brief error info
	 * 	_error($errno) api内部调用, 会自动触发告警信号.
	 * @param $errno 接口出错号.
	 * @param $addmsg 附加提示信息.
	 * @param $warnning 是否触发告警.
	 * @return array() 返回出错号和文字解释数组.
	 *X@access protected
	 * @access public
	 */
	function _error($errno = 0, $addmsg = NULL, $warnning = true) /* {{{ */
	{
		$err = array();
		if ( $warnning ) {
			self::warnning($errno);
		}

		/* $_errinfo = array(
			self::E_OK => 'ok!',
			self::E_ARGS => '参数错误!',
			self::E_LOGOFF => '无用户登陆信息!',
			self::E_DATA_INVALID => '无效数据!',
			self::E_NOOP = '无操作!',
			self::E_IP_DENY = 'IP被拒绝!',
			self::E_DATA_EMPTY = '无数据!',
			self::E_OP_FAIL = '操作失败!',
			self::E_KEEP_NOOP => '没有需要保持的通话!',
			self::E_UNKEEP_NOOP => '没有需要恢复的通话!',
			self::E_EXTEN_NO_CALLING => '分机不在通话中!',
			self::E_EXTEN_NO_EXIST => '分机不存在!',
			self::E_EXTEN_BUSY => '分机忙!',
			self::E_NUMBER_ERROR => '电话号码非法!',
			self::E_EXTEN_OFFLINE => '分机离线!',
			self::E_CHECKSUM => '校验错误, 未受权调用!',
			self::E_API_NO_EXIST => '接口不存在!',
			self::E_SYS_7 => '系统错误, 请联系管理员!',
			self::E_SYS_8 => '系统错误, 请联系管理员!',
			self::E_SYS => '系统严重错误, 请联系管理员!',
			) */
		switch ($errno) {
		case (self::E_OK) :
			$err['errmsg'] = 'ok!'; break;
		case (self::E_ARGS) :
			$err['errmsg'] = '参数错误!'; break;
		case (self::E_LOGOFF) :
			$err['errmsg'] = '无用户登陆信息!'; break;
		case (self::E_DATA_INVALID) :
			$err['errmsg'] = '无效数据!'; break;
		case (self::E_NOOP) :
			$err['errmsg'] = '无操作!'; break;
		case (self::E_IP_DENY) :
			$err['errmsg'] = 'IP被拒绝!'; break;
		case (self::E_DATA_EMPTY) :
			$err['errmsg'] = '无数据!'; break;
		case (self::E_OP_FAIL) :
			$err['errmsg'] = '操作失败!'; break;
		case (self::E_KEEP_NOOP) :
			$err['errmsg'] = '没有需要保持的通话!'; break;
		case (self::E_UNKEEP_NOOP) :
			$err['errmsg'] = '没有需要恢复的通话!'; break;
		case (self::E_EXTEN_NO_CALLING) :
			$err['errmsg'] = '分机不在通话中!'; break;
		case (self::E_EXTEN_NO_EXIST) :
			$err['errmsg'] = '分机不存在!'; break;
		case (self::E_EXTEN_BUSY) :
			$err['errmsg'] = '分机忙!'; break;
		case (self::E_NUMBER_ERROR) :
			$err['errmsg'] = '电话号码非法!'; break;
		case (self::E_EXTEN_OFFLINE) :
			$err['errmsg'] = '分机离线!'; break;
		case (self::E_API_NO_EXIST) :
			$err['errmsg'] = '接口不存在!'; break;
		case (self::E_CHECKSUM) :
			$err['errmsg'] = '校验错误, 未受权调用!'; break;
		case (self::E_SYS_7) :
		case (self::E_SYS_8) :
			$err['errmsg'] = '系统错误, 请联系管理员!'; break;
		case (self::E_SYS) :
			$err['errmsg'] = '系统严重错误, 请联系管理员!';
			/* do other... */
			break;
		default :
			$err['errmsg'] = '未知错误!'; break;
		}
		$err['errno'] = $errno;
		if ( $addmsg != NULL ) {
			$err['errmsg'] = $err['errmsg'].$addmsg;
		}
		return $err;
	} /* }}} */

	/**
	 * @brief 自动告警处理.
	 * 	handler callback function type: doError($errno).
	 * @param $errno 接口出错号, (告警级别).
	 * @param $handler callback, 自定义安装的告警处理程序.
	 * @param $install 'run', 'install', 'remove'
	 * @return void. 
	 * @access public
	 * @static
	 */
	function warnning($errno = 0, $handler = NULL, $install = 'run') /* {{{ */
	{
		static $func = array();

		if ( $install == 'install' ) {
			$func[$errno] = $handler;
			return ;
		}
		if ( $install == 'remove' ) {
			$func[$errno] = NULL;
			return ;
		}

		if ( $handler ) {
			return $handler($errno);
		}
		if ( $func[$errno] ) {
			$_func = $func[$errno];
			return $func[$errno]($errno);
		}

		/* default handler */
		switch ($errno) {
		case (self::E_SYS) :
		case (self::E_SYS_8) :
		case (self::E_SYS_7) :
		case (self::E_API_NO_EXIST) :
			/* email, sms, outcall... 请随意. */
			self::wlog('api-warnning.txt', "E_SYS:$errno: ".self::error($errno)."\r\n".$_SERVER['REQUEST_URI']."\r\nGET:".print_r($_GET, true)."\r\nPOST:".print_r($_POST, true));
			break;
		case (self::E_EXTEN_NO_EXIST) :
		case (self::E_EXTEN_OFFLINE) :
			break;
		default :
			//self::wlog('api-warnning.txt', "$errno: ".self::error($errno)."\r\n".$_SERVER['REQUEST_URI']."\r\nGET:".print_r($_GET, true)."\r\nPOST:".print_r($_POST, true));
			break;
		}
		return ;
	} /* }}} */

	function arraytoxml(&$data = array()) /* {{{ */
	{
		$xml = "<?xml version='1.0' encoding='UTF-8' ?>";
		return $xml.self::_arraytoxml($data);
	} /* }}} */

	function _arraytoxml(&$data = array()) /* {{{ */
	{
		$xml = '';
		foreach ( $data as $key => $val ) {
			if ( is_array($val) ) {
				$xml .= "<$key>".self::_arraytoxml($val)."</$key>";
			} else {
				$xml .= "<$key>$val</$key>";
			}
		}
		return $xml;
	} /* }}} */

	/**
	 * @fn
	 * @brief 日志记录函数
	 * @param 
	 * @return 
	 */
	function wlog($log_file, $log_str, $log_size = 41943040) /* {{{ */
	{
		ignore_user_abort(TRUE);
	
		if ( empty($log_file) ) {
			$log_file = 'wlog.txt';
		}
		if ( defined('APP_LOG_PATH') ) {
			$log_file = APP_LOG_PATH.$log_file;
		}

		if ( !file_exists($log_file) ) { 
			$fp = fopen($log_file, 'a');
		} else if ( filesize($log_file) > $log_size ) {
			$fp = fopen($log_file, 'w');
		} else {
			$fp = fopen($log_file, 'a');
		}

		if ( flock($fp, LOCK_EX) ) {
			$cip	= defined('CLIENT_IP') ? '['.CLIENT_IP.'] ' : '['.getenv('REMOTE_ADDR').'] ';
			$log_str = '['.date('Y-m-d H:i:s').'] '.$cip.$log_str."\r\n";
			fwrite($fp, $log_str);
			flock($fp, LOCK_UN);
		}
		fclose($fp);

		ignore_user_abort(FALSE);
	} /* }}} */
}

/**
 * array('action'=>'', 'method'=>'', 'return'=>'', args...)
 */
function index() /* {{{ */
{
	$ret = '';
	$args = NULL;

	if ( empty($_SESSION['time']) ) {
		$_SESSION['time'] = date('Y-m-d H:i:s');
	}
	if ( !empty($_GET['action']) ) {
		$args = & $_GET;
		$args['call'] = 'get';
	} else if ( !empty($_POST['action']) ) {
		$args = & $_POST;
		$args['call'] = 'post';
	}
	/*if ( $args['action'] == 'login' ) {
		return api::showLogin($args);
	}*/

	$args['method'] = 'show';
	$args['return'] = $args['return'] != 'xml' ? 'json' : 'xml';
	$_this = api::_getInstance($args['action']);
	if ( $_this == false ) {
		return api::_return(api::_error(api::E_LOGOFF), $args['method'], $args['return']);
	}
	if ( $_this->doCheckSum($args) != true ) {
		return $_this->_return($_this->_error(api::E_CHECKSUM), $args['method'], $args['return']);
	}
	$action = 'show'.$args['action'];
	if ( method_exists($_this, $action) ) {
		//$ret = $_this->{$args['action']}($args);
		$ret = api::$action($args);
		return $ret;
	}

	$_this->warnning(api::E_API_NO_EXIST);
	$ret = $_this->_return($_this->_error(api::E_API_NO_EXIST), $args['method'], $args['return']);
	return $ret;
} /* }}} */

//function API($args = array()) 
function API($action = '', $srcno = '', $dstno = '', $type = '', $retrun = '', $chksum = '') /* {{{ */
{
	$ret = '';
	$args['action'] = $action;
	$args['srcno'] = $srcno;
	$args['dstno'] = $dstno;
	$args['type'] = $type;
	$args['return'] = $retrun;
	$args['chksum'] = $chksum;

	$args['method'] = 'ret';
	$args['call'] = 'soap';
	$args['return'] = $args['return'] != 'xml' ? 'json' : 'xml';
	$_this = api::_getInstance($args['action']);
	if ( $_this == false ) {
		return api::_return(api::_error(api::E_LOGOFF), $args['method'], $args['return']);
	}
	if ( $_this->doCheckSum($args) != true ) {
		return $_this->_return($_this->_error(api::E_CHECKSUM), $args['method'], $args['return']);
	}
	$action = 'show'.$args['action'];
	if ( method_exists($_this, $action) ) {
		//$ret = $_this->{$args['action']}($args);
		$ret = api::$action($args);
		return $ret;
	}

	$_this->warnning(api::E_API_NO_EXIST);
	$ret = $_this->_return($_this->_error(api::E_API_NO_EXIST), $args['method'], $args['return']);
	return $ret;
} /* }}} */

function soap() /* {{{ */
{
	$soap = new soap_server; 
	$soap->configureWSDL('api');
	$soap->wsdl->schemaTargetNamespace="urn:api";
	$soap->register('API',
			array('action'=>'xsd:string', 'srcno'=>'xsd:string', 'dstno'=>'xsd:string', 'type'=>'xsd:string', 'return'=>'xsd:string', 'chksum'=>'xsd:string'),
				array("return" => "xsd:string"),
				'urn:api',
				'urn:api#API',
				"rpc",
				"encoded",
				"Wisetalk.cn");
	$soap->service($GLOBALS['HTTP_RAW_POST_DATA']);
} /* }}} */

if ( strstr($_SERVER['REQUEST_URI'], basename(__FILE__)) ) {
	/**
	 * GET/POST
	 * echo $_SERVER['REQUEST_URI']."<br/>\n";
	 */
	if ( !empty($_GET['action']) || !empty($_POST['action']) ) {
		$args['method'] = 'show';
		index();
		exit;
	}

	/**
	 * SOAP
	 */
	if ( is_file($api_config['nusoap_path']) ) {
		require_once($api_config['nusoap_path']);
	}
	soap();
}
?>
