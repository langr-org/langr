<?php
/**
 * @file UsersController.class.php
 * @brief 用户信息接口
 * 
 * Copyright (C) 2016 ZKC.com
 * All rights reserved.
 * 
 * @package Controller
 * @author Langr <hua@langr.org> 2016/05/11 15:09
 * 
 * $Id: UsersController.class.php 62479 2016-06-23 02:23:26Z huanghua $
 */

namespace Home\Controller;
use Home\Controller\AppController;
class UsersController extends AppController 
{
	/**
	 * @brief demo
	 * method: GET
	 * @param access_token
	 * @return test json
	 */
	public function demo() /* {{{ */
	{
		/* 当前授权用户信息： */
		$token = self::$current_token;
		$uid = self::$current_uid;
		$appid = self::$current_appid;

		/* 客户端传递的参数：I(''); */
		/* TODO: ... */

		$token_info = $this->data_store->getTokenInfo(self::$current_token);

		$data = array($_SERVER['REQUEST_METHOD'], $token_info, self::$current_token, self::$current_uid, self::$current_appid);

		/* return */
		return $this->_return(self::_error(self::E_OK, 'demo ok!', $data), empty($_GET['ret']) ? 'json' : $_GET['ret']);
	} /* }}} */

	/**
	 * @brief RESTful 风格支持
	 * 	GET (select),
	 * 	POST (create),
	 * 	PUT (update 完整),
	 * 	PATCH (update 部分),
	 * 	DELETE (delete)
	 */
	public function index() /* {{{ */
	{
		switch ($_SERVER['REQUEST_METHOD']) {
		case 'GET' :
			/* check args */
			if (isset($_GET['ac'])) {
				$this->register();
			}
			break;
		case 'POST' :
			/* register */
			$this->register();
			break;
		case 'PUT' :
			break;
		case 'PATCH' :
			break;
		case 'DELETE' :
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
			return $this->_return(self::_error(451, 'args error.'));
		}
		//$m = new \Home\Model\UsersModel();
		$m = D('Users');
		$res = $m->login($user, $pwd);
		if ($res == false) {
			return $this->_return(self::_error(452, 'password error.'));
		}
		/* 登陆成功 */
		$uid = $res['userid'];
		$appid = I('appid');

		/* set token */
		$token = $this->data_store->refreshAccessToken($uid, $appid, EXPIRES_TIME);

		$data = array('access_token'=>$token['key'], 'token_type'=>'Bearer', 'expires_in'=>EXPIRES_TIME);
		/* return */
		return $this->_return(self::_error(self::E_OK, 'login ok.', $data));
	} /* }}} */

	/**
	 * @brief 刷新token受权接口
	 * 	此接口默认返回OAuth2 密码模式 json数据格式，
	 * request method: POST 此接口建议使用https
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

		$data = array('access_token'=>$token['key'], 'token_type'=>'Bearer', 'expires_in'=>EXPIRES_TIME);
		/* return */
		return $this->_return(self::_error(self::E_OK, 'refresh token ok.', $data));
	} /* }}} */

	/**
	 * @brief 发送短信验证码 接口
	 * request method: GET
	 * @param mobile
	 * @param type: reg 注册,login 登陆,getpwd 找回密码,withdraw 提现, ...
	 * @return null
	 * http code 返回出错码: 200 ok, 
	 * 	4xx 出错
	 */
	public function verify() /* {{{ */
	{
		$mobile = I('mobile');
		$type = empty($_GET['type']) ? '' : $_GET['type'];
		$type_msg = array('reg'=>'88账号注册', 'login'=>'登陆', 'getpwd'=>'找回密码', 'withdraw'=>'提现');

		if (empty($type) || empty($type_msg[$type])) {
			return $this->_return(self::_error(self::E_ARGS));
			//return $this->_return(self::_error(451, 'type does not exist.'));
		}
		if (!preg_match("/^(13|15|18|14|17)[0-9]{9}$/", $mobile)) {
			return $this->_return(self::_error(453, 'mobile error.'));
		}
		/* check old code */
		$redis = $this->data_store->FRedis;
		$key = KEY_PREFIX.':verify_code:'.$type.':'.$tel;
		$v = $redis->hGetAll($key);
		/* 同一手机同一业务不重复发送短信 */
		if ($v) {
			return $this->_return(self::_error(self::E_OK, 'repeat send.'));
		}
		/* random code */
		$code = rand(100000, 999999);
		/* send sms & cache verify code */
		/* TODO: */
		// send_sms($tel, 'verify', array('code'=>$code,'append'=>$type_msg[$type]));
		$v = array('code'=>$code);
		$redis->hMset($key, $v);
		/* 短信验证码180秒后过期 */
		$redis->setTimeout($key, 180);
		return $this->_return(self::_error(self::E_OK));
	} /* }}} */

	/**
	 * @brief 注册新用户，并自动获取token 接口
	 * POST 此接口建议使用https
	 * @param username
	 * @param password md5(password)
	 *!@param appid
	 * 	appid可能值: pc,android,iphone,wap,h5,iosx
	 *!@param type
	 * @return access_token
	 * {
	 * 	"uid":"2YotnFZFEjr1zCsicMWpAA",
	 * 	"username":"Bearer",
	 * 	"tel":"13012345678",
	 * 	"access_token":"2YotnFZFEjr1zCsicMWpAA",
	 * 	"token_type":"Bearer",
	 * 	"expires_in":3600
	 * }
	 * http code 返回出错码: 200 ok, 4xx为出错
	 */
	public function register() /* {{{ */
	{
		$m = D('Users');
		/* check args */
		if ($_GET['ac'] == 'check_username') {
			if ($m->check_mobile(I('username')) == false) {
				return $this->_return(self::_error(499, '用户名已经存在！'));
				//return $this->_return(self::_error(499, 'username exists.'));
			}
			return $this->_return(self::_error(self::E_OK));
		} else if ($_GET['ac'] == 'check_mobile') {
			if ($m->check_mobile(I('mobile')) == false) {
				return $this->_return(self::_error(498, '手机已经存在！'));
			}
			return $this->_return(self::_error(self::E_OK));
		} else if ($_GET['ac'] == 'check_email') {
			return $this->_return(self::_error(497, 'E-mail已经存在！'));
			return $this->_return(self::_error(self::E_OK));
		}
		
		/* register */
		if (empty($_POST['mobile']) || empty($_POST['password'])) {
			return $this->_return(self::_error(451, '手机或密码不能为空！'));
		}
		if (I('post.password') != I('post.repassword') /*|| strlen(I('post.password')) != 32*/) {
			return $this->_return(self::_error(452, '两次输入密码不同！'));
		}
		if (!preg_match("/^(13|15|18|14|17)[0-9]{9}$/", $_POST['mobile'])) {
			return $this->_return(self::_error(453, '手机号非法！'));
		}
		$r = array();
		$r['username'] = I('mobile');
		$r['password'] = I('post.password');
		$r['role'] = $_POST['role'] ? intval($_POST['role']) : 1;
		$r['mobile'] = I('mobile');
		$r['email'] = I('email');
		$r['prov'] = I('prov') ? I('prov') : '广东省';
		$r['city'] = I('city') ? I('city') : '深圳市';
		/* appid可能值: pc,android,iphone,wap,h5,iosx */
		$r['appid'] = I('appid') ? 'pc' : I('appid');

		$r['recommend'] = I('recommend');		/* 推荐人手机或推荐码 */

		$r['channel'] = I('channel');			/* 渠道码 */

		$r['activity'] = I('activity');
		$r['registration_id'] = I('registration_id');	/* 推送设备ID */

		$mobile_code = I('mobile_code');
		$verify_token = I('verify_token');

		$redis = $this->data_store->FRedis;
		/* image verify code */
		if (!empty($verify_token)) {
			$image_code = I('image_code');
			$key_image = KEY_PREFIX.':verify_code:'.$verify_token;
			$v = $redis->hGetAll($key_image);
			if (empty($v) || empty($image_code) || strtolower($image_code) != strtolower($v['code'])) {
				return $this->_return(self::_error(454, '图形验证码错误！'));
			}
			$redis->del($key_image);
		}
		/* register verify code */
		$key = KEY_PREFIX.':verify_code:reg:'.$tel;
		$v = $redis->hGetAll($key);
		if (empty($v) || empty($mobile_code) || $mobile_code != $v['code']) {
			return $this->_return(self::_error(455, '手机验证码错误！'));
		}

		if ($m->check_mobile($r['mobile']) == false) {
			return $this->_return(self::_error(498, '手机号已经存在！'));
		}
		if ($m->check_username($r['username']) == false) {
			return $this->_return(self::_error(499, '用户名已经存在！'));
		}
		$res = $m->register($r);
		if ($res == false) {
			return $this->_return(self::_error(459, '注册失败！'));
		}
		/* 注册完成 */
		$uid = $res['userid'];
		$appid = $r['appid'];
		/* DO Other */
		$this->register_after($uid);

		/* set token */
		$token = $this->data_store->refreshAccessToken($uid, $appid, EXPIRES_TIME);

		$data = array('access_token'=>$token['key'], 'token_type'=>'Bearer', 'expires_in'=>EXPIRES_TIME);
		/* return */
		return $this->_return(self::_error(self::E_OK, 'register ok.', $data));
	} /* }}} */

	/**
	 * @brief 注册完成之后相关事件处理
	 * 	1.将注册信息写入消息队列，通过队列消费者来处理非即时的注册后动作
	 * 	2.添加必要的需要注册后即时处理的动作
	 * @access protected
	 * @param $userid 注册账号userid
	 * @return null
	 */
	protected function register_after($userid) /* {{{ */
	{
		/* 入队列 */
		import('Vendor.zkc.QRedis', APP_PATH, '.php');
		$QRedis = \QRedis::getInstance('register_queue');
		$val = array('userid'=>$userid,'time'=>time());
		$QRedis->push(json_encode($val));

		/* Other... */
		return ;
	} /* }}} */

	/**
	 * @brief demo 注册完成之后相关事件处理
	 * 	读取消息队列注册信息，并处理，
	 * NOTE!!!
	 * 	此接口只作演示用，请将真正的队列消息消费者脚本放在服务器后台定时执行处理
	 * @access public
	 * @return null
	 */
	public function register_queue() /* {{{ */
	{
		/* 出队列 */
		import('Vendor.zkc.QRedis', APP_PATH, '.php');
		$QRedis = \QRedis::getInstance('register_queue');
		$val = array('userid'=>$userid,'time'=>time());

		$_do = 500;
		for ($i = 0; $i < $_do; $i++) {
			$_tmp = $QRedis->pop();
			/* 队列空了? */
			if (isset($_tmp['ret']) && $_tmp['ret'] == 7) {
				break;
			}
			if (!isset($_tmp['val'])) {
				continue;
			}
			$_ret = json_decode($_tmp['val'], true);
			$userid = $_ret['userid'];
			$reg_time = $_ret['time'];

			/* TODO: 注册后动作请在这里处理 */
		}

		exit;
	} /* }}} */
}

/* end file */
