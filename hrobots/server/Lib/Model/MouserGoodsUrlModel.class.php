<?php
/**
 * @file XxxGoodsUrlModel.class.php
 * @brief 商品URL
 * 
 * Copyright (C) 2014 kitsmall.com
 * All rights reserved.
 * 
 * @package Model
 * @author Langr <hua@langr.org> 2014/05/19 17:03
 * 
 * $Id: MouserGoodsUrlModel.class.php 63 2014-05-24 05:49:23Z huanghua $
 */

class MouserGoodsUrlModel extends RelationModel
{
	protected $_link = array(
		'Goods' => array(
			'mapping_type' => HAS_ONE,
			'class_name' => 'MouserGoods',
			'mapping_name' => 'goods',
			'foreign_key' => 'id',		/* 关联的外键名称 */
		)
	);
}

/* end file */
