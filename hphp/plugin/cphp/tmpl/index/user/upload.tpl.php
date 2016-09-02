<!-- 用户相簿管理后台模板 -->
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

<!-- 上传相片 -->

<table width="800" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td>
	{$user}的相簿 >> 上传相片...<hr><br>

    </td>
  </tr>
  <tr>
    <td>
<-- 上传相片 --><br>
单张相片允许大小: 2MB, 允许类型: gif|jpg|png|bmp
<form method="POST" name="form01" action="?" enctype="multipart/form-data">
<table>
	<tr><td>
	</td></tr>
	<tr><td >
	选择相簿:<select  name='aid'>{$album_list}</select><br>
	选择相片:<Br>
<input type="file" name="file[]" size="40"><br>
<input type="file" name="file[]" size="40"><br>
<input type="file" name="file[]" size="40"><br>
<input type="file" name="file[]" size="40"><br>
<input type="file" name="file[]" size="40"><br>
<input type="file" name="file[]" size="40"><br>
	</td>
	</tr>
</table>
	<input type='hidden' name='type' value='upload'>
	<input type='submit' name='submit' value="上传相片">
	<input type=hidden name='module' value='user'>
	<input type=hidden name='action' value='upload'>
</form>

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
