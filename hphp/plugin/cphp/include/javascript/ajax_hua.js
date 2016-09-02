/***
 * ajax 处理
 * id: 接收数据的 id
 * url: 请求数据url
 * type: 请求方法: GET/POST
 */
var ajax_req = false;
var ajax_id = '';
var ajax_data = '';
var ajax_flag = false;

function ajax_updater(id, url, type) {
	if ( type == '' || type == null || type == "undefined" ) {
		type = "GET";
	}
	do_ajax(id, url, type, null);

	return ajax_data;
}

function ajax_isok() {
	return ajax_flag;
}

/***
 * send_data = "a=b&c=d"
 */
function do_ajax(id, url, type, send_data, func) {
	var changefunc = '';

	ajax_id = id;
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
		;
	}
}

function ajax_check_fun() {
	if ( ajax_req.readyState == 4 ) {
		var inputMsg = ajax_id;
		var content = document.getElementById(inputMsg);
		if ( ajax_req.status == 200 ) {
			ajax_data = ajax_req.responseText;
			if ( ajax_data.substr(0, 3) == "<!--OK!-->" ) {
				ajax_flag = true;
				content.innerHTML = ajax_data.substr(3);
			} else if ( ajax_data.substr(0, 5) == "<!--FAIL!-->" ) {
				ajax_flag = false;
				content.innerHTML = ajax_data.substr(5);
			} else {
				ajax_flag = false;
				content.innerHTML = ajax_data;
			}
		} else {
			ajax_flag = false;
			content.innerHTML = "服務暫時不能使用！";
		}
	}
}
