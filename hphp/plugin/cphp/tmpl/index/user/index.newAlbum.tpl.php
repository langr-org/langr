<-- �½��ಾ -->
<form method="POST" name="form01" action="?">
<table>
	<tr><td>
	</td></tr>
	<tr><td >
	�ಾ��:<input type='text' name='name'><br>
	�ಾ����:<select name='attr'><option selected value='1'>����</option>
		<option value='2'>��Ҫ����</option>
		<option value='3'>������</option></select>
	�ಾ����:<select  name='sort'>{$sort_list}</select><br>
	˵��:<textarea name='sign' rows=4 cols=40 ></textarea><br>
	</td>
	</tr>
</table>
	<input type='hidden' name='type' value='new'>
	<input type='submit' name='submit' value="�½��ಾ">
	<input type=hidden name='module' value='user'>
	<input type=hidden name='action' value='ea'>
</form>

