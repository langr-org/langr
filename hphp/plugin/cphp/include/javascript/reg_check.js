/* 提示信息 */
var _err_msg = new Array();
_err_msg['user'] = "由英文字母、數字、下劃線、連字元號組成，長度為6-12個字元";
_err_msg['pwd'] = "密碼長度為6-12位數，字母請區分大小寫，建議請勿與其他遊戲或網站相同";
_err_msg['check_pwd'] = "請再輸入一次上面填寫的密碼。";
_err_msg['nike_name'] = "請輸入 2 至 12 個字元的英文字母或數字，中文最多 6 個字。";
_err_msg['name'] = "姓名只能是中文，長度為2 - 4 個字元。";
_err_msg['sex'] = "請選擇你是帥哥還是美女喔";
_err_msg['birthday'] = "請填寫你出生年月日";
_err_msg['constellation'] = "請填寫你的星座";
_err_msg['e_mail'] = "請填寫你的電子郵箱";
_err_msg['tel'] = "請填寫你的行動電話";
_err_msg['area'] = "請選擇你的身分，海外人士註冊贈送 1000 點。";
_err_msg['id_card'] = "請填寫你的身分證字號，用於取回密碼、儲值等重要事項。";
_err_msg['addr'] = "請填寫真實的詳細地址";
_err_msg['verifyCode'] = "請輸入你看到的驗證碼。";
_err_msg['RoleIcon'] = "請選擇你的遊戲角色。";

var idpre = "_err_";
function gel(a) {
	return document.getElementById?document.getElementById(a):null;
}

function gelstn(a) {
	return document.getElementsByTagName?document.getElementsByTagName(a):new Array();
}

function geln(a) {
	return document.getElementsByName?document.getElementsByName(a):new Array();
}

function w(msg) {
	document.write(msg);
}

function setfocus(a, value) {
	var _id = gel(idpre + a);
	_id.className = "onfocus";
	if ( value == '' || value == null  || value == "undefined") {
		_id.innerHTML = _err_msg[a];
	} else {
		_id.innerHTML = value;
	} 
}

function setblur(a) {
	gel(idpre + a).className = "onblur";
}

function settrue(a, message) {
	var _id = gel(idpre + a);
	_id.className = "check_true";
	_id.innerHTML = message;
}

function setfail(a, message) {
	var _id = gel(idpre + a);
	_id.className = "check_fail";
	_id.innerHTML = message;
}

/***
 * 检测 sV 里的每一个字符是否都是在 sR 中
 * return: true - 全部都由sR中的字符组成
 *	   false - 不全部由sR中的字符组成
 */
function fIsNumber(sV, sR) {
	var sTmp;
	if ( sV.length == 0 ) { 
		return false;
	}
	for ( var i=0; i < sV.length; i++ ) {
		sTmp= sV.substring(i, i+1);
		if ( sR.indexOf(sTmp, 0) == -1 ) {
			return false;
		}
	}
	return true;
}

/***
 * 检测 sV 里的每一个字符是否有一个在 sR 中
 * return: true - 没有一个字符在sR中
 *	   false - 有一个或多个字符在sR中
 */
function noInChars(sV, sR) {
	var sTmp;
	if ( sV.length == 0 ) { 
		return true;
	}
	for ( var i=0; i < sV.length; i++ ) {
		sTmp= sV.substring(i, i+1);
		if ( sR.indexOf(sTmp, 0) != -1 ) {
			return false;
		}
	}

	return true;
}

function isChinese(a) {
	for( var i=0; i < a.length; i++ ) {
		if ( a.charCodeAt(i) < 0xA0 )  
			return false;
	}
	return true;
}

function ajaxtodiv(id, url)
{
	var divinner = new Ajax.Updater(id, url, {asynchronous:true, evalScripts:true, requestHeaders:['X-Update', id]});
}

function get_user(u)
{
	ajaxtodiv('user', '?module=twmj&action=getuser&id='+u);
}

function check_user(id)
{
	var msgid = idpre + id;
	var _id = gel(msgid);
	var user = gel(id).value;
	var url = '?module=check&action=regRepeat&user=' + encodeURIComponent(user);

	setblur(id);
	if ( user == '' || user == null ) {
		setfail(id, "請填寫帳號！");
		return false;
	}
	if ( isChinese(user) ) {
		setfail(id, "帳號不能填寫中文！");
		return false;
	}
	if ( user.length < 4 || user.length > 12 ) {
		setfail(id, "請輸入4至12個字元的英文字母或數字");
		return false;
	}
	if ( fIsNumber(user, "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_") != true ){
		setfail(id, "帳號應該由英文字母或數字組成，不允許出現漢字、空格、點等其他字元，請重新填寫帳號！");
		return false;
	}

	//ajaxtodiv();
	setfocus(id, "正在檢測中，請稍候...");
	ajax_updater(msgid, url);
}

function check_nike_name(id)
{
	var msgid = idpre + id;
	var _id = gel(msgid);
	var v = gel(id).value;
	var url = '?module=check&action=regRepeat&nike_name=' + encodeURIComponent(v);

	setblur(id);
	if ( v == '' || v == null ) {
		setfail(id, "請填寫暱稱！");
		return false;
	}
	if ( v.length < 2 || v.length > 12 ) {
		setfail(id, "請輸入 2 至 12 個字元的英文字母或數字，中文最多 6 個字。");
		return false;
	}

	setfocus(id, "正在檢測中，請稍候...");
	ajax_updater(msgid, url);
//	if ( ajax_isok() ) {
//		_id.className = 'check_true';
//		settrue(id, "暱稱檢測成功，可以使用。");
//		return true;
//	} else {
//		_id.className = 'check_fail';
//		return false;
//	}
}

function check_name(id) {
	var msgid = idpre + id;
	var _id = gel(msgid);
	var v = gel(id).value;

	setblur(id);
	if( v == '' || v == null ) {
		setfail(id, "請填寫你的真實姓名！");
		return false;
	}

	if ( !isChinese(v) ) {
		setfail(id, "姓名只能填寫中文！");
		return false;
	}

	if ( v.length < 2 || v.length > 5 ) {
		setfail(id, "姓名不合法，檢測未通過！");
		return false;
	}

	settrue(id,"姓名檢測通過。");
	return true;
}

function check_email(id)
{
	var msgid = idpre + id;
	var _id = gel(msgid);
	var v = gel(id).value;
	var url = '?module=check&action=regRepeat&email=' + v;
	var email_match = "/^(?:^([a-z0-9][a-z0-9_\\-\\.\\+]*)@([a-z0-9][a-z0-9\\.\\-]{0,63}\\.(com|org|net|biz|info|name|net|pro|aero|coop|museum|[a-z]{2,4}))$)$/";

	setblur(id);
	if ( v == '' || v == null ) {
		setfail(id, "請填寫email地址！");
		return false;
	}

	if ( !regexp_match(email_match, v) ) {
		setfail(id, "email地址非法！");
		return false;
	}

	setfocus(id, "正在檢測中，請稍候...");
	ajax_updater(msgid, url);

	return true;
}

function check_tel(id)
{
	var msgid = idpre + id;
	var _id = gel(msgid);
	var v = gel(id).value;
	var url = '?module=check&action=regRepeat&tel=' + v;
	var tel_match = "/^09[0-9]{8}$/";

	setblur(id);
	if ( v == '' || v == null ) {
		setfail(id, "請填寫你的行動電話！");
		return false;
	}

	if ( !regexp_match(tel_match, v) ) {
		setfail(id, "行動電話非法！");
		return false;
	}

	setfocus(id, "正在檢測中，請稍候...");
	ajax_updater(msgid, url);

	return true;
}

function check_area(id, area)
{
	var msgid = idpre + id;
	var _id = gel(msgid);
	var v = 'T';
	
	for ( i=0; i < area.length; i++ ) {
		if ( area[i].checked == true) {
			v = area[i].value;
			break;
		}
	}

	if ( v == 'T' ) {
		_id.innerHTML = _err_msg[id];
	} else {
		_id.innerHTML = "請填寫你的護照編號！";
	}

	return true;
}

function check_idcard(id, type)
{
	var msgid = idpre + id;
	var _id = gel(msgid);
	var v = gel(id).value;
	var t = 'T';
	var url = '?module=check&action=regRepeat&id_card=' + v;

	setblur(id);
	for ( i=0; i < type.length; i++ ) {
		if ( type[i].checked == true) {
			t = type[i].value;
			break;
		}
	}
	if ( t != 'T' ) {
		if ( v == '' || v == null ) {
			setfail(id, "請填寫你的護照編號！");
			return false;
		}
		if ( v.length < 4 || v.length > 20 ) {
			setfail(id, "護照編號非法！");
			return false;
		}
		settrue(id, "護照編號通過檢測！");
		return true;
	}

	if ( v == '' || v == null ) {
		setfail(id, "請填寫你的身份證！");
		return false;
	}

	if ( !isTWIdCard(v) ) {
		setfail(id, "身分證字號格式不正確！");
		return false;
	}

	setfocus(id, "正在檢測中，請稍候...");
	ajax_updater(msgid, url);

	return true;
}

function regexp_match(reg, str) {
	reg = reg.replace(/^\/*/, "").replace(/\/*$/, "");
	var g = new RegExp(reg, "i");
	return g.test(str);
}

function isTWIdCard(val) 
{
	var id_card_match = "/^[a-zA-Z][12][0-9]{8}$/";
	if ( !regexp_match(id_card_match, val) ) {
		return false;
	}
	
	var tmp = val.toUpperCase();
	var a = 0;
	
	switch ( tmp.charAt(0) ) {
		case 'A':
			a = 10;break;
		case 'B':
			a = 11;break;
		case 'C':
			a = 12;break;
		case 'D':
			a = 13;break;
		case 'E':
			a = 14;break;
		case 'F':
			a = 15;break;
		case 'G':
			a = 16;break;
		case 'H':
			a = 17;break;
		case 'I':
			a = 34;break;
		case 'J':
			a = 18;break;
		case 'K':
			a = 19;break;
		case 'L':
			a = 20;break;
		case 'M':
			a = 21;break;
		case 'N':
			a = 22;break;
		case 'O':
			a = 35;break;
		case 'P':
			a = 23;break;
		case 'Q':
			a = 24;break;
		case 'R':
			a = 25;break;
		case 'S':
			a = 26;break;
		case 'T':
			a = 27;break;
		case 'U':
			a = 28;break;
		case 'V':
			a = 29;break;
		case 'W':
			a = 32;break;
		case 'X':
			a = 30;break;
		case 'Y':
			a = 31;break;
		case 'Z':
			a = 33;break;
		default:
			return false;
	}/*end switch*/
	
	var x1 = 1 * Math.floor(a / 10);
	var x2 = 9 * (a % 10);
	var x3 = 8 * parseInt(tmp.charAt(1));
	var x4 = 7 * parseInt(tmp.charAt(2));
	var x5 = 6 * parseInt(tmp.charAt(3));
	var x6 = 5 * parseInt(tmp.charAt(4));
	var x7 = 4 * parseInt(tmp.charAt(5));
	var x8 = 3 * parseInt(tmp.charAt(6));
	var x9 = 2 * parseInt(tmp.charAt(7));
	var x10 = 1 * parseInt(tmp.charAt(8));
	var xc = parseInt(tmp.charAt(9));
	var xm = (x1+x2+x3+x4+x5+x6+x7+x8+x9+x10) % 10;
	xm = 10 - (xm == 0? 10: xm);
	
	return (xm == xc);
	
}

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

function ajax_updater(id, url, type) {
	if ( type == '' || type == null || type == "undefined" ) {
		type = "GET";
	}
	do_ajax(id, url, type, null, null);

	return ajax_data;	/* x */
}

function get_returned_text () {
	if ( ajax_req.readyState == 4 ) {
		if ( ajax_req.status == 200 ) {
			var messagereturn = ajax_req.responseText;
			return messagereturn;
		} else {
			alert('There was a problem with the request.');
		}
	}
}

/* x暂时基本无效 */
function ajax_isok() {
	if ( !ajax_req ) {
		return false;
	}

	if ( ajax_req.readyState == 4 ) {
		return ajax_flag;
	}
}

/***
 * ajax 数据处理函数
 */
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

//	if ( ajax_c > 100 ) {
//		return false;
//	} else {
//		return ajax_check_fun();
//	}
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
