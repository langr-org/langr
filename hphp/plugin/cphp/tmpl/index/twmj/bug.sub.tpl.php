<!-- Bug 回報 -->
<form name="form1" method="post" action="?">
<table width="550" border="0" cellspacing="0" cellpadding="5" align=center style="font-size: 9pt;">
<tr>
<td style="font-size:9pt;line-height:1.6em">
<font size=5><b>聯絡客服人員</b></font><br>
感謝您所提出的問題及建議，客服人員會盡快給您回覆。
</td>
</tr>
</table>
<table width="550" border="0" cellspacing="1" cellpadding="3" align=center>
<tr style="font-size:9pt;">
<td width=90 align=right>
<font color=red>＊</font> 會員帳號：
</td>
<td width=460>
<input type="text" name="User" size="20" style="font-size:10pt"> 
</td>
</tr>
<tr style="font-size:9pt;">
<td width=90 align=right>
<font color=red>＊</font> 電子信箱：
</td>
<td width=460>
<input type="text" name="Email" size="20" style="font-size:10pt">
</tr>
<tr style="font-size:9pt;">
<td width=90 align=right>
聯絡電話：
</td>
<td width=460>
<input type="text" name="Tel" size="20" style="font-size:10pt">
</tr>
<tr style="font-size:9pt;">
<td width=90 align=right>
Bug 分類：
</td>
<td width=460>
<select name="Sort" style="font-size:9pt;">
<option value="advise" style="font-size:9pt;">遊戲建議</option>
<option value="problem" style="font-size:9pt;">遊戲問題</option>
<option value="install" style="font-size:9pt;">安裝問題</option>
</select>
</tr>
<tr style="font-size:9pt;">
<td width=90 align=right>
作業系統：
</td>
<td width=460>
<select name="System" style="font-size:9pt;">
<option value="" style="font-size:9pt;">請 選 擇</option>
<option value="win98" style="font-size:9pt;">Windows 98/98SE</option>
<option value="winme" style="font-size:9pt;">Windows ME</option>
<option value="win20" style="font-size:9pt;">Windows 2000</option>
<option value="winxp" style="font-size:9pt;">Windows XP</option>
<option value="winvista" style="font-size:9pt;">Windows Vista</option>
</select>
</tr>
<tr style="font-size:9pt;">
<td width=90 align=right>
上網方式：
</td>
<td width=460>
<select name="Net" style="font-size:9pt;">
<option value="" style="font-size:9pt;">請 選 擇</option>
<option value="adsl" style="font-size:9pt;">ADSL</option>
<option value="cable" style="font-size:9pt;">有線電視 Cable</option>
<option value="bar" style="font-size:9pt;">網咖上網</option>
<option value="modle" style="font-size:9pt;">數據機撥接 56K</option>
<option value="wireless" style="font-size:9pt;">3G 無線上網</option>
</select>
</tr>
<tr style="font-size:9pt;">
<td width=90 align=right>
<font color=red>＊</font> 留言標題：
</td>
<td width=460>
<input type='text' name='Title'>
</tr>
<tr style="font-size:9pt;">
<td width=90 align=right valign=top>
<font color=red>＊</font> 留言內容：
</td>
<td width=460>
<textarea name="Content" cols="50" rows="4" maxlength="1000" style="font-size:9pt"></textarea>
</tr>
<tr style="font-size:9pt;">
<td width=90 align=right>
&nbsp;
</td>
<td width=460>
{$game}
<input type="submit" value="填好送出" style="font-size:9pt;">
<input type="reset" value="清除重填" style="font-size:9pt;">
<input type='hidden' name='module' value='twmj'>
<input type='hidden' name='action' value='bug'>
</td>
</tr>
</table>
</form>