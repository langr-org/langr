<!-- Bug �؈� -->
<form name="form1" method="post" action="?">
<table width="550" border="0" cellspacing="0" cellpadding="5" align=center style="font-size: 9pt;">
<tr>
<td style="font-size:9pt;line-height:1.6em">
<font size=5><b>�j�ͷ��ˆT</b></font><br>
���x��������Ć��}�����h���ͷ��ˆT���M��o���ظ���
</td>
</tr>
</table>
<table width="550" border="0" cellspacing="1" cellpadding="3" align=center>
<tr style="font-size:9pt;">
<td width=90 align=right>
<font color=red>��</font> ���T��̖��
</td>
<td width=460>
<input type="text" name="User" size="20" style="font-size:10pt"> 
</td>
</tr>
<tr style="font-size:9pt;">
<td width=90 align=right>
<font color=red>��</font> ������䣺
</td>
<td width=460>
<input type="text" name="Email" size="20" style="font-size:10pt">
</tr>
<tr style="font-size:9pt;">
<td width=90 align=right>
�j�Ԓ��
</td>
<td width=460>
<input type="text" name="Tel" size="20" style="font-size:10pt">
</tr>
<tr style="font-size:9pt;">
<td width=90 align=right>
Bug ���
</td>
<td width=460>
<select name="Sort" style="font-size:9pt;">
<option value="advise" style="font-size:9pt;">�[���h</option>
<option value="problem" style="font-size:9pt;">�[���}</option>
<option value="install" style="font-size:9pt;">���b���}</option>
</select>
</tr>
<tr style="font-size:9pt;">
<td width=90 align=right>
���Iϵ�y��
</td>
<td width=460>
<select name="System" style="font-size:9pt;">
<option value="" style="font-size:9pt;">Ո �x ��</option>
<option value="win98" style="font-size:9pt;">Windows 98/98SE</option>
<option value="winme" style="font-size:9pt;">Windows ME</option>
<option value="win20" style="font-size:9pt;">Windows 2000</option>
<option value="winxp" style="font-size:9pt;">Windows XP</option>
<option value="winvista" style="font-size:9pt;">Windows Vista</option>
</select>
</tr>
<tr style="font-size:9pt;">
<td width=90 align=right>
�ϾW��ʽ��
</td>
<td width=460>
<select name="Net" style="font-size:9pt;">
<option value="" style="font-size:9pt;">Ո �x ��</option>
<option value="adsl" style="font-size:9pt;">ADSL</option>
<option value="cable" style="font-size:9pt;">�о��ҕ Cable</option>
<option value="bar" style="font-size:9pt;">�W���ϾW</option>
<option value="modle" style="font-size:9pt;">�����C�ܽ� 56K</option>
<option value="wireless" style="font-size:9pt;">3G �o���ϾW</option>
</select>
</tr>
<tr style="font-size:9pt;">
<td width=90 align=right>
<font color=red>��</font> ���Ԙ��}��
</td>
<td width=460>
<input type='text' name='Title'>
</tr>
<tr style="font-size:9pt;">
<td width=90 align=right valign=top>
<font color=red>��</font> ���ԃ��ݣ�
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
<input type="submit" value="����ͳ�" style="font-size:9pt;">
<input type="reset" value="�������" style="font-size:9pt;">
<input type='hidden' name='module' value='twmj'>
<input type='hidden' name='action' value='bug'>
</td>
</tr>
</table>
</form>