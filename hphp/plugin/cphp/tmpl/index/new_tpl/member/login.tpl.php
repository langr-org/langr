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
<div style="padding-left:10em; text-align:left; padding-top:3em;">
  <form method="POST" name="L_form" action="?">
  	<input type="hidden" name="nurl" value="{$nurl}">
  	<table width="240" height="139" border="0" cellpadding="0" cellspacing="0"><input type=hidden name='module' value='member'><input type=hidden name='action' value='login'>
                  <tr>
                    <td width="53" height="24"><div align="center" class="style15">��̖:</div></td>
                    <td width="84"><label>
                      <input name="user" type="text" size="12" />
                    </label></td>
                    <td width="29"><!--<label>
                      <input type="checkbox" name="checkbox" value="checkbox" />
                    </label>--></td>
                    <td width="74"><!--<span class="style15">ӛס��̖</span>--></td>
                  </tr>
                  <tr>
                    <td><div align="center" class="style15">�ܴa:</div></td>
                    <td><input name="pwd" type="password" size="12" /></td>
                    <td><div align="right"></div></td>
                    <td><a href="<?=url("?module=member&action=getpwd")?>" class="style15">��ӛ�ܴa</a></td>
                  </tr>
                  <tr>
                    <td height="16"><div align="center" class="style15">��C:</div></td>
                    <td><input name="verifyCode" type="text" size="12" /></td>
                    <td colspan="2">&nbsp;<img src="<?=url("?module=member&action=verifyCode&t=gif")?>"></td>
                  </tr>
                  <tr>
                    <td rowspan="2">&nbsp;</td>
                    <td height="16">&nbsp;</td>
                    <td colspan="2">&nbsp;</td>
                    </tr>
                  <tr>
                    <td><input type="image" src="images/index/login.gif" border="0" name="submit" alt=""></td>
                    <td colspan="2"><a href="<?=url('?module=member&action=register')?>"><img src="images/index/register.gif" width="102" height="55" border="0" /></a></td>
                  </tr>
                </table></form>
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
