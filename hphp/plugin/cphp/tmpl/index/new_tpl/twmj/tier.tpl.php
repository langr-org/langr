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
<div style="padding-left:5px"><br>
<script language='javascript' src='/images/index/js/prototype.js'></script>
<script language='javascript' src='/images/index/js/scriptaculous.js'></script>
<script language="javascript">
function gettier(url_a) {
	if(url_a != "") {
		new Ajax.Updater('tier_sort',url_a, {asynchronous:true, evalScripts:true, requestHeaders:['X-Update', 'tier_sort']});
	}
}
</script>
<center><a href="/?module=twmj&action=event" target=_blank><img src="/event2/468x60_3.gif" width=468 height=60 border=0></a></center>
&nbsp;
<table width="520" border="0" cellspacing="1" cellpadding="2" bgcolor=#486a00 align=center id="tier">
<tr bgcolor=#a4f000 align=center>
<td width=55><a href="#Win_p" onclick="gettier('<?=url("?module=twmj&action=tier&tier=Win_p&show_type=ajax")?>')">贏牌率</a></td>
<td width=55><a href="#Lose_p" onclick="gettier('<?=url("?module=twmj&action=tier&tier=Lose_p&show_type=ajax")?>')">輸牌率</a></td>
<td width=55><a href="#Win" onclick="gettier('<?=url("?module=twmj&action=tier&tier=Win&show_type=ajax")?>')">贏牌數</a></td>
<td width=55><a href="#Lost" onclick="gettier('<?=url("?module=twmj&action=tier&tier=Lose&show_type=ajax")?>')">輸牌數</a></td>
<td width=55><a href="#Zhimo_p" onclick="gettier('<?=url("?module=twmj&action=tier&tier=Zhimo_p&show_type=ajax")?>')">自摸率</a></td>
<td width=55><a href="#Zhimo" onclick="gettier('<?=url("?module=twmj&action=tier&tier=Zhimo&show_type=ajax")?>')">自摸數</a></td>
<td width=55><a href="#Hu_p" onclick="gettier('<?=url("?module=twmj&action=tier&tier=Hu_p&show_type=ajax")?>')">胡牌率</a></td>
<td width=55><a href="#Hu" onclick="gettier('<?=url("?module=twmj&action=tier&tier=Hu&show_type=ajax")?>')">胡牌數</a></td>
<td width=55><a href="#Gun_p" onclick="gettier('<?=url("?module=twmj&action=tier&tier=Gun_p&show_type=ajax")?>')">放槍率</a></td>
<td width=55><a href="#Gun" onclick="gettier('<?=url("?module=twmj&action=tier&tier=Gun&show_type=ajax")?>')">放槍數</a></td>
</tr>
<tr bgcolor=#a4f000 align=center>
<td width=55><a href="#WinPoint" onclick="gettier('<?=url("?module=twmj&action=tier&tier=WinPoint&show_type=ajax")?>')">贏點數</a></td>

<td width=55><a href="#Playround" onclick="gettier('<?=url("?module=twmj&action=tier&tier=Playround&show_type=ajax")?>')">總牌局</a></td>
<td width=55><a href="#Escape" onclick="gettier('<?=url("?module=twmj&action=tier&tier=Escape&show_type=ajax")?>')">落跑數</a></td>
<td width=55>&nbsp;</td>
<td width=55>&nbsp;</td>
<td width=55>&nbsp;</td>
<td width=55>&nbsp;</td>
<td width=55>&nbsp;</td>
<td width=55>&nbsp;</td>
<td width=55>&nbsp;</td>
</tr>
</table>
<div id="tier_sort">
{$body}
</div>
<br />
<font color="#FF0000">註：局數超過 100 局，等級升至小學六年級，才會計入排行榜</font>
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
