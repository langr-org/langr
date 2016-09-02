<?php
/**
 * @file mouser.lib.module.php
 * @brief 
 * 
 * Copyright (C) 2014 kitsmall.com
 * All rights reserved.
 * 
 * @package module
 * @author Langr <hua@langr.org> 2014/05/17 14:11
 * 
 * $Id: mouser.lib.module.php 63 2014-05-24 05:49:23Z huanghua $
 */

/* 根据产品列表分页规则返回列表页链接函数 */
function mouser_goods_pages($first_page, $goods_sum = 0, $pages_count = 1)
{
	$page = 'No=';
	$page_n = 25;
	if ( $goods_sum > 0 ) {
		$pages_count = ceil($goods_sum / $page_n);
	}

	$query = parse_url($first_page, PHP_URL_QUERY);
	$and = empty($query) ? '?' : '&';
	$next_page[] = array('gdslist_url'=>$first_page);
	for ( $i = 1; $i < $pages_count; $i++ ) {
		$next_page[] = array('gdslist_url'=>$first_page.$and.$page.($page_n * $i));
	}

	return $next_page;
}

/* end file */
