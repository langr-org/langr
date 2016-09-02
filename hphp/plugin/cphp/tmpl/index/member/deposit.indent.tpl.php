<script language='javascript' src='images/index/js/prototype.js'></script>
<script language='javascript' src='images/index/js/scriptaculous.js'></script>
<script language=javascript>
function back(url) {window.location.replace(url);}

function payhelp(n)
{
	var url ='tmpl/index/public/pay_help/pay_Hinet3A.'+n+'.tpl.php';
	var help = new Ajax.Updater('pay_help',url, {asynchronous:true, evalScripts:true, requestHeaders:['X-Update', 'pay_help']});
}
</script>
<!--
<form name="form1" method="post" action="?">
<table algin=cneter>
	<tr>
	<td>
	<table>
	<tr><th align="right">您選擇的商品: </th><td> 點數加值 {$point} 點 </td></tr>
	<tr><th align="right">金額: </th><td>{$money} 元 </td></tr>
	<tr><th align="right">付款方式: </th><td>{$pay_type_title} {$tel_type}</td></tr>
	<tr><td colspan="2" align="center"><b>本商品附赠</b></td></tr>
	<tr><td colspan="2">{$present}</td></tr>
	</table><br>
	<input type="hidden" name="UID" value="{$UID}">
	<input type="hidden" name="DepositType" value="{$deposit_type}">
	<input type="hidden" name="PayType" value="{$pay_type}">
	<input type="hidden" name="module" value="member">
	<input type="hidden" name="action" value="pay">
	{$game}
	<input type="submit" name="enter" value="下一步"> &nbsp;
	<input type="reset" name="reset" value="返回" OnClick="return history.back();">
	</td>
	</tr>
</table>
</form>
-->
<form name="form1" method="post" action="?">
<input type="hidden" name="UID" value="{$UID}">
<input type="hidden" name="DepositType" value="{$deposit_type}">
<input type="hidden" name="PayType" value="{$pay_type}">
<input type="hidden" name="module" value="member">
<input type="hidden" name="action" value="pay">
{$game}
<input type="hidden" name="enter" value="下一步">
<table width="550" border="0" cellspacing="0" cellpadding="0">
<tr>
<td align=center><img src="images/index/ss02.gif" width=520 height=64 border=0></td>
</tr>
</table>
&nbsp;<br>
<table width="550" border="0" cellspacing="0" cellpadding="0">
<tr>
<td width=90><img src="images/index/{$pay_type_pic}" width=73 height=70 border=0></td>
<td style="font-size:9pt; line-height:1.5em;" valign=top align='left'>{$pay_type_title}</td>
</tr>
</table>
&nbsp;<br>
<table width="550" border="0" cellspacing="1" cellpadding="0" bgcolor=#486a00 style="font-size:12pt;line-height:1.5em">
<tr bgcolor=#a4f000 align=center>
<td width=225>商品名稱</td>
<td width=100>點數</td>
<td width=100>金額</td>
<td width=125>贈送</td>
</tr>
<tr bgcolor=#FFFFFF align=center>
<td>麻將大悶鍋遊戲點數</td>
<td>{$point}</td>
<td>{$money}</td>
<td style="font-size:9pt">{$present}</td>
</tr>
{$tel_type}
<tr bgcolor=#FFFFFF>
<td colspan='4'>
{$invoiceInfo}
</td>
</tr>
</table><br>
&nbsp;<br>
<table width="550" border="0" cellspacing="0" cellpadding="0">
<tr>
<td align=center><a href="#" onclick="return history.back();"><img src="images/index/prev.gif" width=125 height=44 border="0"></a>　　<input type='image' src="images/index/next.gif" width=125 height=44></td>
</tr>
</table>
</form>
<br>
<div id='pay_help' name='pay_help' style="width:600px;align:left">{$pay_help}</div>


