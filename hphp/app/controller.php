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
 * $Id: controller.php 21 2012-05-17 10:47:34Z loghua@gmail.com $
 */
$__inc_files[] = __FILE__;

class controller extends hiObject
{
	protected $name = 'welcome controller!';
	protected $_count = 0;
	protected $_model = NULL;
	protected $_view = NULL;
	protected $_data = NULL;

	function __construct()
	{
		echo __CLASS__."__construct()<br/>";
		$this->_count += 1;
		$this->_data = array();
	}

	function set($key, $value = NULL)
	{
		echo __METHOD__."($key=$value)<br/>";
		$this->_data[$key] = $value;
		return ;
	}

	function layout($layout = NULL)
	{
		echo __METHOD__."($layout)<br/>";
	}

	/**
	 * @fn
	 * @brief 底层装载方法, 先自动装载, 或手动装载 loadModel.
	 * @param 
	 * @return 
	 */
	function _loadModel($model = NULL)
	{
		echo __METHOD__."($model)<br/>";
		return ;
	}

	/**
	 * @fn
	 * @brief 底层装载方法, 先自动装载, 或手动装载 loadView.
	 * @param 
	 * @return 
	 */
	function _loadView($view = NULL)
	{
		echo __METHOD__."($view)<br/>";
		return ;
	}

	function _test3($id, $page, $app)
	{
		echo __METHOD__.":_test3($id, $page, $app)<br/>";
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

/* end file */
