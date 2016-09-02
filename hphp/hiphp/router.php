<?php
/** 
 * @file router.php
 * @brief 
 * 
 * Copyright (C) 2011 LangR.Org
 * 
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package hiphp
 * @author Langr <hua@langr.org> 2011/11/16 14:32
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 
 * $Id: router.php 22 2012-06-04 01:14:26Z loghua@gmail.com $
 */
$__inc_files[] = __FILE__;

/**
 * 正常: http://h.l.com/p/www/index.php?u=/c/a/arg1/argx.html&a=b&c=d
 * rewrite: http://h.l.com/p/www/c/a/arg1/argx.html?a=b&c=d
 * PATH_INFO: http://h.l.com/p/www/index.php/c/a/arg1/argx.html?a=b&c=d
 * HTTP_HOST = h.l.com
 * PHP_SELF = /p/www/index.php
 * || isset(_SERVER["PATH_INFO"]) ?
 * || url = http://h.l.com/p/www/index.php/c/a/arg1/argx.html?a=b&c=d
 * || PHP_SELF = /p/www/index.php/c/a/arg1/argx.html
 * || _SERVER["PATH_INFO"] = /c/a/arg1/argx.html
 * || _SERVER["REQUEST_URI"] = /p/www/index.php/c/a/arg1/argx.html?a=b&c=d
 * || _SERVER['SCRIPT_NAME'] = /p/www/index.php
 * $base = /p/www/
 * rewrite: $uri = /p/www/c/a/arg1/argx.html?a=b&c=d
 * no rewrite: $uri = /p/www/index.php?u=c/a/arg1/argx&a=b&c=d
 * $_GET['u'] = c/a/arg1/argx.html
 * url($u) = /c/a/arg1/argx
 * $url = /c/a/arg1/argx.html
 * ... $url = /c3/arg1/arg2/argx.html
 * $routes .= /c/a => /c2/index		controller => 'c2', action => 'index'
 * $routes .= /c/b => /c2/pages		controller => 'c2', action => 'pages'
 * $routes .= /c3/* => /c3/show		controller => 'c3', action => 'show'
 * $params[] = controller => 'c2', action => 'index'
 */
/**
 * url() 要处理脚本在有 rewrite 和没有 rewrite 或 PATH_INFO 支持下的 url 正确指向.
 * url() 不关注 routes. 也就是我们暂时没有支持双向路由的打算. 很长很长一段时间都不会有.
 * url('/c/a/arg1/arg2/argx?a=b&c=d') =>
 * no rewrite:	$base + 'index.php' + '?u=/c/a/arg1/arg2/argx&a=b&c=d'
 * rewrite:	$base + '/c/a/arg1/arg2/argx?a=b&c=d'
 *
 * url('/c/{$a}/arg1/{$arg2}/{$argx}?a={$b}&c={$d}')
 * no rewrite:	$base + 'index.php' + '?u=/c/{$a}/arg1/{$arg2}/{$argx}&a={$b}&c={$d}'
 * PATH_INFO:	$base + 'index.php' + '/c/{$a}/arg1/{$arg2}/{$argx}?a={$b}&c={$d}'
 * rewrite:	$base + '/c/{$a}/arg1/{$arg2}/{$argx}.{$suffix}?a={$b}&c={$d}'
 * url(), params() 转换解释支持空参数: url('/c/a/{$arg1=null}/{$arg2=null}/arg3') => /c/a///arg3 => params()
 * argx 包含 '?','/' ... 字符时需要在调用url()前作 urlencode, 其他的(action,_GET)可不用处理.
 * argx = urlencode(argx)
 *
 * !!! 为提高效率, url 应该在模板编译时处理, 并且只适用在模板中使用的"函数"(或作为模板编译时的特殊标记符).
 * 	支持此标记的模板中使用:						不支持此标记的模板中使用:
 * {url('/c/a/arg1/arg2/argx?a=b&c=d')}			=>	<?=url('/c/a/arg1/arg2/argx?a=b&c=d')?>
 * 直接根据项目配置编译生成如下路径:				而直接使用函数时, 则是在每次调用模板时才执行并返回数据.
 * no rewrite => /p/www/index.php?u=c/a/arg1/arg2/argx&a=b&c=d
 * PATH_INFO => /p/www/index.php/c/a/arg1/arg2/argx?a=b&c=d
 * rewrite: => /p/www/c/a/arg1/arg2/argx.html?a=b&c=d
 */
function url($u)
{
	;
}

/**
 * @brief 先根据 routes 配置规则转换 $url,
 * 	然后解析出参数, $_GET 参数不用作处理,
 * 	最后对需要的 argx 作 urldecode
 */
function params($url)
{
	routes($url);
	/* ... */
}

/** 
 * @brief 设置/读取 route 配置.
 * 	INFO: 为提高效率, 暂不作正则表达式匹配处理, 但有一定的模糊匹配能力:
 * 	'+' 表示匹配一个参数,
 * 	'*' 表示匹配一个或多个参数,
 * 	router('/', array('ctrl'=>'index', 'act'=>'index'));
 * 	router('/cn/blogs/', array('ctrl'=>'blog', 'act'=>'list', 'params'=>array('lang'=>'cn'));
 * 	router('/blogs/', array('ctrl'=>'blog', 'act'=>'list');	绝对匹配'/blogs/'路径
 * 	router('/blogs/*', array('ctrl'=>'blog');			'*'优先匹配act; 找不到匹配act则匹配默认act, '*'则作为默认act参数
 * 	router('/blog/*', array('ctrl'=>'blog', 'act'=>'content'));	直接匹配'content', '*'作为'content'参数
 * 	router('/news/+', array('ctrl'=>'news', 'act'=>'content', 'params'=>array('id'=>'5')));
 * 	router('/news/+/+', array('ctrl'=>'news', 'act'=>'archive', 'params'=>array('year'=>'2012', 'month'=>'05')));
 * 	router('/news/+/+/*', array('ctrl'=>'news', 'act'=>'tags', 'params'=>array('year'=>'2012', 'month'=>'05', 'tags'=>'5')));
 * @param $path 需要配置路由的url路径
 * @param $routes 路由配置信息: array('ctrl'=>'index', 'act'=>'index', 'params'=>array())
 * @return 
 * 	不指定 $path: 返回所有的 route 配置数组;
 * 	不指定 $routes: 返回指定的 $path route 数组数据;
 * 	都指定: 配置指定的 $path 到路由 $routes.
 */
function router($path = null, $routes = array())
{
	static $__hi__router;
	
	if ( $path === null ) {
		return $__hi__router;
	}
	if ( $routes === null ) {
		return isset($__hi__router[$path]) ? $__hi__router[$path] : false;
	} else {
		$__hi__router[$path] = $value;
		return true;
	}
}

/**
 * @brief 反向路由: 对有配置 routes 的 action 生成正确的 url
 * 	TODO: 保留设计思想, 暂时不作设计.
 * @param $u url($u) 函数的参数.
 * @return $url 模板中相应action有正确反向路由的url
 */
function retuor($u)
{
	return $u;
}

/**
 * @brief 对有配置 routes 的 url 处理到正确的 action
 */
function routes($url)
{
	;
}

/* end file */
