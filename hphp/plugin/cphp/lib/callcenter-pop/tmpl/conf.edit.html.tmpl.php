<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style type="text/css">
/*  */
.r0_b1,.r0_b2,.r0_b3,.r0_b4,.r0_b1b,.r0_b2b,.r0_b3b,.r0_b4b,.r0_b{display:block;overflow:hidden;}
.r0_b1,.r0_b2,.r0_b3,.r0_b1b,.r0_b2b,.r0_b3b{height:1px;}
.r0_b2,.r0_b3,.r0_b4,.r0_b2b,.r0_b3b,.r0_b4b,.r0_b{border-left:1px solid #046BB7;border-right:1px solid #046BB7;}
.r0_b1,.r0_b1b{margin:0 5px;background:#046BB7;}
.r0_b2,.r0_b2b{margin:0 3px;border-width:2px;}
.r0_b3,.r0_b3b{margin:0 2px;}
.r0_b4,.r0_b4b{height:2px;margin:0 1px;}
.r0_bg1{background:#D9D9D9;}
.r0_bh{height:100%;padding:5px;}

.r0_bh0{padding:5px;}
.r0_bh1{height:150px;padding:2px;}
.r0_bh2{height:200px;padding:3px;}
.r0_bh3{height:300px;padding:4px;}

.td_title {
	text-align: right;
	width: 150px;
}
tr {
	padding: 1px;
	font-size: 14px;
}
</style>
</head>
<body>
<div style="width:100%;">
		
<div style="margin:2px;width:80%;float:left;">
	<div class="r0_b1"></div>
	<div class="r0_b2 r0_bg1"></div>
	<div class="r0_b3 r0_bg1"></div>
	<div class="r0_b4 r0_bg1"></div>
	<div class="r0_b r0_bg1 r0_bh0">
<div id="div_tab">
<table name="edit_tab">
	<form name="edit_form" action="?" method="post">
	<input name="act" value="confsave" type="hidden"/>
	<tr>
		<td style="text-align:center;font-size:18px;" colspan="2">
		<?=$d['do_edit']?>配置
		</td>
	</tr>
	<tr>
		<td class="td_title">
		配置文件名:
		</td>
		<td>
		<input name="fn" value="<?=$d['fn']?>" type="text" style="width:200px;"/>
		<font style="color:#ff0000">(为了方便您引用配置, 请使用英文字母.)</font>
		</td>
	</tr>
	<tr>
		<td class="td_title">
		弹屏url地址:
		</td>
		<td>
		<input name="pop_url" value="<?=$d['pop_url']?>" type="text" style="width:400px;"/>
		<font style="color:#ff0000"></font>
		</td>
	</tr>
	<tr>
		<td class="td_title">
		弹屏页打开位置:
		</td>
		<td>
		<input name="pop_target" value="<?=$d['pop_target']?>" type="text" style="width:200px;"/>
		(_self: 当前页打开, _blank: 新开窗口打开, 指定iframe name: 在指定iframe中打开)
		</td>
	</tr>
	<tr>
		<td class="td_title">
		弹屏页显示方式:
		</td>
		<td>
		<!--<input name="pop_show_" value="<?=$d['pop_show']?>" type="text" style="width:200px;"/>-->
		<select name="pop_show">
			<option value="iframe" <?=$d['pop_show_iframe']?>>目标页层中直接显示</option>
			<option value="div" <?=$d['pop_show_div']?>>目标页层中ajax显示</option>
			<option value="replace" <?=$d['pop_show_replace']?>>替换目标页面显示</option>
		</select>
		</td>
	</tr>
	<tr>
		<td class="td_title">
		弹屏页显示右边滚动条:
		</td>
		<td>
		<select name="pop_show_scroll">
			<option value="true" <?=$d['pop_show_scroll_true']?>>显示</option>
			<option value="false" <?=$d['pop_show_scroll_false']?>>不显示</option>
		</select>
		</td>
	</tr>
	<tr>
		<td class="td_title">
		弹屏页显示宽度:
		</td>
		<td>
		宽 <input name="pop_width" value="<?=$d['pop_width']?>" type="text" style="width:50px;"/>&nbsp;x&nbsp;
		高 <input name="pop_height" value="<?=$d['pop_height']?>" type="text" style="width:50px;"/>
		<font style="color:#ff0000">(当 弹屏显示方式 不为 "替换目标页面显示" 时有效, 可为百分比"80%"或像素"600px")</font>
		</td>
	</tr>
	<tr>
		<td class="td_title">
		弹屏页头部显示标题:
		</td>
		<td>
		<select name="pop_show_title">
			<option value="true" <?=$d['pop_show_title_true']?>>显示</option>
			<option value="false" <?=$d['pop_show_title_false']?>>不显示</option>
		</select>
		<font style="color:#ff0000">(当 弹屏显示方式 不为 "替换目标页面显示" 时有效)</font>
		</td>
	</tr>
	<tr>
		<td class="td_title">
		弹屏来去电文字说明:
		</td>
		<td>
		<input name="pop_title" value="<?=$d['pop_title']?>" type="text" style="width:300px;"/>&nbsp;
		<!--<br/>
		来电:<input name="pop_title[from]" value="<?=$d['pop_title_from']?>" type="text" style="width:100px;"/><br/>
		去电:<input name="pop_title[to]" value="<?=$d['pop_title_to']?>" type="text" style="width:100px;"/><br/>
		内部分机互打:<input name="pop_title[inv]" value="<?=$d['pop_title_inv']?>" type="text" style="width:100px;"/><br/>-->
		<font style="color:#ff0000">(当 弹屏显示方式 不为 "替换目标页面显示" 时有效)</font>
		</td>
	</tr>
	<tr>
		<td class="td_title">
		浏览器标题闪烁:
		</td>
		<td>
		<select name="pop_flash_title">
			<option value="true" <?=$d['pop_flash_title_true']?>>闪烁</option>
			<option value="false" <?=$d['pop_flash_title_false']?>>不闪</option>
		</select>
		<font style="color:#ff0000"></font>
		</td>
	</tr>
	<tr>
		<td class="td_title">
		长连接设置:
		</td>
		<td>
		<select name="pop_connect">
			<option value="0" <?=$d['pop_connect_0']?>>默认20s</option>
			<option value="1" <?=$d['pop_connect_1']?>>立即返回</option>
			<option value="2" <?=$d['pop_connect_2']?>>4s返回</option>
			<option value="3" <?=$d['pop_connect_3']?>>8s返回</option>
			<option value="5" <?=$d['pop_connect_5']?>>16s返回</option>
			<option value="9" <?=$d['pop_connect_9']?>>32s返回</option>
			<option value="17" <?=$d['pop_connect_17']?>>64s返回</option>
			<option value="33" <?=$d['pop_connect_33']?>>128s返回</option>
		</select>
		<font style="color:#000000">(使用长连接可以减轻连接服务器负担, 并增强用户体验.)</font>
		</td>
	</tr>
	<tr>
		<td class="td_title">
		<font style="color:#929292">api服务器地址:</font>
		</td>
		<td>
		<input name="api_host" value="<?=$d['api_host']?>" type="text" style="width:400px;"/>&nbsp;
		<font style="color:#000000">(请咨询您的网络管理员, 或不修改此处.)</font>
		</td>
	</tr>
	<tr>
		<td class="td_title">
		<font style="color:#929292">api服务器key:</font>
		</td>
		<td>
		<input name="api_key" value="<?=$d['api_key']?>" type="text" style="width:300px;"/>&nbsp;
		<font style="color:#000000">(请咨询您的网络管理员, 或不修改此处.)</font>
		</td>
	</tr>
	<tr id="edit_use" style="display:<?=$d['is_edit_display']?>;">
		<td class="td_title">
		使用配置文件:
		</td>
		<td>
		<font style="color:#ff0000">(请将如下html代码复制到您需要弹屏的页面.)</font><br/>
		<textarea name="include_js" value="<?=$d['api_key']?>" style="width:600px;height:80px;font-size:12px;" readonly>
<script type="text/javascript" src="<?=$d['host_api']?>/callcenter/lib/pop/popwindow.conf.<?=$d['fn']?>.js"></script>
<script type="text/javascript" src="<?=$d['host_api']?>/callcenter/lib/pop/popwindow.tool.js"></script></textarea>
		</td>
	</tr>
	<tr>
		<td style="text-align:center;font-size:18px;" colspan="2">
		<input name="submit" value="保 存" type="submit" style="width:80px;"/>&nbsp;&nbsp;&nbsp;&nbsp;
		<input name="reset" value="重 置" type="reset" style="width:80px;"/>
		</td>
	</tr>
	</form>
</table>
</div>
	</div>
	<div class="r0_b4b r0_bg1"></div>
	<div class="r0_b3b r0_bg1"></div>
	<div class="r0_b2b r0_bg1"></div>
	<div class="r0_b1b"></div>
</div>

</div>
</body>
</html>
