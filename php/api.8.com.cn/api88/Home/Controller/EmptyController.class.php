<?php
/**
 * @file EmptyController.class.php
 * @brief 
 * 
 * Copyright (C) 2016 ZKC.com
 * All rights reserved.
 * 
 * @package Controller
 * @author Langr <hua@langr.org> 2016/06/21 09:58
 * 
 * $Id$
 */
namespace Home\Controller;
use Think\Controller;

class EmptyController extends AppController {
	public function index() 
	{
		return $this->_return(self::_error(self::E_API_NO_EXIST, CONTROLLER_NAME));
	}
}

/* end file */
