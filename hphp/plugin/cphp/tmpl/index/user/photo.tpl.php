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
	<a href="{$set_cover}">设为相簿封面</a> <a href="{$set_portrait}">设为头像</a>
</td></tr>
<form name='form01' method="POST" action="?">
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr><td><a href="{$photo_pre}">上一张</a> <a href="{$photo_next}">下一张</a></td></tr>
  <tr>
    <td>
	<img src="{$filename}">
	<br>
    </td>
  </tr>
  <tr><td algin="center">相片名:<input size=60 name="title"  maxlength="100" type="text" value="{$title}"> </td></tr>
	<tr><td>创建时间: {$create_time} <br>
相片说明:<textarea name='remark' rows=4 cols=40 >{$remark}</textarea>
<input type='submit' name='submit' value='提交修改'>
<input type=hidden name='pid' value='{$pid}'>
<input type=hidden name='type' value='modif'>
<input type=hidden name='module' value='user'>
<input type=hidden name='action' value='ep'>
</td></tr>
  <tr><td><a href="{$photo_pre}">上一张</a> <a href="{$photo_next}">下一张</a></td></tr>
</table>
</form>
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
