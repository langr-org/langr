<?php
/** 
 * @file view.php
 * @brief 
 * 
 * Copyright (C) 2011 LangR.Org
 * 
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package hiphp
 * @author Langr <hua@langr.org> 2011/11/28 23:57
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 
 * $Id: view.php 21 2012-05-17 10:47:34Z loghua@gmail.com $
 */
$__inc_files[] = __FILE__;

class view extends hiObject
{
	/**
	 * 指定模板:
	 */
	protected $tmplName = 'default';

	/**
	 * 设定模板编译解释引擎:
	 * 默认为内置模板引擎。
	 */
	protected $tmplEngine = NULL;

	/**
	 * 模板变量
	 */
	protected $tmplVar = array();

	function __construct($type)
	{
		echo __CLASS__."__construct(type=$type)<br/>";
		parent::__construct();
	}

	/**
	 * @fn
	 * @brief set template.
	 * 必须在模板目录有安装此模板。
	 * @param 
	 * @return 
	 */
	function setTmpl($name = NULL)
	{
		echo __METHOD__."($name)<br/>";
		if ( $name ) {
			$this->tmplName = $name;
		}
		return ;
	}

	/**
	 * @fn
	 * @brief set template compile engine.
	 * @param 
	 * @return 
	 */
	function setTmplEngine($engine = NULL)
	{
		echo __METHOD__."($engine)<br/>";
		if ( $engine ) {
			$this->tmplEngine = $engine;
		}
		return ;
	}

	function set($key, $value = NULL)
	{
		echo __METHOD__."($key=$value)<br/>";
		if ( $value ) {
			$this->tmpl[$key] = $value;
		}

		return true;
	}

	function get($key = NULL)
	{
		echo __METHOD__."($key)<br/>";
		if ( $key ) {
			return $this->tmpl[$key];
		}

		return $this->tmpl;
	}

	/**
	 * @fn
	 * @brief set template value.
	 * 综合 set(), get().
	 * @see this::set(), this::get()
	 * @param 
	 * @return 
	 */
	function value($key = NULL, $value = NULL)
	{
		echo __METHOD__."($key=$value)<br/>";
		if ( $key == NULL && $value == NULL ) {
			return $this->tmpl;
		} else if ( $key && $value == NULL ) {
			return $this->tmpl[$key];
		} else if ( $key && $value ) {
			$this->tmpl[$key] = $value;
		}
		if ( $value ) {
			$this->tmpl[$key] = $value;
		}

		return false;
	}

	function layout($tmpl = NULL)
	{
		echo __METHOD__."($tmpl)<br/>";
		if ( $tmpl ) {
			echo $tmpl;
		}

		return ;
	}

	function element($tmpl = NULL)
	{
		echo __METHOD__."($tmpl)<br/>";
		if ( $tmpl ) {
			echo $tmpl;
		}

		return ;
	}

	function display($tmpl = NULL)
	{
		echo __METHOD__."($tmpl)<br/>";
		if ( $tmpl ) {
			echo $tmpl;
		}

		return ;
	}

	function __destruct()
	{
		echo __CLASS__."__destruct()<br/>";
	}
}

/* end file */
