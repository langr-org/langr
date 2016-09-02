<?php 
/**
 * @file EmptyAction.class.php
 * @brief 
 * 
 * Copyright (C) 2014 kitsmall.com
 * All rights reserved.
 * 
 * @package Action
 * @author Langr <hua@langr.org> 2014/04/15 14:56
 * 
 * $Id: EmptyAction.class.php 58 2014-05-23 09:23:38Z huanghua $
 */

class EmptyAction extends Action {
	public function index(){
		header("Content-Type:text/html; charset=utf-8");
		echo "-404 Hello,I'm HqRobots!";
		return ;
	}

	public function _empty(){
		echo '-404 '.ACTION_NAME;
		return ;
	}
}
/* end file */
