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
<!-- ���������ಾ ��Ӧ��Ӧ�� index.TopList.tpl.php ģ�� -->
{$subTop}
<br>
<-- ad ������ --><br>
<!-- �����ಾ ��Ӧ��Ӧ�� index.list.tpl.php ģ�� -->
{$subBody}
<br>
<!-- ����ϴ��ಾ ��Ӧ��Ӧ�� index.list.tpl.php ģ�� -->
{$subNew}
    </td>
  </tr>
</table>

<!-- ���������� -->
<table width="224" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td>
<!-- {$subNav} -->
<?this->loadTmplate(TEMPLATE_PATH."public/left.tpl.php");?>
    </td>
  </tr>
</table>
<?
$this->loadTmplate(TEMPLATE_PATH."public/foot.tpl.php");
?>
</div>
</body>
</html>
