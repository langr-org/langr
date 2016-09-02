<?php 
/**
 * @file IndexAction.class.php
 * @brief 
 * 
 * Copyright (C) 2014 kitsmall.com
 * All rights reserved.
 * 
 * @package Action
 * @author Langr <hua@langr.org> 2014/04/14 15:04
 * 
 * $Id: IndexAction.class.php 14 2014-05-06 02:08:24Z huanghua $
 */

class IndexAction extends Action 
{
	public function index()  /* {{{ */
	{
		header("Content-Type:text/html; charset=utf-8");
		echo "Hello,I'm Robots!";
	} /* }}} */

	public function _empty() /* {{{ */
	{
		/* client post data: */
		$data = file_get_contents('php://input');
		$act = ACTION_NAME;
		echo $act.':post:'.$data;
	} /* }}} */
}
/* end file */
