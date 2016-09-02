<-- 新建相簿 -->
<form method="POST" name="form01" action="?">
<table>
	<tr><td>
	</td></tr>
	<tr><td >
	相簿名:<input type='text' name='name'><br>
	相簿属性:<select name='attr'><option selected value='1'>公开</option>
		<option value='2'>需要密码</option>
		<option value='3'>不公开</option></select>
	相簿分类:<select  name='sort'>{$sort_list}</select><br>
	说明:<textarea name='sign' rows=4 cols=40 ></textarea><br>
	</td>
	</tr>
</table>
	<input type='hidden' name='type' value='new'>
	<input type='submit' name='submit' value="新建相簿">
	<input type=hidden name='module' value='user'>
	<input type=hidden name='action' value='ea'>
</form>

