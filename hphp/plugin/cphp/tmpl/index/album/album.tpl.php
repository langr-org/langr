<!-- 显示某个相册的模板 -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>{$webSiteTitle}</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<link href="../../../include/css/admin.css" rel="stylesheet" type="text/css">
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<div id='global' algin="center" >
<-- 公共头部 -->
<?
$this->loadTmplate(TEMPLATE_PATH."public/head.tpl.php");
?>
<br>
<-- 相簿信息 -->
<table align="center" width="80%"> 
<tr><td>
	{$user}的相簿 ...<hr><br>
	<font size=4>{$name} </font><br>
	<font size=2>点阅数:今天{$view_today}次;总共{$view_count}次;相片:{$p_count}张<br>
	创建时间:{$create_time}</font><br>
	{$sign}
</td></tr>
<tr><td>

<-- 显示相簿里的相片预览 对应相应的 album.list.tpl.php 模板 --><br>
{$subPreview}
<br>
{$page}


</td>
<td>
<!---
<-- 边栏导航条 --
<table border="1" width="200" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td>
<-- {$subNav} --
<?$this->loadTmplate(TEMPLATE_PATH."public/userLeft.tpl.php");?>
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
