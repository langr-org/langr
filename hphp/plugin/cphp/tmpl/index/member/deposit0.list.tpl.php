<script language="javascript">
function c2(p) {
	document.deposit.pay_type.value = p;
	document.deposit.submit();
}
</script>
<form method="post" action="?" name="deposit">
<input name="deposit_type" type="hidden" value="{$deposit_type}" />
<input type="hidden" name="pay_type" value="ATM" />
<input type='hidden' name='module' value='member'>
<input type='hidden' name='action' value='deposit'>
{$game}
<div id="ptype" style="width:520px; text-align:left">
<div><img src="images/index/ss02.gif" width="520" height="64"></div>
<table width="508" border="0" align="center" cellpadding="0" cellspacing="0">
<tr>
<td width=239 align=left><a href="#p1" onclick="c2('ATM')"><IMG SRC="images/index/buy_01.gif" WIDTH="239" HEIGHT="49" BORDER="0" ALT=""></a></td>
<td width=30 align=left>&nbsp;</td>
<td width=239 align=left><a href="#p2" onclick="c2('CreditCard')"><IMG SRC="images/index/buy_02.gif" WIDTH="239" HEIGHT="49" BORDER="0" ALT=""></a></td>
</tr>
<tr>
<td align=left valign=top style="font-size:9pt;line-height:1.5"><a href="#p1" onclick="c2('ATM')"><font size=3>ATM �D�����Mُ�I<font color=red><b>(���r��ֵ)</b></font></font></a><br>24С�rATM�D��ُ�I���D����10��20�����ϵ�y���ԄӃ�ֵ��</td>
<td align=left>&nbsp;</td>
<td align=left valign=top style="font-size:9pt;line-height:1.5"><a href="#p2" onclick="c2('CreditCard')"><font size=3>���ÿ�����ˢ��ӆُ<font color=red><b>(���r��ֵ)</b></font></font></a><br>24С�r���ϰ�ȫ���ܽ��ף��ڙ�ɹ���ϵ�y���ԄӃ�ֵ��</td>
<tr><td colspan=3><hr size=1></td></tr>
{$hinet3a1}
</table>


</form>