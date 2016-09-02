/**
 * cphp.js 
 * cphp 开发架构常用 js 函数
 * 主要用来控制 div 属性等
 * 
 * by: hua <hua@langr.org> Nov 2007
 * $Id: cphp.js 15 2009-10-20 08:34:02Z langr $
 */

/***
 * loadtodiv (需 ajax 支持)
 * 通过 ajax 装载一个页面内容到 div 层
 */
function loadtodiv(id, url)
{
	var dininner = new Ajax.Updater(id, url, {asynchronous:true, evalScripts:true, requestHeaders:['X-Update', id]});
}

function copytodiv(id, str)
{
	document.getElementById(id).innerHTML = str;
}

/***
 * 显示/隐藏 层内容
 */
function showhidediv(id)
{
	if ( document.getElementById(id).style.display == "none" ) {
		document.getElementById(id).style.display = "";
	} else {
		document.getElementById(id).style.display = "none";
	}
}

/***
 * 更新验证码
 */
function getverifycode(img)
{
	document.verify_code_img.src = img;
}

/***
 * 设置连接 target 为新开窗口
 */
function externallinks() 
{ 
	if (!document.getElementsByTagName) 
		return; 
	var anchors = document.getElementsByTagName("a"); 
	for (var i=0; i<anchors.length; i++) { 
		var anchor = anchors; 
		if ( anchor.getAttribute("href") && anchor.getAttribute("rel") == "external" ) {
			anchor.target = "_blank"; 
		}
	}
} 
/* window.onload = externalLinks; */

/***
 * 设置对象风格
 */
function setstylebyid(id, style, value)
{
	document.writeln("<script language='javascript'>document.getElementById('"+id+"').style."+style+" = "+value+";</script>");
}

/***
 * 获取对象风格值
 */
function getstylebyid(id, style)
{
	;
}

/***
 * Move Up and Down JS from:  
 * Bob Rockers (brockers@subdimension.com) [javascript.internet.com] 
 */
function move(fbox,tbox) {
	var i = 0;
	if(fbox.value != "") {
		var no = new Option();
		no.value = fbox.value;
		no.text = fbox.value;
		tbox.options[tbox.options.length] = no;
		fbox.value = "";
	}
}
function remove(box) {
	for(var i=0; i<box.options.length; i++) {
		if(box.options[i].selected && box.options[i] != "") {
			box.options[i].value = "";
			box.options[i].text = "";
		}
	}
	BumpUp(box);
} 
function BumpUp(abox) {
	for(var i = 0; i < abox.options.length; i++) {
		if(abox.options[i].value == "")  {
			for(var j = i; j < abox.options.length - 1; j++)  {
				abox.options[j].value = abox.options[j + 1].value;
				abox.options[j].text = abox.options[j + 1].text;
			}
		var ln = i;
		break;
		}
	}
	if(ln < abox.options.length)  {
		abox.options.length -= 1;
		BumpUp(abox);
	}
}
function Moveup(dbox) {
	for(var i = 0; i < dbox.options.length; i++) {
		if (dbox.options[i].selected && dbox.options[i] != "" && dbox.options[i] != dbox.options[0]) {
			var tmpval = dbox.options[i].value;
			var tmpval2 = dbox.options[i].text;
			dbox.options[i].value = dbox.options[i - 1].value;
			dbox.options[i].text = dbox.options[i - 1].text
			dbox.options[i-1].value = tmpval;
			dbox.options[i-1].text = tmpval2;
			dbox.options[i-1].selected='selected'; //Improved by Bob
			dbox.options[i].selected=''; //Improved by Bob
		}
	}
}
function Movedown(ebox) {
	for(var i = 0; i < ebox.options.length; i++) {
		if (ebox.options[i].selected && ebox.options[i] != "" && ebox.options[i+1] != ebox.options[ebox.options.length]) {
			var tmpval = ebox.options[i].value;
			var tmpval2 = ebox.options[i].text;
			ebox.options[i].value = ebox.options[i+1].value;
			ebox.options[i].text = ebox.options[i+1].text
			ebox.options[i+1].value = tmpval;
			ebox.options[i+1].text = tmpval2;
			ebox.options[i+1].selected='selected'; //Improved by Bob
			ebox.options[i].selected=''; //Improved by Bob
			break; //Improved by Bob
		}
	}
}
/* End Move Up and Down JS */

function GetOptions(ebox, urlnew) {
	var optionsout='';
	for(var i = 0; i < ebox.options.length; i++) {
		optionsout+=ebox.options[i].value+':';
	}
	var urlnews=urlnew+optionsout;
	window.location=urlnews;
}

/***
 * 改变模块区域列表 (admin.php?action=sortModules)
 */
function changarea(str)
{
	url	= "?module=pu&action=sortModules&area="+str;
	window.location = url;
}
