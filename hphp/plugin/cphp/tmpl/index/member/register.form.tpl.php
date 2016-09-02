<form name="form1" method="post" action="?">
<img src="images/index/register_2.gif" width="550" height="49" />
<table width="550" border="0" cellspacing="0" cellpadding="3" align=center style="font-size: 9pt;">
<tr><th>&nbsp;</th><th><font color="red" size="+1">★加油！完成註冊就送3000點！</font></th></tr>
<tr>
<td width=100 align=right>遊戲帳號：</td>
<td width=500><input name="user" type="text" size="25" maxlength="12" style="font-size: 9pt;"> <font color=red>★</font>　<input type="button" name="check_user" onClick="if(this.form.user.value != '') {window.open('?module=member&action=CheckRepeat&user='+encodeURIComponent(this.form.user.value),'','height=300, width=600,toolbar=no,scrollbars=no,menubar=no,resizable=1');} else {alert('請輸入帳號！');}" value="檢測帳號" style="font-size: 9pt;"></td>
</tr>
<tr>
<td>&nbsp;</td>
<td style="color: #707070">請輸入 4 至 12 個字元的英文字母或數字。</td>
</tr>
<tr>
<td align=right>遊戲密碼：</td>
<td><input name="pwd" type="password" size="25" maxlength="12" style="font-size: 9pt;"> <font color=red>★</font></td>
</tr>
<tr>
<td>&nbsp;</td>
<td style="color: #707070">請輸入 4 至 12 個字元的英文字母或數字，請注意英文大小寫。</td>
</tr>
<tr>
<td align=right>確認密碼：</td>
<td><input name="check_pwd" type="password" size="25" style="font-size: 9pt;"> <font color=red>★</font></td>
</tr>
<tr>
<td>&nbsp;</td>
<td style="color: #707070">請再輸入一次密碼，避免錯誤！</td>
</tr>
<td align=right>遊戲暱稱：</td>
<td><input name="nike_name" type="text" size="25" maxlength="12" style="font-size: 9pt;"> <font color=red>★</font>　<input type="button" name="check_nikename" onClick="if(this.form.nike_name.value != '') {window.open('?module=member&action=CheckRepeat&nike_name='+encodeURIComponent(this.form.nike_name.value),'','height=300, width=600,toolbar=no,scrollbars=no,menubar=no,resizable=1');} else {alert('請輸入暱稱！');}" value="檢測暱稱" style="font-size: 9pt;">(不能使用&%()&lt;&gt;/\'")
</td>
</tr>
<tr>
<td>&nbsp;</td>
<td style="color: #707070">請輸入 2 至 12 個字元的英文字母或數字，中文最多 6 個字。</td>
</tr>
<tr>
<td width=100 align=right>真實姓名：</td>
<td width=500><input name="name" type="text" size="25" style="font-size: 9pt;"></td>
</tr>
<tr>
<td width=100 align=right>性　　別：</td>
<td width=500><input name="sex" type="radio" value="B" checked style="font-size: 9pt;"> 我是帥哥　<input name="sex" type="radio" value="G" style="font-size: 9pt;"> 我是美女</td>
</tr>
<tr>
<td style="color: #ff0000" colspan="2">台灣地區民眾請填寫身份證號碼即可，可不填護照號碼；海外人士請填您的護照號碼。</td>
</tr>
<tr>
<td width=100 align=right> </td>
<td width=500><input name="area" type="radio" value="T" checked style="font-size: 9pt;"> 台灣地區　<input name="area" type="radio" value="O" style="font-size: 9pt;"> 海外人士 <font color=red>★(海外人士贈送 1000 點)</font></td>
</tr>
<tr>
<td width=100 align=right>身份證號：<br>(或護照)：</td>
<td width=500><input name="id_card" type="text" size="25" style="font-size: 9pt;" maxlength="10"> <font color=red>★(以便活動贈品連絡用)</font></td>
</tr>
<!-- tr>
<td width=100 align=right>或 護照：</td>
<td width=500><input name="overseas_card" type="text" size="25" style="font-size: 9pt;" maxlength="10"> <font color=red>★(海外人士贈送 1000 點)</font></td>
</tr -->
<tr>
<td width=100 align=right>生　　日：</td>
<td width=500><input name="year" type="text" size="6" style="font-size: 9pt;"> 年 <select name="month" style="font-size:9pt;">
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
                      </select> 月 
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
                      </select> 日
                      </td>
</tr>
<tr> 
  <td align="right">星　　座:</td>
  <td> 
  {$constellation}
  </td>
</tr>
<tr>
<td width=100 align=right>電子郵件：</td>
<td width=500><input name="e_mail" type="text" size="25" style="font-size: 9pt;"> <font color=red>★(以便活動贈品連絡用)</font></td>
</tr>
<tr>
<td width=100 align=right>聯絡電話：</td>
<td width=500><input name="tel" type="text" size="25" style="font-size: 9pt;"> <font color=red>★(以便活動贈品連絡用)</font></td>
</tr>
<tr> 
  <td align="right">聯絡地址：</td>
  <td > 
   <input name="addr" type="text" tabindex="2" size=50 maxlength="200">
  </td>
 </tr>
<tr>
<td width=100 align=right>註冊驗證：</td>
<td width=500><input name="verifyCode" type="text" size="14" style="font-size: 9pt;">　<img src="<?=url("?module=member&action=verifyCode&t=gif")?>" align="absbottom"> <font color=red>★</font></td>
</tr>
<tr>
<td align=right valign=top>遊戲角色：</td>
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
<input name="enter" type="hidden" value="送出註冊資料" style="font-size: 12pt;">
<!--<input type="reset" name="button" value="清除重填" style="font-size: 12pt;">-->
<a href="#" onclick="document.form1.reset()"><img src="images/index/register_2a2.gif" border="0" /></a>
<input type="hidden" name="module" value="member">
<input type="hidden" name="action" value="register">
{$game}
<input type="image" src="images/index/register_2a1.gif" />
</td>
</tr>
<tr>
<td colspan="2"><font color="red">※如不能註冊，請連絡客服。<br>服務電話：(02)2278-7789   《週一至週五上午09:00~下午20:00》<br>服務信箱：<a href="mailto:service@betcity.com.tw"><u>service@betcity.com.tw</u></a></font></td>
</tr>
</table>
