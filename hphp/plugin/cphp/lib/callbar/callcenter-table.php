<?php
/**
 * 话务导航条, 可作为话务 api 接口的调用示例.
 * 请使用Cookie存储相应的用户和分机信息.
 * 登陆信息:
 * 	Cookie['extension'] 登陆者的分机号(或工号).
 * 	Cookie['name'] 登陆者的名称.
 * 	!Cookie['login_exten'] 被登陆的分机, 用于在动态登陆时.
 * 	X!Cookie['key'] 当前用户的校验key: md5(CLIENT_IP+'@'+api_key);
 * 导航条自己设置的:
 * 	Cookie['agent'] 示忙标志, 1 已经示忙, 0 未示忙.
 * 	Cookie['keep'] 通话保持标志, 1 已经保持, 0 未保持.
 * 	!!!Cookie['chann'] 被保持通话的对方通道, 用于通话恢复.
 *
 * @author Langr <hua@langr.org> 2012/01/12 12:05
 * $Id: callcenter-table.php 537 2012-04-20 01:45:25Z huangh $
 */
session_start();
$api_key = 'DeL>rty<:JKO#:k+_p';
$api_url = 'http://192.168.1.226/callcenter/app/control/index/api.class.php';
$pop_url = '/ecshop/admin/order.php?act=pop';//'srcno=&dstno=&type=';
$index_page = '../index.php?act=main';
$target_frame = 'main-frame';
//$target_frame = '_blank';
$key = !empty($_GET['key']) ? $_GET['key'] : md5(getenv('REMOTE_ADDR').'@'.$api_key);
if ( empty($_COOKIE['key']) ) {
	setcookie('key', $key, 0, '/');
}

/* $pop_url '?&' */
$_end = substr($pop_url, -1);
if ( $_end != '?' && $_end != '&' ) {
	if ( strpos($pop_url, '?') !== false ) {
		$pop_url .= '&';
	} else {
		$pop_url .= '?';
	}
}

/* popwindow */
if ( isset($_GET['action']) && $_GET['action'] == 'ajaxPop' ) {
	//popwindow;
?>
<script type="text/javascript" src="jquery.min.js"></script>
<script language="javascript" type="text/javascript">
var keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
function encode64(input) {
	//input = escape(input);
	var output = "";
	var chr1, chr2, chr3 = "";
	var enc1, enc2, enc3, enc4 = "";
	var i = 0;

	do {
		chr1 = input.charCodeAt(i++);
		chr2 = input.charCodeAt(i++);
		chr3 = input.charCodeAt(i++);

		enc1 = chr1 >> 2;
		enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
		enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
		enc4 = chr3 & 63;

		if (isNaN(chr2)) {
			enc3 = enc4 = 64;
		} else if (isNaN(chr3)) {
			enc4 = 64;
		}

		output = output +
		keyStr.charAt(enc1) +
		keyStr.charAt(enc2) +
		keyStr.charAt(enc3) +
		keyStr.charAt(enc4);
		chr1 = chr2 = chr3 = "";
		enc1 = enc2 = enc3 = enc4 = "";
	} while (i < input.length);

	return output;
}
function popWin()
{
	var url = "<?php echo $api_url;?>";
	var param = "action=PopWindow&srcno=<?php echo $_COOKIE['extension'];?>&type=3";
	var myAjax = $.ajax({
			type: 'GET',
			url: url,
			data: param,
			success : popReDone,
			dataType : 'json'
			});
}
function popReDone(data)
{
	if (data.errno == 0) {
		//alert(data.errmsg+data.ret['type']+data.ret['dstno']);
		var blank = '<?php echo $target_frame;?>';
		if ( blank != '_blank' ) {
			var _referer = encode64(top.frames['<?php echo $target_frame;?>'].location.href);
			top.frames[blank].location.href = '<?php echo $pop_url;?>srcno='+data.ret['srcno']+'&dstno='+data.ret['dstno']+'&type='+data.ret['type']+'&rurl='+_referer;
		} else {
			window.open('<?php echo $pop_url;?>srcno='+data.ret['srcno']+'&dstno='+data.ret['dstno']+'&type='+data.ret['type'], blank);
		}
		setTimeout("popWin();", 20000);
	} else {
		setTimeout("popWin();", 4000);
	}
	
	return ;
}
popWin();
</script>
<?php
	exit;
}

/* 第一次登陆时, 取分机状态(SetCookie('agent')), 检测并处理动态登陆分机. */
if ( !isset($_COOKIE['agent']) ) {
	$api_param = "?action=GetDB&type=DND&srcno=".$_COOKIE['extension'];
	$ret = json_decode(file_get_contents($api_url.$api_param));
	$ret = (array) $ret;
	setcookie('agent', $ret['ret'], 0, '/');
	echo "<!--('".print_r($ret,true)."')-->";

	/* 动态登陆, 设置 asterisk DB */
	/*$api_param = "?action=GetDB&type=&srcno=".$_COOKIE['extension'];
	$ret = json_decode(file_get_contents($api_url.$api_param));
	$ret = (array) $ret;*/
	if ( !empty($_COOKIE['login_exten']) && strlen($_COOKIE['login_exten']) > 2 ) {
		if ( $ret['errno'] == 0 ) {
			$api_param = "?action=SetDB&type=&srcno=".$_COOKIE['extension']."&dstno=".$_COOKIE['login_exten'];
			$ret = file_get_contents($api_url.$api_param);
		}
	} else {
		if ( $ret['errno'] == 0 ) {
		}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Language" content="zh-cn">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Wisetalk.cn CallCenter TooBar</title>
<script type="text/javascript" src="jquery.min.js"></script>
<SCRIPT language=JavaScript type=text/JavaScript>
//<!--
var api_url = '<?php echo $api_url;?>';
var pop_url = '<?php echo $pop_url;?>';//'srcno=&dstno=';
var key_ck = GetCookie('key');
var key = key_ck ? key_ck : "<?php echo $key; ?>";
var srcno = GetCookie('extension');

/* */
get_key = QueryGET('key');
if ( key.length < 20 ) {
	key = get_key;
}
get_extension = QueryGET('extension');
if ( String(srcno).length < 3 ) {
	srcno = get_extension;
}
SetCookie('key', key, null, '/');
SetCookie('extension', srcno, null, '/');

function extension_name()
{
	if ( srcno == null || srcno.length < 2 ) {
		$('#user_status').html('未登陆');
	} else {
		var dynamic_exten = GetCookie('login_exten') ? '('+GetCookie('login_exten')+')' : '';
		$('#extension_name').html("姓名: "+GetCookie('name')+"&nbsp;分机: "+srcno+dynamic_exten);
		$('#user_status').html(GetCookie('agent') ? '示忙' : '在线');
	}
}

/* get */
function QueryGET(key) {
	var urlt = window.location.href.split("?");
	var gets = urlt[1].split("&");
	for (var i=0;i<gets.length;i++) {
		var get = gets[i].split("=");
		if (get[0] == key) {
			var value = get[1];
			break;
		}
	}
	return value;
}

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

/* 获得Cookie解码后的值 */
function GetCookieVal(offset)
{
	var endstr = document.cookie.indexOf (";", offset);
	if (endstr == -1)
		endstr = document.cookie.length;
	return unescape(document.cookie.substring(offset, endstr));
}

/* 设定Cookie值 
 * SetCookie(name, value[, expires[, path[, domain[, secure]]]])
 */
function SetCookie(name, value)
{
	var expdate = new Date();
	var argv = SetCookie.arguments;
	var argc = SetCookie.arguments.length;
	var expires = (argc > 2) ? argv[2] : null;
	var path = (argc > 3) ? argv[3] : null;
	var domain = (argc > 4) ? argv[4] : null;
	var secure = (argc > 5) ? argv[5] : false;
	if(expires!=null) 
		expdate.setTime(expdate.getTime() + ( expires * 1000 ));

	document.cookie = name + "=" + escape (value) +((expires == null) ? "" : ("; expires="+ expdate.toGMTString()))
			+((path == null) ? "" : ("; path=" + path)) +((domain == null) ? "" : ("; domain=" + domain))
			+((secure == true) ? "; secure" : "");
}

/* 获得Cookie的原始值 */
function GetCookie(name)
{
	var arg = name + "=";
	var alen = arg.length;
	var clen = document.cookie.length;
	var i = 0;

	while (i < clen) {
		var j = i + alen;
		if (document.cookie.substring(i, j) == arg)
			return GetCookieVal(j);
		i = document.cookie.indexOf(" ", i) + 1;
		if (i == 0) 
			break;
	}

	return null;
}

/* 删除Cookie */
function DelCookie(name)
{
	var exp = new Date();
	exp.setTime (exp.getTime() - 1);
	var cval = GetCookie(name);
	document.cookie = name + "=" + cval + "; expires="+ exp.toGMTString();
}

/* */
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->
</SCRIPT>

<style>
#body_div {margin: 0; padding: 0;}
/*#top_table td{background:url(images/top/top_bg_1.gif) repeat-x;}*/
.help_msg {
	border: 1px solid red;
	position:relative;
	overflow: hidden;
	/*width:1px;
	height:1px;*/
	top:13px;
}
.msg_init {
	color:#949494;
	border: 0px;
	position: relative;
	overflow: hidden;
	width: 90%;
	height: 0px;
	top: 13px;
}
.onblur {
	background:#FEF9E1;
	border:1px solid #DDDDDD;
	color:#666666;
}
.onfocus {
	background:#E2F5FF;
	border:1px solid #00A8FF;
	color:#333333;
}
.check_true {
	background:#e6ffe6 url("images/top/check_true.png") no-repeat scroll 3px 50%;
	border:1px solid #009100;
	color:#009100;
	padding: 0px 0px 0px 18px;
}
.check_fail {
	background:#fff2e9 url("images/top/check_fail.png") no-repeat scroll 3px 50%;
	border:1px solid #ff0000;
	color:#ff0000;
	padding: 0px 0px 0px 18px;
}
.ontrans {
	background:#DDF9E1;
	border:1px solid #88DDDD;
	color:#6666DD;
}
.onthree {
	background:#AAF9E1;
	border:1px solid #DDDDDD;
	color:#226666;
}
</style>
</head>
<body style="border:none;margin:0px;">
<table width="100%" id="top_table" border=0 cellpadding=0 cellspacing=0 bgcolor="#E1ECFA" style="float: right;">
	<tr>
	  <td width="149">
	  <a href='<?php echo $index_page;?>' target='<?php echo $target_frame;?>'><img src="images/top/top_s_02.gif" height="30" alt="" border="0" ></a></td>
	<td width="18">&nbsp;</td>
	<td width="65" nowrap><a href='<?php echo $index_page;?>' target='<?php echo $target_frame;?>'><img src="images/top/gohome.gif" alt="" border="0" ></a></td>
	<td width="18">&nbsp;</td>

		<td width="65" nowrap><a href="javascript:setAgent();" style="text-decoration: none"><img
      src="images/top/btn_busy.gif" id="imgbusy" border=0 name=imgbusy  width=74 height=34 ></a></td>
		<td width="18">&nbsp;</td>
		<td width="65"><a target="_self" href="javascript:setKeep();" ><img src="images/top/btn_bc.gif" name=imgkeep id=imgkeep  border=0 ></a></td>
		<td width="18">&nbsp;</td>
		<td width="65"><a onMouseOver="MM_swapImage('imgzj','','images/top/btn_zj.gif',1)" onMouseOut="MM_swapImage('imgzj','','images/top/btn_zj.gif',1)"  target="_self" href="javascript:feedback();" ><img src="images/top/btn_zj.gif" name=imgzj id=imgzj  border=0 ></a></td>
		<td width="18">&nbsp;</td>
		<td width="65">
			<a id="trans_id" href="javascript:;" title="转移"><img src="images/top/btn_transfer.gif" alt="转移" width=74 height=34 border="0"></a>
		</td>

		<td width="18">&nbsp;</td>
		<td width="65">
			<a id="three_id" href="javascript:;" title="三方通话"><img src="images/top/help04.gif" alt="三方通话" width="74" height="34" border="0"/></a>
		</td>
		<td width="18">&nbsp;</td>
		<td width="12%">
			<div style="float: left;"><span id="dstinfo" wtalk="OutCall" lock="off">拔:</span><input id="dstno" name="dstno" class="input" type="text" size="11" height="34" border="0"/></div><div name="cc" id="cc" style="float:left;margin-left:2px;cursor: pointer;border: 1px solid #80bdcb;" onClick="clickCall();return false;">&nbsp;<img src="images/top/dial.gif" border=0 alt="快速呼叫">&nbsp;</div>
		</td>
		<!--td width="18">&nbsp;</td>
			<td width="65">
			<a href="index.php?module=callLog&action=cdrList&menu_id=90&cate_id=24" target="<?php echo $target_frame;?>"><img src="images/top/btn_cdr.gif" alt="" width=74 height=34 border="0"></a></td-->
		<!--td width="18"><img src="images/top/space.gif" alt="" border="0" ></td>
		<td width="61">
			<a href="index.php?module=privateset&amp;action=searchVoiceMail&amp;kkk=search" target="<?php echo $target_frame;?>"><img src="images/top/top_s_15.gif" alt="" width=81 height=32 border="0"></a></td>
		<td width="5">&nbsp;</td>
		<td width="61" style="display: none">
		<a href="index.php?module=callCortrol&amp;action=meetLogin" target="<?php echo $target_frame;?>"><img src="images/top/top_s_17.gif" alt="" width=81 height=32 border="0"></a></td-->
		<!--td width="18"><img src="images/top/space.gif" alt="" border="0" ></td>
		<td width="65">
			<a href="sms.php?module=index&action=index" target="_blank">
		    <img src="images/top/top_s_19.gif" alt="" width=74 height=34 border="0"></a></td-->
		<!--td width="50">
			<a target="<?php echo $target_frame;?>" href="./faq/faq.php" title="帮助"><img src="images/top/help04.gif" alt="帮助" width="30" height="29" border="0"/></a>
		</td-->
		<td width="25%">
		<div id="help_msg"></div>
	  </td>
	   <td width="15%" align="left" style="text-align:center;font-size:10pt;line-height:20px;color:#0000ff" nowrap><div>
	    <div id="extension_name" style="float:left">姓名：admin&nbsp;分机：604&nbsp;</div><div id="user_stauts" style="color:red;">(&nbsp;在线&nbsp;)</div></div>
	  </td>
		<!--<td width="7">&nbsp;</td>
		<td width="50">
			<a target="_parent" href="index.php?module=user&action=Logout" title="注销"><img src="images/top/logout05.gif" alt="注销" width="30" height="30" border="0"/></a>
		</td>-->
	</tr>
</table>

<script>
$("#trans_id").click(tarnsfer);
$("#three_id").click(threeCall);
$("#dstno").keydown(function(e){if (e.keyCode == '13'){clickCall();}});
//$(document).keydown(function(ev){var e = window.event ? window.event : ev; var k = e.keyCode ? e.keyCode : e.which; alert(e.keyCode+':w:'+e.which+':c:'+e.charCode+':sc:'+String.fromCharCode(k))});

/* popwindow */
function popWindow()
{
	;
}
/* 通话转移 */
function tarnsfer() {
	showClickOut('CallTransfer', '转', '请在左边输入转移电话号码.');
}
/* 三方通话 */
function threeCall()
{
	showClickOut('MultiCall', '三', '请在左边输入第三方号码.');
}
function showClickOut(act, name, help) 
{
	$("#dstinfo").attr({wtalk: act});
	$("#dstinfo").html(name+':');
	if ( help != '' ) {
		_help_msg(help, -1);
	}
	return ;
}
/* 点击拔号 */
function clickCall()
{
	var act = $("#dstinfo").attr('wtalk');
	var dstno = $("#dstno").val();
	if ( dstno.length < 3 ) {
		_help_msg('输入号码非法, 请重新输入.', 1);
		$('#dstno').focus();
		return ;
	}
		
	var url = api_url;
	var param = "action="+act+"&srcno="+srcno+"&dstno="+dstno+"&type=" + GetCookie('agent');
	var myAjax = $.ajax({
			type: 'GET',
			url: url,
			data: param,
			success : clickDone,
			dataType: 'json'
			});
}
/* 示忙/闲 */
function setAgent()
{
	var url = api_url;
	var param = "action=AgentBusy&srcno="+srcno+"&type=" + GetCookie('agent');
	var myAjax = $.ajax({
			type: 'GET',
			url: url,
			data: param,
			success : agentDone,
			dataType: 'json'
			});
}
/* 保持/取消保持 */
function setKeep()
{
	//outcall();
	var url = api_url;
	var param = "action=CallKeep&srcno="+srcno+"&dstno=&type=" + GetCookie('keep');
	//var myAjax = do_ajax('agi_ret', url+'?'+param, 'GET', null, busydone);
	var myAjax = $.ajax({
			type: 'GET',
			url: url,
			data: param,
			success : keepDone,
			dataType : 'json'
			});
}
/* 质检(反馈), 评分 */
function feedback() {
	var url = api_url;
	var param = "action=CallFeedback&srcno="+srcno+"&dstno=";
	var myAjax = $.ajax({
			type: 'GET',
			url: url,
			data: param,
			success : feedbackDone,
			dataType : 'json'
			});
 //document.getElementById(divname).style.display = "";
}

/* 分机退出 */
function extenExit()
{
	var url = api_url;
	var pars = "action=Logout&srcno="+srcno;
	var myAjax = $.ajax({
			type: 'GET',
			url: url,
			data: param,
			success : pubDone(),
			dataType: 'json'
			});
}
/* */
function _help_msg(msg, flag)
{
	$("#help_msg").attr('class', 'msg_init');
	$("#help_msg").css('display', '');
	$("#help_msg").css('opacity', '1');
	if ( flag > 0 ) {
		/* error */
		$("#help_msg").addClass('check_fail');
	} else if ( flag < 0 ) {
		/* warnning */
		$("#help_msg").addClass('onfocus');
	} else {
		/* ok */
		$("#help_msg").addClass('check_true');
	}
	$("#help_msg").html(msg);
	$("#help_msg").animate({"top": "0px","height": "18px"}, 1000).animate({"top": "0px","height": "18px"}, 3000).animate({"opacity": "0.1","top": "13px","height": "0px"},1000, _help_msg_donoe);
}
function _help_msg_donoe()
{
	$("#help_msg").addClass('msg_init');
	$("#help_msg").css('display', 'none');
}
/* 重新加载(刷新)时的状态显示 */
function reloading()
{
	if (GetCookie('agent') == 1) {
		$('#imgbusy').attr({src: "images/top/btn_idle.gif"});
		$('#user_stauts').html('示忙');
	} else {
		$('#imgbusy').attr({src: "images/top/btn_busy.gif"});
		$('#user_stauts').html('在线');
	}

	if (GetCookie('keep') == 1) {
		$('#imgkeep').attr({src: "images/top/btn_hf.gif"});
	} else {
		$('#imgkeep').attr({src: "images/top/btn_bc.gif"});
	}
}
/* */
function pubDone(data)
{
	if (data.errno != 0) {
		_help_msg("错误:"+data.errmsg, data.errno);
	} else {
		_help_msg(data.errmsg, data.errno);
	}
	return ;
}
/* 示忙完成后图标处理 */
function agentDone(data)
{
	if (GetCookie('agent') != 1) {
		SetCookie('agent', 1, null, '/');
		$('#imgbusy').attr({src: "images/top/btn_idle.gif"});
		$('#user_stauts').html('示忙');
		_help_msg("示忙返回,"+data.errmsg, data.errno);
	} else {
		SetCookie('agent', 0, null, '/');
		$('#imgbusy').attr({src: "images/top/btn_busy.gif"});
		$('#user_stauts').html('在线');
		_help_msg("示忙取消,"+data.errmsg, data.errno);
	}
}
/* 通话保持后图标处理 */
function keepDone(data)
{
	_help_msg(data.errmsg, data.errno);
	if (data.errno != 0) {
		return ;
	}

	if (GetCookie('keep') != 1) {
		SetCookie('keep', 1, null, '/');
		$('#imgkeep').attr({src: "images/top/btn_hf.gif"});
	} else {
		SetCookie('keep', 0, null, '/');
		$('#imgkeep').attr({src: "images/top/btn_bc.gif"});
	}
}
/* 通话质检, 打分 */
function feedbackDone(data)
{
	if (data.errno != 0) {
		_help_msg(data.errmsg, data.errno);
	} else {
		_help_msg("质检已经发出,"+data.errmsg, data.errno);
	}
}
/* 拔, 转, 三 */
function clickDone(data)
{
	_help_msg(data.errmsg, data.errno);

	$("#dstinfo").attr({wtalk: 'OutCall'});
	$("#dstinfo").html('拔:');
	showClickOut('OutCall', '拔', '');
}
reloading();
extension_name();

function reportError(request)
{
	////alert('ajaxError!');
}
function imgswith(img1,img2,imgid,svalue){
	if($(svalue) == 0){
		MM_swapImage(imgid,'',img1,1);
	}else{
		MM_swapImage(imgid,'',img2,1);
	}
}
function imgswith1(img1,img2,imgid,svalue){
	if($(svalue) == 0){
		MM_swapImage(imgid,'',img2,1);
	}else{
		MM_swapImage(imgid,'',img1,1);
	}
}
function showNewCall(extenstat) {
	var newcall = prompt("请输入需要转接的分机号","");
	while(1)
	{
		if( newcall != "" && newcall!= 'null'){
			var url = api_url;
			var param = "action=callTransfer&srcno=&type=&stat=" + extenstat+"&exten=820&targetexten="+newcall;
			var myAjax = $.ajax({type: 'GET',
				url: url,
				data: param,
				success : busydone,
				dataType : 'html'
				});
			break;
		}
		newcall = prompt("分机号不能为空！\n请输入需要转移的目的分机号","");
		continue;
	}
}
/*
window.onunload=ccc;
function ccc()
{
	extenExit();
}
*/

function outcall()
{
	var url = 'http://192.168.1.226/callcenter/app/control/index/api.class.php';
	var action = 'OutCall';
	var srcno = '808';
	//var dstno = document.getElementById('called').value;
	var dstno = '805';
	var chksum = Crypto.MD5(action+srcno+dstno+key);
	var parms = 'action='+action+'&srcno='+srcno+'&dstno='+dstno+'&chksum='+chksum;
	do_ajax('agi_ret', url+'?'+parms, 'GET', null, doOutCall);
	//do_ajax('agi_ret', url+'?'+parms);
}
</script>
<iframe src="?action=ajaxPop" frameborder="no" scrolling="no" width="0" height="0"></iframe>
</body>
</html>
