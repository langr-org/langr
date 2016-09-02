<?php
/** 
 * @file hiobject.php
 * @brief 
 * 
 * Copyright (C) 2011 LangR.Org
 * 
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package hiphp
 * @author Langr <hua@langr.org> 2011/11/15 00:29
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 
 * $Id: hiobject.php 21 2012-05-17 10:47:34Z loghua@gmail.com $
 */
$__inc_files[] = __FILE__;

/**
 * @class hiObject
 * @brief base object
 */
class hiObject
{
	protected $hi = 0;

	function __construct() /* {{{ */
	{
		static $all = 0;
		$all++;
		$this->hi = $all;
		echo '<br/>'.$all.'='.$this->hi.__CLASS__.'__construct()<br/>';
	} /* }}} */

	function test() /* {{{ */
	{
		echo '<br/>'.__METHOD__;
		echo date('Y-m-d H:i:s').'<br/>';
	} /* }}} */

	/**
	 * @fn
	 * @brief 获取类对象实例.
	 * @param 类名, 默认获取自己.
	 * @return 对象
	 */
	function & getInstance($class = null) /* {{{ */
	{
		static $instance = array();
		if ( !empty($class) ) {
			if ( !$instance || strtolower($class) != strtolower(get_class($instance[0])) ) {
				$instance[0] = & new $class();
			}
		}

		if ( !$instance ) {
			$class = get_class();
			$instance[0] = & new $class();
		}

		return $instance[0];
	} /* }}} */

	function __destruct() /* {{{ */
	{
		echo __CLASS__.$this->hi.'__destruct()<br/>';
	} /* }}} */
}

/* end file */
