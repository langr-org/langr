<?php
/**
 * @file CtgUrlModel.class.php
 * @brief 
 * 
 * Copyright (C) 2014 kitsmall.com
 * All rights reserved.
 * 
 * @package Model
 * @author Langr <hua@langr.org> 2014/05/05 09:57
 * 
 * $Id: CtgUrlModel.class.php 14 2014-05-06 02:08:24Z huanghua $
 */

class CtgUrlModel extends Model{
	public function __construct($name='') {
		parent::__construct();
		if ( !empty($name) ) {
			$this->trueTableName = $this->tablePrefix.$name;
		}
	}
}
