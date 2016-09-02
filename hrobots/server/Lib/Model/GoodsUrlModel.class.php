<?php
/**
 * @file GoodsUrlModel.class.php
 * @brief 商品URL
 * 
 * Copyright (C) 2014 kitsmall.com
 * All rights reserved.
 * 
 * @package Model
 * @author Langr <hua@langr.org> 2014/04/25 17:03
 * 
 * $Id: GoodsUrlModel.class.php 44 2014-05-20 12:25:56Z huanghua $
 */

class GoodsUrlModel extends RelationModel
{
	protected $_link = array(
		'Goods' => array(
			'mapping_type' => HAS_ONE,
			'class_name' => 'Goods',
			'mapping_name' => 'goods',
			'foreign_key' => 'id',		/* 关联的外键名称 goods.id */
			//'condition' => 'goods.id=goods_url.id',	/* 关联条件 */
		),
	);

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
			$this->trueTableName = $this->tablePrefix.$module.'_goods_url';
			$this->_link['Goods']['class_name'] = ucfirst($module).'Goods';
		}
	}
}

/* end file */
