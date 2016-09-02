<?php
/**
 * @file api.class.php
 * @brief asterisk manager interface function api.
 * 应用层功能扩展, 可供内部或外部直接调用.
 * 
 * Copyright (C) 2011 WiseTalk.cn
 * All rights reserved.
 * 
 * @package index
 * @author Langr <hua@langr.org> 2011/12/16 11:27
 * 
 * $Id: api.class.php 44 2012-02-18 04:45:55Z loghua@gmail.com $
 */
/* i_i 此处 sessioni_start() 经常需要 5s 以上时间才返回??
session_start();
 */
define('API_DEBUG', 'run');
if ( !defined('CFG_AGI_HOST_PBX') ) {
	define('CFG_AGI_HOST_PBX', 'localhost');
	define('CFG_AGI_USER_PBX', 'admin');
	define('CFG_AGI_PWD_PBX', 'amp111');
}
if ( !class_exists('ami') ) {
	include(dirname(__FILE__).'/ami.class.php');
}

class api extends ami
{
	public $linkid = NULL;
	public $config = NULL;
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
		/*include(dirname(__FILE__).'/api.config.php');
		 */
		$api_config['allow'] = '0';
		$api_config['allow'] = '192.168.1.166,192.168.1.226,127.0.0.1';
		$api_config['bid'] = '1001';
		$api_config['key'] = 'DeL>rty<:JKO#:k+_p';
		$this->config = $api_config;
		$this->config['allow'] = strlen($api_config['allow']) ? $api_config['allow'] : '0';
		$this->config['key'] = md5(getenv('REMOTE_ADDR').'@'.$api_config['key']);
		$this->linkid = lagi_connect(CFG_AGI_HOST_PBX, '5038', CFG_AGI_USER_PBX, CFG_AGI_PWD_PBX);
	} /* }}} */

	function __destruct() /* {{{ */
	{
		if ( $this->linkid ) {
			lagi_close($this->linkid);
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
		if ( API_DEBUG != 'test' && getenv('REMOTE_ADDR') != '127.0.0.1' ) {
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
	 * @param $args['chksum'] checksum: md5(action+srcno+dstno+md5(client_ip+'#'+key)).
	 * @return true, success; false, failure.
	 */
	function doCheckSum(&$args = array()) /* {{{ */
	{
		if ( API_DEBUG == 'test' ) {
			return true;	/* test */
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
		if ( ($args['type'] == 'mute') || ($args['type'] == 'unmute') || ($args['type'] == 'kick') ) {
			$res = lagi_command('meetme '.$args['type'].' '.$confno.' '.$args['dstno']);
			sleep(1);
		}
		$res = lagi_command('meetme list '.$confno);
		$line= split("\n", $res['data']);
		
		/* ... */

		$ret = $_this->_error(self::E_OK);
		$ret['ret'] = $res;
		return $_this->_return($ret, $args['method'], $args['return']);
	} /* }}} */
	
	/**
	 * @fn
	 * @brief 示忙/闲.
	 * @param $args['method']
	 * @param $args['return']
	 * @param $args['srcno'] 需要示忙的分机.
	 *!@param $args['type'] 示忙动作: 1 (已经示忙)取消示忙, 0 示忙, 当session无效时(soap)需要带type.
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
		if ( $args['type'] != 1 && $_SESSION['userinfo'][$exten]['stat'] != 1 ) {
			$res = lagi_command('database put DND '.$exten.' YES');
			$_SESSION['userinfo'][$exten]['stat'] = 1;
		/* 取消示忙 */
		} else {
			$res = lagi_command('database del DND '.$exten.' ');
			$_SESSION['userinfo'][$exten]['stat'] = 0;
		}

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
		$hints = lagi_hints($args['srcno']);
		if ( $hints[$args['srcno']]['stat'] != 'State:Idle' ) {
			return $_this->_return(self::_error(self::E_EXTEN_BUSY), $args['method'], $args['return']);
		}
		
		$_this->_clickcall($hints[$args['srcno']]['dial'], $clid);

		return $_this->_return(self::_error(self::E_OK), $args['method'], $args['return']);
	} /* }}} */

	/**
	 * @fn
	 * @brief 点击拔号.
	 * @param $args['method']
	 * @param $args['return']
	 * @param $args['srcno'] 主叫号
	 * @param $args['dstno'] 被叫号
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
		if ( strlen($args['dstno']) < 3 ) {
			return $_this->_return(self::_error(self::E_NUMBER_ERROR, $args['dstno']), $args['method'], $args['return']);
		}
		$hints = lagi_hints($args['srcno']);
		if ( $hints[$args['srcno']]['stat'] != 'State:Idle' ) {
			return $_this->_return(self::_error(self::E_EXTEN_BUSY), $args['method'], $args['return']);
		}
		
		$_this->_outcall($hints[$args['srcno']]['dial'], $args['srcno'], $args['dstno']);

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
		$de = $_this->_getDialByExt($clid);
		if ( $de ) {
			$_this->_clickcall($de, trim($args['srcno']));
			return $_this->_return(self::_error(self::E_OK), $args['method'], $args['return']);
		} else {
			/*TODO: $prefix = $_this->getdialout();*/
			$firstclid = substr($target, 0, 1);
			$targetnum = 'local/';
			if ( $firstclid ) {
				$targetnum .= $prefix['local'];
				$targetnum .= $clid;
			} else {
				$targetnum .= $prefix['long'];
				$targetnum .= $clid;
			}
			$targetnum .= '@from-internal';
			$_this->_clickcall($targetnum, trim($args['srcno']));
		}
		return $_this->_return(self::_error(self::E_OK), $args['method'], $args['return']);
	} /* }}} */
	
	/**
	 * @fn
	 * @brief 通话保持.
	 * 	NOTE: _SESSION 对接口应只起附加功能, 因为很可能接口调用者无法获取session_id.
	 * @param $args['method']
	 * @param $args['return']
	 * @param $args['srcno'] 需要保持/恢复的电话号
	 *!@param $args['dstno'] 需要恢复的电话保持时的通道, type = 0.
	 *!@param $args['type'] 保持动作: 1 (已经保持了)取消保持, 0 保持, 当session无效时(soap)需要带type.
	 * @return keep 被保持的状态, 1 保持了, 0 未保持.
	 *!@return channel 被保持的通道.
	 */
	function showCallKeep(&$args = array()) /* {{{ */
	{
		$_this = & self::_getInstance();
		if ( $_this == false ) {
			return self::_return(self::_error(self::E_LOGOFF), $args['method'], $args['return']);
		}

		$exten = $args['srcno'];
		$extenchannel = $_this->_getDialByExt($exten);
		/* 保持 */
		if ( $args['type'] != 1 && $_SESSION['userinfo'][$exten]['keep'] != 1 ) {
			$rs = lagi_command('show channels');
			if ( !substr_count($rs['data'], 'active') ) {
				return self::_return(self::_error(self::E_EXTEN_NO_CALLING), $args['method'], $args['return']);
			}
			$thischannels = lagi_channel_analyse(trim($exten), $rs['data']);
			$_this->_transfercall($thischannels[1], 70);
			sleep(2);				/* ... asterisk 更新数据, 需要时间等待 */
			$rs = lagi_command('show parkedcalls');
			$thispark = lagi_parked($thischannels[1], $rs['data']);
			if ( $thispark == false ) {
				return $_this->_return(self::_error(self::E_KEEP_NOOP), $args['method'], $args['return']);
			}
			$_SESSION['userinfo'][$exten]['keep'] = 1;
			$_SESSION['userinfo'][$exten]['park'] = $thispark;
			$_SESSION['userinfo'][$exten]['channel'] = $thischannels[1];
			$ret = self::_error(self::E_OK);
			$ret['ret']['keep'] = 1;
			$ret['ret']['channel'] = $thischannels[1];
			return $_this->_return(self::_error(self::E_OK), $args['method'], $args['return']);
		/* 恢复 */
		} else {
			$rs = lagi_command('show parkedcalls');
			$dstchannel = empty($args['dstno']) ? $_SESSION['userinfo'][$exten]['channel'] : $args['dstno'];
			$thispark = lagi_parked($dstchannel, $rs['data']);
			if ( $thispark == false ) {
				$_SESSION['userinfo'][$exten]['keep'] = 0;
				$_SESSION['userinfo'][$exten]['park'] = 0;
				$_SESSION['userinfo'][$exten]['channel'] = '';
				$ret = self::_error(self::E_UNKEEP_NOOP);
				$ret['ret']['keep'] = 0;
				return $_this->_return($ret, $args['method'], $args['return']);
			}
			$dial = $extenchannel;
			$res_park = $_this->_restorePark($dial, $exten, $thispark);
			if ( $res_park == 'Success' ) {
				$_SESSION['userinfo'][$exten]['keep'] = 0;
				$_SESSION['userinfo'][$exten]['park'] = 0;
				$_SESSION['userinfo'][$exten]['channel'] = '';
				$ret = self::_error(self::E_OK);
				$ret['ret']['keep'] = 0;
				return $_this->_return($ret, $args['method'], $args['return']);
			}
			return $_this->_return(self::_error(self::E_NOOP), $args['method'], $args['return']);
		}
	} /* }}} */
	
	/**
	 * @fn
	 * @brief 监听电话.
	 * @param $args['method']
	 * @param $args['srcno'] 监听者分机号
	 * @param $args['dstno'] 被监听者分机号
	 * @param $args['return']
	 * @param $args['monitornum']
	 * @return 
	 */
	function showCallMonitor(&$args = array()) /* {{{ */
	{
		$_this = & self::_getInstance();
		if ( $_this == false ) {
			return self::_return(self::_error(self::E_LOGOFF), $args['method'], $args['return']);
		}

		$monitor = $args['srcno'];
		$caller = $args['monitornum'];
		$target_dial = $_this->_getDialByExt(trim($args['monitornum']));
		/*$target_rs = explode('/', $target_dial);
		$target_device = $target_rs['1'];*/

		$if = '';
		$chan = '';
		$monitorif = '';
		/* 被监听分机的dial */
		$rs = lagi_command('database show DIAL/'.$target_dial);
		$lines = explode("\n", $rs['data']);
		foreach ( $lines as $key => $line ) {
			preg_match("/\/([0-9a-zA-Z]+\/[0-9a-zA-Z]+)\s+:\s+([0-9a-zA-Z]+)/", $line, $match);
			if ( $match[2] == $caller ) {
				$if = $match[1];
			}
		}
		/* 监听分机（管理员）的dial */
		$monitor_dial	= $_this->_getDialByExt(trim($monitor));
		$rs = lagi_command('database show DIAL/'.$monitor_dial);
		$lines = explode("\n", $rs['data']);
		foreach ( $lines as $key => $line ) {
			preg_match("/\/([0-9a-zA-Z]+\/[0-9a-zA-Z]+)\s+:\s+([0-9a-zA-Z]+)/", $line, $match);
			if ( $match[2] == $monitor ) {
				$monitorif = $match[1];
			}
		}

		if ( empty($if) ) {
			return $_this->_return(self::_error(self::E_EXTEN_NO_EXIST, '无法查找被监听分机'), $args['method'], $args['return']);
		}
		if ( empty($monitorif) ) {
			return $_this->_return(self::_error(self::E_EXTEN_NO_EXIST, '无法查找管理分机'), $args['method'], $args['return']);
		}
		
		$callerif = $if;
		$if = str_replace('/', '\/', $if);
		$rs = lagi_command('core show channels');
		$lines = explode("\n", $rs['data']);
		foreach ( $lines as $key => $line ) {
			if ( preg_match("/^($if-[0-9a-zA-Z]+)/", $line, $match) ) {
				$chan = $match[1];
			}
		}

		if ( empty($chan) ) {
			return $_this->_return(self::_error(self::E_EXTEN_NO_CALLING), $args['method'], $args['return']);
		}
		/*$re = lagi_action("Action: originate\r\nChannel: $monitorif\r\nWaitTime: 50\r\nCallerId: Monitor <>\r\nExten: 0$callerif\r\nContext: wisetalk-monitor\r\nPriority: 1\r\n\r\n");*/
		$re = lagi_originate($monitorif, 'Monitor<>', '0'.$callerif, 'wisetalk-monitor', 1);

		return $_this->_return(self::_error(self::E_OK), $args['method'], $args['return']);
	} /* }}} */

	/**
	 * @fn
	 * @brief 监听...
	 * @param $args['method']
	 * @param $args['return']
	 * @return 
	 */
	function showCallMonitor_disable(&$args = array()) /* {{{ */
	{
		$_this = & self::_getInstance();
		if ( $_this == false ) {
			return self::_return(self::_error(self::E_LOGOFF), $args['method'], $args['return']);
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
		$target_dial = $_this->_getDialByExt($monitored);
		/*$target_rs = explode('/', $target_dial);
		$target_device = $target_rs['1'];*/

		$if = '';
		$chan = '';
		$monitorif = '';
		/* 被强插分机的dial */
		/***$rs = lagi_command('database show DIAL/'.$target_dial);
		$lines = explode("\n", $rs['data']);
		foreach ( $lines as $key => $line ) {
			preg_match("/\/([0-9a-zA-Z]+\/[0-9a-zA-Z]+)\s+:\s+([0-9a-zA-Z]+)/", $line, $match);
			if ( $match[2] == $monitored ) {
				$if = $match[1];
			}
		}*/
		/* 强插分机（管理员）的dial */
		$monitor_dial	= $_this->_getDialByExt($monitor);
		/***$rs = lagi_command('database show DIAL/'.$monitor_dial);
		$lines = explode("\n", $rs['data']);
		foreach ( $lines as $key => $line ) {
			preg_match("/\/([0-9a-zA-Z]+\/[0-9a-zA-Z]+)\s+:\s+([0-9a-zA-Z]+)/", $line, $match);
			if ( $match[2] == $monitor ) {
				$monitorif = $match[1];
			}
		}*/

		if ( empty($if) ) {
			return $_this->_return(self::_error(self::E_EXTEN_NO_EXIST, '无法查找需要插断的分机'), $args['method'], $args['return']);
		}
		if ( empty($monitorif) ) {
			return $_this->_return(self::_error(self::E_EXTEN_NO_EXIST, '无法查找管理分机'), $args['method'], $args['return']);
		}
		
		$monitoredif = $if;
		$if = str_replace('/', '\/', $if);
		$rs = lagi_command('core show channels');
		$channel = lagi_channel_analyse($if, $rs['data']);
		$chan = $channel[0];
		/***$lines = explode("\n", $rs['data']);
		foreach ( $lines as $key => $line ) {
			if ( preg_match("/^($if-[0-9a-zA-Z]+)/", $line, $match) ) {
				$chan = $match[1];
			}
		}*/

		if ( empty($chan) ) {
			return $_this->_return(self::_error(self::E_EXTEN_NO_CALLING, "被强拆分机不在通话状态."), $args['method'], $args['return']);
		}
		/*$re = lagi_action("Action: originate\r\nChannel: $monitorif\r\nWaitTime: 50\r\nCallerId: Monitor <>\r\nExten: $monitored\r\nContext: wisetalk-whisper\r\nPriority: 1\r\n\r\n");*/
		$re = lagi_originate($monitorif, 'Monitor<>', $monitored, 'wisetalk-whisper', 1);

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

		$rs = lagi_command('show channels');
		$dial = $_this->_getDialByExt(trim($args['srcno']));
		$extenchannel = $dial;
		$channel = lagi_channel_analyse($args['srcno'], $rs['data']);
		if ( $channel[0] ) {
			$_this->_hangupcall($channel[0]);
			return $_this->_return(self::_error(self::E_OK), $args['method'], $args['return']);
		} else {
			return $_this->_return(self::_error(self::E_EXTEN_NO_CALLING), $args['method'], $args['return']);
		}

		return $_this->_return(self::_error(self::E_OK), $args['method'], $args['return']);
	} /* }}} */

	/**
	 * @fn
	 * @brief 通话质检...通话服务评分.
	 * @param $args['method']
	 * @param $args['return']
	 * @param $args['srcno']
	 * @param $args['type'] 要打分的 featurecodes.
	 * @return 
	 */
	function showCallFeedBack(&$args = array()) /* {{{ */
	{
		$_this = & self::_getInstance();
		if ( $_this == false ) {
			return self::_return(self::_error(self::E_LOGOFF), $args['method'], $args['return']);
		}

		$rs = lagi_command('show channels');
		$extenchannel = $_SESSION['userinfo']['dial'];
		$channel = lagi_channel_analyse($args['srcno'], $rs['data']);
		/* 把对方的通话通道转接到要打分的 featurecodes */
		if ( $channel[0] ) {
			/* select * from asterisk.featurecodes where featurename='feedback' */
			$feedkey = '15';
			$is_set	= lagi_action("Action: SetVar\r\nChannel: {$channel[1]}\r\nVariable: num\r\nValue: {$_SESSION['userinfo']['extension']}\r\n\r\n");
			$_this->_transfercall($channel[1], $feedkey);
			return $_this->_return(self::_error(self::E_OK), $args['method'], $args['return']);
		} else {
			return $_this->_return(self::_error(self::E_EXTEN_NO_CALLING), $args['method'], $args['return']);
		}
	} /* }}} */

	/**
	 * @fn
	 * @brief 电话转移(后转).
	 * @param $args['method']
	 * @param $args['return']
	 * @param $args['srcno']
	 * @param $args['dstno']
	 * @return 
	 */
	function showCallTransfer(&$args = array()) /* {{{ */
	{
		$_this = & self::_getInstance();
		if ( $_this == false ) {
			return self::_return(self::_error(self::E_LOGOFF), $args['method'], $args['return']);
		}

		if ( $args['dstno'] == 'null' || !is_numeric($args['dstno']) || !is_numeric($args['srcno']) ) {
			return $_this->_return(self::_error(self::E_EXTEN_NO_EXIST), $args['method'], $args['return']);
		}
	
		$rs = lagi_command('show channels');
		/* 获取转出源分机的通道 */
		$extenchannel = $_this->_getDialByExt($args['srcno']);
		$channel = lagi_channel_analyse($args['srcno'], $rs['data']);

		if ( !$channel[1] ) {
			return $_this->_return(self::_error(self::E_EXTEN_NO_CALLING), $args['method'], $args['return']);
		}
		/* 转出目的分机的设备号 */
		/*$target_device = $_this->_getDialByExt($args['targetexten']);
		$target_rs = explode('/', $target_device);
		$target_device = $target_rs['1'];*/
		$target_device = $args['dstno'];
		$status = lagi_hints($args['dstno']);

		/* 示忙 */
		if ( strstr($_this->_getDnd($args['dstno']), 'YES') ) {
			return $_this->_return(self::_error(self::E_EXTEN_BUSY, '目标分机不在空闲状态'), $args['method'], $args['return']);
		/* 通话中 */
		} else if ( $status[$target_device]['stat'] == 'State:InUse' ) {
			return $_this->_return(self::_error(self::E_EXTEN_BUSY, '目标分机正在通话中'), $args['method'], $args['return']);
		/* 不可用 */
		} else if ( $status[$target_device]['stat'] == 'State:Unavailable' ) {
			return $_this->_return(self::_error(self::E_EXTEN_OFFLINE, '目标分机不在线'), $args['method'], $args['return']);
		} else {
			$_this->_transfercall($channel[1], $args['dstno']);
		}

		return $_this->_return(self::_error(self::E_OK), $args['method'], $args['return']);
	} /* }}} */

	/**
	 * @fn
	 * @brief 转移传真...
	 * @TODO ...
	 * @param $args['method']
	 * @param $args['return']
	 * @param $args['srcno']
	 * @param $args['dstno']
	 * @return 
	 */
	function showFaxTransfer(&$args = array()) /* {{{ */
	{
		$_this = & self::_getInstance();
		if ( $_this == false ) {
			return self::_return(self::_error(self::E_LOGOFF), $args['method'], $args['return']);
		}

		if ( $args['dstno'] == 'null' || !is_numeric($args['dstno']) || !is_numeric($args['srcno']) ) {
			return $_this->_return(self::_error(self::E_EXTEN_NO_EXIST), $args['method'], $args['return']);
		}
	
		$rs = lagi_command('show channels');
		/* 获取转出源分机的通道 */
		$extenchannel = $_this->_getDialByExt($args['srcno']);
		$channel = lagi_channel_analyse($args['srcno'], $rs['data']);

		if ( !$channel[1] ) {
			return $_this->_return(self::_error(self::E_EXTEN_NO_CALLING), $args['method'], $args['return']);
		}
		$_this->_transfercall($channel[1], $args['targetexten']);

		return $_this->_return(self::_error(self::E_OK), $args['method'], $args['return']);
	} /* }}} */

	/**
	 * @fn
	 * @brief 队列管理...
	 * @param 
	 * @return 
	 */
	function showQueueList(&$args = array()) /* {{{ */
	{
		$_this = & self::_getInstance();
		if ( $_this == false ) {
			return self::_return(self::_error(self::E_LOGOFF), $args['method'], $args['return']);
		}

		/* TODO... */

		return $_this->_return(self::_error(self::E_OK), $args['method'], $args['return']);
	} /* }}} */

	/**
	 * @fn
	 * @brief 点击拔号, 第三方用, 什么意思哦?
	 * @param $args['method']
	 * @param $args['return']
	 * @param $args['srcno']
	 * @param $args['dstno']
	 * @return 
	 */
	function showExtClickCall(&$args = array()) /* {{{ */
	{
		$_this = & self::_getInstance();
		if ( $_this == false ) {
			return self::_return(self::_error(self::E_LOGOFF), $args['method'], $args['return']);
		}

		$exten = $args['srcno'];
		$hints = lagi_hints($exten);
		$dial = $_this->_getDialByExt($exten);
		if ( $hints[$exten]['stat'] != 'State:Idle' ) {
			return $_this->_return(self::_error(self::E_EXTEN_BUSY, '分机'.$exten.'不在空闲状态'), $args['method'], $args['return']);
		}
		
		$_this->_clickcall($dial, $args['dstno']);
		return $_this->_return(self::_error(self::E_OK), $args['method'], $args['return']);
	} /* }}} */

	/**
	 * @fn
	 * @brief 三方通话, 多方通话.
	 * @param $args['method']
	 * @param $args['return']
	 * @param $args['srcno']
	 * @param $args['dstno']
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
		$status = lagi_hints($args['srcno']);
		if ( $status[$args['srcno']]['stat'] != 'State:Busy' && $status[$args['srcno']]['stat'] != 'State:InUse' ) {
			return $_this->_return(self::_error(self::E_EXTEN_NO_CALLING), $args['method'], $args['return']);
		}

		$re = $_this->_multicall($args['srcno'], $args['dstno']);
		$ret = self::_error(self::E_OK);
		$ret['ret'] = $re;
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
		$ret['ret'] = $re;
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
		$ret['ret'] = $re;
		return $_this->_return($ret, $args['method'], $args['return']);
	} /* }}} */

	/**
	 * @fn
	 * @brief 通话转移: 无条件转 CF, 遇忙转 CFB, 无应答转 CFU.
	 * @param $args['method']
	 * @param $args['return']
	 * @param $args['srcno'] 需要转移的分机.
	 * @param $args['dstno'] 转出分机, 为空则会删除分机转移数据.
	 *!@param $args['type'] 转移类型: CF(默认), CFB, CFU.
	 *!@param $args['return'] 返回数据格式: ajax(默认), xml.
	 * @return 
	 */
	function showSetCF(&$args = array()) /* {{{ */
	{
		$_this = & self::_getInstance();
		if ( $_this == false ) {
			return self::_return(self::_error(self::E_LOGOFF), $args['method'], $args['return']);
		}

		if ( empty($args['srcno']) ) {
			return self::_return(self::_error(self::E_ARGS), $args['method'], $args['return']);
		} else if ( empty($args['type']) ) {
			$args['type'] = 'CF';
		}
		if ( $args['srcno'] == $args['dstno'] ) {
			return self::_return(self::_error(self::E_ARGS, '转移分机不能为自己.'), $args['method'], $args['return']);
		}

		$re = $_this->_setDB($args['type'], $args['srcno'], $args['dstno']);
		$re = $_this->_getCF($args['type'], $args['srcno']);
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
		$_this = & self::_getInstance();
		if ( $_this == false ) {
			return self::_return(self::_error(self::E_LOGOFF), $args['method'], $args['return']);
		}

		$long_c = (isset($args['type']) && $args['type'] >= 1) ? $args['type'] : 6;
		$long_t = 4;
		$re = array();
		for ( $i = 0; $i < $long_c; ) {
			$i++;
			$_this->wlog('poplog.txt', $_SERVER["REQUEST_URI"]);
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
		$db = mysql_connect('localhost', 'root', 'hlzx20110928');
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
	function &_getInstance() /* {{{ */
	{
		/*if ( self::doCheckLogin() != true ) {
			return false;
		}*/

		static $instance = NULL;
		if ( !$instance ) {
			$instance = & new api();
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
			self::wlog('api-warnning.txt', "$errno: ".self::error($errno)."\r\n".$_SERVER['REQUEST_URI']."\r\nGET:".print_r($_GET, true)."\r\nPOST:".print_r($_POST, true));
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
	$_this = api::_getInstance();
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
	$_this = api::_getInstance();
	if ( $_this == false ) {
		return api::_return(api::_error(api::E_LOGOFF), $args['method'], $args['return']);
	}
	/*if ( $_this->doCheckSum($args) != true ) {
		return $_this->_return($_this->_error(api::E_CHECKSUM), $args['method'], $args['return']);
	}*/
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
	require_once("../../../lib/nusoap/nusoap.php");
	soap();
}

?>
