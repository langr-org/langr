<form name="form1" method="post" action="?">
<img src="images/index/register_2.gif" width="550" height="49" />
<table width="550" border="0" cellspacing="0" cellpadding="3" align=center style="font-size: 9pt;">
<tr><th>&nbsp;</th><th><font color="red" size="+1">����ͣ�����]�Ծ���3000�c��</font></th></tr>
<tr>
<td width=100 align=right>�[��̖��</td>
<td width=500><input name="user" type="text" size="25" maxlength="12" style="font-size: 9pt;"> <font color=red>��</font>��<input type="button" name="check_user" onClick="if(this.form.user.value != '') {window.open('?module=member&action=CheckRepeat&user='+encodeURIComponent(this.form.user.value),'','height=300, width=600,toolbar=no,scrollbars=no,menubar=no,resizable=1');} else {alert('Ոݔ�뎤̖��');}" value="�z�y��̖" style="font-size: 9pt;"></td>
</tr>
<tr>
<td>&nbsp;</td>
<td style="color: #707070">Ոݔ�� 4 �� 12 ����Ԫ��Ӣ����ĸ���֡�</td>
</tr>
<tr>
<td align=right>�[���ܴa��</td>
<td><input name="pwd" type="password" size="25" maxlength="12" style="font-size: 9pt;"> <font color=red>��</font></td>
</tr>
<tr>
<td>&nbsp;</td>
<td style="color: #707070">Ոݔ�� 4 �� 12 ����Ԫ��Ӣ����ĸ���֣�Ոע��Ӣ�Ĵ�С����</td>
</tr>
<tr>
<td align=right>�_�J�ܴa��</td>
<td><input name="check_pwd" type="password" size="25" style="font-size: 9pt;"> <font color=red>��</font></td>
</tr>
<tr>
<td>&nbsp;</td>
<td style="color: #707070">Ո��ݔ��һ���ܴa�������e�`��</td>
</tr>
<td align=right>�[�򕿷Q��</td>
<td><input name="nike_name" type="text" size="25" maxlength="12" style="font-size: 9pt;"> <font color=red>��</font>��<input type="button" name="check_nikename" onClick="if(this.form.nike_name.value != '') {window.open('?module=member&action=CheckRepeat&nike_name='+encodeURIComponent(this.form.nike_name.value),'','height=300, width=600,toolbar=no,scrollbars=no,menubar=no,resizable=1');} else {alert('Ոݔ�땿�Q��');}" value="�z�y���Q" style="font-size: 9pt;">(����ʹ��&%()&lt;&gt;/\'")
</td>
</tr>
<tr>
<td>&nbsp;</td>
<td style="color: #707070">Ոݔ�� 2 �� 12 ����Ԫ��Ӣ����ĸ���֣�������� 6 ���֡�</td>
</tr>
<tr>
<td width=100 align=right>�挍������</td>
<td width=500><input name="name" type="text" size="25" style="font-size: 9pt;"></td>
</tr>
<tr>
<td width=100 align=right>�ԡ����e��</td>
<td width=500><input name="sex" type="radio" value="B" checked style="font-size: 9pt;"> ���ǎ��硡<input name="sex" type="radio" value="G" style="font-size: 9pt;"> ������Ů</td>
</tr>
<tr>
<td style="color: #ff0000" colspan="2">̨���؅^��Ո�����C̖�a���ɣ��ɲ����o��̖�a��������ʿՈ�������o��̖�a��</td>
</tr>
<tr>
<td width=100 align=right> </td>
<td width=500><input name="area" type="radio" value="T" checked style="font-size: 9pt;"> ̨���؅^��<input name="area" type="radio" value="O" style="font-size: 9pt;"> ������ʿ <font color=red>��(������ʿٛ�� 1000 �c)</font></td>
</tr>
<tr>
<td width=100 align=right>����C̖��<br>(���o��)��</td>
<td width=500><input name="id_card" type="text" size="25" style="font-size: 9pt;" maxlength="10"> <font color=red>��(�Ա���ٛƷ�B�j��)</font></td>
</tr>
<!-- tr>
<td width=100 align=right>�� �o�գ�</td>
<td width=500><input name="overseas_card" type="text" size="25" style="font-size: 9pt;" maxlength="10"> <font color=red>��(������ʿٛ�� 1000 �c)</font></td>
</tr -->
<tr>
<td width=100 align=right>�������գ�</td>
<td width=500><input name="year" type="text" size="6" style="font-size: 9pt;"> �� <select name="month" style="font-size:9pt;">
                        <option value=-1 style="font-size:9pt;">--</option>
                        <option value=01 style="font-size:9pt;">01</option>
                        <option value=02 style="font-size:9pt;">02</option>
                        <option value=03 style="font-size:9pt;">03</option>
						<option value=04 style="font-size:9pt;">04</option>
                        <option value=05 style="font-size:9pt;">05</option>
                        <option value=06 style="font-size:9pt;">06</option>
                        <option value=07 style="font-size:9pt;">07</option>
                        <option value=08 style="font-size:9pt;">08</option>
                        <option value=09 style="font-size:9pt;">09</option>
                        <option value=10 style="font-size:9pt;">10</option>
                        <option value=11 style="font-size:9pt;">11</option>
                        <option value=12 style="font-size:9pt;">12</option>
                      </select> �� 
                      <select name="day" style="font-size:9pt;">
                        <option value=-1 style="font-size:9pt;">--</option>
                        <option value=01 style="font-size:9pt;">01</option>
                        <option value=02 style="font-size:9pt;">02</option>
                        <option value=03 style="font-size:9pt;">03</option>
			<option value=04 style="font-size:9pt;">04</option>
                        <option value=05 style="font-size:9pt;">05</option>
                        <option value=06 style="font-size:9pt;">06</option>
                        <option value=07 style="font-size:9pt;">07</option>
                        <option value=08 style="font-size:9pt;">08</option>
                        <option value=09 style="font-size:9pt;">09</option>
                        <option value=10 style="font-size:9pt;">10</option>
                        <option value=11 style="font-size:9pt;">11</option>
                        <option value=12 style="font-size:9pt;">12</option>
                        <option value=01 style="font-size:9pt;">13</option>
                        <option value=02 style="font-size:9pt;">14</option>
                        <option value=03 style="font-size:9pt;">15</option>
			<option value=04 style="font-size:9pt;">16</option>
                        <option value=05 style="font-size:9pt;">17</option>
                        <option value=06 style="font-size:9pt;">18</option>
                        <option value=07 style="font-size:9pt;">19</option>
                        <option value=08 style="font-size:9pt;">20</option>
                        <option value=09 style="font-size:9pt;">21</option>
                        <option value=10 style="font-size:9pt;">22</option>
                        <option value=11 style="font-size:9pt;">23</option>
                        <option value=12 style="font-size:9pt;">24</option>
                        <option value=03 style="font-size:9pt;">25</option>
			<option value=04 style="font-size:9pt;">26</option>
                        <option value=05 style="font-size:9pt;">27</option>
                        <option value=06 style="font-size:9pt;">28</option>
                        <option value=07 style="font-size:9pt;">29</option>
                        <option value=08 style="font-size:9pt;">30</option>
                        <option value=09 style="font-size:9pt;">31</option>
                      </select> ��
                      </td>
</tr>
<tr> 
  <td align="right">�ǡ�����:</td>
  <td> 
  {$constellation}
  </td>
</tr>
<tr>
<td width=100 align=right>����]����</td>
<td width=500><input name="e_mail" type="text" size="25" style="font-size: 9pt;"> <font color=red>��(�Ա���ٛƷ�B�j��)</font></td>
</tr>
<tr>
<td width=100 align=right>�j�Ԓ��</td>
<td width=500><input name="tel" type="text" size="25" style="font-size: 9pt;"> <font color=red>��(�Ա���ٛƷ�B�j��)</font></td>
</tr>
<tr> 
  <td align="right">�j��ַ��</td>
  <td > 
   <input name="addr" type="text" tabindex="2" size=50 maxlength="200">
  </td>
 </tr>
<tr>
<td width=100 align=right>�]����C��</td>
<td width=500><input name="verifyCode" type="text" size="14" style="font-size: 9pt;">��<img src="<?=url("?module=member&action=verifyCode&t=gif")?>" align="absbottom"> <font color=red>��</font></td>
</tr>
<tr>
<td align=right valign=top>�[���ɫ��</td>
<td style="color: #707070">
<table width="350" border="0" cellspacing="1" cellpadding="0" bgcolor=#888888>
<tr bgcolor=#FFFFFF align=center>
<td><input name='RoleIcon' type='radio' value='9' {$role_9}></td>
<td><input name='RoleIcon' type='radio' value='8' {$role_8}></td>
<td><input name='RoleIcon' type='radio' value='7' {$role_7}></td>
</tr>
<tr bgcolor=#FFFFFF align=center height=82>
<td align="center"><img src="images/index/a09.gif" width="80" height="93"></td>	
<td align="center"><img src="images/index/a08.gif" width="80" height="93"></td>
<td align="center"><img src="images/index/a07.gif" width="80" height="93"></td>
</tr>
<tr bgcolor=#FFFFFF align=center>
<td><input name="RoleIcon" type="radio" value="6" {$role_6}></td>
<td><input name="RoleIcon" type="radio" value="1" {$role_1}></td>
<td><input name="RoleIcon" type="radio" value="2" {$role_2}></td>
</tr>
<tr bgcolor=#FFFFFF align=center height=82>
<td align="center"><img src="images/index/a06.gif" width="80" height="93"></td>
<td align="center"><img src="images/index/a01.gif" width="80" height="93"></td>
<td align="center"><img src="images/index/a02.gif" width="80" height="93"></td>
</tr>
<tr bgcolor=#FFFFFF align=center>
<td><input name="RoleIcon" type="radio" value="3" {$role_3}></td>
<td><input name="RoleIcon" type="radio" value="4" {$role_4}></td>
<td><input name="RoleIcon" type="radio" value="5" {$role_5}></td>
</tr>
<tr bgcolor=#FFFFFF align=center height=82>
<td align="center"><img src="images/index/a03.gif" width="80" height="93"></td>
<td align="center"><img src="images/index/a04.gif" width="80" height="93"></td>
<td align="center"><img src="images/index/a05.gif" width="80" height="93"></td>
</tr>
</table>
</td>
</tr>
<tr>
<td>&nbsp;</td>
<td style="color: #707070">
<input name="enter" type="hidden" value="�ͳ��]���Y��" style="font-size: 12pt;">
<!--<input type="reset" name="button" value="�������" style="font-size: 12pt;">-->
<a href="#" onclick="document.form1.reset()"><img src="images/index/register_2a2.gif" border="0" /></a>
<input type="hidden" name="module" value="member">
<input type="hidden" name="action" value="register">
{$game}
<input type="image" src="images/index/register_2a1.gif" />
</td>
</tr>
<tr>
<td colspan="2"><font color="red">���粻���]�ԣ�Ո�B�j�ͷ���<br>�����Ԓ��(02)2278-7789   ���Lһ���L������09:00~����20:00��<br>�������䣺<a href="mailto:service@betcity.com.tw"><u>service@betcity.com.tw</u></a></font></td>
</tr>
</table>
