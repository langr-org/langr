/***
 * loadtodiv (�� ajax ���֧��)
 * ͨ�� ajax װ��һ��ҳ�����ݵ� div ��
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
 * ȫѡ��������ѡ��
 */
function checkAll(ckb, parentid)
{
	var parent = document.getElementById(parentid);
	if(parent)
	{
		var checkBoxs = parent.getElementsByTagName('input');
			for(var i = 0; i < checkBoxs.length; i++)
			{
				if(checkBoxs[i].type == 'checkbox')
				checkBoxs[i].checked=ckb.checked;
			}
	}
}

/***
 * ��ʾ/���� ������
 */
function showhidediv(id)
{
	if ( document.getElementById(id).style.display == "none" ) {
		document.getElementById(id).style.display = "";
	} else {
		document.getElementById(id).style.display = "none";
	}
}

function showdiv(id)
{
	document.getElementById(id).style.display = "";
}

function hidediv(id)
{
	document.getElementById(id).style.display = "none";
}

/* ��չ������ */
function showbar(id)
{
	var tmp = "";

	if ( id == null ) 
		return 0;
//	if ( id != null )
//		tmp = GetCookie('showbar');
	tmp	= document.getElementById(id).style.display;

	hide_all();
	
//	if ( tmp != id ) {
	if ( tmp == "none" ) {
		document.getElementById(id).style.display = "";
		SetCookie('showbar', id, 60 * 60 * 24 * 30);	/* һ������Ч�� */
	} else {
		document.getElementById(id).style.display = "none";
		SetCookie('showbar', '', 60 * 60 * 24 * 30);	/*  */
	}
}

function load_showbar()
{
	var bar = GetCookie('showbar');
	
	hide_all();
	if ( bar != null )
		document.getElementById(bar).style.display = "";
}

/* Ҫ����������, ��ͬʱ���������޸� */
function hide_all()
{
	document.getElementById('statis').style.display = "none";
	document.getElementById('user').style.display = "none";
	document.getElementById('deporder').style.display = "none";
	document.getElementById('game').style.display = "none";
	document.getElementById('honor').style.display = "none";
	document.getElementById('depwave').style.display = "none";
	document.getElementById('shop').style.display = "none";
	document.getElementById('vbank').style.display = "none";
	document.getElementById('dealer').style.display = "none";
	document.getElementById('accadmin').style.display = "none";
	document.getElementById('announce').style.display = "none";
	document.getElementById('system').style.display = "none";
	document.getElementById('adminpower').style.display = "none";
}

/* ���Cookie������ֵ */
function GetCookieVal(offset)
{
	var endstr = document.cookie.indexOf (";", offset);
	if (endstr == -1)
		endstr = document.cookie.length;

	return unescape(document.cookie.substring(offset, endstr));
}

/* �趨Cookieֵ */
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

/* ���Cookie��ԭʼֵ */
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

/* ɾ��Cookie */
function DelCookie(name)
{
	var exp = new Date();
	exp.setTime (exp.getTime() - 1);
	var cval = GetCookie(name);
	document.cookie = name + "=" + cval + "; expires="+ exp.toGMTString();
}

