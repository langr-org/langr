<?php
/**
 * @file GoodsModel.class.php
 * @brief 商品
 * 
 * Copyright (C) 2014 kitsmall.com
 * All rights reserved.
 * 
 * @package Model
 * @author Langr <hua@langr.org> 2014/04/25 17:03
 * 
 * $Id: GoodsModel.class.php 44 2014-05-20 12:25:56Z huanghua $
 */

class GoodsModel extends Model
{
	/**
	 * @fn
	 * @brief 多个模块使用不同数据表时，设置数据表名
	 * @param 
	 * @return 
	 */
	public function __construct($module='')
	{
		parent::__construct();
		if ( !empty($module) ) {
			$this->trueTableName = $this->tablePrefix.$module.'_goods';
		}
	}
}

/* end file */
