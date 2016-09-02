<?php
/**
 * @file digikey.lib.module.php
 * @brief 
 * 
 * Copyright (C) 2014 kitsmall.com
 * All rights reserved.
 * 
 * @package module
 * @author Langr <hua@langr.org> 2014/06/03 11:44
 * 
 * $Id: digikey.lib.module.php 85 2014-06-04 10:59:44Z huanghua $
 */

/* 根据产品列表分页规则返回列表页链接函数 */
function digikey_goods_pages($first_page, $goods_sum = 0, $pages_count = 1)
{
	$page = 'page/';
	$page_n = 25;
	if ( $goods_sum > 0 ) {
		$pages_count = ceil($goods_sum / $page_n);
	}

	$and = substr($first_page, -1, 1) == '/' ? '' : '/';
	$next_page[] = array('gdslist_url'=>$first_page);
	for ( $i = 1; $i < $pages_count; $i++ ) {
		$next_page[] = array('gdslist_url'=>$first_page.$and.$page.($i+1));
	}

	return $next_page;
}

/* end file */
