<script language="javascript">
function c1(d) {
	document.deposit.deposit_type.value = d;
	document.deposit.submit();
	
}
function c2(p) {
	document.deposit.pay_type.value = p;
	document.deposit.submit();
}
</script>
<form method="post" action="?" name="deposit">
<input name="deposit_type" type="hidden" value="1" />
<input type="hidden" name="pay_type" value="ATM" />
<input type='hidden' name='module' value='member'>
<input type='hidden' name='action' value='deposit0'>
{$game}
<div id="dtype" style="display:block" align="center" style="width:520px; text-align:left">
<div style="width:520px"><img src="/images/index/ss01.gif" width="520" height="64"></div>

<!--
<table width="560" border="0" align="center" cellpadding="0" cellspacing="0">
<tr>
<td width=120 align=left><a href='#cd1' onclick="c1(1)"><img src="images/index/card_100.gif" border="0"></a><br><a href='#cd1' onclick="c1(1)"><img src="images/index/order3.gif" border="0"></a></td>
<td width=160 align=left valign=top style="font-size:9pt;">�Ƀ�ֵ�c�� <font color=red face="Century Gothic" size=3><b>10000</b></font> �c<p>&nbsp;<p>�؃r$<font color=blue face="Century Gothic" size=3><b><i>100</i></b></font></td>
<td width=120 align=left><a href='#cd2' onclick="c1(2)"><img src="images/index/card_300.gif" border="0"></a><br><a href='#cd2' onclick="c1(2)"><img src="images/index/order3.gif" border="0"></a></td>
<td width=160 align=left valign=top style="font-size:9pt;">�Ƀ�ֵ�c�� <font color=red face="Century Gothic" size=3><b>30000</b></font> �c<p>&nbsp;<p>�؃r$<font color=blue face="Century Gothic" size=3><b><i>300</i></b></font></td>
</tr>
<tr><td colspan=4><hr size=1></td></tr>
<tr>
<td width=120 align=left><a href='#cd3' onclick="c1(3)"><img src="images/index/card_500.gif" border="0"></a><br><a href='#cd3' onclick="c1(3)"><img src="images/index/order3.gif" border="0"></a></td>
<td width=160 align=left valign=top style="font-size:9pt;">�Ƀ�ֵ�c�� <font color=red face="Century Gothic" size=3><b>50000</b></font> �c<p>ֱ��С�W���꼉<p>�؃r$<font color=blue face="Century Gothic" size=3><b><i>500</i></b></font></td>
<td width=120 align=left><a href='#cd4' onclick="c1(4)"><img src="images/index/card_1000.gif" border="0"></a><br><a href='#cd4' onclick="c1(4)"><img src="images/index/order3.gif" border="0"></a></td>
<td width=160 align=left valign=top style="font-size:9pt;">�Ƀ�ֵ�c�� <font color=red face="Century Gothic" size=3><b>100000</b></font> �c<p>ֱ��С�W���꼉<p>�؃r$<font color=blue face="Century Gothic" size=3><b><i>1000</i></b></font></td>
</tr>
<tr><td colspan=4><hr size=1></td></tr>
<tr>
<td width=120 align=left><a href='#cd5' onclick="c1(5)"><img src="images/index/card_2000.gif" border="0"></a><br><a href='#cd5' onclick="c1(5)"><img src="images/index/order3.gif" border="0"></a></td>
<td width=160 align=left valign=top style="font-size:9pt;">�Ƀ�ֵ�c�� <font color=red face="Century Gothic" size=3><b>200000</b></font> �c<p>ֱ��С�W���꼉<p>�؃r$<font color=blue face="Century Gothic" size=3><b><i>2000</i></b></font></td>
<td width=120 align=left><a href='#cd6' onclick="c1(6)"><img src="images/index/card_3000.gif" border="0"></a><br><a href='#cd6' onclick="c1(6)"><img src="images/index/order3.gif" border="0"></a></td>
<td width=160 align=left valign=top style="font-size:9pt;">�Ƀ�ֵ�c�� <font color=red face="Century Gothic" size=3><b>300000</b></font> �c<p>ֱ�����ж��꼉<p>�؃r$<font color=blue face="Century Gothic" size=3><b><i>3000</i></b></font></td>
</tr>
<tr><td colspan=4><hr size=1></td></tr>
<tr>
<td width=120 align=left><a href='#cd7' onclick="c1(7)"><img src="images/index/card_5000.gif" border="0"></a><br><a href='#cd7' onclick="c1(7)"><img src="images/index/order3.gif" border="0"></a></td>
<td width=160 align=left valign=top style="font-size:9pt;">�Ƀ�ֵ�c�� <font color=red face="Century Gothic" size=3><b>500000</b></font> �c<p>ֱ���������꼉<p>�؃r$<font color=blue face="Century Gothic" size=3><b><i>5000</i></b></font></td>
<td width=120 align=left>&nbsp;</td>
<td width=160 align=left valign=top style="font-size:9pt;"> &nbsp;</td>
</tr>
<tr><td colspan=4><hr size=1></td></tr>
</table>
-->

<div style="font-size:9pt">
<!-- ���ݻ�� BEGIN -->
<table width="550" border="0" cellspacing="0" cellpadding="3" align=center>
<tr><td><img src="images/index/dis_title.gif" width="550" height="30"></td></tr>
</table>
<table width="550" border="0" cellspacing="0" cellpadding="3" align=center>
<tr>
<td width=120 align=center><a href='#cd2' onclick="c1(2)"><img src="images/index/card_300.gif" border=0></a></td>
<td width=310 valign=top>ُ�I�c����300Ԫ+��<span class="explain">3000</span>�c<br>ԭ��ֵ�c��<span class="explain">30000</span>�c�ټ���<span class="explain">3000</span>�c��</td>
<td width=120 align=center><a href='#cd2' onclick="c1(2)"><img src="images/index/order3.gif" border=0 alt="��Ҫ��ֵ"></a></td>
</tr>
<tr align=center>
<td colspan=3><hr size=1></td>
</tr>
<tr>
<td width=120 align=center><a href='#cd3' onclick="c1(3)"><img src="images/index/card_500.gif" border=0></a></td>
<td width=310 valign=top>ُ�I�c����500Ԫ+��<span class="explain">5000</span>�c<br>ԭ��ֵ�c��<span class="explain">50000</span>�c�ټ���<span class="explain">5000</span>�c��</td>
<td width=120 align=center><a href='#cd3' onclick="c1(3)"><img src="images/index/order3.gif" border=0 alt="��Ҫ��ֵ"></a></td>
</tr>
<tr align=center>
<td colspan=3><hr size=1></td>
</tr>
<tr>
<td width=120 align=center><a href='#cd4' onclick="c1(4)"><img src="images/index/card_1000.gif" border=0></a></td>
<td width=310 valign=top>ُ�I�c����1000Ԫ+��<span class="explain">10000</span>�c<br>ԭ��ֵ�c��<span class="explain">100000</span>�c�ټ���<span class="explain">10000</span>�c��</td>
<td width=120 align=center><a href='#cd4' onclick="c1(4)"><img src="images/index/order3.gif" border=0 alt="��Ҫ��ֵ"></a></td>
</tr>
</table>
<!-- ���ݻ�� END -->
<table width="550" border="0" cellspacing="0" cellpadding="3" align=center>
<tr><td><img src="images/index/card_title.gif" width="550" height="30"></td></tr>
</table>
<table width="550" border="0" cellspacing="0" cellpadding="2" align=center>
<tr>
<td width=120 align=center><a href='#cd1' onclick="c1(1)"><img src="images/index/card_100.gif" border=0></a></td>
<td width=155 rowspan="2" valign=top>�Ƀ�ֵ�c�� <span class="explain">10000</span> �c<br><br><br>�؃r<span class="explain">100</span>Ԫ</td>
<td width=120 align=center><a href='#cd2' onclick="c1(2)"><img src="images/index/card_300.gif" border=0></a></td>
<td width=155 rowspan="2" valign=top>�Ƀ�ֵ�c�� <span class="explain">30000</span> �c<br>�ټ���<span class="explain">3000</span>�c��<br><br>�؃r<span class="explain">300</span>Ԫ</td>
</tr>
<tr align=center>
<td><a href='#cd1' onclick="c1(1)"><img src="images/index/order3.gif" border=0 alt="��Ҫ��ֵ"></a></td>
<td><a href='#cd2' onclick="c1(2)"><img src="images/index/order3.gif" border=0 alt="��Ҫ��ֵ"></a></td>
</tr>
<tr align=center>
<td colspan=4 height=10><hr size=1></td>
</tr>
<tr>
<td width=120 align=center><a href='#cd3' onclick="c1(3)"><img src="images/index/card_500.gif" border=0></a></td>
<td width=155 rowspan="2" valign=top>�Ƀ�ֵ�c�� <span class="explain">50000</span> �c<br>�ټ���<span class="explain">5000</span>�c��<br><br>�؃r<span class="explain">500</span>Ԫ</td>
<td width=120 align=center><a href='#cd4' onclick="c1(4)"><img src="images/index/card_1000.gif" border=0></a></td>
<td width=155 rowspan="2" valign=top>�Ƀ�ֵ�c�� <span class="explain">100000</span> �c<br>�ټ���<span class="explain">10000</span>�c��<br><br>�؃r<span class="explain">1000</span>Ԫ</td>
</tr>
<tr align=center>
<td><a href='#cd3' onclick="c1(3)"><img src="images/index/order3.gif" border=0 alt="��Ҫ��ֵ"></a></td>
<td><a href='#cd4' onclick="c1(4)"><img src="images/index/order3.gif" border=0 alt="��Ҫ��ֵ"></a></td>
</tr>
<tr align=center>
<td colspan=4 height=10><hr size=1></td>
</tr>
<tr>
<td width=120 align=center><a href='#cd5' onclick="c1(5)"><img src="images/index/card_2000.gif" border=0></a></td>
<td width=155 rowspan="2" valign=top>�Ƀ�ֵ�c�� <span class="explain">200000</span> �c<br><br><br>�؃r<span class="explain">2000</span>Ԫ</td>
<td width=120 align=center><a href='#cd6' onclick="c1(6)"><img src="images/index/card_3000.gif" border=0></a></td>
<td width=155 rowspan="2" valign=top>�Ƀ�ֵ�c�� <span class="explain">300000</span> �c<br><br><br>�؃r<span class="explain">3000</span>Ԫ</td>
</tr>
<tr align=center>
<td><a href='#cd5' onclick="c1(5)"><img src="images/index/order3.gif" border=0 alt="��Ҫ��ֵ"></a></td>
<td><a href='#cd6' onclick="c1(6)"><img src="images/index/order3.gif" border=0 alt="��Ҫ��ֵ"></a></td>
</tr>
<tr align=center>
<td colspan=4 height=10><hr size=1></td>
</tr>
<tr>
<td width=120 align=center><a href='#cd7' onclick="c1(7)"><img src="images/index/card_5000.gif" border=0></a></td>
<td width=155 rowspan="2" valign=top>�Ƀ�ֵ�c�� <span class="explain">500000</span> �c<br><br><br>�؃r<span class="explain">5000</span>Ԫ</td>
<td width=120 align=center>&nbsp;</td>
<td width=155 rowspan="2" valign=top>&nbsp;</td>
</tr>
<tr align=center>
<td><a href='#cd7' onclick="c1(7)"><img src="images/index/order3.gif" border=0 alt="��Ҫ��ֵ"></a></td>
<td>&nbsp;</td>
</tr>
<tr align=center>
<td colspan=4 height=10><hr size=1></td>
</tr>
</table>
</div>

</form>
