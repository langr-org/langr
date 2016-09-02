<!-- 相片 回复子模板 -->
<table>
	<tr valign="top">
        <!--TMPL:Line-->
	<td align="center"> 
		<a href="{$album_url}" title="标题:{$name};总浏览量:{$view_count}">
			<img src="{$cover}"></a><br>
		<a href="{$album_url}">{$name}</a><br>
		<font size=2>共 {$p_count} 张<br>
		浏览:今天 {$view_today} 次;总共 {$view_count} 次</font><br>
		{$sign}
	</td>
	<!--TMPL:Line-->
	</tr>
</table>