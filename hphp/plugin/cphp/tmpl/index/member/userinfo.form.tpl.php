<form name="form1" method="post" action="?">
<table width="550" border="0" cellspacing="0" cellpadding="3" align=center style="font-size: 9pt;">
<tr>
<td width=100 align=right>�[��̖��</td>
<td width=500>{$Account}</td>
</tr>

<tr>
	<td width=100 align=right>����C��̖��</td>
<td width=500>{$IdCard}{$IdCard_c}</td>
</tr>
<tr>
	<td width=100 align=right>�[�򕿷Q��</td>
<td width=500><input name="NikeName" type="text" maxlength="12" value="{$NikeName}"><input type="button" name="check_nikename" onClick="if(this.form.NikeName.value != '') {window.open('?module=member&action=CheckRepeat&uid={$Id}&nike_name='+encodeURIComponent(this.form.NikeName.value),'','height=300, width=600,toolbar=no,scrollbars=no,menubar=no,resizable=1');} else {alert('Ոݔ�땿�Q��');}" value="�z�y���Q" style="font-size: 9pt;">(����ʹ��&%()<>/\'")</td>
</tr>
<tr>
<td align=right>�[���ܴa��</td>
<td><input name="Password" type="password" size="25" maxlength="12" style="font-size: 9pt;"> (���Ąt����)</td>
</tr>
<tr>
<td>&nbsp;</td>
<td style="color: #707070">Ոݔ�� 4 �� 12 ����Ԫ��Ӣ����ĸ���֣�Ոע��Ӣ�Ĵ�С����</td>
</tr>
<tr>
<td align=right>�_�J�ܴa��</td>
<td><input name="PWDCheck" type="password" size="25" style="font-size: 9pt;"> </td>
</tr>
<tr>
<td>&nbsp;</td>
<td style="color: #707070">Ո��ݔ��һ���ܴa�������e�`��</td>
</tr>
<tr> 
  <td align="right">�ǡ�����:</td>
  <td> 
  {$constellation}
  </td>
</tr>
<tr>
<td align=right valign=top>�[���ɫ��</td>
<td style="color: #707070">
<table width="350" border="0" cellspacing="1" cellpadding="0" bgcolor=#888888>
<tr bgcolor=#FFFFFF align=center>
<td><input name='RoleIcon' type='radio' value='8' {$RoleIcon8}></td>
<td><input name='RoleIcon' type='radio' value='7' {$RoleIcon7}></td>
<td><input name='RoleIcon' type='radio' value='6' {$RoleIcon6}></td>
</tr>
<tr bgcolor=#FFFFFF align=center height=82>
<td><img src="images/index/a08.gif" width="80" height="93"></td>
<td><img src='images/index/a07.gif' width='80' height='93'></td>
<td><img src='images/index/a06.gif' width='80' height='93'></td>
</tr>
<tr bgcolor=#FFFFFF align=center>
	<td><input name="RoleIcon" type="radio" value="1" {$RoleIcon1}></td>
	<td><input name="RoleIcon" type="radio" value="2" {$RoleIcon2}></td>
	<td><input name="RoleIcon" type="radio" value="3" {$RoleIcon3}></td>
</tr>
<tr bgcolor=#FFFFFF align=center height=82>
	<td><img src="images/index/a01.gif" width="80" height="93"></td>
	<td><img src="images/index/a02.gif" width="80" height="93"></td>
	<td><img src="images/index/a03.gif" width="80" height="93"></td>
</tr>
<tr bgcolor=#FFFFFF align=center>
	<td><input name="RoleIcon" type="radio" value="4" {$RoleIcon4}></td>
	<td><input name="RoleIcon" type="radio" value="5" {$RoleIcon5}></td>
	<td>�����ǈ�</td>
	</tr>
<tr bgcolor=#FFFFFF align=center height=82>
		<td><img src="images/index/a04.gif" width="80" height="93"></td>
	<td><img src="images/index/a05.gif" width="80" height="93"></td>
	<td><img src="images/index/new_b.gif" width="80" height="93"></td>
</tr>
</table>
</td>
</tr>
<tr>
<td width=100 align=right>�挍������</td>
<td width=500>{$Name}{$Name_c}</td>
</tr>
<tr>
<td width=100 align=right>�ԡ����e��</td>
<td width=500><input name="Sex" type="radio" value="B" style="font-size: 9pt;" {$SexB}> ���ǎ��硡<input name="Sex" type="radio" value="G" style="font-size: 9pt;" {$SexG}> ������Ů</td>
</tr>
<tr>
<td width=100 align=right>�������գ�</td>
<td width=500>
		 {$year} �� {$month} �� {$day} ��
                      </td>
</tr>
<tr>
<td width=100 align=right>����]����</td>
<td width=500><input name="Email" type="text" size="25" style="font-size: 9pt;" value="{$Email}"> </td>
</tr>
<tr>
<td width=100 align=right>�j�Ԓ��</td>
<td width=500><input name="Tel" type="text" size="25" style="font-size: 9pt;" value="{$Tel}"></td>
</tr>
 <tr> 
  <td align="right">�j��ַ��</td>
  <td > 
   <input name="Addr" type="text" tabindex="2" size=50 maxlength="200" value="{$Addr}">
  </td>
 </tr>
<tr>
<tr>
<td>&nbsp;</td>
<td style="color: #707070">
<input type="hidden" name="module" value="member">
<input type="hidden" name="action" value="userinfo">

{$game}
<input name="Id" type="hidden" value="{$Id}">
<input type="reset" name="button" value="�������" style="font-size: 12pt;">
<input name="enter" type="submit" value="�ͳ��Y��" style="font-size: 12pt;">
</td>
</tr>
</table>

