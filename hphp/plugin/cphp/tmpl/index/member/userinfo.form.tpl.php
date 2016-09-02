<form name="form1" method="post" action="?">
<table width="550" border="0" cellspacing="0" cellpadding="3" align=center style="font-size: 9pt;">
<tr>
<td width=100 align=right>遊戲帳號：</td>
<td width=500>{$Account}</td>
</tr>

<tr>
	<td width=100 align=right>身分證字號：</td>
<td width=500>{$IdCard}{$IdCard_c}</td>
</tr>
<tr>
	<td width=100 align=right>遊戲暱稱：</td>
<td width=500><input name="NikeName" type="text" maxlength="12" value="{$NikeName}"><input type="button" name="check_nikename" onClick="if(this.form.NikeName.value != '') {window.open('?module=member&action=CheckRepeat&uid={$Id}&nike_name='+encodeURIComponent(this.form.NikeName.value),'','height=300, width=600,toolbar=no,scrollbars=no,menubar=no,resizable=1');} else {alert('請輸入暱稱！');}" value="檢測暱稱" style="font-size: 9pt;">(不能使用&%()<>/\'")</td>
</tr>
<tr>
<td align=right>遊戲密碼：</td>
<td><input name="Password" type="password" size="25" maxlength="12" style="font-size: 9pt;"> (不改則不填)</td>
</tr>
<tr>
<td>&nbsp;</td>
<td style="color: #707070">請輸入 4 至 12 個字元的英文字母或數字，請注意英文大小寫。</td>
</tr>
<tr>
<td align=right>確認密碼：</td>
<td><input name="PWDCheck" type="password" size="25" style="font-size: 9pt;"> </td>
</tr>
<tr>
<td>&nbsp;</td>
<td style="color: #707070">請再輸入一次密碼，避免錯誤！</td>
</tr>
<tr> 
  <td align="right">星　　座:</td>
  <td> 
  {$constellation}
  </td>
</tr>
<tr>
<td align=right valign=top>遊戲角色：</td>
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
	<td>即將登場</td>
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
<td width=100 align=right>真實姓名：</td>
<td width=500>{$Name}{$Name_c}</td>
</tr>
<tr>
<td width=100 align=right>性　　別：</td>
<td width=500><input name="Sex" type="radio" value="B" style="font-size: 9pt;" {$SexB}> 我是帥哥　<input name="Sex" type="radio" value="G" style="font-size: 9pt;" {$SexG}> 我是美女</td>
</tr>
<tr>
<td width=100 align=right>生　　日：</td>
<td width=500>
		 {$year} 年 {$month} 月 {$day} 日
                      </td>
</tr>
<tr>
<td width=100 align=right>電子郵件：</td>
<td width=500><input name="Email" type="text" size="25" style="font-size: 9pt;" value="{$Email}"> </td>
</tr>
<tr>
<td width=100 align=right>聯絡電話：</td>
<td width=500><input name="Tel" type="text" size="25" style="font-size: 9pt;" value="{$Tel}"></td>
</tr>
 <tr> 
  <td align="right">聯絡地址：</td>
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
<input type="reset" name="button" value="清除重填" style="font-size: 12pt;">
<input name="enter" type="submit" value="送出資料" style="font-size: 12pt;">
</td>
</tr>
</table>

