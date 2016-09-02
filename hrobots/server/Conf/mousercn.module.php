<?php
/**
 * @file mouser.module.php
 * @brief mouser 采集模块
 * 
 * Copyright (C) 2014 kitsmall.com
 * All rights reserved.
 * 
 * @package Conf
 * @author Langr <hua@langr.org> 2014/04/29 09:36
 * 
 * $Id: mousercn.module.php 19 2014-05-07 13:57:30Z huanghua $
 */

/* 模块版本 */
//$mod_config['mod_name'] = 'mouser';
$mod_config['mod_version'] = '0.1';

/* 模拟header, 浏览器 */
$__browser = array(
	'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154',	/* chrome 33 */
	'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0)',		/* ie8 */
	'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Win64; x64; Trident/5.0)',	/* ie9 */
	'Mozilla/5.0 (Windows; U; Windows NT 6.1) Gecko/2013070208 Firefox/28.0.1',	/* firefox */
	'Opera/9.20 (Windows NT 6.0; U; en)',						/* opera */
	'Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/125.2 (KHTML, like Gecko) Safari/125.8',	/* safari */
);
$mod_config['header'] = array(
	'User-Agent'=>$__browser[rand(0, count($__browser) - 1)], 
	//'Accept-Language'=>'zh-CN,zh;q=0.8', 
	'Accept-Language'=>'en-US,en;q=0.6', 
	/* Accept-Encoding: identity 默认 */
	'Accept-Encoding'=>'', 
	'Referer'=>'http://www.mouser.com/Electronic-Components'
);

/* 分类 url 入口页, 采集产品列表用 */
$mod_config['category_index'] = array(
	'ctg_url'=>'http://www.mouser.com/Electronic-Components/',
	'ctg_level'=>'0',
	'gds_sum'=>'2568899',
);

/* 子分类正则匹配, 匹配的指定层级没有url返回则表示是最后级 */
$mod_config['category_rules'] = array(
	/* level 1 */
	1 => array(
		/*array('rule'=>array('text'=>'%<span class="bold">Total Results:</span>&nbsp;(.*)</td>%iUs',count=>1,'do'=>'ctg_url'),
			1=>'goods_sum'),*/		/* 1级 结果总数 */
		/* 当前页面关键规则，没有匹配则表示(无效页面)非此页面 */
		'key'=>array('rule'=>array('text'=>'% class="SearchResultsTopLevelCategory" href="(.*)"><h2 class=\'seoh2DefaultPage\'>(.*)</h2>%iUs','count'=>0,'do'=>''),
			1=>'ctg_url',
			2=>'ctg_name'),			/* 1级 子分类名和子分类url */
	),
	/* level 2 */
	2 => array(
		/* 当前页面关键规则，没有匹配则表示(无效页面)非此页面 */
		'key'=>array('rule'=>'%_lnkCategory" class="SearchResultsSubLevelCategory" itemprop="significantLink" href="(.*)">(.*)</a>%iUs',
			1=>'ctg_url',
			2=>'ctg_name'),			/* 2级 子分类名和子分类url */
	),
	/* level 3 */
	3 => array(
		/* 当前页面关键规则，没有匹配则表示(无效页面)非此页面 */
		'key'=>array('rule'=>'%_lnkCategory" class="SearchResultsSubLevelCategory" itemprop="significantLink" href="(.*)"><h3>(.*)</h3></a>.*_lblRecordCount">(.*)</span>%iUs',
			1=>'ctg_url',
			2=>'ctg_name',
			3=>'gds_sum'),		/* 3级 子分类名和子分类url */
		/*array('rule'=>'%_lblRecordCount">(.*)</span>%iUs',
			1=>'goods_sum'),*/		/* 3级 结果总数 */
	),
	/* level 4, 可能有, 如果没有或者可以直接归为上级分类，则最好不配置此子分类，这样可以大大节约采集时间 */
	/*4 => array(
		'key'=>array('rule'=>'%_lnkCategory" class="SearchResultsSubLevelCategory" itemprop="significantLink" href="(.*)"><h3>(.*)</h3></a>.*_lblRecordCount">(.*)</span>%iUs',
			1=>'ctg_url',
			2=>'ctg_name',
			3=>'gds_sum'),
	)*/
);

/* 产品列表正则匹配, TODO: 支持二级子匹配 */
$mod_config['goodslist_rules'] = array(
	array('rule'=>'%<span class="bold">.*</span>&nbsp;(.*)</td>%iUs',
		1=>'gds_count'), 
	array('rule'=>'%<span class="bold">.*</span>&nbsp;(.*)</td>%iUs',
		1=>'gdslist_p'), 
	array('rule'=>'% class="SearchResultsTopLevelCategory" href="(.*)"><h2 class=\'seoh2DefaultPage\'>(.*)</h2>%iUs',
		1=>'gds_url',
		2=>'gds_name'),
);

/* 产品正则匹配 */
$mod_config['goods_rules'] = array(
	array('rule'=>'% class="SearchResultsTopLevelCategory" href="(.*)"><h2 class=\'seoh2DefaultPage\'>(.*)</h2>%iUs',
		1=>'gds_name',
		2=>'gds_sn',
		3=>'gds_thumb',
		4=>'gds_doc'),
);

/* end file */
