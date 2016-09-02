<?php
/**
 * @file AccountController.class.php
 * @brief 账号资金，流水相关
 * 
 * Copyright (C) 2016 ZKC.com
 * All rights reserved.
 * 
 * @package Controller
 * 
 * $Id$
 */

namespace Home\Controller;
use Home\Controller\AppController;
class AccountController extends AppController 
{
	/**
	 * @brief 
	 * @param 
	 *!@param appid
	 * @return 
	 * {
	 * 	"info":[{},{}],
	 * }
	 */
	public function index() /* {{{ */
	{
		/* 当前授权用户信息： */
		$token = self::$current_token;
		$uid = self::$current_uid;
		$appid = self::$current_appid;

		/* 客户端传递的参数在：$this->args; */
		/* TODO: ... */
		$data = array();

		return $this->_return($data);
	} /* }}} */
}

/* end file */
