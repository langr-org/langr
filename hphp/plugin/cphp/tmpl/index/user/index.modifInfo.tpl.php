<form method="POST" name="form01" action="?">
<table>
	<tr><td>
	</td></tr>
	<tr><td >
	用户昵称:<input type='text' name='name' value='{$nike_name}'><br>
	用户密码:<input type='password' name='pwd'>(不改则不填)<br>
	确认密码:<input type='password' name='check_pwd'><br>
	
<!--
	性别:<select name='sex'>{$sex}
		<option value='B'>男</option>
		<option value='G'>女</option>
	     </select>
	生日:
	......
-->
	签名:<textarea name='sign' rows=4 cols=40 >{$sign}</textarea><br>
	</td>
	</tr>
</table>
	<input type='hidden' name='type' value='modifInfo'>
	<input type='submit' name='submit' value="修改名片">
	<input type=hidden name='module' value='user'>
	<input type=hidden name='action' value='ea'>
</form>
