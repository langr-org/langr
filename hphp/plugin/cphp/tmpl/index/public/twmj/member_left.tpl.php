<?php 
if($this->is_login) {
?>
<table border=1 width="162" bordercolor="#CCCCCC" bgcolor="#FFFFFF" border="1" cellspacing="1">
	<tr>
		<th bgcolor="#08aa08">
���T����
		</th>
	</tr>
	<tr align='center'>
	<td><br>
<div style="padding-left:1em;line-height:1.5em">
<a href='<?=url('?module=member&action=myinfo')?>'>�ҵęn��</a><br>
<a href='<?=url('?module=member&action=record')?>'>����ӛ�</a><br>
<a href='<?=url('?module=member&action=deposit')?>'>��ֵ�c��</a><br>
<a href='<?=url('?module=member&action=userinfo')?>'>�޸��Y��</a><br>
</div><br>
	</td>
	</tr>
</table>
<?php }?>