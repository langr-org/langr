<script language=javascript>
function del(string)
{
	D_topic=confirm('��ȷ��:'+string);
	if(D_topic) return ture;	// window.location.replace(URL);
	else return false;
}
</script>
<form method="POST" name="form01" action="?module=user" onSubmit="return del('�ಾ����!')">
<table>
	<tr>
        <!--TMPL:Line-->
	<td algin="center"> 
	<a href="{$album_url}"<img src="{$cover}"></a><br>
	<a href="{$album_url}">{$name}</a> <br>�� {$p_count} ��<br>{$sign}</td>
	<!--TMPL:Line-->
	</tr>
</table>
</form>