<script language=javascript>
function del(string)
{
	D_topic=confirm('��ȷ��:'+string);
	if(D_topic) return ture;	// window.location.replace(URL);
	else return false;
}
</script>
<form method="POST" name="form01" action="?" onSubmit="return del('ɾ����Ƭ����!');">
<table>
	<tr>
        <!--TMPL:Line-->
	<td algin="center"> 
	<a href="{$photo_url}"><img src="{$filename}"></a><br>
	<input type='checkbox' name='p_id[]' value='{$id}'><a href="{$photo_url}">{$title}</a> <br></td>
	<!--TMPL:Line-->
	</tr>
	<tr><td >
	
	</td>
	</tr>
</table>
	<input type='hidden' name='aid' value='{$aid}'>
	<input type='hidden' name='type' value='del'>
	<input type='submit' name='submit' value="ɾ����ѡ��Ƭ">
	<input type=hidden name='module' value='user'>
	<input type=hidden name='action' value='ep'>
</form>
