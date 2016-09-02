<?php
/**
 * @file SmsTmplModel.class.php
 * @brief 
 * 
 * Copyright (C) 2016 ZKC.com
 * All rights reserved.
 * 
 * @package Model
 * @author Langr <hua@langr.org> 2016/06/27 15:02
 * 
 * $Id$
 */

namespace Home\Model;
use Think\Model;
class SmsTmplModel extends Model 
{
	protected $day_ip_max = 100;	/* 每天每ip发送短信限制 */
	protected $day_tel_max = 10;	/* */

	protected $trueTableName = 'data_sms_tmpl';
	protected $trueTableNameLog = 'data_sms_log';
	protected $dbName = 'system_data';
	protected $pk = 'id';
	//protected $fields = array('title','content');
	protected $_map = array(
	);
	
	/**
	 * @brief 短信发送
	 * 	ip 限制当天一整天
	 * 	tel 限制1小时活跃度
	 * 	WARNNING!!! 如果是服务器端调用此接口，需要调整 day_ip_max 为一个稍大的值
	 * @param tel 
	 * @param action 发送动作/标题
	 * @param tmpl 模板变量 array('k'=>'v','k1'=>'v1'...'content'=>'自定义内容')
	 * @return array('code'=>0, 'msg'='ok')
	 */
	public function send($tel, $action, $tmpl = array()) /* {{{ */
	{
		$redis = \FRedis::getCacheSingleton();
		$key_ip = KEY_PREFIX.':sms:'.CLIENT_IP;
		$key_tel = KEY_PREFIX.':sms:'.$tel;
		$redis->incr($key_ip);
		$redis->incr($key_tel);
		/* ip 限制当天一整天 */
		$redis->expire($key_ip, strtotime(date('Y-m-d 23:59:59')) - time());
		/* tel 限制1小时活跃度 */
		$redis->expire($key_tel, 3600);
		$count_ip = $redis->get($key_ip);
		$count_tel = $redis->get($key_tel);		
		if ($count_ip > $this->day_ip_max || $count_tel > $this->day_tel_max) {
			return array('code'=>5, 'msg'=>'超过允许发送量');
		}

		if (empty($action)) {
			return array('code'=>4, 'msg'=>'无此action');
		}
		if (empty($tel) || !is_numeric($tel)) {
			return array('code'=>2, 'msg'=>'请设置正确tel信息');
		}
		$sms_tmpl = $this->tmpl($action);
		if (empty($sms_tmpl['status']) || $sms_tmpl['status'] == 0) {
			return array('code'=>9, 'msg'=>'当前action:'.$action.'取消发送短信');
		}

		if (!empty($tmpl)) {
			foreach ($tmpl as $k => $v) {
				$sms_tmpl['content'] = str_replace('{'.$k.'}', $v, $sms_tmpl['content']);
			}
		}

		import('Vendor.zkc.Sms', APP_PATH, '.php');
		$sms = new \Sms();
		$res = $sms->send(array($sms_tmpl['content']=>$tel));
		/* 保存短信记录 */
		$msg['tel'] = $tel;
		$msg['action'] = $action;
		$msg['content'] = $sms_tmpl['content'];
		$msg['status'] = 0;
		$msg['created'] = time();

		if (!isset($res['code']) || $res['code'] != 0) {
			$msg['status'] = empty($res['code']) ? 1 : $res['code'];
		}

		$log = M('sms_log', 'data_');
		$log->add($msg);

		return $res;
	} /* }}} */

	/**
	 * @brief tmpl
	 * @param action
	 * @return 
	 */
	public function tmpl($action) /* {{{ */
	{
		$res = $this->where("action='$action'")->find();
		return $res;
	} /* }}} */

}

/* end file */
