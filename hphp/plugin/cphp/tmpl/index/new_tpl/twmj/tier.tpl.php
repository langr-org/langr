<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<meta name="Keywords" content="�錢���,̨���錢,16���錢,�錢,�錢�[��,�[��,�����錢,���錢,���錢,С�[��,�����[��,�����[��^,���M�[��,���M�����[��,���M�錢�[�����d,���M�錢,�W·�[��,online game,�����[��,�[�����d,��l�r�g" /> 
<meta name="Description" content="�ṩ�������M�錢�[�򣬴��錢����3ȱ1����ȣ����h����į�������������[����������̨����ģ���㣬���������  ��" />
<meta name="author" content="���¾W�Ƽ��ɷ����޹�˾" /> 
<script language="javascript" src="/images/index/majong.js"></script>
<style type="text/css">@import url(/images/index/majong.css);</style>
<title>{$webSiteTitle}</title>
</head>
<body onload="MM_preloadImages('images/index/menu_01-over.gif','images/index/menu_02-over.gif','images/index/menu_04-over.gif','images/index/menu_05-over.gif','images/index/menu_06-over.gif','images/index/menu_07-over.gif','images/index/menu_08-over.gif')">
<center>
<!-- head -->
{$header}
<!-- end head -->
<table border="0" cellpadding="0" cellspacing="0" width="950" align="center">
<tr>
<!-- left -->
<td width="175" bgcolor="#08aa08" valign="top">
<?
$this->loadTmplate(TEMPLATE_PATH."public/twmj/member_left.tpl.php");
echo "<br />";
$this->loadTmplate(TEMPLATE_PATH."public/twmj/left.tpl.php");
?>
<br />

<img src="images/index/mj.gif" width="160" height="160" alt="" style="vertical-align:text-bottom" />
</td>
<!-- end left -->
<!-- middle -->
<td width="775" bgcolor="#FFFFFF" valign="top">
<div style="padding-left:5px"><br>
<script language='javascript' src='/images/index/js/prototype.js'></script>
<script language='javascript' src='/images/index/js/scriptaculous.js'></script>
<script language="javascript">
function gettier(url_a) {
	if(url_a != "") {
		new Ajax.Updater('tier_sort',url_a, {asynchronous:true, evalScripts:true, requestHeaders:['X-Update', 'tier_sort']});
	}
}
</script>
<center><a href="/?module=twmj&action=event" target=_blank><img src="/event2/468x60_3.gif" width=468 height=60 border=0></a></center>
&nbsp;
<table width="520" border="0" cellspacing="1" cellpadding="2" bgcolor=#486a00 align=center id="tier">
<tr bgcolor=#a4f000 align=center>
<td width=55><a href="#Win_p" onclick="gettier('<?=url("?module=twmj&action=tier&tier=Win_p&show_type=ajax")?>')">�A����</a></td>
<td width=55><a href="#Lose_p" onclick="gettier('<?=url("?module=twmj&action=tier&tier=Lose_p&show_type=ajax")?>')">ݔ����</a></td>
<td width=55><a href="#Win" onclick="gettier('<?=url("?module=twmj&action=tier&tier=Win&show_type=ajax")?>')">�A�Ɣ�</a></td>
<td width=55><a href="#Lost" onclick="gettier('<?=url("?module=twmj&action=tier&tier=Lose&show_type=ajax")?>')">ݔ�Ɣ�</a></td>
<td width=55><a href="#Zhimo_p" onclick="gettier('<?=url("?module=twmj&action=tier&tier=Zhimo_p&show_type=ajax")?>')">������</a></td>
<td width=55><a href="#Zhimo" onclick="gettier('<?=url("?module=twmj&action=tier&tier=Zhimo&show_type=ajax")?>')">������</a></td>
<td width=55><a href="#Hu_p" onclick="gettier('<?=url("?module=twmj&action=tier&tier=Hu_p&show_type=ajax")?>')">������</a></td>
<td width=55><a href="#Hu" onclick="gettier('<?=url("?module=twmj&action=tier&tier=Hu&show_type=ajax")?>')">���Ɣ�</a></td>
<td width=55><a href="#Gun_p" onclick="gettier('<?=url("?module=twmj&action=tier&tier=Gun_p&show_type=ajax")?>')">�Ř���</a></td>
<td width=55><a href="#Gun" onclick="gettier('<?=url("?module=twmj&action=tier&tier=Gun&show_type=ajax")?>')">�Ř���</a></td>
</tr>
<tr bgcolor=#a4f000 align=center>
<td width=55><a href="#WinPoint" onclick="gettier('<?=url("?module=twmj&action=tier&tier=WinPoint&show_type=ajax")?>')">�A�c��</a></td>

<td width=55><a href="#Playround" onclick="gettier('<?=url("?module=twmj&action=tier&tier=Playround&show_type=ajax")?>')">���ƾ�</a></td>
<td width=55><a href="#Escape" onclick="gettier('<?=url("?module=twmj&action=tier&tier=Escape&show_type=ajax")?>')">���ܔ�</a></td>
<td width=55>&nbsp;</td>
<td width=55>&nbsp;</td>
<td width=55>&nbsp;</td>
<td width=55>&nbsp;</td>
<td width=55>&nbsp;</td>
<td width=55>&nbsp;</td>
<td width=55>&nbsp;</td>
</tr>
</table>
<div id="tier_sort">
{$body}
</div>
<br />
<font color="#FF0000">�]���֔����^ 100 �֣��ȼ�����С�W���꼉���ŕ�Ӌ�����а�</font>
</div>
</td>
<!-- end middle -->
</tr>
<tr height="20"><td bgcolor="#08aa08"></td><td bgcolor="#FFFFFF"></td></tr>
</table>
<!-- foot -->
{$footer}
<!-- end foot -->
</center>

</body>
</html>
