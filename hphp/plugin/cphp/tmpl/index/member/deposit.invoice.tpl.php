<script type="text/javascript">
function showForm() {
	if(document.form1.getInvoice.checked == true) {
		document.getElementById('invoice').style.display = "";
	} else {
		document.getElementById('invoice').style.display = "none";
	}
}
</script>


<table border="0" cellspacing="0" cellpadding="3">
<tr><td>
<label><input id="getInvoice" name="getInvoice" class="checkbox" type="checkbox"  value="1" onclick="showForm()" tabindex="12" />發票請郵寄給我</label>
</td></tr>
</table>

<div id="invoice" style="display: none;">
<table cellpadding="0" cellspacing="0"  border="0" >
  <tr>
    <td align=right style="font-size:12pt;line-height:1.5">真實姓名：</td>
    <td><input name="Name" type="text" value="" size="20">
        <font color=red>＊</font></td>
  </tr>
  <tr>
    <td align=right style="font-size:12pt;line-height:1.5">郵遞區號：</td>
    <td><input name="PostCode" type="text" class="input" value="" size="5">
        <font color=red>＊</font></td>
  </tr>
  <tr>
    <td align=right style="font-size:12pt;line-height:1.5">收件地址：</td>
    <td><input name="Addr" type="text" class="input" value="" size="60">
        <font color=red>＊</font></td>
  </tr>
  <tr>
    <td align=right style="font-size:12pt;line-height:1.5">發票方式：</td>
    <td><SELECT NAME = checkType><option value="0" SELECTED>個人二聯式</option>
<option value="1">公司三聯式</option>
</SELECT></td>
  </tr>
  <tr>
    <td align=right style="font-size:12pt;line-height:1.5">發票抬頭：</td>
    <td><input name="checkHead" type="text" class="input" value="" size="30"></td>
  </tr>
  <tr>
    <td align=right style="font-size:12pt;line-height:1.5">統一編號：</td>
    <td><input name="checkCode" type="text" class="input" value="" size="20"></td>
  </tr>
</table>
<input name="Note" type="hidden" class="input" value="">
</div>
