<!-- ��ʾĳ���û�����ҳģ�� -->
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
<-- �û��ಾ��ҳ -->
<table align="center" width="80%"> 
<tr><td>{$user}���ಾ
</td></tr>
<tr><td valign="top">
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr > 
    <td>
<-- �û��ಾ ��Ӧ��Ӧ�� user.list.tpl.php ģ�� -->
{$subUser}
<br>
{$page}
    </td>
  </tr>
</table>

</td>
<td>

<!-- ���������� -->
<table border="1" width="200" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td><--�û�ͷ��--><br>
<a href="{$user_url}"><img src="{$portrait}"></a><br>
{$user_sign}<br>
�û��ಾ��ַ:<br>
<a href="{$user_url}">{$user_url}</a>
<?$this->loadTmplate(TEMPLATE_PATH."public/left.tpl.php");?>
    </td>
  </tr>
</table>

</td></tr>
</table>

<?
$this->loadTmplate(TEMPLATE_PATH."public/foot.tpl.php");
?>
</div>
</body>
</html>
