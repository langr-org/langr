<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>{$webSiteTitle}</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<link href="../../../include/css/admin.css" rel="stylesheet" type="text/css">
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<div algin="center">
<!-- 公共头部 -->
<?
$this->loadTmplate(TEMPLATE_PATH."public/head.tpl.php");
?>

<!-- 相册首页 -->

<table width="800" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td>
<!-- 当天人气相簿 对应相应的 index.TopList.tpl.php 模板 -->
{$subTop}
<br>
<-- ad 或其它 --><br>
<!-- 热门相簿 对应相应的 index.list.tpl.php 模板 -->
{$subBody}
<br>
<!-- 最近上传相簿 对应相应的 index.list.tpl.php 模板 -->
{$subNew}
    </td>
  </tr>
</table>

<!-- 边栏导航条 -->
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
