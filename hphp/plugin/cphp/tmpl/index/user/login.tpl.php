 
<html>
<head><title>{$webSiteTitle}</title>
</head>
<body> 
<div align=center>
  <form method="POST" name="L_form" action="?">
  <table name="tab" bgcolor=#eeeeee Frame='hsides' rules="none">
  <tr><td width=80><br>
  �û���:</td><td width=250><br><input type='text' name='user' maxlength=18 onMouseOver='this.focus()'><br></td></tr>
  <tr><td><br>��&nbsp;&nbsp;��:</td><td><br><input type='password' name='pwd' class=textbox maxlength=16>
  <br></td></tr>
<tr><td>  ��֤��: </td><td><input type=text name='verifyCode'> <img src="<?=url("?module=user&action=verifyCode&t=gif")?>">
  <input type=hidden name='module' value='user'>
  <input type=hidden name='action' value='login'>
  </td></tr>
  <tr Frame='hsides' rules="rows"><td><br>
  <a href="<?=url("?module=user&action=register")?>">ע��</a><br>
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td><br>
 <input type="submit" value="�� ½" >&nbsp;&nbsp; 
 <input type="reset" value="�� ��" name="B2"><br>
 </td></tr>
 </table>
 </form>
</div>
</body>
</html>