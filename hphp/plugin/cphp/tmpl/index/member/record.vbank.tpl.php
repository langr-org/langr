<script language="javascript1.2" src="../../../include/javascript/selectAll.js"></script>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="lineborder">
  <tr> 
    <td height="30" class="bigwhite">&nbsp; 
	<a href="<?=url("?module=member&action=record&record=deposit")?>">儲值訂單記錄</a>&nbsp; 
	<a href="<?=url("?module=member&action=record&record=vbank")?>">vBank 轉帳記錄</a>&nbsp; 
	<a href="<?=url("?module=member&action=record&record=exchange")?>">兌換獎品記錄</a>&nbsp; 
    </td>
  </tr>
  <tr> 
    <td height="30" background="../../../images/admin/titlembg.jpg" class="bigwhite">&nbsp;銀行轉帳記錄 (總共 {$totalRecord} 條記錄)   </td>
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
	<td background="../../../images/admin/titlebg.jpg" width="6%"><a href="<?=url("?s=Id$getArgs")?>">訂單号</a></td>
	<td background="../../../images/admin/titlebg.jpg" width="6%">轉出者</td>
	<td background="../../../images/admin/titlebg.jpg" width="6%">轉入者</td>
	<td background="../../../images/admin/titlebg.jpg" width="7%"><a href="<?=url("?s=Name$getArgs")?>">轉帳類型</a></td>
	<td background="../../../images/admin/titlebg.jpg" width="7%"><a href="<?=url("?s=Point$getArgs")?>">轉帳點數</a></td>
	<td background="../../../images/admin/titlebg.jpg" width="7%"><a href="<?=url("?s=hcPoint".$getArgs)?>">手續費</a></td>
	<td background="../../../images/admin/titlebg.jpg" width="12%"><a href="<?=url("?s=CreateTime$getArgs")?>">轉帳時間</a></td>
	<td background="../../../images/admin/titlebg.jpg" width="6%"><a href="<?=url("?s=UID$getArgs")?>">方式</a></td>
	<td background="../../../images/admin/titlebg.jpg" width="6%"><a href="<?=url("?s=State$getArgs")?>">狀態</a></td>
        <td background="../../../images/admin/titlebg.jpg" width="7%">備注</td>
        </tr>
        <!--TMPL:Line-->
        <tr align="center" {$rowColor}> 
          <td align="center">{$Id}</td>
          <td align="center">{$byUser}({$byUID})</td>
          <td align="center">{$User}({$UID})</td>
          <td align="center">{$Name}</td>
          <td align="center">{$Point}</td>
          <td align="center">{$hcPoint}</td>
	  <td align="center">{$CreateTime}</td>
          <td align="center">{$Way}</td>
          <td align="center">{$State}</td>
          <td align="center">{$Note}</td>
        </tr><!--TMPL:Line-->
        <tr align="center">
          <td colspan="7" align="left">{$page}</td>
          <td>
            <input name="module" type="hidden" value="twmj">
            <input name="action" type="hidden" value="vbank">
            </td>
        </tr>
      </form></table>
    </td>
  </tr>
</table>


