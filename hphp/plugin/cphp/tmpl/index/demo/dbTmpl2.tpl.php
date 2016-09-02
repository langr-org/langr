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
<p>{$publicMsg}</p>
<p>以下是表格输出的内容：</p>
<table width="400">
<tr>
<td>编号</td>
<td>姓名</td>
<td>金额</td>
</tr>
<?while (list($key,$val)=@each($this->TDat)) {?>
<tr>
<td>{$val[id]}</td>
<td>{$val[name]}</td>
<td>{$val[money]}</td>
</tr>
<?}?>
</table>
</body>
</html>