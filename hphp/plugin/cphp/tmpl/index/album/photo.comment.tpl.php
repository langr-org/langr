<!-- ��Ƭ �ظ���ģ�� -->
<table>
	<tr><td colspan="2">
	��Ӧ: </td></tr>
        <!--TMPL:Line-->
	<tr><td align="center" colspan="2"> 
	 ����:{$title} ʱ��:{$cm_time} by:<a href="">{$cm_name}</a>
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
   <td align="right" valign="top">�ؑ���</td>
   <td><input name="cm_name" type="text" size="30" value="{$n}"></td>
</tr>
<tr>
   <td align="right" valign="top">�ؑ�����</td>
   <td><textarea name="body" cols="45" rows="5"></textarea></td>
</tr>
<tr>
   <td align="right" valign="top">��֤��</td>
   <td><input name="verifyCode" type="text" size="10">
       <img src="{$verifyCode}">
   </td>
</tr>
<tr>
   <td colspan="2" align="center">
   <input name="submit" type="submit" value="�� ��">      ��
   <input name="reset" type="reset" value="ȡ ��">
   </td>
</tr>
</table>