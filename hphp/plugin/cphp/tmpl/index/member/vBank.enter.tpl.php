<form name="form1" method="post" action="?">
您選擇轉帳類型: {$Name}<br>
轉出點數: {$Point} 點<br>
手續費: {$hcPoint} 點<br>
共需點數: {$tPoint} 點<br>
轉帳給: {$user}<br>
條件滿足, 請
<input type="hidden" name="vbanktype" value="{$vbanktype}">
<input type="hidden" name="user" value="{$user}">
<input type="hidden" name="module" value="member">
<input type="hidden" name="action" value="vBank">
<input type="submit" name="enter" {$disabled} value="确认转帐"> &nbsp; 
<input type="reset" name="reset" value="取消" OnClick="return history.back();">
</form>
