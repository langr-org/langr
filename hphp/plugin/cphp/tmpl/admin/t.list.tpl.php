<script language="javascript1.2" src="../../../include/javascript/selectAll.js"></script>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="lineborder">
  <tr> 
    <td height="30" background="../../../images/admin/titlembg.jpg" class="bigwhite">&nbsp;IVR�û�ͨ������ (���� {$totalRecord} �lӛ�)   </td>
  </tr>
  <tr> 
    <td height="30" valign="top">
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <form method="get" action=""><td width="60%">
            ��
                <select name="year1">
              {$year}
            </select>
            ��
            <select name="month1">
              {$month}
            </select>
            ��
            <select name="day1">
              {$day}
            </select>
            �� --
            <select name="year2">
              {$year}
            </select>
            ��
            <select name="month2">
              {$month}
            </select>
            ��
            <select name="day2">
              {$day}
            </select>
            ��
            <input type="submit" name="findd" value="��ԃ">
            <input name="module" type="hidden" value="ivrTalk">            
            <input name="action" type="hidden" value="list">
          </td></form>
<!--
	    <form method="get" action="">
              <td width="40%">�� {$cityList} 
                <input type="submit" name="findc" value="��ԃ">
                <input name="module" type="hidden" value="ivrTalk">
                <input name="action" type="hidden" value="list"></td>
          </form>
-->
	</tr>
        <tr>
         <form method="get" action=""> <td>��
            <select name="type">
              <option value="caller">����</option>
              <option value="called">����</option>
		  <option value="all">����֮һ</option>
            </select>
            <input type="text" name="tel">
            <input type="submit" name="findt" value="��ԃ">
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
          <td background="../../../images/admin/titlebg.jpg" width="5%"><a href="?s=id{$getArgs}">���</a></td>
          <td background="../../../images/admin/titlebg.jpg" width="7%"><a href="?s=caller{$getArgs}">����</a></td>
          <td background="../../../images/admin/titlebg.jpg" width="7%"><a href="?s=called{$getArgs}">����</a></td>
          <td background="../../../images/admin/titlebg.jpg" width="25%"><a href="?s=start_time{$getArgs}">ͨ����ʼʱ��</a></td>
          <td background="../../../images/admin/titlebg.jpg" width="10%"><a href="?s=talk_time{$getArgs}">ʱ��(��)</a></td>
          <td background="../../../images/admin/titlebg.jpg" width="10%">�xȡ</td>
          <td background="../../../images/admin/titlebg.jpg" width="20%">����</td>
        </tr>
        <!--TMPL:Line-->
        <tr align="center" {$rowColor}> 
          <td align="center">{$id}</td>
          <td align="left">{$caller}</td>
          <td align="center">{$called}</td>
          <td align="center">{$start_time}</td>
          <td align="center">{$talk_time}</td>
          <td align="center"><input type=checkbox name=\"list[]\" value="{$id}" class=noborder></td>
          <td align="center"><a href="./admin.php?module=ivrTalk&action=editForm&id={$id}">��݋</a> 
            <a href="./admin.php?module=ivrTalk&action=del&id={$id}" onclick="return confirm('�_�JҪ�h���ˆ���᣿');">�h��</a></td>
        </tr><!--TMPL:Line-->
        <tr align="center">
          <td colspan="4" align="left">{$page}</td>
          <td>ȫ�x��</td>
          <td><input type="checkbox" name="allbox" onClick="SelectAll(this.form);" class="noborder" ></td>
          <td>
            <input name="module" type="hidden" value="ivrTalk">
            <input type="submit" value="�h��" onclick="return confirm('�_�JҪ�h������᣿');">
            <input name="action" type="hidden" value="del">
            </td>
        </tr>
      </form></table>
    </td>
  </tr>
</table>
