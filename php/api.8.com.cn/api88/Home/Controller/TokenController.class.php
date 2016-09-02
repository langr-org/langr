<?php
/**
 * @file TokenController.class.php
 * @brief 用户信息接口
 * 
 * Copyright (C) 2016 ZKC.com
 * All rights reserved.
 * 
 * @package Controller
 * @author Langr <hua@langr.org> 2016/06/24 10:08
 * 
 * $Id: TokenController.class.php 62479 2016-06-23 02:23:26Z huanghua $
 */

namespace Home\Controller;
use Home\Controller\AppController;
class TokenController extends AppController 
{
	public function index() /* {{{ */
	{
		switch ($_SERVER['REQUEST_METHOD']) {
		case 'GET' :
			break;
		case 'POST' :
			/* 登陆 */
			$this->login();
			break;
		case 'PUT' :
			/* 刷新 */
			$this->refresh();
			break;
		case 'PATCH' :
			break;
		case 'DELETE' :
			/* 登出 */
			$this->logout();
			break;
		case 'OPTIONS' :
			break;
		default :
			break;
		}

		return $this->_return(self::_error(self::E_METHOD));
	} /* }}} */

	/**
	 * @brief 登陆/获取token受权接口
	 * 	此接口默认返回OAuth2 密码模式 json数据格式，
	 * request method: POST 此接口建议使用https
	 * @param grant_type=password
	 * @param username
	 * @param password md5(password)
	 *!@param appid
	 * @return access_token
	 * {
	 * 	"access_token":"2YotnFZFEjr1zCsicMWpAA",
	 * 	"token_type":"Bearer",
	 * 	"expires_in":3600,
	 * 	"refresh_token":"tGzv3JOkF0XG5Qx2TlKWIA"
	 * }
	 * http code 返回出错码: 200 ok, 4xx为出错
	 */
	public function login() /* {{{ */
	{
		//$redis = $this->data_store->FRedis;

		/* do login */
		$user = I('username');
		//$pwd = I('post.password');
		$pwd = I('password');
		if (empty($user) || empty($pwd)) {
			return $this->_return(self::_error(451, '账号或密码错误！'));
			//return $this->_return(self::_error(451, 'args error.'));
		}
		//$m = new \Home\Model\UsersModel();
		$m = D('Users');
		$res = $m->login($user, $pwd);
		if ($res == false) {
			/* TODO: 限制登陆次数... */
			return $this->_return(self::_error(452, '密码错误！'));
			//return $this->_return(self::_error(452, 'password error.'));
		}
		/* 登陆成功 */
		$uid = $res['userid'];
		$appid = I('appid');

		/* set token */
		$token = $this->data_store->refreshAccessToken($uid, $appid, EXPIRES_TIME);
		if ($token === false) {
			return $this->_return(self::_error(self::E_OP_FAIL));
		}

		$data = array('access_token'=>$token['key'], 'token_type'=>'Bearer', 'expires_in'=>EXPIRES_TIME);
		/* return */
		return $this->_return(self::_error(self::E_OK, 'login ok.', $data));
	} /* }}} */

	/**
	 * @brief 刷新token受权接口
	 * 	此接口默认返回OAuth2 密码模式 json数据格式，
	 * request method: PUT 此接口建议使用https
	 * @param access_token old_token
	 * @param refresh_token	暂未使用
	 * @return access_token
	 * {
	 * 	"access_token":"2YotnFZFEjr1zCsicMWpAA",
	 * 	"token_type":"Bearer",
	 * 	"expires_in":3600,
	 * 	"refresh_token":"tGzv3JOkF0XG5Qx2TlKWIA"
	 * }
	 * http code: 200 ok, 4xx为出错
	 */
	public function refresh() /* {{{ */
	{
		$uid = self::$current_uid;
		$appid = self::$current_appid;

		/* refresh token */
		$token = $this->data_store->refreshAccessToken($uid, $appid, EXPIRES_TIME);
		if ($token === false) {
			return $this->_return(self::_error(self::E_OP_FAIL));
		}

		$data = array('access_token'=>$token['key'], 'token_type'=>'Bearer', 'expires_in'=>EXPIRES_TIME);
		/* return */
		return $this->_return(self::_error(self::E_OK, 'refresh token ok.', $data));
	} /* }}} */

	/**
	 * @brief 登出，删除token
	 * request method: DELETE
	 * @param access_token old_token
	 * @return null
	 * {
	 * 	"access_token":"2YotnFZFEjr1zCsicMWpAA",
	 * 	"token_type":"Bearer",
	 * 	"expires_in":3600,
	 * 	"refresh_token":"tGzv3JOkF0XG5Qx2TlKWIA"
	 * }
	 * http code: 200 ok
	 */
	public function logout() /* {{{ */
	{
		$uid = self::$current_uid;
		$appid = self::$current_appid;

		/* delete token */
		$token = $this->data_store->delAccessToken($uid);

		/* return */
		return $this->_return(self::_error(self::E_OK, 'delete token ok.'));
	} /* }}} */
}

/* end file */
