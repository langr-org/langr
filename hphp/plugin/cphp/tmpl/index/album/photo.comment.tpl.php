<!-- 相片 回复子模板 -->
<table>
	<tr><td colspan="2">
	回应: </td></tr>
        <!--TMPL:Line-->
	<tr><td align="center" colspan="2"> 
	 标题:{$title} 时间:{$cm_time} by:<a href="">{$cm_name}</a>
	</td></tr>
	<tr><td colspan="2"> {$body} <br></td></tr>
	<!--TMPL:Line-->

<form name="formRe" method="post">
   <input type="hidden" name="pid" value="{$pid}">
   <input type="hidden" name="uid" value="{$uid}">
   <input type="hidden" name="action" value="reply">
<!-- <input name="e_mail" type="text" size="50"> -->
<!-- <input name="title" type="text" size="50"> -->
<tr>
   <td align="right" valign="top">回應人</td>
   <td><input name="cm_name" type="text" size="30" value="{$n}"></td>
</tr>
<tr>
   <td align="right" valign="top">回應內容</td>
   <td><textarea name="body" cols="45" rows="5"></textarea></td>
</tr>
<tr>
   <td align="right" valign="top">验证码</td>
   <td><input name="verifyCode" type="text" size="10">
       <img src="{$verifyCode}">
   </td>
</tr>
<tr>
   <td colspan="2" align="center">
   <input name="submit" type="submit" value="送 出">      　
   <input name="reset" type="reset" value="取 消">
   </td>
</tr>
</table>