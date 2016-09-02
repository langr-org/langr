<?php
/** 
 * @file dispatcher.php
 * @brief 
 * 
 * Copyright (C) 2011 LangR.Org
 * 
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package hiphp
 * @author Langr <hua@langr.org> 2011/11/15 00:19
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 
 * $Id: dispatcher.php 21 2012-05-17 10:47:34Z loghua@gmail.com $
 */
$__inc_files[] = __FILE__;

class dispatcher
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

/* end file */
