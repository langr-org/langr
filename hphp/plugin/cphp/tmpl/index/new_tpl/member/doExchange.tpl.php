<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<meta name="Keywords" content="麻將大悶鍋,台灣麻將,16張麻將,麻將,麻將遊戲,遊戲,線上麻將,打麻將,玩麻將,小遊戲,好玩遊戲,好玩遊戲區,免費遊戲,免費線上遊戲,免費麻將遊戲下載,免費麻將,網路遊戲,online game,益智遊戲,遊戲下載,打發時間" /> 
<meta name="Description" content="提供線上免費麻將遊戲，打麻將不怕3缺1，免等，永遠不寂寞！畫面清晰，遊戲流暢，港台明星模仿秀，真人錄音超  ！" />
<meta name="author" content="艾德網科技股份有限公司" /> 
<script language="javascript" src="/images/index/majong.js"></script>
<style type="text/css">@import url(/images/index/majong.css);</style>
<title>{$webSiteTitle}</title>
</head>
<body onload="MM_preloadImages('images/index/menu_01-over.gif','images/index/menu_02-over.gif','images/index/menu_04-over.gif','images/index/menu_05-over.gif','images/index/menu_06-over.gif','images/index/menu_07-over.gif','images/index/menu_08-over.gif')">
<center>
<!-- head -->
{$header}
<!-- end head -->
<table border="0" cellpadding="0" cellspacing="0" width="950" align="center">
<tr>
<!-- left -->
<td width="175" bgcolor="#08aa08" valign="top">
<?
$this->loadTmplate(TEMPLATE_PATH."public/twmj/member_left.tpl.php");
echo "<br />";
$this->loadTmplate(TEMPLATE_PATH."public/twmj/left.tpl.php");
?>
<br />

<img src="images/index/mj.gif" width="160" height="160" alt="" style="vertical-align:text-bottom" />
</td>
<!-- end left -->
<!-- middle -->
<td width="775" bgcolor="#FFFFFF" valign="top">
<div style="padding-left:5px">
{$body}
</div>
</td>
<!-- end middle -->
</tr>
<tr height="20"><td bgcolor="#08aa08"></td><td bgcolor="#FFFFFF"></td></tr>
</table>
<!-- foot -->
{$footer}
<!-- end foot -->
</center>
</body>
</html>
