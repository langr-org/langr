<form method="POST" name="form01" action="?">
<table>
	<tr><td>
	</td></tr>
	<tr><td >
	�û��ǳ�:<input type='text' name='name' value='{$nike_name}'><br>
	�û�����:<input type='password' name='pwd'>(��������)<br>
	ȷ������:<input type='password' name='check_pwd'><br>
	
<!--
	�Ա�:<select name='sex'>{$sex}
		<option value='B'>��</option>
		<option value='G'>Ů</option>
	     </select>
	����:
	......
-->
	ǩ��:<textarea name='sign' rows=4 cols=40 >{$sign}</textarea><br>
	</td>
	</tr>
</table>
	<input type='hidden' name='type' value='modifInfo'>
	<input type='submit' name='submit' value="�޸���Ƭ">
	<input type=hidden name='module' value='user'>
	<input type=hidden name='action' value='ea'>
</form>
