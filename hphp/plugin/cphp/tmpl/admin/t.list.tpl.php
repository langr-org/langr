<script language="javascript1.2" src="../../../include/javascript/selectAll.js"></script>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="lineborder">
  <tr> 
    <td height="30" background="../../../images/admin/titlembg.jpg" class="bigwhite">&nbsp;IVR用户通信资料 (總共 {$totalRecord} 條記錄)   </td>
  </tr>
  <tr> 
    <td height="30" valign="top">
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <form method="get" action=""><td width="60%">
            按
                <select name="year1">
              {$year}
            </select>
            年
            <select name="month1">
              {$month}
            </select>
            月
            <select name="day1">
              {$day}
            </select>
            日 --
            <select name="year2">
              {$year}
            </select>
            年
            <select name="month2">
              {$month}
            </select>
            月
            <select name="day2">
              {$day}
            </select>
            日
            <input type="submit" name="findd" value="查詢">
            <input name="module" type="hidden" value="ivrTalk">            
            <input name="action" type="hidden" value="list">
          </td></form>
<!--
	    <form method="get" action="">
              <td width="40%">按 {$cityList} 
                <input type="submit" name="findc" value="查詢">
                <input name="module" type="hidden" value="ivrTalk">
                <input name="action" type="hidden" value="list"></td>
          </form>
-->
	</tr>
        <tr>
         <form method="get" action=""> <td>按
            <select name="type">
              <option value="caller">主叫</option>
              <option value="called">被叫</option>
		  <option value="all">两者之一</option>
            </select>
            <input type="text" name="tel">
            <input type="submit" name="findt" value="查詢">
            <input name="module" type="hidden" value="ivrTalk">
            <input name="action" type="hidden" value="list"></td></form>
           <form method="post" action="?" target="_blank"><td> <input name="where" type="hidden" value="{$where}">
            <input name="action" type="hidden" value="export">
            <input name="module" type="hidden" value="ivrTalk">
            {$exportSimit}</td>
           </form>
        </tr>
      </table>
      <table width="100%" border="0" cellpadding="4" cellspacing="2"><form name='op' method="get" action="?">
        <tr align="center">  
          <td background="../../../images/admin/titlebg.jpg" width="5%"><a href="?s=id{$getArgs}">编号</a></td>
          <td background="../../../images/admin/titlebg.jpg" width="7%"><a href="?s=caller{$getArgs}">主叫</a></td>
          <td background="../../../images/admin/titlebg.jpg" width="7%"><a href="?s=called{$getArgs}">被叫</a></td>
          <td background="../../../images/admin/titlebg.jpg" width="25%"><a href="?s=start_time{$getArgs}">通话开始时间</a></td>
          <td background="../../../images/admin/titlebg.jpg" width="10%"><a href="?s=talk_time{$getArgs}">时长(秒)</a></td>
          <td background="../../../images/admin/titlebg.jpg" width="10%">選取</td>
          <td background="../../../images/admin/titlebg.jpg" width="20%">操作</td>
        </tr>
        <!--TMPL:Line-->
        <tr align="center" {$rowColor}> 
          <td align="center">{$id}</td>
          <td align="left">{$caller}</td>
          <td align="center">{$called}</td>
          <td align="center">{$start_time}</td>
          <td align="center">{$talk_time}</td>
          <td align="center"><input type=checkbox name=\"list[]\" value="{$id}" class=noborder></td>
          <td align="center"><a href="./admin.php?module=ivrTalk&action=editForm&id={$id}">編輯</a> 
            <a href="./admin.php?module=ivrTalk&action=del&id={$id}" onclick="return confirm('確認要刪除此問卷嗎？');">刪除</a></td>
        </tr><!--TMPL:Line-->
        <tr align="center">
          <td colspan="4" align="left">{$page}</td>
          <td>全選：</td>
          <td><input type="checkbox" name="allbox" onClick="SelectAll(this.form);" class="noborder" ></td>
          <td>
            <input name="module" type="hidden" value="ivrTalk">
            <input type="submit" value="刪除" onclick="return confirm('確認要刪除問卷嗎？');">
            <input name="action" type="hidden" value="del">
            </td>
        </tr>
      </form></table>
    </td>
  </tr>
</table>
