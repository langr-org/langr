<?php
/** 
 * @file controller.php
 * @brief 
 * 
 * Copyright (C) 2011 LangR.Org
 * 
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package test
 * @author Huang Hua <hua@langr.org> 2011/11/02 15:58
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 
 * $Id: controller.php 9 2011-11-02 10:26:06Z loghua@gmail.com $
 */

echo dirname(__FILE__);
echo '<br/><br/>';

class appController extends controller
{
	public $model = null;
	public $view = null;
	protected $models = array();
	protected $views = array();
	protected $data = array();
	private static $count = 0;

	function __construct($view)
	{
		echo __CLASS__."__construct()<br/>";

		$this->view = $view;
		$this->count += 1;
		parent::__construct();
	}

	function test($id, $page)
	{
		echo "action: ".__METHOD__.":test($id, $page)<br/>";
		echo $this->view.'<br/>';
		$this->test2($id, $page);
		$this->_test3($id, $page);
	}

	function test2($id, $page)
	{
		echo "action: ".__METHOD__.":test2($id, $page)<br/>";
		echo $this->name.'<br/>';
	}

	function _test3($id, $page)
	{
		echo "action: ".__METHOD__.":_test3($id, $page)<br/>";
		parent::_test3($id, $page, $this->name);
	}

	static function getThis()
	{
		echo __METHOD__.' self::count: '.'<br/>';
	}

	/**
	 * 析构函数
	 */
	function __destruct()
	{
		if ( $this->model ) {
			/* $this->model->free(); */
		}
		
		echo __CLASS__."__destruct()<br/>";
	}
}

class controller
{
	protected $name = 'welcome controller!';
	protected $_count = 0;

	function __construct()
	{
		echo __CLASS__."__construct()<br/>";
		$this->_count += 1;
	}

	function _test3($id, $page, $app)
	{
		echo "action: ".__METHOD__.":_test3($id, $page, $app)<br/>";
	}

	static function getThis()
	{
		echo __METHOD__.' self::_count: '.'<br/>';
	}

	/**
	 * 析构函数
	 */
	function __destruct()
	{
		echo __CLASS__."__destruct()<br/>";
	}
}

function wlog($log_file, $log_str, $log_size = 4194304) 
{
	ignore_user_abort(TRUE);

	if ( empty($log_file) ) {
		$log_file = 'log_file';
	}
	if ( defined("APP_LOG_PATH" ) /*&& substr($log_file, 0, 1) != '/'*/) {
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
		$cip	= defined("CLIENT_IP") ? "[".CLIENT_IP."] " : '';
		$log_str = "[".date('Y-m-d H:i:s')."] ".$cip.$log_str."\r\n";
		fwrite($fp, $log_str);
		flock($fp, LOCK_UN);
	}
	fclose($fp);

	ignore_user_abort(FALSE);
}

echo controller::getThis();
echo appController::getThis();
echo controller::_test3(123, 456, 789);

echo '<br/><br/>';

/**
 * 
 */
$app = new appController(date('Y-m-d H:i:s'));

$app->getThis();
$ret = call_user_func_array(array($app, 'test'), $_GET);

echo '<br/><br/>';

/* 多参数不警告，少参数有警告 */
$app->test('app', 'action', null, null);

echo controller::getThis();
echo appController::getThis();

/* end file */
