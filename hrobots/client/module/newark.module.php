<?php
/**
 * @file newark.module.php
 * @brief 
 * 
 * Copyright (C) 2014 kitsmall.com
 * All rights reserved.
 * 
 * @package module
 * @author Langr <hua@langr.org> 2014/07/02 11:59
 * 
 * $Id: newark.module.php 107 2014-06-17 08:31:25Z huanghua $
 */

/* 模块版本 */
//$mod_config['mod_name'] = 'newark';
$mod_config['mod_version'] = '0.2';

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
	'Cache-Control'=>'no-cache',
	'Pragma'=>'no-cache',
	'Accept'=>'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
	//'Accept-Language'=>'zh-CN,zh;q=0.8', 
	'Accept-Language'=>'en-US,en;q=0.6', 
	/* Accept-Encoding: identity 默认 */
	'Accept-Encoding'=>'', 
	'Referer'=>'http://www.newark.com/browse-for-products'
);

/* 分类 url 入口页, 采集产品列表用 */
$mod_config['category_index'] = array(
	'ctg_url'=>'http://www.newark.com/browse-for-products',
	'ctg_level'=>'0',
	'gds_sum'=>'1398375',
);

/**
 * 页面匹配规则配置:
 * 	每页面 非列表匹配（单次匹配）可以有多个匹配规则，每条规则用一个数组表示，
 * 	每页面 列表匹配（多次匹配）只有一个匹配规则，[TODO]但需要支持二级或多级匹配，
 * 	规则名并非必须，但规则名一定是采集程序指定的几个名称，一般为对应匹配数据处理(以匹配结果为参数)的子函数，
 * 	或特别定义的名称，规则名为空则表示此匹配结果按默认处理；
 * 	规则为php pcre正则表达式，表达式中匹配的结果赋值给对应下标的匹配名。
 * array(
 * 	'rule_name'=>array(
 * 			'rule'=>'%regular expression (matching1)...%iUs',
 * 			1=>'matching1_name',
 * 			...
 * 		),
 * 	'rule_name2'=>array(
 * 			'rule'=>'%regular expression (matching1) (matching2)...%iUs',
 * 			1=>'matching1_name',
 * 			2=>'matching2_name',
 * 			...
 * 		),
 * 	...
 * 	);
 */
/* 子分类正则匹配, 匹配的指定层级没有url返回则表示是最后级 */
$mod_config['category_rules'] = array(
	/* level 1 */
	1 => array(
		/* 当前页面关键规则，没有匹配则表示(无效页面)非此页面 */
		'key'=>array('rule'=>'%						<li>.*<a href="(.*)">(.*)<span>\((.*)\)</span>.*</a>%iUs',
			1=>'ctg_url',
			2=>'ctg_name',
			3=>'gds_sum',
		),			/* 1级 子分类名；子分类url 手动生成 */
	),

);
$mod_config['category_ruless'] = array(
	/* level 1 */
	1 => array(
		/* 当前页面关键规则，没有匹配则表示(无效页面)非此页面 */
		'key'=>array('rule'=>'%<ul class="categoryList">.*<h2>(.*)</h2>%iUs',
			1=>'ctg_url',
		),			/* 1级 子分类名；子分类url 手动生成 */
	),
	/* level 2 */
	2 => array(
		/* 当前页面关键规则，没有匹配则表示(无效页面)非此页面 */
		'key'=>array('rule'=>'%						<li>.*<a href="(.*)">(.*)<span>\((.*)\)</span>.*</a>%iUs',
			1=>'ctg_url',
			2=>'ctg_name',			/* 2级 子分类名和子分类url */
			3=>'gds_sum'),
	),
	/* level 3, 可能有, 如果没有或者可以直接归为上级分类，则最好不配置此子分类，这样可以大大节约采集时间 */
	3 => array(
		/* 当前页面关键规则，没有匹配则表示(无效页面)非此页面 */
		'key'=>array('rule'=>'%						<li>.*<a href="(.*)">(.*)<span>\((.*)\)</span>.*</a>%iUs',
			1=>'ctg_url',
			2=>'ctg_name',			/* 2级 子分类名和子分类url */
			3=>'gds_sum'),
	),
);

/* 产品列表正则匹配, TODO: 支持二级子匹配 */
$mod_config['goodslist_rules'] = array(
	'goods_sum'=>array('rule'=>'%<span id="titleProdCount">(.*)</span> Products Found%iUs',
		1=>'gds_sum'), 
	/* 列表匹配，需要支持二级匹配 (为了尽可能多的抓取数据，第一轮匹配越简单越少越好) */
	'gdslist'=>array('rule'=>'%<a class="sku" href="(.*)" title=.*>(.*)</a>.*<p class="wordBreak">.*<a href=.*>(.*)</a>%iUs',
		1=>'gds_url',		/* (必须) 产品详情页 */
		2=>'gds_sn',		/* 供应商产品名(产品编号) */
		3=>'gds_name',		/* (必须) 制造商产品编号 */
		//4=>'provider',		/* 制造商 */
		//5=>'inventory',	/* 库存 */
	),
);

/**
 * 标准通用匹配规则
 * 递归N次匹配，并同时支持（列表匹配，非列表）多个匹配规则
 * 返回匹配规则类似的数组。
 * demo:
   $mod_config['goods_rules'] = array(
	array(
		'rule'=>array(
			'match'=>'% class="SearchResultsTopLevelCategory" href="(.*)"><h2 class=\'seoh2DefaultPage\'>(.*)</h2>%iUs',
			'list'=>1,		//1 单次匹配,n (整数n)次匹配,all 全部匹配
		),
		1=>'gds_name',
		2=>'gds_sn',
	),
	array(
		'rule'=>array(
			'match'=>'% class="SearchResultsTopLevelCategory" href="(.*)"><h2 class=\'seoh2DefaultPage\'>(.*)</h2>%iUs',
			'list'=>'all',		// 1 单次匹配,n (整数n)次匹配,all 全部匹配 
			'key'=>'prices',	// 多次匹配需要指定key name值，匹配结果列表会赋值给此key
		),
		1=>'gds_count',
		2=>'gds_price'
	),
	array(
		'rule'=>array(
			'match'=>'% class="SearchResultsTopLevelCategory" href="(.*)"><h2 class=\'seoh2DefaultPage\'>(.*)</h2>%iUs',
			'list'=>2,		// 1 单次匹配,n (整数n)次匹配,all 全部匹配
			'key'=>'list',		// 多次匹配需要指定key name值，匹配结果列表会赋值给此key
		),
		1=>'gds_thumb',
		2=>array(
			array(
				'rule'=>array('match'=>'%(.*)pdf%iUs','list'=>'all,'key'=>'gds_imgs'),1=>'gds_img'
			),
		),
	),
);
 * return:
 * array(
 * 	'gds_name'=>'goods name',
 * 	'gds_sn'=>'goods sn',
 * 	'prices'=>array(
 * 			array('gds_count'=>'1','gds_price'=>'1.2'),
 * 			array('gds_count'=>'10','gds_price'=>'1.1'),
 * 			array('gds_count'=>'100','gds_price'=>'0.9')
 * 	),
 * 	'list'=>array(
 * 			array(
 * 				'gds_thumb'=>'http://xxx/goods_thumb.jpg',
 * 				'gds_imgs'=>array(
 * 						array('gds_img'=>'http://xxx/gds1.jpg'),
 * 				)
 * 			),
 * 			array(
 * 				'gds_thumb'=>'http://xxx/goods.jpg',
 * 				'gds_imgs'=>array(
 * 						array('gds_img'=>'http://xxx/gdsn1.jpg'),
 * 						array('gds_img'=>'http://xxx/gdsn2.jpg'),
 * 				)
 * 			)
 * 	),
 * )
 */
/* 产品正则匹配，非列表匹配 （目前兼容通用规则） */
$mod_config['goods_rules'] = array(
	array(
		'rule'=>array(
			'match'=>'%<strong>Newark Part No.:</strong>(.*)</li>%iUs',
			'list'=>1,		/* 1 单次匹配,n (整数n)次匹配,all 全部匹配 */
		),
		1=>'gds_sn',			/* 供应商产品名(产品编号) */
	),
	array(
		'rule'=>array(
			'match'=>'%<strong>Manufacturer Part No</strong>(.*)</li>%iUs',
			'list'=>'1',		
		),
		1=>'gds_name',			/* 制造商产品编号 */
	),
	array(
		'rule'=>array(
			'match'=>'%<ul id="technicalData">(.*)</ul>%iUs',
			'list'=>'1',		
		),
		1=>array(
			array(
				'rule'=>array(
					'match'=>'%href="(.*)" %iUs',
					'list'=>'1',		/* */
				),
				1=>'gds_doc',		/* 产品数据表，可能有 */
			),
		),
	),
	array(
		'rule'=>array(
			'match'=>'%<img id="productMainImage" src="(.*)" alt=%iUs',
			'list'=>'1',		
		),
		1=>'gds_img',			/* 产品 images ，可能有 */
	),
	array(
		'rule'=>array(
			'match'=>'%<title>.* - .* - (.*)\| Newark element14 US</title>%iUs',
			'list'=>'1',		
		),
		1=>'gds_description',		/* 产品说明，可能有 */
	),
	array(
		'rule'=>array(
			'match'=>'%<strong>RoHS Compliant:</strong>(.*)<br/>%iUs',
			'list'=>'1',		
		),
		1=>'rohsstate',		/* 产品说明，可能有 */
	),
	array(
		'rule'=>array(
			'match'=>'%<strong>Minimum order quantity:</strong>(.*)</p>%iUs',
			'list'=>'1',		
		),
		1=>'moq',		/* 标准包装量，可能有 */
	),
	array(
		'rule'=>array(
			'match'=>'%<span class="product" id="descAttributeName.*">(.*)</span>.*<span id="descAttributeValue.*">(.*)</span>%iUs',
			'list'=>'all',		/* 1 单次匹配,n (整数n)次匹配,all 全部匹配 */
			'key'=>'gds_attrs',	/* 多次匹配需要指定key name值，匹配结果list会赋值给此key */
		),
		1=>'attr_n',		/* 属性名 */
		2=>'attr_v',		/* 属性值 */
	),
	array(
		'rule'=>array(
			//'match'=>'%<td id=quantityavailable align=left>.*([0-9,]+)<br>%iUs',
			'match'=>'%<strong>Availability:</strong>&nbsp;(.*)</p>%iUs',
			'list'=>'1',		/* */
		),
		1=>'inventory',			/* 库存，可能有，但很重要 */
	),
	array(
		'rule'=>array(
			'match'=>'%<td class="qty">(.*)&nbsp;-.*<td class="threeColTd">.*\$(.*)</td>%iUs',
			'list'=>'all',		/* 1 单次匹配,n (整数n)次匹配,all 全部匹配 */
			'key'=>'prices',	/* 多次匹配需要指定key name值，匹配结果list会赋值给此key */
		),
		1=>'price_c',		/* 阶梯数量 */
		2=>'price_v',		/* 阶梯价格 */
	),
	array(
		'rule'=>array(
			'match'=>'%<strong>Manufacturer:</strong>(.*)				(.*)</%iUs',
			'list'=>'1',		/* */
		),
		1=>array(
			array(
				'rule'=>array(
					'match'=>'%href="(.*)">%iUs',
					'list'=>'1',		/* */
				),
				1=>'provider_url',		/* 制造商信息, 可能有 */
			),
		),
		2=>'provider',					/* 制造商, 一定有 */
	),
	/*array(
		'rule'=>array(
			'match'=>'%<span id="ctl00_ContentMain_Specifications_dlspec_ctl.*_lblDimension">(.*)</span>.*<span id="ctl00_ContentMain_Specifications_dlspec_ctl.*_lblName">(*)</span>%iUs',
			'list'=>'all',
			'key'=>'attr',
		),
		1=>'gds_attr_n',
		2=>array(
			array(
				'rule'=>array('match'=>'%(.*)pdf%iUs','list'=>1),
				1=>'gds_attr_n'
			),
		),
		3=>'gds_price'
	),*/
);

include_once('newark.lib.module.php');

/* end file */
