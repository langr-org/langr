<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<meta name="Keywords" content="�錢���,̨���錢,16���錢,�錢,�錢�[��,�[��,�����錢,���錢,���錢,С�[��,�����[��,�����[��^,���M�[��,���M�����[��,���M�錢�[�����d,���M�錢,�W·�[��,online game,�����[��,�[�����d,��l�r�g" /> 
<meta name="Description" content="�ṩ�������M�錢�[�򣬴��錢����3ȱ1����ȣ����h����į�������������[����������̨����ģ���㣬���������  ��" />
<meta name="author" content="���¾W�Ƽ��ɷ����޹�˾" /> 
<script language="javascript" src="/images/index/majong.js"></script>
<script language='javascript' src='/images/index/js/prototype.js'></script>
<script language='javascript' src='/images/index/js/scriptaculous.js'></script>
<style type="text/css">@import url(/images/index/majong.css);</style>
<title>{$webSiteTitle}</title>
</head>

<body onload="MJ_preloadImages()">
<center>
<!-- head -->
{$header}
<!-- end head -->
<div style="width:950px; background-color:#CDFAD8; height:102px;">
<a href="<?=url('?module=member&action=register')?>"><img src="images/index/Steps1.gif" width="138" height="56" style="padding-left:10px; padding-top:23px" /></a>
<a href="{$downloadUrl}"><img src="images/index/Steps2.gif" width="138" height="56" style="padding-left:10px; padding-top:23px" /></a>
<a href="<?=url('?module=twmj&action=game&sub=new_user')?>"><img src="images/index/Steps3.gif" width="138" height="56" style="padding-left:10px; padding-top:23px" /></a>
<a href="{$AdUrl}"><img src="{$AdLogo}" width="468" height="60" style="padding-left:5px; padding-top:21px" /></a>
</div>
<!--<div style="height:2px"></div>-->
<table border="0" cellpadding="0" cellspacing="0" width="950" align="center">
<tr>
<!-- left -->
<td width="175" bgcolor="#08aa08" valign="top">
<!-- menu-->
<?
$this->loadTmplate(TEMPLATE_PATH."public/twmj/left.tpl.php");
?>
<!-- end menu -->
<br />
<!-- rank -->
<div style="text-align:center">
<a href="/?module=twmj&action=event2" target="_blank"><img src="/event/images/challenges_a.gif" border="0"></a><br />
<a href="/?module=twmj&action=event2" target="_blank"><img src="/event/images/challenges_b.gif" border="0"></a><br />
<a href="/?module=twmj&action=event2" target="_blank"><img src="/event/images/challenges_c.gif" border="0"></a><br />
<a href="/?module=twmj&action=event2" target="_blank"><img src="/event/images/challenges_d.gif" border="0"></a><br />
</div><br />
<!-- end rank -->
<!-- promote -->
<div style="text-align:center">
<div id='shoppro'></div>
</div>
<script language="javascript">
	var pro1 = new Ajax.Updater('shoppro','/auto/SyncShop.php', {asynchronous:true, evalScripts:true, requestHeaders:['X-Update', 'shoppro']});
</script>
<!-- end promote -->
<img src="images/index/mj.gif" width="160" height="160" alt="" style="vertical-align:text-bottom" />
</td>
<!-- end left -->
<!-- middle -->
<td width="525" bgcolor="#FFFFFF" valign="top">
<div style="padding-left:5px">
<!-- game -->
<table width=512 cellpadding=0 cellspace=1 border=0 bgcolor=red>
<tr bgcolor=#FFFFFF>
<td width=84><img src="images/index/event_q.gif" width=84 height=24></td>
<td width=428>
<marquee scrollamount=3 id='ann' onmouseover=ann.stop() onmouseout=ann.start() style="font-size:9pt">
<a href="/?module=twmj&action=event" target=_blank>����^�_Ļ:��헿��~20�fԪ���������~</a>����
<a href="/?module=twmj&action=event" target=_blank>����^:������һɫ������Ԫ...�ȼ��͸߼�Һ��ΞĻ�@ʾ��..��80�����</a>����
<a href="#" onclick="window.open('http://www.betcity.com.tw/event/score.html','','height=480, width=640,toolbar=no,scrollbars=yes,menubar=no,resizable=1');"><font color="#FF0000"><b>�µġ������ƶȡ����������w�Ĵ���ˮ��!</b></font></a>
</marquee>
</td></tr>
</table>
<table width="455" height="10%" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td align="left"><table width="405" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="108" bgcolor="#088227" align="center" height="29"><font color="#FFFFFF"><b>�[����ɫ</b></font></td>
                <td width="108" bgcolor="#E2FFF0" align="center" height="29"><a href="<?=url('?module=twmj&action=game&sub=set_explain')?>"><font color="#0000FF"><b>�[���B</b></font></a></td>
                <td width="108" bgcolor="#E2FFF0" align="center" height="29"><a href="<?=url('?module=twmj&action=game&sub=new_user')?>"><font color="#0000FF"><b>���ֈ�</b></font></a></td>
                <td width="108" bgcolor="#E2FFF0" align="center" height="29"><a href="<?=url('?module=twmj&action=game&sub=rules')?>"><font color="#0000FF"><b>�[��Ҏ�t</b></font></a></td>
				<td width="108" bgcolor="#E2FFF0" align="center" height="29"><a href="#" onclick="window.open('http://www.betcity.com.tw/event/score.html','','height=480, width=640,toolbar=no,scrollbars=yes,menubar=no,resizable=1');"><font color="#FF0000"><b>�����ƶ�</b></font></a></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td><img src="images/index/c1.gif" width="515" height="17" /></td>
          </tr>
          <tr>
            <td height="200" align="center" background="images/index/c2.gif"><table width="500" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td colspan="3"><div align="center">
                  <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="485" height="410">
                    <param name="movie" value="images/index/mj.swf" />
                    <param name="quality" value="high" />
                    <param name="wmode" value="transparent" />
                    <embed src="images/index/mj.swf" width="485" height="410" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" wmode="transparent"></embed>
                  </object>
                  <br />
                  <br />                
                </div></td>
                </tr>
              <tr>
                <td colspan="3" align="center"><div align="left"><span class="style16">��</span>�������M�桭����������(<a href="{$downloadUrl}">���d</a>�� <br />
                    <span class="style16">��</span>���ָ����t����Ʒ�ֵ <br />
                    <span class="style16">��</span>�������£���Ԓ���Ц
  <br />
                  <span class="style16">��</span><span class="style4">ԭ��������߅��߅ �����ܣ�</span> <br />
                  <span class="style16">��</span>��X���򣬲��£���ȱһ���ƾ��Д� <br />
                  <span class="style16">��</span>�[��ҕ�Ƿ������w���W��������� <br />
                  <span class="style16">��</span>�ƾ���������������p� <br />
                  <span class="style16">��</span>�[��n��С����XӲ�wҪ��ͣ�<span class="style19">���dֻҪ10��犣��R���� </span><br />
                </div></td>
                </tr>
              <tr>
                <td height="60" colspan="3" align="center"><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="370" height="80">
                  <param name="movie" value="images/index/sound-2.swf" />
                  <param name="quality" value="high" />
                  <param name="wmode" value="transparent" />
                  <embed src="images/index/sound-2.swf" width="370" height="80" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" wmode="transparent"></embed>
                </object></td>
                </tr>
              <tr>
                <td width="123"><div align="center"><a href="<?=url('?module=twmj&action=game&sub=set_explain')?>">�[��Ԕ����B��</a></div></td>
                <td width="183"><div align="right"><a href="ftp://down1.betcity.com.tw/pub/{$mj_edition}"><img src="images/index/download-01.gif" width="170" height="95" border="0" /></a></div></td>
                <td width="176"><div align="right"><a href="ftp://down2.betcity.com.tw/pub/{$mj_edition}"><img src="images/index/download-02.gif" width="170" height="95" border="0" /></a></div></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td height="17"><img src="images/index/c3.gif" width="515" height="17" /></td>
          </tr>
        </table>
<!-- end game -->
<br />
<div style="text-align:center; width:520px">
<a href="<?=url('?module=twmj&action=event')?>" target="_blank"><img src="/event/images/515.gif" width="515" height="95" border="0"></a>
<a href="/event/images/DSCI0001_b.jpg" target="_blank"><img src="/event/images/DSCI0001_s.jpg" width="117" height="88" border="1"></a>
<a href="/event/images/DSCI0002_b.jpg" target="_blank"><img src="/event/images/DSCI0002_s.jpg" width="117" height="88" border="1"></a>
<a href="/event/images/DSCI0003_b.jpg" target="_blank"><img src="/event/images/DSCI0003_s.jpg" width="117" height="88" border="1"></a>
<a href="/event/images/DSCI0004_b.jpg" target="_blank"><img src="/event/images/DSCI0004_s.jpg" width="117" height="88" border="1"></a>
</div>
<div style="text-align:center; width:520px">
<a href="/event/images/DSCI0008_b.jpg" target="_blank"><img src="/event/images/DSCI0008_s.jpg" width="117" height="88" border="1"></a>
<a href="/event/images/DSCI0009_b.jpg" target="_blank"><img src="/event/images/DSCI0009_s.jpg" width="117" height="88" border="1"></a>
<a href="/event/images/DSCI0010_b.jpg" target="_blank"><img src="/event/images/DSCI0010_s.jpg" width="117" height="88" border="1"></a>
<a href="/event/images/DSCI0011_b.jpg" target="_blank"><img src="/event/images/DSCI0011_s.jpg" width="117" height="88" border="1"></a>
</div>
<br />
<!-- Sort -->
<div style="width:455px">

<script language="javascript">
function gettier(url_a) {
	if(url_a != "") {
		new Ajax.Updater('tier_sort',url_a, {asynchronous:true, evalScripts:true, requestHeaders:['X-Update', 'tier_sort']});
	}
}
gettier('/?module=twmj&action=tier&tier=Win_p&show_type=ajax&show_loc=index');
</script>
<table width="520" border="0" cellspacing="1" cellpadding="2" bgcolor=#486a00 align=center id="tier">
<tr bgcolor=#a4f000 align=center>
<td width=55><a href="#Win_p" onclick="gettier('/?module=twmj&action=tier&tier=Win_p&show_type=ajax&show_loc=index')">�A����</a></td>
<td width=55><a href="#Lose_p" onclick="gettier('/?module=twmj&action=tier&tier=Lose_p&show_type=ajax&show_loc=index')">ݔ����</a></td>
<td width=55><a href="#Win" onclick="gettier('/?module=twmj&action=tier&tier=Win&show_type=ajax&show_loc=index')">�A�Ɣ�</a></td>
<td width=55><a href="#Lost" onclick="gettier('/?module=twmj&action=tier&tier=Lose&show_type=ajax&show_loc=index')">ݔ�Ɣ�</a></td>
<td width=55><a href="#Zhimo_p" onclick="gettier('/?module=twmj&action=tier&tier=Zhimo_p&show_type=ajax&show_loc=index')">������</a></td>
<td width=55><a href="#Zhimo" onclick="gettier('/?module=twmj&action=tier&tier=Zhimo&show_type=ajax&show_loc=index')">������</a></td>
<td width=55><a href="#Hu_p" onclick="gettier('/?module=twmj&action=tier&tier=Hu_p&show_type=ajax&show_loc=index')">������</a></td>
<td width=55><a href="#Hu" onclick="gettier('/?module=twmj&action=tier&tier=Hu&show_type=ajax&show_loc=index')">���Ɣ�</a></td>
<td width=55><a href="#Gun_p" onclick="gettier('/?module=twmj&action=tier&tier=Gun_p&show_type=ajax&show_loc=index')">�Ř���</a></td>
<td width=55><a href="#Gun" onclick="gettier('/?module=twmj&action=tier&tier=Gun&show_type=ajax&show_loc=index')">�Ř���</a></td>
</tr>
<tr bgcolor=#a4f000 align=center>
<td width=55><a href="#WinPoint" onclick="gettier('/?module=twmj&action=tier&tier=WinPoint&show_type=ajax&show_loc=index')">�A�c��</a></td>

<td width=55><a href="#Playround" onclick="gettier('/?module=twmj&action=tier&tier=Playround&show_type=ajax&show_loc=index')">���ƾ�</a></td>
<td width=55><a href="#Escape" onclick="gettier('/?module=twmj&action=tier&tier=Escape&show_type=ajax&show_loc=index')">���ܔ�</a></td>
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
	�Y���d����...
</div>
<a href="<?=url('?module=twmj&action=tier')?>">�������а�</a><br />
<font color="#FF0000" size="-1">�]���֔����^ 100 �֣��ŕ�Ӌ�����а�</font><br />
<font color="#FF0000" size="-1">�]��ÿ���犸���һ��</font>
</div>
<!-- end Sort -->
<br>
<!-- forum -->
<div id="mjpro">
	�Y���d����....
</div>
<script language="javascript">
	var t1 = new Ajax.Updater('mjpro','/auto/SyncZbbs2.php', {asynchronous:true, evalScripts:true, requestHeaders:['X-Update', 'mjpro']});
</script>
<!-- end forum -->
		  <br />
<!-- forum -->
<div id="zbbs">
	�Y���d����....
</div>
<script language="javascript">
	var t = new Ajax.Updater('zbbs','/auto/SyncZbbs.php', {asynchronous:true, evalScripts:true, requestHeaders:['X-Update', 'zbbs']});
</script>
<!-- end forum -->
		  <br />
		  <!-- exchange -->
		  <!-- end exchange -->
</div>
</td>
<!-- end middle -->
<!-- right -->
<td width="245" bgcolor="#FFFFFF" valign="top"><br>
<a href="<?=url('?module=member&action=deposit')?>"><img src="/images/index/points.gif" width="245" height="60" border="0"></a><br><br>
<?php
if(!$this->is_login) {
?>
<!-- login -->
<table width="245" border="0" cellspacing="0" cellpadding="0"><tr><td><img src="images/index/member-login.gif" width="245" height="41" /></td></tr></table>
<table width="245" height="13%" border="1" cellpadding="0" cellspacing="0" bordercolor="#E1E1E1">
            <tr>
              <td height="142" bgcolor="f5f5f5" scope="col"><div align="center">
                <form method="POST" name="L_form" action="?"><input type=hidden name='module' value='member'><input type=hidden name='action' value='login'><input type="hidden" name="nurl" value="/">
                	<table width="240" height="139" border="0" cellpadding="0" cellspacing="2">
                  <tr><td height="24"><span class="style15">��̖:</span><input name="user" type="text" size="12" />&nbsp;<!--<input type="checkbox" name="checkbox" value="checkbox" /><span class="style15">ӛס��̖</span>--></td></tr>
                  <tr><td height="24"><span class="style15">�ܴa:</span><input name="pwd" type="password" size="12" />&nbsp;<a href="<?=url("?module=member&action=getpwd")?>" class="style15">��ӛ�ܴa</a></td></tr>
                  <tr><td height="16" valign="middle"><span class="style15">��C:</span><input name="verifyCode" type="text" size="12" />&nbsp;<img src="<?=url("?module=member&action=verifyCode&t=gif")?>"></td></tr>
                  <tr><td height="16">&nbsp;</td></tr>
                  <tr>
                    <td align="center"><table border="0" cellpadding="0" cellspacing="3"><tr><td><input type="image" src="images/index/login.gif" border="0" name="submit" alt=""></td><td><a href="<?=url('?module=member&action=register')?>"><img src="images/index/freeregister.gif" width="150" height="55" border="0" /></a></td></tr></table></td>
                  </tr>
                </table></form>
                <br />
              </div></td>
            </tr>
          </table>
<!-- end login -->
<?php } 
else {
	$this->loadTmplate(TEMPLATE_PATH."public/twmj/member_left_index.tpl.php");
}
?>
<br />
<!-- announce -->
<table width="245" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td><img src="images/index/nwes.gif" width="245" height="41" /></td>
            </tr>
          </table>
          <table width="245" border="1" cellpadding="0" cellspacing="0" bordercolor="#E1E1E1">
            <tr>
              <td height="129" align="center" bgcolor="f5f5f5" scope="col">
			  {$notice}
			  </td>
            </tr>
          </table>
<!-- end announce -->
</td>
<!-- end right -->
</tr>
<tr height="20"><td bgcolor="#08aa08"></td><td bgcolor="#FFFFFF"></td><td bgcolor="#FFFFFF"></td></tr>
</table>
<!-- foot -->
{$footer}
<!-- end foot -->
</center>
</body>
</html>
