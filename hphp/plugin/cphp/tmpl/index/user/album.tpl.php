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
	<a href="{$upload_url}">上传相片</a> <a href="{$del_url}" onClick="return confirm('删除操作将不可恢复!\n请确认删除此相簿!')">删除相簿</a><br>
	<!-- <font size=2>点阅数:今天{$view_today}次;总共{$view_count}次; -->
	相片:{$p_count}张<br>
	创建时间:{$create_time}</font><br>

	<-- 编辑相簿属性 -->
	<div id='editAlbm' >
	<form name="edit_album" method="POST" action="<?=url("?module=user&action=ea")?>">
相簿名:	<input type='text' name='name' maxlength=18 onMouseOver='this.focus()' value='{$name}'><br>
属性:	<select  name='attr'><option selected value='1'>公开</option>
		<option value='2'>需要密码</option>
		<option value='3'>不公开</option></select>
相簿分类:<select name='sort'>{$sort_list}</select><br>
密码:	<input type='password' name='pwd' maxlength=16 value='{$password}'><br>
签名:	<textarea name='sign' rows=4 cols=40 >{$sign}</textarea>
	<input type='hidden' name='aid' value='{$aid}'>
	<input type='hidden' name='type' value='modif'>
	<input type='submit' name='submit' value="提交">
	<input type=hidden name='module' value='user'>
	<input type=hidden name='action' value='ea'>
	</form>
	</div>
</td></tr>
<tr><td>
<-- 显示相簿里的相片预览 对应相应的 album.list.tpl.php 模板 --><br>
{$subPhoto}
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
