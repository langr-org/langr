<?php
/**
 * @file ami.class.php
 * @brief asterisk manager interface function.
 * 对底层接口的打包和扩展.
 * 
 * Copyright (C) 2011 WiseTalk.cn
 * All rights reserved.
 * 
 * @package index
 * @author Langr <hua@langr.org> 2011/12/15 10:14
 * 
 * $Id: ami.class.php 42 2012-01-31 08:23:44Z loghua@gmail.com $
 */

/*class ami extends Cortrol*/
class ami 
{
	/**
	 * @fn
	 * @brief 
	 * @param 
	 * @return 
	 */
	function _restorePark($channel, $user, $parkno) /* {{{ */
	{
		$re = lagi_action("Action: originate\r\nChannel: $channel\r\nWaitTime: 30\r\nCallerId: restorepark <$user>\r\nExten: $parkno\r\nContext: from-internal\r\nPriority: 1\r\n\r\n");
		return $re['Response'];
	} /* }}} */
	
	/**
	 * @fn
	 * @brief 转移.
	 * @param 
	 * @return 
	 */
	function _transfercall($channel, $tarnum) /* {{{ */
	{
		$res = lagi_action("Action: redirect\r\nChannel: $channel\r\nExten: $tarnum\r\nContext: from-internal\r\nPriority: 1\r\n\r\n");
		return ;
	} /* }}} */
	
	function _transfercall_web($channel, $tarnum) /* {{{ */
	{
		$res = lagi_action("Action: atredirect\r\nChannel: $srcchannel\r\nExten: $tarnum\r\nContext: from-internal\r\nPriority: 1\r\n\r\n");
		return ;
	} /* }}} */
	
	/**
	 * @fn
	 * @brief 通过分机号查DIAL.
	 * @param 
	 * @return 
	 */
	function _getDialByExt($exten) /* {{{ */
	{
		$res = lagi_command('database show USER '.$exten);
		$dial = explode(':', $res['data']);
		$dial = trim($dial[2]);
		return $dial;
	} /* }}} */

	/**
	 * @fn
	 * @brief 通过分机号查Device.
	 * @param 
	 * @return 
	 */
	function _getDevByExt($exten) /* {{{ */
	{
		$res = lagi_command('database show AMPUSER '.$exten.'/device');
		$dev = explode(':', $res['data']);
		$dev = trim($dev[2]);
		return $dev;
	} /* }}} */

	/**
	 * @fn
	 * @brief 通过通道号查来电号码.
	 * @param 
	 * @return 
	 */
	function _getClidByChannel($channel) /* {{{ */
	{
		$res = lagi_command('database show CALLER '.$channel);
		$clid = explode(':', $res['data']);
		$clid = trim($clid[2]);
		return $clid;
	} /* }}} */

	/**
	 * @fn
	 * @brief 通过DIAL查ID.
	 * @param 
	 * @return 
	 */
	function _getIdByDial($dial) /* {{{ */
	{
		$res = lagi_command('database show DIAL '.$dial);
		$dial = explode(':', $res['data']);
		$dial = trim($dial[2]);
		return $dial;
	} /* }}} */

	/**
	 * @fn
	 * @brief 查询DIAL.
	 * @param 
	 * @return 
	 */
	function _getDial($dial) /* {{{ */
	{
		$res = lagi_command('database show DIAL '.$dial);
		$lines = explode("\n", $res['data']);
		unset($lines[0]);
		foreach($lines as $key => $line)
		{
			$line = str_replace('/DIAL/', '', $line);
			$line = explode(':', $line);
			$_dial = $line[0];

			$list[trim($_dial)] = trim($line[1]);
		}
	        return $list;
	} /* }}} */

	/**
	 * @fn
	 * @brief 通过XX查CF, CFB, CFU.
	 * 	无条件转, 遇忙转, 无应答转.
	 * @param 
	 * @return 
	 */
	function _getCF($type = 'CF', $exten = '') /* {{{ */
	{
		$result = lagi_dbop('show '.$type.' '.$exten);

		$lines	= explode("\n", $result['data']);
		foreach ( $lines as $key => $line ) {
			/*if ( $line[0] == '/' )*/ 
			if ( preg_match("/^\//", $line) ) {
				$array = preg_split("/\s+/",$line);
				$ext = substr(trim($array[0]), strlen($type) + 2);
				$cf[$ext] = $array[2];
			}
		}
		return $cf;
	} /* }}} */

	/**
	 * @fn
	 * @brief get DB.
	 * @param 
	 * @return 
	 */
	function _getDB($family, $key) /* {{{ */
	{
		$result = lagi_dbop('show '.$family.' '.$key);
		return $result;
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
			$result = lagi_dbop("put $family $key $value");
		} else {
			$result = lagi_dbop("del $family $key");
		}
        	return $result;
	} /* }}} */
	
	/**
	 * @fn
	 * @brief 
	 * @param 
	 * @return 
	 */
	function _getDnd($exten) /* {{{ */
	{
		$res = lagi_command('database get DND '.$exten);
		return $res['data'];
	} /* }}} */

	/**
	 * @fn
	 * @brief 端口挂断通道
	 * @param 
	 * @return 
	 */
	function _hangupcall($channel) /* {{{ */
	{
		$res = lagi_action("Action: hangup\r\nChannel: $channel\r\n\r\n");
		return ;
	} /* }}} */
	
	/**
	 * @fn
	 * @brief 电话监听
	 * NOTE: 更灵活的应用可以使用 lagi_originate() 代替 lagi_outcall().
	 * @param $channel 当前拔出电话通道. 
	 * @param $exten 当前被拔(接听)电话号. 
	 * @return 
	 */
	function _originatecall($channel, $exten) /* {{{ */
	{
		$caller = $this->_getIdByDial($channel);
		$re = lagi_outcall($channel, $caller, $exten);
		return ;
	} /* }}} */

	/**
	 * @fn
	 * @brief 点击拔号
	 * NOTE: 更灵活的应用可以使用 lagi_originate() 代替 lagi_outcall().
	 * @param $channel 当前主叫电话通道. 
	 * @param $exten 当前被叫电话号. 
	 * @return 
	 */
	function _clickcall($channel, $exten) /* {{{ */
	{
		$prefix	= '';
		if ( strlen($exten) >= 5 ) {
			$prefix = outcall_get_prefix($channel, $exten);
		}
		if ( strpos($channel, '@') === false ) {
			$exten = $prefix.$exten;
		} else {
			/* 加入会议室的号码 */
			preg_match( '/\/([\d]+)@/', $channel, $matches);
			$call_num = $matches[1];
			$channel = str_replace($call_num, $prefix.$call_num, $channel);
		}
		/*
		$caller = $this->_getIdByDial($channel);
		$re = lagi_outcall($channel, $caller, $exten);
		 */
		$re = lagi_outcall($channel, $_SESSION['userinfo']['extension'], $exten);
		return ;
	} /* }}} */

	/**
	 * @fn
	 * @brief 点击拔号...
	 * NOTE: 更灵活的应用可以使用 lagi_originate() 代替 lagi_outcall().
	 * @param $channel 当前主叫电话通道. 
	 * @param $exten 当前被叫电话号. 
	 * @return 
	 */
	function _outcall($channel, $caller, $exten) /* {{{ */
	{
		$prefix	= '';
		if ( strlen($exten) >= 5 ) {
			$prefix = outcall_get_prefix($channel, $exten);
		}
		if ( strpos($channel, '@') === false ) {
			$exten = $prefix.$exten;
		} else {
			/* 加入会议室的号码 */
			preg_match( '/\/([\d]+)@/', $channel, $matches);
			$call_num = $matches[1];
			$channel = str_replace($call_num, $prefix.$call_num, $channel);
		}
		/*$caller = $this->_getIdByDial($channel);*/
		$re = lagi_outcall($channel, $caller, $exten);
		return ;
	} /* }}} */
	
	/**
	 * @fn
	 * @brief 
	 * @param 
	 * @return 
	 */
	function _multicall($caller, $called) /* {{{ */
	{
		/*$re = lagi_action("Action: multicalling\r\nVariables1: $caller\r\nVariables2: $called\r\n\r\n");*/
		$re = lagi_action("Action: threewaycalling\r\nVariables1: $caller\r\nVariables2: $called\r\n\r\n");
		return trim(substr($re['Response'], 0, 2));
	} /* }}} */
}

/* {{{ */
if ( !function_exists('outcall_get_prefix') ) {
	function outcall_get_prefix($channel, $called) {
		return '';
	}
}
/* }}} */
?>
