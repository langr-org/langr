<script language=javascript>
function del(string)
{
	D_topic=confirm('请确认:'+string);
	if(D_topic) return ture;	// window.location.replace(URL);
	else return false;
}
</script>
<form method="POST" name="form01" action="?module=user" onSubmit="return del('相簿操作!')">
<table>
	<tr>
        <!--TMPL:Line-->
	<td algin="center"> 
	<a href="{$album_url}"<img src="{$cover}"></a><br>
	<a href="{$album_url}">{$name}</a> <br>共 {$p_count} 张<br>{$sign}</td>
	<!--TMPL:Line-->
	</tr>
</table>
</form>