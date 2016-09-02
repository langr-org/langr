var REGPATTERN = {
VALID_NOT_EMPTY: "/.+/",
VALID_NUMBER: "/^[-+]?\\b[0-9]*\\.?[0-9]+\\b$/",
VALID_EMAIL: "/^(?:^([a-z0-9][a-z0-9_\\-\\.\\+]*)@([a-z0-9][a-z0-9\\.\\-]{0,63}\\.(com|org|net|biz|info|name|net|pro|aero|coop|museum|[a-z]{2,4}))$)$/",
VALID_ALPHANUMERIC:"/[\\dA-Z]/",
VALID_URL: "/^(?:(?:https?|ftps?|file|news|gopher):\\/\\/)?(?:(?:(?:25[0-5]|2[0-4]\d|(?:(?:1\d)?|[1-9]?)\d)\.){3}(?:25[0-5]|2[0-4]\d|(?:(?:1\d)?|[1-9]?)\d)|(?:[0-9a-z]{1}[0-9a-z\\-]*\\.)*(?:[0-9a-z]{1}[0-9a-z\\-]{0,56})\\.(?:[a-z]{2,6}|[a-z]{2}\\.[a-z]{2,6})(?::[0-9]{1,4})?)(?:\\/?|\\/[\\w\\-\\.,\'@?^=%&:;\/~\\+#]*[\\w\\-\\@?^=%&\/~\\+#])$/",
VALID_TWMOBILE: "/^09[0-9]{8}$/",
VALID_TWIDCARD: "/^[a-zA-Z][12][0-9]{8}$/",
VALID_CNMOBILE: "/^((\(\d{2,3}\))|(\d{3}\-))?13\d{9}$/",
VALID_QQ: "/^[1-9]\d{4,8}$/"
};

function trim(str) {
	return str.replace(/^\s*/, "").replace(/\s*$/, "");
}/*end trim*/
function valid(reg, str) {
	reg = reg.replace(/^\/*/, "").replace(/\/*$/, "");
	var g = new RegExp(reg, "i");
	return g.test(str);
}/* end valid */
function isArray(obj) {
	return !(obj.constructor.toString().indexOf("Array") == -1)
}/* end isArray */
function isString(obj) {
	return !(obj.constructor.toString().indexOf("String") == -1);
}/* end isString */
function applyRule(rule, str) {
	str = trim(str);
	if(isArray(rule)) {
		ret = "var tmp = " + rule[0] + "(str";
		for(var k = 1; k < rule.length; k++) {
			if(isString(rule[k]))
				ret += (",'" + rule[k] + "'");
			else
				ret += ("," + rule[k]);
		}
		ret += ");";
		
		eval(ret);
		return tmp;
	}/* end for function validation*/
	else {
		if(rule.indexOf("VALID_") == 0) {
			return valid(REGPATTERN[rule], str);
		}/*end buildin pattern*/
		else
			return valid(rule, str);
	}/* end for regex validation*/
}/* end applyRule*/

function getChoiceVal(elm) {
	var elms = document.getElementsByName(elm.name);
	if(!elms[0]) {
		if(elms.checked)
			return "0";
	}/*end single element */
	else {
		var ret ="";
		for(var i = 0; i < elms.length; i++) {
			if(elms[i].checked)
				ret += "0";
		}/* end navigate elements*/
		return ret;
	}/* end multiple element */
}/* getChoiceVal*/

function getSelectVal(elm) {
	var ret = "";
	for(var i = 0; i < elm.options.length; i++) {
		if(elm.options[i].selected && elm.options[i].value != "")
			ret += "0";
	}/* end navigate selector */
	return ret;
}/* getSelectVal */

function getVal(elm) {
	var t = elm.type;
	switch(t) {
		case "hidden":
		case "text":
		case "password":
		case "file":
		case "textarea":
			return elm.value;
		case "checkbox":
		case "radio":
			return getChoiceVal(elm);
		case "select-one":
		case "select-multiple":
			return getSelectVal(elm);
	}/*end switch*/
}/* end getVal */

function between(val, minval, maxval) {
	return (val.length <= maxval && val.length >= minval);
}/*end between */

function maxLength(val, maxval) {
	return (val.length <= maxval);	
}/*end maxLength*/

function minLength(val, minval) {
	return (val.length >= minval);
}/* end minLength */

function range(val, minval, maxval){
	return (!isNaN(val) && val >= minval && val <= maxval);
}/* end range */

function choose(val, num) {
	return (val.length >= num);
}/* end choose*/

function twmobile(val) {
	return valid("/^09[0-9]{8}$/", val);
}/* end twmobile */

function twidcard(val) {
	return valid("/^[A-Z][12][0-9]{8}$/", val);
}/* end twidcard */
function identicalVal(val, field) {
	//alert(document.getElementsByName(field));
	return (document.getElementsByName(field) && val == document.getElementsByName(field)[0].value);
}/*end identicalVal */
function notIdenticalVal(val, field) {
	return (document.getElementsByName(field) && val != document.getElementsByName(field)[0].value);
}/*end notIdenticalVal*/

function checkTWIdCard(val) {
	if(!twidcard(val))
		return false;
	
	var tmp = val.toUpperCase();
	var a = 0;
	
	switch(tmp.charAt(0)) {
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
	
}/* end checkTWIdCard */
function checkForm(frm) {
	var elms = frm.elements;
	var ret = true;
	var errmsg = "";
	for(var i = 0; i < elms.length; i++) {
		var attr = elms[i].getAttribute("myrules");
		if(attr) {
			var rules = eval(attr);
			for(var j = 0; j < rules.length; j++) {
				var blk = document.getElementById("_err_"+elms[i].name);
				if(applyRule(rules[j].r, getVal(elms[i]))) {
					if(blk)
						blk.innerHTML = "";
				}/* end value is legal*/
				else {
					ret = false;
					if(blk)
						blk.innerHTML = rules[j].m;
					else
						errmsg += (rules[j].m + "\r\n");
					break;
				}/* end value is illegal*/
			}/* end navigate rules */
		}/* has rule */
	}/* end navigate form elements*/
	if(!ret && errmsg != "")
		alert(errmsg);
	return ret;
}/* end checkForm */

// ���밲ȫ�ȼ��
// Password strength meter v1.0
// Matthew R. Miller - 2007
// www.codeandcoffee.com
// Based off of code from  http://www.intelligent-web.co.uk

// Settings
// -- Toggle to true or false, if you want to change what is checked in the password
var bCheckNumbers = true;
var bCheckUpperCase = true;
var bCheckLowerCase = true;
var bCheckPunctuation = true;
var nPasswordLifetime = 365;

// Check password
function checkPassword(strPassword)
{
	// Reset combination count
	nCombinations = 0;
	
	// Check numbers
	if (bCheckNumbers)
	{
		strCheck = "0123456789";
		if (doesContain(strPassword, strCheck) > 0) 
		{ 
        		nCombinations += strCheck.length; 
    		}
	}
	
	// Check upper case
	if (bCheckUpperCase)
	{
		strCheck = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		if (doesContain(strPassword, strCheck) > 0) 
		{ 
        		nCombinations += strCheck.length; 
    		}
	}
	
	// Check lower case
	if (bCheckLowerCase)
	{
		strCheck = "abcdefghijklmnopqrstuvwxyz";
		if (doesContain(strPassword, strCheck) > 0) 
		{ 
        		nCombinations += strCheck.length; 
    		}
	}
	
	// Check punctuation
	if (bCheckPunctuation)
	{
		strCheck = ";:-_=+\|//?^&!.@$?#*()%~<>{}[]";
		if (doesContain(strPassword, strCheck) > 0) 
		{ 
        		nCombinations += strCheck.length; 
    		}
	}
	
	// Calculate
	// -- 500 tries per second => minutes 
    	var nDays = ((Math.pow(nCombinations, strPassword.length) / 500) / 2) / 86400;
 
	// Number of days out of password lifetime setting
	var nPerc = nDays / nPasswordLifetime;
	
	return nPerc;
}
 
// Runs password through check and then updates GUI 
function runPassword(strPassword, strFieldID) 
{
	// Check password
	nPerc = checkPassword(strPassword);
	
	 // Get controls
	var ctlHtml = "";
    	var ctlBar = ""; 
    	var ctlText = "";
	var _ctlText = document.getElementById("_err_" + strFieldID);
    	//if (!ctlBar || !ctlText)
    	//	return;
    	
    	// Set new width
    	var nRound = Math.round(nPerc * 100);
	if (nRound < (strPassword.length * 5)) 
	{ 
		nRound += strPassword.length * 5; 
	}
	if (nRound > 100)
		nRound = 100;
    	ctlBar = nRound + "%";
 
 	// Color and text
	_ctlText.className = "check_true";
 	if (nRound > 95)
 	{
 		strText = "强";
 		strColor = "#3bce08";
 	}
 	else if (nRound > 75)
 	{
 		strText = "中";
 		strColor = "orange";
	}
 	else if (nRound > 50)
 	{
 		strText = "弱";
 		strColor = "#ffd801";
 	}
 	else
 	{
 		strColor = "red";
 		strText = "太弱";
 	}
	if ( strPassword == '' || strPassword == null ) {
		strText = "請輸入密碼！";
		_ctlText.className = "check_fail";
	} else if ( strPassword.length < 6 || strPassword.length > 16 ) {
		strText = strText + " 密碼長度為6-12位！";
		_ctlText.className = "check_fail";
	}

	ctlText = "<span style='color: " + strColor + ";'>" + strText + "</span>";
	ctlHtml = "<div style=\"width:150px;\"><div id=\"pwd_text\" style=\"font-size: 11px;\">" + ctlText + "</div><div id=\"pwd_bar\" style=\"background: " + strColor + ";font-size: 1px; height: 2px; width: " + ctlBar + "; border: 1px solid white;\"></div></div>";

	_ctlText.innerHTML = ctlHtml;

	return true;
}
 
function check_password(str1, str2, check_id)
{
	var text = document.getElementById("_err_" + check_id); 
	if ( str1 == '' || str1 == null ) {
		text.className =  "check_fail";
		text.innerHTML = "請輸入確認密碼";
	} else if ( str1 != str2 ) {
		text.className =  "check_fail";
		text.innerHTML = "兩次輸入密碼不同";
	} else {
		text.className =  "check_true";
		text.innerHTML = "密碼確認通過";
	}
}

// Checks a string for a list of characters
function doesContain(strPassword, strCheck)
 {
    	nCount = 0; 
 
	for (i = 0; i < strPassword.length; i++) 
	{
		if (strCheck.indexOf(strPassword.charAt(i)) > -1) 
		{ 
	        	nCount++; 
		} 
	} 
 
	return nCount; 
} 

