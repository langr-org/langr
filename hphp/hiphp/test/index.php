<?php
/** 
 * @file index.php
 * @brief 
 * 
 * Copyright (C) 2011 LangR.Org
 * 
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package test
 * @author Huang Hua <hua@langr.org> 2011/10/27 18:09
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 
 * $Id: index.php 8 2011-11-02 10:07:04Z loghua@gmail.com $
 */

echo dirname(__FILE__);
phpinfo();

/**
 * http://h.l.com/p/www/index.php?u=/c/a/arg1/argx.html&a=b&c=d
 * http://h.l.com/p/www/c/a/arg1/argx.html?a=b&c=d
 * || http://h.l.com/p/www/index.php/u/aba/bbb/ccc.html?a=b&c=d
 * HTTP_HOST = h.l.com
 * PHP_SELF = /p/www/index.php
 * || isset(_SERVER["PATH_INFO"]) ?
 * || url = http://h.l.com/p/www/index.php/u/aba/bbb/ccc.html?a=b&c=d
 * || PHP_SELF = /p/www/index.php/u/aba/bbb/ccc.html
 * || _SERVER["PATH_INFO"] = /u/aba/bbb/ccc.html
 * || _SERVER["REQUEST_URI"] = /p/www/index.php/c/a/arg1/argx.html?a=b&c=d
 * $base = /p/www/
 * rewrite: $uri = /p/www/c/a/arg1/argx.html?a=b&c=d
 * no rewrite: $uri = /p/www/index.php?u=c/a/arg1/argx&a=b&c=d
 * $_GET['u'] = c/a/arg1/argx.html
 * url($u) = /c/a/arg1/argx
 * $url = /c/a/arg1/argx.html
 * ... $url = /c3/arg1/arg2/argx.html
 * $routes .= /c/a => /c2/index		control => 'c2', action => 'index'
 * $routes .= /c/b => /c2/pages		control => 'c2', action => 'pages'
 * $routes .= /c3/* => /c3/show		control => 'c3', action => 'show'
 * $params[] = control => 'c2', action => 'index'
 */
/**
 * url() 要处理脚本在有 rewrite 和没有 rewrite 支持下的 url 正确指向.
 * url() 不关注 routes. 也就是我们暂时没有支持双向路由的打算. 很长很长一段时间都不会有.
 * url('/c/a/arg1/arg2/argx?a=b&c=d') =>
 * no rewrite:	$base + 'index.php' + '?u=/c/a/arg1/arg2/argx&a=b&c=d'
 * rewrite:	$base + '/c/a/arg1/arg2/argx?a=b&c=d'
 *
 * url('/c/{$a}/arg1/{$arg2}/{$argx}?a={$b}&c={$d}')
 * no rewrite:	$base + 'index.php' + '?u=/c/{$a}/arg1/{$arg2}/{$argx}&a={$b}&c={$d}'
 * rewrite:	$base + '/c/{$a}/arg1/{$arg2}/{$argx}?a={$b}&c={$d}'
 * url(), params() 转换解释支持空参数: url('/c/a/{$arg1=null}/{$arg2=null}/arg3') => /c/a///arg3 => params()
 * argx 包含 '?','/' ... 字符时需要在调用url()前作 urlencode, 其他的(action,_GET)可不用处理.
 * argx = urlencode(argx)
 */
function url($u)
{
	;
}

/**
 * 先根据 routes 配置规则转换 $url,
 * 然后解析出参数, $_GET 参数不用作处理,
 * 最后对需要的 argx 作 urldecode
 */
function params($url)
{
	routes($url);
	/* ... */
}

/**
 * 对有配置 routes 的 url 处理到正确的 action
 */
function routes($url)
{
	;
}

/* end file */
