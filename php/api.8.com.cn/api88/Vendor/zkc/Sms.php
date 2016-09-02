<?php
/**
 * YunPian Sms
 */
class Sms {
	/**
	 * 发送短信API
	 * @var	String
	 */
	private	$_apiUrl = 'http://api.msg.zkc.com/sms.php';
	private	$_account = 'ezkc2015';
	private	$_code = '';

	/**
	 * 发送短信API
	 * @param Array	$sendConf
	 *		   like	array(
	 *					'sms content1' => array('mobile	num1', 'mobile num2', 'mobile num3'),
	 *					'sms content2' => array('mobile	num1', 'mobile num5', 'mobile num6'),
	 *				)
	 * @return Array $return
	 */
	public function send($sendConf)
	{
		if (!is_array($sendConf)) {
			return 'please set mobile num or content';
		}
		$rs = array();
		foreach	($sendConf as $content => $mobile) {
			if (!is_array($mobile))	{
				$rs	= $this->_doRequest($content, $mobile);
			} else {
				foreach	($mobile as	$num) {
					$rs[$num] = $this->_doRequest($content, $num);
				}
			}
		}
		return $rs;
	}

	/**
	 * CURL执行请求
	 * @param String $content 短信内容
	 * @param Int $num 手机号码
	 * @return Array $return 发送后返回值
	 */
	private function _doRequest($content,$mobile)
	{

		$account = $this->_account;
		$code = $this->_code;

		$token = md5(md5($code) . $account . $content . $mobile . md5($account . $code));

		$post_data = array(
			'account'=>$account,
			'content' => $content,
			'mobile' => $mobile,
			'token' => $token,
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->_apiUrl);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT,30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

		$res = curl_exec($ch);
		curl_close($ch);

		$res = json_decode($res, true);
		wlog('sms_log.'.date('Ym').'.log', $mobile.' '.$content.' '.$res['info']['code']);
		if ($res['status'] == 'success') {
			return array('code'=>0, 'msg'=>'短信发送成功');
		} else {
			return array('code'=>1, 'msg'=>'短信发送失败');
		}
	}

}

/* end file */
