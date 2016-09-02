<?php
/** 
 * @file model.php
 * @brief 
 * 
 * Copyright (C) 2011 LangR.Org
 * 
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package hiphp
 * @author Langr <hua@langr.org> 2011/11/13 23:20
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 
 * $Id: model.php 21 2012-05-17 10:47:34Z loghua@gmail.com $
 */
$__inc_files[] = __FILE__;

class model extends hiObject
{
	/**
	 * file, db, mem, net...
	 * 具体在 appModel 中设置:
	 * file: filename
	 * db: device(mysql, mssql, sqlite3, db2, odbc, adodb, pecl:DB...)
	 * mem: memcache
	 * net: 自定义协议
	 * other: ...
	 */
	protected $_type = 'db';
	protected $_model = NULL;

	function __construct($type)
	{
		echo __CLASS__."__construct(type=$type)<br/>";
		parent::__construct();
	}

	function type($type = NULL)
	{
		echo __METHOD__."($type)<br/>";
		if ( $type ) {
			$this->_type = $type;
		}
		return ;
	}

	function __destruct()
	{
		echo __CLASS__."__destruct()<br/>";
	}
}

/* end file */
