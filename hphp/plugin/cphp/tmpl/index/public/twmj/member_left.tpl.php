<?php 
if($this->is_login) {
?>
<table border=1 width="162" bordercolor="#CCCCCC" bgcolor="#FFFFFF" border="1" cellspacing="1">
	<tr>
		<th bgcolor="#08aa08">
會員中心
		</th>
	</tr>
	<tr align='center'>
	<td><br>
<div style="padding-left:1em;line-height:1.5em">
<a href='<?=url('?module=member&action=myinfo')?>'>我的檔案</a><br>
<a href='<?=url('?module=member&action=record')?>'>交易記錄</a><br>
<a href='<?=url('?module=member&action=deposit')?>'>儲值點數</a><br>
<a href='<?=url('?module=member&action=userinfo')?>'>修改資料</a><br>
</div><br>
	</td>
	</tr>
</table>
<?php }?>