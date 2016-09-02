<!-- 浏览某相册相片模板 -->
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
<-- 相册首页 -->
<table align="center" width="80%"> 
<tr><td>
	{$user}的相簿>>{$name}  ...<hr><br>
</td></tr>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr><td><a href="{$photo_pre}#photo" name='photo'>上一张</a> <a href="{$photo_next}#photo">下一张</a></td></tr>
  <tr>
    <td>
	<a href="{$photo_next}" title="点击浏览下一张"><img src="{$filename}">
	<br>
    </td>
  </tr>
  <tr><td algin="center"> {$title} </td></tr>
	<tr><td> {$create_time} <br> {$remark} </td></tr>
  <tr><td><a href="{$photo_pre}">上一张</a> <a href="{$photo_next}">下一张</a></td></tr>
</table>

</td>
<td>
{$subComment}
</td></tr>
</table>

<?
$this->loadTmplate(TEMPLATE_PATH."public/foot.tpl.php");
?>
</div>
</body>
</html>
