<?php
/**
 * @file VerifycodeController.class.php
 * @brief 用户信息接口
 * 
 * Copyright (C) 2016 ZKC.com
 * All rights reserved.
 * 
 * @package Controller
 * @author Langr <hua@langr.org> 2016/06/24 14:50
 * 
 * $Id: VerifycodeController.class.php 62479 2016-06-23 02:23:26Z huanghua $
 */

namespace Home\Controller;
use Home\Controller\AppController;
class VerifycodeController extends AppController 
{
	public function index() /* {{{ */
	{
		switch ($_SERVER['REQUEST_METHOD']) {
		case 'GET' :
			if (isset($_GET['ac']) && $_GET['ac'] == 'sms') {
				$this->sms();
			} else if (isset($_GET['ac']) && $_GET['ac'] == 'image') {
				$this->image();
			}
			break;
		case 'POST' :
			break;
		case 'PUT' :
			break;
		case 'PATCH' :
			break;
		case 'DELETE' :
			break;
		default :
			break;
		}

		return $this->_return(self::_error(self::E_METHOD));
	} /* }}} */

	/**
	 * @brief 发送短信验证码 接口
	 * request method: POST
	 * @param mobile
	 * @param verify_token
	 * @param verify_code
	 * @param type: reg 注册,login 登陆,getpwd 找回密码,withdraw 提现, ...
	 * @return null
	 * http code 返回出错码: 200 ok, 
	 * 	4xx 出错
	 */
	public function sms() /* {{{ */
	{
		$mobile = I('mobile');
		$type = I('type');
		//$type_msg = array('reg'=>'88账号注册', 'login'=>'登陆', 'getpwd'=>'找回密码', 'withdraw'=>'提现');
		$type_act = array('default'=>'验证码', 'reg'=>'验证码', 'login'=>'验证码', 'getpwd'=>'找回密码', 'getpwd2'=>'找回交易密码', 'changeinfo'=>'修改手机号码');
		$redis = $this->data_store->FRedis;

		//$mobile = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, SMS_KEY, $mobile, MCRYPT_MODE_CBC, SMS_IV));
		if (strlen($mobile) > 15) {
			/* 加密过的手机号 */
			$mobile = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, SMS_KEY, base64_decode($mobile), MCRYPT_MODE_CBC, SMS_IV));
		} else if (SMS_CC) {
			/* 防刷短信验证 */
			$verify_token = I('verify_token');
			$image_code = I('verify_code');
			/* 加签名验证 */
			$hash = I('hash');
			$salt = array('0'=>'26', '1'=>'27', '2'=>'4', '3'=>'15', '4'=>'30', '5'=>'31', '6'=>'32', '7'=>'33', '8'=>'34', '9'=>'1',
				'a'=>'0', 'b'=>'35', 'c'=>'2', 'd'=>'3', 'e'=>'28', 'f'=>'5', 'g'=>'6', 'h'=>'7', 'i'=>'8', 'j'=>'9', 'k'=>'10',
				'l'=>'11', 'm'=>'12', 'n'=>'13', 'o'=>'14', 'p'=>'29', 'q'=>'16', 'r'=>'17', 's'=>'18', 't'=>'19', 'u'=>'20',
				'v'=>'21', 'w'=>'22', 'x'=>'23', 'y'=>'24', 'z'=>'25');
			$hash_token = '';
			$_l = strlen($verify_token);
			for ($i = 0; $i < $_l; $i++) {
				$hash_token .= $salt[$verify_token[$i]];
			}
			$append = ($mobile % 2 == 0) ? '88' : '8';
			$hash_chk = md5($mobile.$hash_token.$image_code.$append);
			if ($hash != $hash_chk) {
				return $this->_return(self::_error(454, '验证错误！'.$mobile.$hash_token.$image_code.$append));
			}

			$key_image = KEY_PREFIX.':verify_code:'.$verify_token;
			$v = $redis->hGetAll($key_image);
			if (empty($v) || empty($image_code) || strtolower($image_code) != strtolower($v['code'])) {
				//return $this->_return(self::_error(454, 'verification code error.'));
				return $this->_return(self::_error(454, '验证码错误！'));
			}
			$redis->del($key_image);
		}

		if (empty($type) || empty($type_act[$type])) {
			$type = 'reg';
			//return $this->_return(self::_error(self::E_ARGS));
		}
		if (!preg_match("/^(13|15|18|14|17)[0-9]{9}$/", $mobile)) {
			return $this->_return(self::_error(453, '手机号错误！'));
		}
		/* check old code */
		$key = KEY_PREFIX.':verify_code:'.$type.':'.$mobile;
		$v = $redis->hGetAll($key);
		/* 同一手机同一业务不重复发送短信 */
		if ($v) {
			return $this->_return(self::_error(self::E_OK, '重复发送！'));
		}

		/* random code */
		$code = rand(100000, 999999);
		/* send sms & cache verify code */
		$sms = D('SmsTmpl');
		$ret = $sms->send($mobile, $type_act[$type], array('code'=>$code));
		if ($ret['code'] != 0) {
			return $this->_return(self::_error(460 + $ret['code'], $ret['msg']));
		}
		$v = array('code'=>$code);
		$redis->hMset($key, $v);
		/* 短信验证码300秒后过期 */
		$redis->setTimeout($key, 300);
		return $this->_return(self::_error(self::E_OK));
	} /* }}} */

	/**
	 * @brief 生成图片验证码 接口
	 * request method: GET
	 * @param verify_token: 客户端随机产生的验证码识别token，不小于32
	 * @return image
	 * http code 返回出错码: 200 ok, 
	 * 	4xx 出错
	 */
	public function image() /* {{{ */
	{
		$verify_token = I('verify_token');

		if (empty($verify_token) || strlen($verify_token) != 32) {
			return $this->_return(self::_error(self::E_ARGS));
		}
		/* random code */
		//$code = rand(1000, 9999);
		/* */
		//$string = "a2s3d4f5g6hj8k9qwertyupzxcvbnm";
		$string = "ABCDEFGHJKLMNPQRSTWXY23456789";
		$length = strlen($string);
		mt_srand();
		$code = '';
		for ($i = 0; $i < 4; $i++) {
			$code .= $string[mt_rand(0, $length)];
		}
		/* save code */
		$redis = $this->data_store->FRedis;
		$key = KEY_PREFIX.':verify_code:'.$verify_token;
		$v = array('code'=>$code);
		$redis->hMset($key, $v);
		/* 验证码300秒后过期 */
		$redis->setTimeout($key, 300);

		/* 验证码图片的宽度 */
		$width = 50;
		/* 验证码图片的高度 */
		$height = 25;

		/* 创建一个图层 */
		$im = imagecreate($width, $height);
		/* 背景色 */
		$back = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);
		/* 模糊点颜色 */
		$pix  = imagecolorallocate($im, 187, 230, 247);
		/* 字体色 */
		$font = imagecolorallocate($im, 41, 163, 238);
		/* 画矩形 */
		imagerectangle($im, 0, 0, $width -1, $height -1, $font);
		/* 绘模糊作用的点 */
		mt_srand();
		for ($i = 0; $i < 300; $i++) {
			imagesetpixel($im, mt_rand(0, $width), mt_rand(0, $height), $pix);
		}
		/* 输出字符 */
		//imagestring($im, 5, 7, 5, $code, $font);
		for ($i = 0; $i < strlen($code); $i++) {
			$x = 3 + $i * ($width - 3)/4;
			$y = mt_rand(1, $height/3);
			$color = imagecolorallocate($im, mt_rand(0,50), mt_rand(0,150), mt_rand(100,250));
			imagechar($im, 5, $x, $y, $code[$i], $color);
		}

		$style = array($pix,$pix,$pix,$pix,$pix,
			$font,$font,$font,$font,$font
		);
		imagesetstyle($im, $style);
		$y1 = rand(0, $height);
		$y2 = rand(0, $height);
		$y3 = rand(0, $height);
		$y4 = rand(0, $height);
		//imageline($im, 0, $y1, $width, $y3, IMG_COLOR_STYLED);
		imageline($im, 0, $y2, $width, $y4, IMG_COLOR_STYLED);

		/* 输出图片 */
		header("Content-type:image/jpeg");
		imagejpeg($im);
		imagedestroy($im);
		//return $this->_return(self::_error(self::E_OK, $key.'='.$code));
		exit;
	} /* }}} */
}

/* end file */
