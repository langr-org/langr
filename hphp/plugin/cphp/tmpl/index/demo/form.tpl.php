<html>
<head>
<title>{$webSiteTitle}</title>
<meta name="Generator" content="editplus">
<meta name="Author" content="kinger">
<meta name="Keywords" content="">
<meta name="Description" content="">
<meta http-equiv="Content-Type" content="Text/Html; charset=gb2312">
<link rel="StyleSheet" href="../../../include/css/index.css" type="Text/css">
</head>
<body>
<?
$this->loadTmplate(TEMPLATE_PATH."public/head.tpl.php");
?>
<p>{$publicMsg}</p>
{$noteTagStart}
<p>��������ǣ�{$name}</p>
<p>����ID�ǣ�{$id}</p>
{$noteTagEnd}
<p>����ʾ��</p>
<form name="form1" action="?" method="post">
	�����룺
	<input name="name" type="text" id="name">
	<input name="Submit" type="Submit" id="Submit" value="ȷ��">
	<input name="module" type="hidden" id="module" value="demo">
	<input name="action" type="hidden" id="action" value="form">
	<input name="id" type="hidden" id="id" value="1234">
</form>
<?
$this->loadTmplate(TEMPLATE_PATH."public/foot.tpl.php");
?>
</body>
</html>