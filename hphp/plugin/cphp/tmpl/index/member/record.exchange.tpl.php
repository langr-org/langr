<script language="javascript1.2" src="../../../include/javascript/selectAll.js"></script>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="lineborder">
  <tr> 
    <td height="30" class="bigwhite">&nbsp; 
	<a href="<?=url("?module=member&action=record&record=deposit")?>">��ֵӆ��ӛ�</a>&nbsp; 
	<a href="<?=url("?module=member&action=record&record=vbank")?>">vBank �D��ӛ�</a>&nbsp; 
	<a href="<?=url("?module=member&action=record&record=exchange")?>">���Q��Ʒӛ�</a>&nbsp; 
    </td>
  </tr>
  <tr> 
    <td height="30" background="../../../images/admin/titlembg.jpg" class="bigwhite">&nbsp;���Q��Ʒӆ��ӛ� (���� {$totalRecord} �lӛ�)   </td>
  </tr>
  <tr> 
    <td height="30" valign="top">
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
	</tr>
        <tr>
         <form method="get" action=""> <td> </td></form>
         </td>
        </tr>
      </table>
      <table width="100%" border="0" cellpadding="4" cellspacing="2"><form name='op' method="get" action="?">
        <tr align="center">  
	<td background="../../../images/admin/titlebg.jpg" width="6%"><a href="<?=url("?s=Id$getArgs")?>">ӆ�κ�</a></td>
	<td background="../../../images/admin/titlebg.jpg" width="7%"><a href="<?=url("?s=WaveName$getArgs")?>">��Ʒ��</a></td>
	<td background="../../../images/admin/titlebg.jpg" width="8%"><a href="<?=url("?s=Name".$getArgs)?>">��؛��</a></td>
	<td background="../../../images/admin/titlebg.jpg" width="8%"><a href="<?=url("?s=Money".$getArgs)?>">�rֵ(̨��)</a></td>
	<td background="../../../images/admin/titlebg.jpg" width="7%"><a href="<?=url("?s=Bonus$getArgs")?>">����t��</a></td>
	<td background="../../../images/admin/titlebg.jpg" width="12%"><a href="<?=url("?s=OrderTime$getArgs")?>">ӆ�Εr�g</a></td>
	<td background="../../../images/admin/titlebg.jpg" width="12%"><a href="<?=url("?s=PostTime$getArgs")?>">�]�ĕr�g</a></td>
	<td background="../../../images/admin/titlebg.jpg" width="7%"><a href="<?=url("?s=State$getArgs")?>">��B</a></td>
          <td background="../../../images/admin/titlebg.jpg" width="12%">��ע</td>
        </tr>
        <!--TMPL:Line-->
        <tr align="center" {$rowColor}> 
          <td align="center">{$Id}</td>
          <td align="center">{$WaveName}</td>
          <td align="center">{$Name}</td>
          <td align="center">{$Money}</td>
          <td align="center">{$Bonus}</td>
	  <td align="center">{$OrderTime}</td>
          <td align="center">{$PostTime}</td>
          <td align="center">{$State}</td>
          <td align="center">{$Note}</td>
        </tr><!--TMPL:Line-->
        <tr align="center">
          <td colspan="8" align="left">{$page}</td>
          <td>
            <input name="module" type="hidden" value="twmj">
            <input name="action" type="hidden" value="del">
            </td>
        </tr>
      </form></table>
    </td>
  </tr>
</table>


