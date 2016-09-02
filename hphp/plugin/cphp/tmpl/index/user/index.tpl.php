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

<!-- 相册首页 -->

<table width="800" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td>
	{$user}的相簿 ...<hr><br>
	您的相簿公开地址:<a href="{$user_url}">{$user_url}</a><br>
	已使用空间:共 {$a_count} 本相簿, 使用 {$size}
    </td>
<!-- 用户边栏导航条 -->
    <td width="224" rowspan=2 border="1" valign="top">
<?$this->loadTmplate(TEMPLATE_PATH."public/userLeft.tpl.php");?>
    </td>
  </tr>
  <tr>
    <td>
<!-- 用户相簿管理 对应相应的 index.TopList.tpl.php 模板 -->
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
