/**
 * @file popwindow.fun.js
 * @brief 弹屏插件js实现
 * 
 * @package popwindow
 * @author Langr <hua@langr.org> 2012/03/13 15:41
 * 
 * $Id: popwindow.tool.js 538 2012-04-20 01:48:16Z huangh $
 */

/**
 * add '?' or '&'
 * pop_url = pop_url + ['&']/['?'] + 'srcno=808&dstno=15912345678&type=in'
 */
if ( typeof pop_url == "undefined" ) {
	var pop_url = "/callcenter/index.php?module=crm&action=popWindow&";
}
/* _self, frame_name, _blank */
if ( typeof pop_target == "undefined" ) {
	var pop_target = "_blank";
}
/* pop_target show: replace, div, iframe */
if ( typeof pop_show == "undefined" ) {
	var pop_show = 'iframe';
}
if ( typeof pop_show_scroll == "undefined" ) {
	var pop_show_scroll = 'no';
}
if ( typeof pop_width == "undefined" ) {
	var pop_width = '99.8%';
}
if ( typeof pop_height == "undefined" ) {
	var pop_height = '100%';
}
if ( typeof pop_flash_title == "undefined" ) {
	var pop_flash_title = true;
}
if ( typeof pop_title == "undefined" ) {
	var pop_title = {'from':'来电:','to':'去电:','inv':'内线:'};
}
if ( typeof pop_show_title == "undefined" ) {
	var pop_show_title = true;
}
/* long connect setting: 1=realtime, n=(n-1)*4s */
if ( typeof pop_connect == "undefined" ) {
	var pop_connect = 5;
}

if ( typeof api_host == "undefined" || api_host == "" ) {
	var api_host = window.location.hostname;
}
if ( typeof api_key == "undefined" ) {
	var api_key = "";
}
var api_url = "http://" + api_host + "/callcenter/lib/api/api.class.php";
var action = "popWindow";

var srcno = '';
if ( typeof callbar_extension == "undefined" ) {
	srcno = GetCookie('callbar_extension');
} else {
	srcno = callbar_extension;
}
if ( String(srcno).length < 3 ) {
	/* ... */;
}

/* lib */
var keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
function encode64(input) 
{
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

/* get */
function QueryGET(key) 
{
	var value = '';
	/**
	 * 慎用 script.js?a=v 来传递参数
	 * var scripts = document.getElementsByTagName('script');
	 * var currentScriptUrl = scripts[scripts.length - 1].getAttribute('src');
	 */

	var urlt = window.location.href.split("?");

	if ( !urlt[1] ) {
	//if ( typeof urlt[1] == "undefined" ) {}
		return value;
	}
	var gets = urlt[1].split("&");
	for ( var i = 0; i < gets.length; i++ ) {
		if ( !gets[i] ) {
			continue;
		}
		var get = gets[i].split("=");
		if ( get[0] == key ) {
			value = get[1];
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

/***
 * ajax 处理
 * @author hua@langr.org
 */
var ajax_req = false;
var ajax_id = '';
var ajax_data = '';
var ajax_flag = false;
var ajax_fun = null;

function ajax_default_check_fun() 
{
	if ( ajax_req.readyState == 4 ) {
		if ( ajax_req.status == 200 ) {
			if ( ajax_fun ) {
				ajax_fun(ajax_req.responseText);
				return true;
			}
			ajax_data = ajax_req.responseText;
			ajax_data.substr(0, 3);
			ajax_flag = true;
		} else {
			ajax_flag = false;
		}
		return true;
	}
}

/***
 * id: 接收数据的XML元素id
 * url: 请求数据url
 * type: 请求方法: GET/POST
 * send_data: 如: "a=b&c=d"
 */
function ajax_to_div(id, url, type, send_data) 
{
	ajax_id = id;
	do_ajax(url, type, send_data, null); 
	return ;
}

/**
 * func: 处理当前ajax的javascript函数名
 */
function do_ajax(url, type, send_data, func) 
{
	var changefunc = '';
	ajax_req = false;
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
			ajax_fun = func;
			ajax_req.onreadystatechange = ajax_default_check_fun;
			//eval("ajax_req.onreadystatechange = "+func);
		} else {
			ajax_req.onreadystatechange = ajax_default_check_fun;
		}
		ajax_req.open(type, url, true);
		ajax_req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		ajax_req.send(send_data);
	} else {
		return false;
	}
}

var pop_old_title = top.document.title;
function setPopTitle(title, time)
{
	if ( pop_old_title == '' ) {
		pop_old_title = top.document.title;
	}
	title = '【'+title+'】';
	var c = time;
	flash_title = function() {
			c--;
			if ( c < 1 ) {
				top.document.title = pop_old_title;
				return ;
			}
			if ( top.document.title == '' || top.document.title == pop_old_title ) {
				top.document.title = title;
			} else {
				top.document.title = pop_old_title;
			}
			top.window.focus();
			setTimeout(flash_title, 500);
		};
	flash_title();
}

function setWinHeight(obj)
{
	var win	= obj;
	if ( win && !window.opera ) {
		if ( win.contentDocument && win.contentDocument.body.offsetHeight ) {
			win.height = win.contentDocument.body.offsetHeight;
		} else if ( win.Document && win.Document.body.scrollHeight ) {
			win.height = win.Document.body.scrollHeight;
		}
	}
}
/* end lib */

/* test */
function outcall()
{
	var url = api_url;
	var action = 'OutCall';
	//var srcno = '808';
	var dstno = '805';
	var chksum = '&chksum=' + Crypto.MD5(action+srcno+dstno+api_key);
	var parms = 'action='+action+'&srcno='+srcno+'&dstno='+dstno + chksum;
	do_ajax(url+'?'+parms, 'GET', null, doOutCall);
}

function doOutCall(d)
{
	//var content = document.getElementById("_content");
	_data = eval("("+d+")");
	if ( _data.errno == 0 ) {
		//content.innerHTML = _data.errmsg + "ok~" + _data.errno;
	} else {
		//content.innerHTML = _data.errmsg + "操作失败!" + _data.errno;
	}

	return true;
}
/* end test */

function popWin()
{
	var url = api_url;
	var chksum = '&chksum=' + Crypto.MD5(action+srcno+api_key);
	var param = "action=PopWindow&srcno="+srcno+"&type=" + pop_connect + chksum;
	do_ajax(url+'?'+param, 'GET', null, popReDone);
}

function popReDone(d)
{
	data = eval("("+d+")");
	if ( data.errno == 0 ) {
		var blank = pop_target;
		var _p_url = pop_url+'srcno='+data.ret['srcno']+'&dstno='+data.ret['dstno']+'&type='+data.ret['type'];
		var _referer = '';
		/* TODO: cookie record ... */
		if ( blank == '_self' ) {
			_referer = encode64(window.location.href);
			_p_url += '&rurl='+_referer;
			data.url = _p_url;
			if ( pop_show == 'replace' ) {
				window.location.href = _p_url;
			} else if ( pop_show == 'div' ) {
				createD(data);
			} else {
				createF(data);
			}
		} else if ( blank == '_blank' ) {
			window.open(_p_url, blank);
		} else {
			_referer = encode64(top.frames[blank].location.href);
			_p_url += '&rurl='+_referer;
			data.url = _p_url;
			if ( pop_show == 'replace' ) {
				top.frames[blank].location.href = _p_url;
			} else if ( pop_show == 'div' ) {
				createD(data);
			} else {
				createF(data);
			}
		}
		/* 标题闪烁 */
		if ( pop_flash_title ) {
			var title = (pop_title[data.ret['type']] ? pop_title[data.ret['type']] : (data.ret['type'] + ': ')) + data.ret['dstno'];
			setPopTitle(title, 30);
		}
		setTimeout("popWin();", 20000);
	} else {
		setTimeout("popWin();", 4000);
	}
	
	return ;
}

/* ajax */
function createD(d)
{
	var div_bar = '_pop_tool_div_body';
	//var Fobj = top.frames[pop_target].contentWindow;
	var Fobj = null;
	if ( pop_target == '_self' ) {
		Fobj = self;
	} else {
		Fobj = top.frames[pop_target];
	}

	if ( !Fobj.document.getElementById(div_bar) ) {
		var p = document.createElement('div');
		p.setAttribute('id', div_bar);
		p.setAttribute('style', 'top:0px;left:0px;width:'+pop_width+';height:'+pop_height+';border:1px solid #006699;background:#ffffff;position:absolute;z-index:998;');
		Fobj.document.body.appendChild(p);
		Fobj.document.getElementById(div_bar).innerHTML = "<div id='_pop_tool_div_bar' style='top:5px;left:0px;width:100%;height:20px;position:absolute;text-align:center;padding:1px;z-index:999;'></div>"
			+"<div id='_pop_tool_div_page' style='top:0px;left:0px;width:100%;height:100%;position:absolute;overflow-y:auto;'></div>";
	} else {
		Fobj.document.getElementById(div_bar).style.display = '';
	}
	if ( pop_show_title ) {
		var title = (pop_title[d.ret['type']]?(pop_title[d.ret['type']]+'&nbsp;'+d.ret['dstno']):'');
		Fobj.document.getElementById('_pop_tool_div_bar').innerHTML = "<span style='border:1px solid #ff0000;color:red;font-size:18px;'>"+title+"&nbsp;<span><a href='"+d.url+"' target='_blank'>新窗口</a>&nbsp;<a href='javascript:;' title='关闭' onclick=\"document.getElementById('"+div_bar+"').style.display='none';\"><img src='/callcenter/images/close.gif' style='border:0px' alt='close'/></a></span></span>";
	}
	do_ajax(d.url, 'GET', null, function(data){Fobj.document.getElementById('_pop_tool_div_page').innerHTML = data;});
}

/* iframe src */
function createF(d)
{
	var div_bar = '_pop_tool_div_body';
	var Fobj = null;
	if ( pop_target == '_self' ) {
		Fobj = self;
	} else {
		Fobj = top.frames[pop_target];
	}

	if ( !Fobj.document.getElementById(div_bar) ) {
		var p = document.createElement('div');
		p.setAttribute('id', div_bar);
		p.setAttribute('style', 'top:0px;left:0px;width:'+pop_width+';height:'+pop_height+';border:1px solid #006699;background:#ffffff;position:absolute;z-index:998;');
		Fobj.document.body.appendChild(p);
		Fobj.document.getElementById(div_bar).innerHTML = "<div id='_pop_tool_div_bar' style='top:5px;left:0px;width:100%;height:20px;position:absolute;text-align:center;padding:1px;z-index:999;'></div>"
			+"<iframe id='_pop_tool_div_page' style='top:0px;left:0px;width:100%;height:100%;position:absolute;' src='"+d.url+"' frameborder='0' scrolling='"+pop_show_scroll+"' allowtransparency='false' onload=\"function setWinHeight(obj){var win=obj;if(win&&!window.opera){if(win.contentDocument&&win.contentDocument.body.offsetHeight){win.height=win.contentDocument.body.offsetHeight;}else if(win.Document&&win.Document.body.scrollHeight){win.height=win.Document.body.scrollHeight;}}};\"></iframe>";
	} else {
		Fobj.document.getElementById(div_bar).style.display = '';
		Fobj.document.getElementById('_pop_tool_div_page').setAttribute('src', d.url);
	}
	if ( pop_show_title ) {
		var title = (pop_title[d.ret['type']]?(pop_title[d.ret['type']]+'&nbsp;'+d.ret['dstno']):'');
		Fobj.document.getElementById('_pop_tool_div_bar').innerHTML = "<span style='border:1px solid #ff0000;color:red;font-size:18px;'>"+title+"&nbsp;<span><a href='"+d.url+"' target='_blank'>新窗口</a>&nbsp;<a href='javascript:;' title='关闭' onclick=\"document.getElementById('"+div_bar+"').style.display='none';\"><img src='/callcenter/images/close.gif' style='border:0px' alt='close'/></a></span></span>";
	}
}

//document.ready(popWin());
document.onload = popWin();
