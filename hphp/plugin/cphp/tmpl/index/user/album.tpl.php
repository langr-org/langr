<!-- ��ʾĳ������ģ�� -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>{$webSiteTitle}</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<link href="../../../include/css/admin.css" rel="stylesheet" type="text/css">
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<div id='global' algin="center" >
<-- ����ͷ�� -->
<?
$this->loadTmplate(TEMPLATE_PATH."public/head.tpl.php");
?>
<br>
<-- �ಾ��Ϣ -->
<table align="center" width="80%"> 
<tr><td>
	{$user}���ಾ ...<hr><br>
	<font size=4>{$name} </font><br>
	<a href="{$upload_url}">�ϴ���Ƭ</a> <a href="{$del_url}" onClick="return confirm('ɾ�����������ɻָ�!\n��ȷ��ɾ�����ಾ!')">ɾ���ಾ</a><br>
	<!-- <font size=2>������:����{$view_today}��;�ܹ�{$view_count}��; -->
	��Ƭ:{$p_count}��<br>
	����ʱ��:{$create_time}</font><br>

	<-- �༭�ಾ���� -->
	<div id='editAlbm' >
	<form name="edit_album" method="POST" action="<?=url("?module=user&action=ea")?>">
�ಾ��:	<input type='text' name='name' maxlength=18 onMouseOver='this.focus()' value='{$name}'><br>
����:	<select  name='attr'><option selected value='1'>����</option>
		<option value='2'>��Ҫ����</option>
		<option value='3'>������</option></select>
�ಾ����:<select name='sort'>{$sort_list}</select><br>
����:	<input type='password' name='pwd' maxlength=16 value='{$password}'><br>
ǩ��:	<textarea name='sign' rows=4 cols=40 >{$sign}</textarea>
	<input type='hidden' name='aid' value='{$aid}'>
	<input type='hidden' name='type' value='modif'>
	<input type='submit' name='submit' value="�ύ">
	<input type=hidden name='module' value='user'>
	<input type=hidden name='action' value='ea'>
	</form>
	</div>
</td></tr>
<tr><td>
<-- ��ʾ�ಾ�����ƬԤ�� ��Ӧ��Ӧ�� album.list.tpl.php ģ�� --><br>
{$subPhoto}
<br>
{$page}


</td>
<td>
<!---
<-- ���������� --
<table border="1" width="200" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td>
<-- {$subNav} --
<?$this->loadTmplate(TEMPLATE_PATH."public/left.tpl.php");?>
    </td>
  </tr>
</table>
--->

</td></tr>
</table>

<?
$this->loadTmplate(TEMPLATE_PATH."public/foot.tpl.php");
?>
</div>
</body>
</html>
