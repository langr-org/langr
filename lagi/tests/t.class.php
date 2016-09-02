<?php
/**
 *
 */
echo basename(__FILE__).'<br/>';
echo dirname(__FILE__).'<br/>';
include('api.class.php');
$key = 'DeL>rty<:JKO#:k+_p';

/* 直接调用: */
$parms = array('srcno'=>'604', 'dstno'=>'808', 'type'=>'CFB');
$ret = api::showSetCF($parms);
echo "<br/>\nSetCF:";var_dump($ret);

$ret = api::showMeetList($parms);
echo "<br/>\nMeetList:";var_dump($ret);

$parms = array('srcno'=>'604', 'dstno'=>'808', 'type'=>'CFB');
//$ret = api::showOutCall($parms);
echo "<br/>\nOutCall:";var_dump($ret);

/* 远程对接调用 GET/POST: */
$wsdl = 'http://192.168.1.226/callcenter/app/control/index/api.class.php?wsdl';
$url = 'http://192.168.1.226/callcenter/app/control/index/api.class.php';
$parms = array('action'=>'SetCF', 'srcno'=>'604', 'dstno'=>'808', 'type'=>'CFB');
$parms['chksum'] = md5($parms['action'].$parms['srcno'].$parms['dstno'].md5(getenv('SERVER_ADDR').'@'.$key));
/* posttohost() 类似 file_get_contents() */
$ret = posttohost($url, $parms, 'GET');
echo "<br/>\nGET SetCF:";var_dump($ret);
$parms['srcno'] = '804';
$parms['chksum'] = md5($parms['action'].$parms['srcno_'].$parms['dstno'].md5(getenv('SERVER_ADDR').'@'.$key));
$ret = posttohost($url, $parms, 'GET');
echo "<br/>\nGET SetCF:";var_dump($ret);

$ret = posttohost($url, $parms, 'POST');
echo "<br/>\nPOST SetCF:";var_dump($ret);

/* 远程对接调用 SOAP: */
require("../../../lib/nusoap/nusoap.php");
$parms = array('action'=>'SetCF', 'srcno'=>'604', 'dstno'=>'808', 'type'=>'CFB', 'return'=>'');
$client = new soapclient($url);
$ret = $client->call('API', $parms);
echo "<br/>\nSOAP SetCF:";var_dump($ret);

/* Web Ajax 客户端直接调用: */
?>
<br/>
ajax:
<a onclick='outcall();'>点击拔号:</a><input type='text' id='called' />
<div id='agi_'>
	<div id='agi_ret'></div>
</div>

<script language="javascript">
/* md5 */
(typeof Crypto=="undefined"||!Crypto.util)&&function(){var m=window.Crypto={},o=m.util={rotl:function(h,g){return h<<g|h>>>32-g},rotr:function(h,g){return h<<32-g|h>>>g},endian:function(h){if(h.constructor==Number)return o.rotl(h,8)&16711935|o.rotl(h,24)&4278255360;for(var g=0;g<h.length;g++)h[g]=o.endian(h[g]);return h},randomBytes:function(h){for(var g=[];h>0;h--)g.push(Math.floor(Math.random()*256));return g},bytesToWords:function(h){for(var g=[],i=0,a=0;i<h.length;i++,a+=8)g[a>>>5]|=h[i]<<24-
a%32;return g},wordsToBytes:function(h){for(var g=[],i=0;i<h.length*32;i+=8)g.push(h[i>>>5]>>>24-i%32&255);return g},bytesToHex:function(h){for(var g=[],i=0;i<h.length;i++)g.push((h[i]>>>4).toString(16)),g.push((h[i]&15).toString(16));return g.join("")},hexToBytes:function(h){for(var g=[],i=0;i<h.length;i+=2)g.push(parseInt(h.substr(i,2),16));return g},bytesToBase64:function(h){if(typeof btoa=="function")return btoa(n.bytesToString(h));for(var g=[],i=0;i<h.length;i+=3)for(var a=h[i]<<16|h[i+1]<<8|
h[i+2],b=0;b<4;b++)i*8+b*6<=h.length*8?g.push("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/".charAt(a>>>6*(3-b)&63)):g.push("=");return g.join("")},base64ToBytes:function(h){if(typeof atob=="function")return n.stringToBytes(atob(h));for(var h=h.replace(/[^A-Z0-9+\/]/ig,""),g=[],i=0,a=0;i<h.length;a=++i%4)a!=0&&g.push(("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/".indexOf(h.charAt(i-1))&Math.pow(2,-2*a+8)-1)<<a*2|"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/".indexOf(h.charAt(i))>>>
6-a*2);return g}},m=m.charenc={};m.UTF8={stringToBytes:function(h){return n.stringToBytes(unescape(encodeURIComponent(h)))},bytesToString:function(h){return decodeURIComponent(escape(n.bytesToString(h)))}};var n=m.Binary={stringToBytes:function(h){for(var g=[],i=0;i<h.length;i++)g.push(h.charCodeAt(i)&255);return g},bytesToString:function(h){for(var g=[],i=0;i<h.length;i++)g.push(String.fromCharCode(h[i]));return g.join("")}}}();
(function(){var m=Crypto,o=m.util,n=m.charenc,h=n.UTF8,g=n.Binary,i=m.MD5=function(a,b){var h=o.wordsToBytes(i._md5(a));return b&&b.asBytes?h:b&&b.asString?g.bytesToString(h):o.bytesToHex(h)};i._md5=function(a){a.constructor==String&&(a=h.stringToBytes(a));for(var b=o.bytesToWords(a),g=a.length*8,a=1732584193,d=-271733879,e=-1732584194,c=271733878,f=0;f<b.length;f++)b[f]=(b[f]<<8|b[f]>>>24)&16711935|(b[f]<<24|b[f]>>>8)&4278255360;b[g>>>5]|=128<<g%32;b[(g+64>>>9<<4)+14]=g;for(var g=i._ff,j=i._gg,k=
i._hh,l=i._ii,f=0;f<b.length;f+=16)var m=a,n=d,p=e,q=c,a=g(a,d,e,c,b[f+0],7,-680876936),c=g(c,a,d,e,b[f+1],12,-389564586),e=g(e,c,a,d,b[f+2],17,606105819),d=g(d,e,c,a,b[f+3],22,-1044525330),a=g(a,d,e,c,b[f+4],7,-176418897),c=g(c,a,d,e,b[f+5],12,1200080426),e=g(e,c,a,d,b[f+6],17,-1473231341),d=g(d,e,c,a,b[f+7],22,-45705983),a=g(a,d,e,c,b[f+8],7,1770035416),c=g(c,a,d,e,b[f+9],12,-1958414417),e=g(e,c,a,d,b[f+10],17,-42063),d=g(d,e,c,a,b[f+11],22,-1990404162),a=g(a,d,e,c,b[f+12],7,1804603682),c=g(c,a,
d,e,b[f+13],12,-40341101),e=g(e,c,a,d,b[f+14],17,-1502002290),d=g(d,e,c,a,b[f+15],22,1236535329),a=j(a,d,e,c,b[f+1],5,-165796510),c=j(c,a,d,e,b[f+6],9,-1069501632),e=j(e,c,a,d,b[f+11],14,643717713),d=j(d,e,c,a,b[f+0],20,-373897302),a=j(a,d,e,c,b[f+5],5,-701558691),c=j(c,a,d,e,b[f+10],9,38016083),e=j(e,c,a,d,b[f+15],14,-660478335),d=j(d,e,c,a,b[f+4],20,-405537848),a=j(a,d,e,c,b[f+9],5,568446438),c=j(c,a,d,e,b[f+14],9,-1019803690),e=j(e,c,a,d,b[f+3],14,-187363961),d=j(d,e,c,a,b[f+8],20,1163531501),
a=j(a,d,e,c,b[f+13],5,-1444681467),c=j(c,a,d,e,b[f+2],9,-51403784),e=j(e,c,a,d,b[f+7],14,1735328473),d=j(d,e,c,a,b[f+12],20,-1926607734),a=k(a,d,e,c,b[f+5],4,-378558),c=k(c,a,d,e,b[f+8],11,-2022574463),e=k(e,c,a,d,b[f+11],16,1839030562),d=k(d,e,c,a,b[f+14],23,-35309556),a=k(a,d,e,c,b[f+1],4,-1530992060),c=k(c,a,d,e,b[f+4],11,1272893353),e=k(e,c,a,d,b[f+7],16,-155497632),d=k(d,e,c,a,b[f+10],23,-1094730640),a=k(a,d,e,c,b[f+13],4,681279174),c=k(c,a,d,e,b[f+0],11,-358537222),e=k(e,c,a,d,b[f+3],16,-722521979),
d=k(d,e,c,a,b[f+6],23,76029189),a=k(a,d,e,c,b[f+9],4,-640364487),c=k(c,a,d,e,b[f+12],11,-421815835),e=k(e,c,a,d,b[f+15],16,530742520),d=k(d,e,c,a,b[f+2],23,-995338651),a=l(a,d,e,c,b[f+0],6,-198630844),c=l(c,a,d,e,b[f+7],10,1126891415),e=l(e,c,a,d,b[f+14],15,-1416354905),d=l(d,e,c,a,b[f+5],21,-57434055),a=l(a,d,e,c,b[f+12],6,1700485571),c=l(c,a,d,e,b[f+3],10,-1894986606),e=l(e,c,a,d,b[f+10],15,-1051523),d=l(d,e,c,a,b[f+1],21,-2054922799),a=l(a,d,e,c,b[f+8],6,1873313359),c=l(c,a,d,e,b[f+15],10,-30611744),
e=l(e,c,a,d,b[f+6],15,-1560198380),d=l(d,e,c,a,b[f+13],21,1309151649),a=l(a,d,e,c,b[f+4],6,-145523070),c=l(c,a,d,e,b[f+11],10,-1120210379),e=l(e,c,a,d,b[f+2],15,718787259),d=l(d,e,c,a,b[f+9],21,-343485551),a=a+m>>>0,d=d+n>>>0,e=e+p>>>0,c=c+q>>>0;return o.endian([a,d,e,c])};i._ff=function(a,b,g,d,e,c,f){a=a+(b&g|~b&d)+(e>>>0)+f;return(a<<c|a>>>32-c)+b};i._gg=function(a,b,g,d,e,c,f){a=a+(b&d|g&~d)+(e>>>0)+f;return(a<<c|a>>>32-c)+b};i._hh=function(a,b,g,d,e,c,f){a=a+(b^g^d)+(e>>>0)+f;return(a<<c|a>>>
32-c)+b};i._ii=function(a,b,g,d,e,c,f){a=a+(g^(b|~d))+(e>>>0)+f;return(a<<c|a>>>32-c)+b};i._blocksize=16;i._digestsize=16})();
/* end md5 */
var key = "<?php echo md5(getenv('REMOTE_ADDR').'@'.$key); ?>";
var res = Crypto.MD5('1');
document.write(res+'<br/>');
document.write('<?php echo md5('1') ?>');
/***
 * ajax 处理
 * PS: 个人感觉ajax.onreadystatechange 调用的处理函数为非阻塞调用@@~
 * 函数可能是不定期主动调用检测数据是否准备好,
 * 所以我们不能期望在调用 ajax_updater 或 do_ajax 函数后就立即得到服务
 * 端返回的数据和检测状态, 这些应该最好是通过ajax.onreadystatechange 
 * 指定的函数来检测接收数据并同时处理.
 */
var ajax_req = false;
var ajax_id = '';
var ajax_data = '';
var ajax_flag = false;
var ajax_c = 0;

function ajax_check_fun() {
	ajax_c++;
	if ( ajax_req.readyState == 4 ) {
		var inputMsg = ajax_id;
		var content = document.getElementById(inputMsg);
		if ( ajax_req.status == 200 ) {
			ajax_data = ajax_req.responseText;
			if ( ajax_data.substr(0, 3) == "OK!" ) {
				ajax_flag = true;
				content.className = "check_true";
				content.innerHTML = ajax_data.substr(3);
			} else if ( ajax_data.substr(0, 5) == "FAIL!" ) {
				ajax_flag = false;
				content.className = "check_fail";
				content.innerHTML = ajax_data.substr(5);
			} else {
				ajax_flag = false;
				content.className = "check_fail";
				content.innerHTML = ajax_data;
			}
		} else {
			ajax_flag = false;
			content.className = "check_fail";
			content.innerHTML = "服務暫時不能使用！";
		}
		return true;
	}
}

/***
 * id: 接收数据的XML元素id
 * url: 请求数据url
 * type: 请求方法: GET/POST
 * send_data: 如: "a=b&c=d"
 * func: 处理当前ajax的javascript函数名
 */
function do_ajax(id, url, type, send_data, func) {
	var changefunc = '';

	ajax_id = id;
	ajax_req = false;
	ajax_c	= 0;
	if ( type == '' || type == null || type == "undefined" ) {
		type = "GET";
	}
	if (window.XMLHttpRequest) {
		ajax_req = new XMLHttpRequest();
	} else if (window.ActiveXObject) {
		ajax_req = new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	if (ajax_req) {
		if ( func ) {
			changefunc = "ajax_req.onreadystatechange = "+func;
			eval(changefunc);
		} else {
			ajax_req.onreadystatechange = ajax_check_fun;
		}
		ajax_req.open(type, url, true);
		ajax_req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		ajax_req.send(send_data);
	} else {
		return false;
	}
}

/* */
function doOutCall()
{
	if ( ajax_req.readyState == 4 ) {
		var inputMsg = ajax_id;
		var content = document.getElementById(inputMsg);
		if ( ajax_req.status == 200 ) {
			ajax_data = ajax_req.responseText;
			ajax_data = eval("("+ajax_data+")");
			if ( ajax_data.errno == 0 ) {
				content.innerHTML = ajax_data.errmsg + "ok~ok" + ajax_data.errno;
			} else {
				content.innerHTML = ajax_data.errmsg + "操作失败!" + ajax_data.errno;
			}
		} else {
			content.innerHTML = "Server Error";
		}
		return true;
	}
}

function outcall()
{
	var url = 'http://192.168.1.226/callcenter/app/control/index/api.class.php';
	var action = 'OutCall';
	var srcno = '808';
	var dstno = document.getElementById('called').value;
	var chksum = Crypto.MD5(action+srcno+dstno+key);
	var parms = 'action='+action+'&srcno='+srcno+'&dstno='+dstno+'&chksum='+chksum;
	do_ajax('agi_ret', url+'?'+parms, 'GET', null, doOutCall);
	//do_ajax('agi_ret', url+'?'+parms);
}
</script>

<?php
/**
 * 向 $url 以 POST 方式传递数据, 支持 ssl 和 http 方式, 支持 http 帐号验证.
 * $data 为要 POST 的数据, 为空时以 GET 方式传递 ($data['name'] = 'value')
 * $header http header 头数据.
 * 返回 $url 传回的数据, 
 * $return = "data" 以字符串方式返回, $return = "array" 以数组(行)方式返回
 */
function posttohost($url, $data = array(), $method = 'POST', $header = array(), $cookie_path = '', $return = 'data') {
	$encoded = "";
	$post = $method;
	$line = '';

	if (count($data) == 0) {
		$post	= 'GET';
	} else {
		while (list($k, $v) = each($data)) {
			$encoded .= rawurlencode($k)."=".rawurlencode($v)."&";
		}
		$encoded = substr($encoded, 0, -1);
	}

	if ( $post == 'GET' ) {
		if ( substr($url, -1) != '?' ) {
			$encoded = '?'.$encoded;
		}
		$url .= $encoded;
	}
	$url = parse_url($url);
	if (!$url) 
		return "couldn't parse url";
	if (!isset($url['port'])) { $url['port'] = ""; }
	if (!isset($url['query'])) { $url['query'] = ""; }

	$m	= '';
	if ($url['scheme'] == 'ssl' || $url['scheme'] == 'udp') 
		$m = $url['scheme']."://";
	$url['port'] = $url['port'] ? $url['port'] : 80;
	$fp = fsockopen($m.$url['host'], $url['port']);
	if (!$fp) {
		return "Failed to open socket to {$url['host']}:{$url['port']}";
	}

	$clitxt .= sprintf($post." %s%s%s HTTP/1.1\n", $url['path'], $url['query'] ? "?" : "", $url['query']);
	fputs($fp, sprintf($post." %s%s%s HTTP/1.1\n", $url['path'], $url['query'] ? "?" : "", $url['query']));
	$clitxt .= "Host: {$url['host']}\n";
	fputs($fp, "Host: {$url['host']}\n");
	if ( !empty($url['user']) ) {
		$clitxt .= "Authorization: Basic ".base64_encode($url['user'].':'.$url['pass'])."\n";
		fputs($fp, "Authorization: Basic ".base64_encode($url['user'].':'.$url['pass'])."\n");
	}
	$clitxt .= "User-Agent: Mozilla/5.0 hua@langr.org\n";
	fputs($fp, "User-Agent: Mozilla/5.0 hua@langr.org\n");
	$clitxt .= "Content-type: application/x-www-form-urlencoded\n";
	fputs($fp, "Content-type: application/x-www-form-urlencoded\n");
	if ($post == "POST") {
		$clitxt .= "Content-length: " . strlen($encoded) . "\n";
		fputs($fp, "Content-length: " . strlen($encoded) . "\n");
	}
	if (is_array($header) && count($header) > 0) {
		while (list($k, $v) = each($header)) {
			$clitxt .= "$k: $v\n";
			fputs($fp, "$k: $v\n");
		}
	} else if ( is_string($header) ) {
		$clitxt .= $header;
		fputs($fp, $header);
	}
	$cookie_file = $cookie_path.$url['host'].'.cookie';
	if ( file_exists($cookie_file) ) {
		$clitxt .= "Cookie: " . trim(file_get_contents($cookie_file)) . "\n";
		fputs($fp, "Cookie: " . trim(file_get_contents($cookie_file)) . "\n");
	}
	$clitxt .= "Connection: close\n\n";
	fputs($fp, "Connection: close\n\n");

	if ($post == "POST") {
		$clitxt .= "$encoded\n";
		fputs($fp, "$encoded\n");
	}
	//api::wlog('server.txt', "\r\n".$clitxt);

	$text = '';
	$line = fgets($fp, 2048);	/* 出错? */
	$text .= $line;
	if (!eregi("^HTTP/1\.. 200", $line)) {
		/* return 0; */
	}

	if ($return == "array") {
		$results = array();
	} else {
		$results = "";
	}
	$inheader = 1;
	$i	= 0;
	$cookie_o = array();
	$cookie_n = array();
	while(!feof($fp)) {
		$line = fgets($fp, 2048);
		$text .= $line;
		if ( substr($line, 0, 12) == 'Set-Cookie: ' ) {
			$line = substr(trim($line), 12);
			if ( file_exists($cookie_file) ) {
				$cookie_old = trim(file_get_contents($cookie_file));
				$cookie_array = explode('; ', $cookie_old);
				$cookie_new = explode('; ', $line);
				foreach ( $cookie_array as $k=>$v) {
					$eq = strpos($v, '=');
					if ( $eq === false ) continue;
					$cookie_o[substr($v, 0, $eq)] = substr($v, $eq + 1);
				}
				foreach ( $cookie_new as $k=>$v) {
					$eq = strpos($v, '=');
					if ( $eq === false ) continue;
					$cookie_n[substr($v, 0, $eq)] = substr($v, $eq + 1);
				}
				$cookie_n = array_merge($cookie_o, $cookie_n);
				$line = '';
				foreach ( $cookie_n as $k=>$v) {
					$line .= $k.'='.$v.'; ';
				}
				$line = substr($line, 0, -2);
			}
			file_put_contents($cookie_file, $line);
		}
		/* 去掉第一次的空行 */
		if ($inheader && ($line == "\n" || $line == "\r\n")) {
			$inheader = 0;
		} elseif (!$inheader) {
			if ($return == "array") {
				$results[$i] = $line;
				$i++;
			} else {
				$results .= $line;
			}
		}
	}
	fclose($fp);
	//api::wlog('server.txt', "\r\n".$text);

	return $results;
}
?>
