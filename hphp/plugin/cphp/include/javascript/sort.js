/***
 * Move Up and Down JS
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
			dbox.options[i-1].selected='selected';
			dbox.options[i].selected='';
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
			ebox.options[i+1].selected='selected';
			ebox.options[i].selected='';
			break;
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
	url	= "?module=dmg&action=sortModules&area="+str;
	window.location = url;
}
