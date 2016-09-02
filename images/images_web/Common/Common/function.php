<?php
if ( !defined('CLIENT_IP') ) { define('CLIENT_IP', getenv('HTTP_X_FORWARDED_FOR') ? getenv('HTTP_X_FORWARDED_FOR') : getenv('REMOTE_ADDR')); }

/**
 * UTF8 中文字符串截取
 * @param   string	$str		
 * @param   int		$length	 
 * @param   bool	$append	
 * @return  string
 */
function sub_str($str, $length = 0, $append = true)
{
	$str = trim($str);
	$strlength = strlen($str);

	if ($length == 0 || $length >= $strlength) {
		return $str;
	} else if ($length < 0) {
		$length = $strlength + $length;
		if ($length < 0) {
			$length = $strlength;
		}
	}

	if (function_exists('mb_substr')) {
		$newstr = mb_substr($str, 0, $length, 'utf-8');
	} else if (function_exists('iconv_substr')) {
		$newstr = iconv_substr($str, 0, $length, 'utf-8');
	} else {
		$newstr = substr($str, 0, $length);
	}

	if ($append && $str != $newstr) {
		$newstr .= '...';
	}

	return $newstr;
}

/**
 * 处理表单提交的字符过滤
 *
 * @param   string	$string 需要处理的字符串		
 * @param   int		$trim   需要删除空格	 
 * @return  string
 */
function tpaddslashes($string = '' , $trim = true ,$js = false) {
	$arr = array('<' => '＜', '>' => '＞'); //防止script注入
	if(!get_magic_quotes_gpc()) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				
				if ($js) {
					$val		  = strtr($val, $arr);
				}
				$string[$key] = tpaddslashes($val, $trim);
			}
		} else {
			if ($trim) {
				$string = trim($string);
			}
			
			if ($js) {
				$string		= strtr($string, $arr);
			}
			$string		= addslashes($string);
		}
	}
	return $string;
}

/**
 * 通过密码，产生加密后的密码以及密钥
 * @param  string  $pwd		   密码明文
 * @return array  $result      供应商物料参数
 */
function pwd_and_salt($pwd = '') {
	$pwd_salt	= substr(md5(rand(10000,99999)),0,8);
	$pwd		= md5($pwd_salt.(md5($pwd)));
	return array('pwd' => $pwd , 'pwd_salt' => $pwd_salt);
}

/**
 * 价格格式化（保留两位小数）
 * @param float $price
 * @param intval $money_type 价格类型1为人民币，2为美元
 * @param string $sign 结算 two保留2位，显示保留4位
 * @return float
 */
function price_format($price = 0 , $money_type = 0,$sign = '') {
	if ($price > 0) {
		if ($sign == 'two') {
			$price = number_format_temp($price,'two');
		} else {
			$price = number_format_temp($price);
		}
	} else {
		$price = 0;
	}
	
	if ($money_type) {
		return price_currency_format($money_type).$price;
	}
	return $price;
}

/**
 * 验证输入的邮件地址是否合法
 * @access  public
 * @param   string      $email      需要验证的邮件地址
 * @return bool
 */
function is_email($user_email)
{
    $chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
    if (strpos($user_email, '@') !== false && strpos($user_email, '.') !== false) {
        if (preg_match($chars, $user_email)) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/**
 * 生成分页链接
 * @param integer $offset 
 * @param integer $count
 * @param ineger $limit
 * @param string $url
 * @param string $page_key
 * @return string
 */
function create_page_link($offset = 1, $count, $limit = 20, $url, $page_key = 'p') {
	$return_str = '';
	$roll_page = 5;
	$page_count = ceil($count / $limit);
	$cool_page = ceil($page_count / $roll_page);
	$now_cool_page = ceil($offset / $roll_page);
	if ($page_count > 1) {
		$up_row = $offset - 1;
		$down_row = $offset + 1;
		// 上下翻页字符串
		if ($up_row > 0) {
			$up_page = "<a href='" . $url . "&" . $page_key . "=$up_row' class=\"page-next\">上一页</a>"; 
		} else {
			$up_page = "";
		}
		if ($down_row <= $page_count) {
			$down_page = "<a href='" . $url . "&" . $page_key . "=$down_row' class=\"page-next\">下一页</a>"; 
		} else {
			$down_page = "";
		}
		// << < > >>
		if ($now_cool_page == 1) {
			$the_first = "";
			$pre_page = "";
		} else {
			$pre_row = $offset - $roll_page;
			$pre_page = " ... ";
			$the_first = "<a href='" . $url . "&" . $page_key . "=1' class=\"page-num\" >1</a>";
		}
		if ($now_cool_page == $cool_page) {
			$next_page = "";
			$the_end = "";
		} else {
			$next_row = $offset + $roll_page;
			$the_end_row = $page_count;
			$next_page = " ... ";
			$the_end = "<a href='" . $url . "&" . $p . "=$the_end_row' class=\"page-num\" >" . $the_end_row . "</a>";
		}
		// 1 2 3 4 5 
		$link_page = "";
		for ($i = 1; $i <= $roll_page; $i++) {
			$page = ($now_cool_page - 1) * $roll_page + $i;
			if ($page != $offset) {
				if($page <= $page_count){
                    $link_page .= "<a href='" . $url . "&" . $page_key . "=$page' class=\"page-num\">" . $page . "</a>";
                }else{
                    break;
                }
			} else {
				if ($page_count != 1) {
					 $link_page .= "<strong class='page-cur'>" . $page . "</strong>";
				}
			}
		}
		$return_str = "$up_page $the_first $pre_page $link_page $next_page $the_end $down_page";
	} 
	return $return_str;
}

/**
 * 验证手机号码格式
 *
 * @access  public
 * @param   $mobile   手机号码
 * @return  boolean	  是否正常的手机号码格式
 */
function valid_mobile($mobile) {
	
	return preg_match("/^1\d{10}$/is",$mobile);
}

/**
 * thinkphp excel解析excel
 *@param  $filename  string excel文件地址
 *@return $array	 array  返回excel文件内容
 */
 function explain_excel($filename = '') {	
	Vendor('phpexcel.PHPExcel.IOFactory');
	$reader = PHPExcel_IOFactory::createReader('Excel2007'); //设置以Excel5格式(Excel97-2003工作簿)
	if(!$reader->canRead($filename)){ 
		$reader = PHPExcel_IOFactory::createReader('Excel5');
		if(!$reader->canRead($filename)){ 
		echo 'no Excel'; 
		return ; 
		} 
	} 
	$PHPExcel = $reader->load($filename); // 载入excel文件
	$sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
	$highestRow = $sheet->getHighestRow(); // 取得总行数
	$highestColumm = $sheet->getHighestColumn(); // 取得总列数
	$array		= array();
	$i			= 0;
	/** 循环读取每个单元格的数据 */
	for ($row = 2; $row <= $highestRow; $row++){//行数是以第1行开始
		for ($column = 'A'; $column <= $highestColumm; $column++) {//列数是以A列开始
			
			if ($column == 'F') {
				$val = $sheet->getCell($column.$row)->getValue();
				if (preg_match('/\d{5}/',$val)) {
					$array[$i][]=gmdate("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($val));
				} else {
					$array[$i][] = $sheet->getCell($column.$row)->getValue();
				} 
			} else {
				$array[$i][] = $sheet->getCell($column.$row)->getValue();
			}
			
		}
		$i++;
	}
	
	return $array;
 }

 /**
 * 使用CURL模拟post
 * 返回结果string
 * @access  public
 * @param   string       $url 请求地址
 * @return  void
 */
function curl_post($url = '' , $args = '') {
	$ch = curl_init(); //初始化curl
	curl_setopt($ch, CURLOPT_URL, $url);//设置链接
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息
	curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
	curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式
	curl_setopt($ch, CURLOPT_POSTFIELDS,$args );//POST数据
	$output = curl_exec($ch);//接收返回信息
	curl_close($ch); //关闭curl链接
	return $output;//显示返回信息
}

/** 
 * @fn
 * @brief 数字货币转化为中文大写
 * @access public
 * @param string $money	数字格式货币
 * @return  中文格式货币
 */  
function cny_cn($money) { /* {{{ */
	$money = sprintf("%01.2f", $money);
	if ($money <= 0) {
		return '零圆';
	}
	$units = array ( '', '拾', '佰', '仟', '', '万', '亿', '兆' );
	$amount = array( '零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖' );
	$arr = explode('.', $money);
	$money = strrev($arr[0]);			/* 翻转整数 */
	$length = strlen($money);
	for ($i = 0; $i < $length; $i++) {
		$int[$i] = $amount[$money[$i]];		/* 获取大写数字 */
		/* 去掉连继零 */
		if ( $i > 0 && $money[$i-1] == 0 && $money[$i] == 0 ) {
			$int[$i] = '';
		}
		/* (金额超过10) 圆，万，亿，兆 前零不读 */
		if ( ($i == 0 || $i == 4 || $i == 8) && $money[$i] == 0 ) {
			$int[$i] = '';
		}

		if (!empty($money[$i])) {  
			$int[$i] .= $units[$i%4];	/* 获取整数位 */
		}
		if ($i%4 == 0) {
			$int[$i] .= $units[4+floor($i/4)];	/* 取整 */
		}
	}
	$con = isset($arr[1]) ? '圆' . $amount[$arr[1][0]] . '角' . $amount[$arr[1][1]] . '分' : '圆整';  
	return implode('', array_reverse($int)) . $con;	/* 整合数组为字符串 */
} /* }}} */

/**
 * @fn
 * @brief 日志记录函数
 * @param $log_file	日志文件名
 * @param $log_str	日志内容
 * @param $show		日志内容是否show出
 * @param $log_size	日志文件最大大小，默认20M
 * @return void
 */
function wlog($log_file, $log_str, $show = false, $log_size = 20971520) /* {{{ */
{
	ignore_user_abort(TRUE);

	$time = '['.date('Y-m-d H:i:s').'] ';
	if ( $show ) {
		echo $time.$log_str.((PHP_SAPI == "cli") ? "\r\n" : "<br>\r\n");
	}
	if ( empty($log_file) ) {
		$log_file = 'wlog.txt';
	}
	if ( defined('APP_LOG_PATH') ) {
		$log_file = APP_LOG_PATH.$log_file;
	}

	if ( !file_exists($log_file) ) { 
		$fp = fopen($log_file, 'a');
	} else if ( filesize($log_file) > $log_size ) {
		$fp = fopen($log_file, 'w');
	} else {
		$fp = fopen($log_file, 'a');
	}

	if ( flock($fp, LOCK_EX) ) {
		$cip	= defined('CLIENT_IP') ? '['.CLIENT_IP.'] ' : '['.getenv('REMOTE_ADDR').'] ';
		$log_str = $time.$cip.$log_str."\r\n";
		fwrite($fp, $log_str);
		flock($fp, LOCK_UN);
	}
	fclose($fp);

	ignore_user_abort(FALSE);
} /* }}} */

/**
 * @fn
 * @brief 通过http协议模拟浏览器向服务器请求数据
 * 	此函数有超级牛力^^
 * 	支持并独立保存COOKIE, 支持HTTP/1.1 Transfer-Encoding: chunked.
 * 	向 $url 以 POST 方式传递数据, 支持 ssl 和 http 方式, 支持 http 帐号验证.
 * 	TODO: 将 cookie 保存为 json 格式, 或者以行记录.
 * 	NOTE: 如果服务器端 Accept-Encoding 默认不为 identity, 
 * 		请发送 Accept-Encoding 头, 确保返回的消息不被编码:
 * 		"Accept-Encoding: " or "Accept-Encoding: identity"
 * @author Langr<hua@langr.org>
 * @param $url		
 * @param $data		POST 到对方的数据, 为空时以 GET 方式传递, e.g. array('n1'=>'v1','n2'=>'v2') or "n1=v1&n2=v2"
 * @param $header	http header 头数据, e.g. array('User-Agent'=>'Mozilla/5.0') or "User-Agent: Mozilla/5.0\r\n"
 * @param $cookie_path	cookie存储的路径, 为 'nonsupport' 时则不存储, 
 * 			如果定义了 'H2H_COOKIE_PATH' 常量, 则不关注 'nonsupport' 值, 全部存储.
 * @param $debug	调试: false 不调试, true 调试, 记录发送接收头到日志文件.
 * 			如果定义了 'APP_DEBUG' 常量, 则不关注此参数.
 * @return $url 传回的web数据
 */
function http2host($url, $data = array(), $header = "User-Agent: Mozilla/5.0 (Windows NT 6.1) Chrome/33.0\r\n", $cookie_path = 'nonsupport', $debug = false) { /* {{{ */
	$encoded = '';
	$post = 'POST';
	$line = '';

	if ( defined('APP_DEBUG') ) {
		$debug = APP_DEBUG;
	}
	/* 准备数据 */
	if ( is_array($data) && count($data) > 0 ) {
		while ( list($k, $v) = each($data) ) {
			$encoded .= rawurlencode($k)."=".rawurlencode($v)."&";
		}
		$encoded = substr($encoded, 0, -1);
	} else if ( is_string($data) ) {
		$encoded = $data;
	} else {
		$post	= 'GET';
	}

	$urls = parse_url($url);
	if ( !isset($urls['port']) ) { $urls['port'] = 80; }
	if ( !isset($urls['query']) ) { $urls['query'] = ''; }
	if ( !isset($urls['path']) ) { $urls['path'] = '/'; }
	if ( !isset($urls['host']) ) { return '-11 url error'; }

	$m = '';
	if ( $urls['scheme'] == 'https' ) {
		$m = 'ssl://';
		$urls['port'] = ($urls['port'] == 80) ? 443 : $urls['port'];
	}
	if ( ($urls['scheme'] == 'ssl' || $urls['scheme'] == 'udp') ) {
		$m = $urls['scheme'].'://';
	}
	$fp = @fsockopen($m.$urls['host'], $urls['port']);
	if ( !$fp ) {
		return "-12 failed to open socket to {$urls['host']}:{$urls['port']}";
	}

	/* request */
	$request_headers = '';
	//$request_headers .= sprintf($post." %s%s%s HTTP/1.1\r\n", $urls['path'], $urls['query'] ? '?' : '', $urls['query']);
	$request_headers .= sprintf($post." %s%s%s HTTP/1.0\r\n", $urls['path'], $urls['query'] ? '?' : '', $urls['query']);
	$request_headers .= "Host: {$urls['host']}\r\n";
	/* basic 认证 */
	if ( !empty($urls['user']) ) {
		$request_headers .= "Authorization: Basic ".base64_encode($urls['user'].':'.$urls['pass'])."\r\n";
	}
	if ( $post == 'POST' ) {
		$request_headers .= "Content-type: application/x-www-form-urlencoded; charset=utf-8\r\n";
		$request_headers .= "Content-length: ".strlen($encoded)."\r\n";
	}
	/* 自定义 header */
	if ( is_array($header) && count($header) > 0 ) {
		while ( list($k, $v) = each($header) ) {
			$request_headers .= "$k: $v\r\n";
		}
	} else if ( is_string($header) ) {
		$request_headers .= $header;
	}

	/* COOKIE 支持, send */
	$_allow_cookie = true;
	if ( defined('H2H_COOKIE_PATH') ) {
		$cookie_path = H2H_COOKIE_PATH;
	} else if ( $cookie_path == 'nonsupport' ) {
		$_allow_cookie = false;
		$cookie_path = '';
	}
	$cookie_file = $cookie_path.$urls['host'].'.cookie';
	if ( $_allow_cookie && file_exists($cookie_file) ) {
		/* TODO: json */
		$request_headers .= "Cookie: ".trim(file_get_contents($cookie_file))."\r\n";
	}
	$request_headers .= "Connection: close\r\n\r\n";

	if ( $post == 'POST' ) {
		$request_headers .= "$encoded\r\n";
	}
	if ( $debug ) {
		wlog('http2host.log', "Host[$m{$urls['host']}:{$urls['port']}] URL[$url]\r\n".$request_headers);
	}
	fputs($fp, $request_headers);

	/* response */
	$response_headers = '';
	$line = fgets($fp, 4096);
	$response_headers .= $line;
	/* http error? 3xx 不处理 */
	if ( !preg_match("/^HTTP\/1\.. 200/", $line) ) {
		$_errno = substr(trim($line), 9);
		if ( $_errno[0] != '3' ) {
			return '-'.$_errno;
		}
	}

	$results = '';
	$inheader = true;
	$i = 0;
	$cookie_o = array();
	$cookie_n = array();
	$has_chunk = false;	/* 数据分块的？ */
	while ( !feof($fp) ) {
		$line = fgets($fp, 4096);
		if ( $line === false ) {
			break;
		}
		if ( $inheader ) {
			$response_headers .= $line;
		}
		if ( $inheader && substr($line, 0, 19) == 'Transfer-Encoding: ' ) {
			if ( trim(substr($line, 19)) == 'chunked' ) {
				$has_chunk = true;
			}
		}
		/* COOKIE 支持, recv */
		if ( $inheader && $_allow_cookie && substr($line, 0, 12) == 'Set-Cookie: ' ) {
			$line = substr(trim($line), 12);
			$cookie_o = $cookie_n = array();
			/* TODO: json */
			if ( file_exists($cookie_file) ) {
				$cookie_old = trim(file_get_contents($cookie_file));
				$cookie_array = explode('; ', $cookie_old);
				foreach ( $cookie_array as $k=>$v ) {
					$eq = strpos($v, '=');
					if ( $eq === false ) continue;
					$cookie_o[substr($v, 0, $eq)] = substr($v, $eq + 1);
				}
			}
			$cookie_new = explode('; ', $line);
			$eq = strpos($cookie_new[0], '=');
			$cookie_n[substr($cookie_new[0], 0, $eq)] = substr($cookie_new[0], $eq + 1);

			$cookie_n = array_merge($cookie_o, $cookie_n);
			$line = '';
			foreach ( $cookie_n as $k=>$v ) {
				$line .= $k.'='.$v.'; ';
			}
			$line = substr($line, 0, -2);
			file_put_contents($cookie_file, $line);
		}
		/* 去掉第一次的空行 */
		if ( $inheader && ($line == "\n" || $line == "\r\n") ) {
			$inheader = false;
			break;
		}
	}
	/* line 1 */
    	$_data = fgets($fp, 4096);
	$r = trim($_data);
	$rn = 0;		/* 读块长度 */
	/* HTTP/1.1 Transfer-Encoding: chunked 支持，正文中的块长度标识 */
	if ( $has_chunk && is_numeric('0x'.$r) ) {
		$rn = base_convert($r, 16, 10);
		wlog('http2host.log', "length chunk:$r,$rn,total:".strlen($results));
		$_data = fgets($fp, 4096);	/* has_chunk 去掉第一行 \r or \r\n */
		if ( $_data != "\n" && $_data != "\r\n" ) {
			$results .= $_data;
			$rn -= strlen($_data);
		}
	} else {
    		$results .= $_data;
	}
	do {
		/* 读块 */
		if ( $has_chunk && $rn > 0 ) {
			$__tmp = '';
			while ( ($__tmp = fread($fp, $rn)) !== false ) {
				$rn = $rn - strlen($__tmp);
    				$results .= $__tmp;
				if ( $rn == 0 ) { break; }
			}
		}
    		$_data = fgets($fp, 4096);
    		if ( $_data === false ) {
        		break;
		}
		/* 取块长度 */
		if ( $has_chunk ) {
			if ( $_data != "\n" && $_data != "\r\n" ) {
				$rn = base_convert(trim($_data), 16, 10);
				wlog('http2host.log', "length chunk:".trim($_data).",$rn,total:".strlen($results));
			}
			continue;
		}
		$results .= $_data;
	} while ( !feof($fp) );
	fclose($fp);
	if ( $debug ) {
		wlog('http2host.log', "\r\n".$response_headers);
	}

	return $results;
} /* }}} */

/**
 * @fn
 * @brief 去掉url路径中的'../'，并返回正确的路径
 * @param 
 * @return 
 */
function format_url($url) /* {{{ */
{
	if ( substr($url, 0, 4) != 'http' ) { 
		$url = 'http://'.$url;
	}
	$http_arr = parse_url($url);
	$arr = explode("/", $http_arr['path']);
	for ( $i=0; $i < count($arr); $i++ ) {
		if ( $arr[$i] == '..' ) {
			if ( $i == 0 ) {
				array_shift($arr);
			} else {
				array_splice($arr, $i - 1, 2);
			}
			$i = -1;
			continue;
		}
		if ( $arr[$i] == '' ) {
			array_splice($arr, $i, 1);
		}
	}
	$new_url = join("/", $arr);
	$new_query = empty($http_arr['query']) ? '' : '?'.$http_arr['query'];
	$new_url = $http_arr['scheme'].'://'.$http_arr['host'].'/'.$new_url.$new_query;
	return $new_url;
} /* }}} */

/**
 * @fn
 * @brief 连接url，处理一个页面中的href连接，
 * 	同时需处理url中的path, ../, args...
 * @param $purl	父url, 
 * @param $surl	子url, 
 * @return 
 */
function href_url($purl, $surl) /* {{{ */
{
	if ( substr($surl, 0, 4) == 'http' ) {
		return $surl;
	}
	if ( substr($purl, 0, 4) != 'http' ) { 
		$purl = 'http://'.$purl;
	}
	$p = parse_url($purl);
	$p['path'] = empty($p['path']) ? '/' : $p['path'];
	$surl = ($surl[0] == '/' ? $p['scheme'].'://'.$p['host'].$surl
		: (substr($p['path'], -1) == '/' ? $p['scheme'].'://'.$p['host'].$p['path'].$surl 
			: $p['scheme'].'://'.$p['host'].$p['path'].'/../'.$surl));
	$surl = format_url($surl);
	return $surl;
} /* }}} */

/**
 * @brief 数组转换为xml
 */
function _array2xml(&$data = array()) /* {{{ */
{
	$xml = '';
	foreach ( $data as $key => $val ) {
		if ( is_array($val) ) {
			$xml .= "<$key>"._array2xml($val)."</$key>";
		} else {
			$xml .= "<$key>$val</$key>";
		}
	}
	return $xml;
} /* }}} */

/**
 * @fn
 * @brief 数组转换为xml
 * @param 
 * @return 
 */
function array2xml(&$data = array()) /* {{{ */
{
	$xml = "<?xml version='1.0' encoding='UTF-8' ?>";
	return $xml._array2xml($data);
} /* }}} */

/* end file */
