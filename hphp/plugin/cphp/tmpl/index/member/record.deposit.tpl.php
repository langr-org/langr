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
    <td height="30" background="../../../images/admin/titlembg.jpg" class="bigwhite">&nbsp;��ֵӆ��ӛ� (���� {$totalRecord} �lӛ�)   </td>
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
	<td background="../../../images/admin/titlebg.jpg" width="7%"><a href="<?=url("?s=Id$getArgs")?>">ӆ�κ�</a></td>
	<td background="../../../images/admin/titlebg.jpg" width="7%"><a href="<?=url('?s=Name'.$getArgs)?>">��ֵ��</a></td>
	<td background="../../../images/admin/titlebg.jpg" width="9%"><a href="<?=url('?s=PayType'.$getArgs)?>">���ʽ</a></td>
	<td background="../../../images/admin/titlebg.jpg" width="9%"><a href="<?=url("?s=Money".$getArgs)?>">���~(̨��)</a></td>
	<td background="../../../images/admin/titlebg.jpg" width="7%"><a href="<?=url("?s=Point$getArgs")?>">�c��</a></td>
	<td background="../../../images/admin/titlebg.jpg" width="12%"><a href="<?=url("?s=DealTime$getArgs")?>">ӆ�Εr�g</a></td>
	<td background="../../../images/admin/titlebg.jpg" width="12%"><a href="<?=url("?s=SuccTime$getArgs")?>">����r�g</a></td>
	<td background="../../../images/admin/titlebg.jpg" width="7%"><a href="<?=url("?s=State$getArgs")?>">��B</a></td>
          <td background="../../../images/admin/titlebg.jpg" width="7%">��ע</td>
        </tr>
        <!--TMPL:Line-->
        <tr align="center" {$rowColor}> 
          <td align="center">{$Id}</td>
          <td align="center">{$Name}</td>
          <td align="center">{$PayType}</td>
          <td align="center">{$Money}</td>
          <td align="center">{$Point}</td>
	  <td align="center">{$DealTime}</td>
          <td align="center">{$SuccTime}</td>
          <td align="center">{$State}</td>
          <td align="center">{$Note}</td>
        </tr><!--TMPL:Line-->
        <tr align="center">
          <td colspan="8" align="left">{$page}</td>
          <td>
            <input name="module" type="hidden" value="twmj">
            <input name="action" type="hidden" value="deposit">
            </td>
        </tr>
      </form></table>
    </td>
  </tr>
</table>

