<!-- �û��ಾ�����̨ģ�� -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>{$webSiteTitle}</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<link href="../../../include/css/admin.css" rel="stylesheet" type="text/css">
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<div algin="center">
<!-- ����ͷ�� -->
<?
$this->loadTmplate(TEMPLATE_PATH."public/head.tpl.php");
?>

<!-- �����ҳ -->

<table width="800" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td>
	{$user}���ಾ ...<hr><br>
	�����ಾ������ַ:<a href="{$user_url}">{$user_url}</a><br>
	��ʹ�ÿռ�:�� {$a_count} ���ಾ, ʹ�� {$size}
    </td>
<!-- �û����������� -->
    <td width="224" rowspan=2 border="1" valign="top">
<?$this->loadTmplate(TEMPLATE_PATH."public/userLeft.tpl.php");?>
    </td>
  </tr>
  <tr>
    <td>
<!-- �û��ಾ���� ��Ӧ��Ӧ�� index.TopList.tpl.php ģ�� -->
{$subAlbum}
<br>
    </td>
  </tr>
</table>
<?
$this->loadTmplate(TEMPLATE_PATH."public/foot.tpl.php");
?>
</div>
</body>
</html>
