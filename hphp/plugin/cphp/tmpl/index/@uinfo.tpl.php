<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>{$webSiteTitle}</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<link href="../../../include/css/admin.css" rel="stylesheet" type="text/css">
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<?
$this->loadTmplate(TEMPLATE_PATH."public/head.tpl.php");
?>
<table width="1002" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td colspan="2"><img src="../../../images/admin/null.gif" width="10" height="10"></td>
  </tr>
  <tr> 
    <td width="185" valign="top">
<?
$this->loadTmplate(TEMPLATE_PATH."public/menu.tpl.php");
?>
    </td>
    <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="5">&nbsp;</td>
          <td> 
            <table width="100%" border="0" cellpadding="0" cellspacing="0" class="lineborder">
              <form name="systemadd" method="post" action="?">
                <tr> 
                  <td height="30" background="../../../images/admin/titlembg.jpg" class="bigwhite">&nbsp;查看儲值訂單</td>
                </tr>
                <tr> 
                  <td height="30" valign="top"> 
                    <table width="100%" border="0" align="center" cellpadding="2" cellspacing="1">
                      <tr> 
                        <td width="44%" height="24" align="right" class="mainbg">訂單 ID:</td>
                        <td width="56%" class="mainbg"> {$Id}</td>
                      </tr>
                      <tr> 
                        <td height="24" align="right" class="mainbg">訂單用戶(ID):</td>
                        <td class="mainbg"> 
                          {$Account}({$UID})
                        </td>
                      </tr>
                      <tr> 
                        <td height="24" align="right" class="mainbg">訂單類型(ID):</td>
                        <td class="mainbg"> 
                          {$Name}({$OrderType})
                        </td>
		      </tr>
                      <tr> 
                        <td width="44%" height="24" align="right" class="mainbg">價值(台幣):</td>
                        <td width="56%" class="mainbg">
			  {$Money}
			</td>
                      </tr>
                      <tr> 
                        <td height="24" align="right" class="mainbg">點數:</td>
                        <td class="mainbg"> 
                          {$Point}
                        </td>
		      </tr>
                      <tr> 
                        <td height="24" align="right" class="mainbg">資付類型:</td>
                        <td class="mainbg"> 
                          {$PayType}
                        </td>
		      </tr>
                      <tr> 
                        <td width="44%" height="24" align="right" class="mainbg">經銷商(版本):</td>
                        <td class="mainbg"> 
                          {$Note1}({$Note1})
                        </td>
                      </tr>
                      <tr> 
                        <td height="24" align="right" class="mainbg">交易時間:</td>
                        <td class="mainbg"> 
                          {$DealTime}
                        </td>
		      </tr>
                      <tr> 
                        <td height="24" align="right" class="mainbg">交易成功時間:</td>
                        <td class="mainbg"> 
                          {$SuccTime}
                        </td>
                      </tr>
                      <tr> 
                        <td height="24" align="right" class="mainbg">交易狀態:</td>
                        <td class="mainbg"> 
                          {$State}
                        </td>
		      </tr>
                      <tr> 
                        <td height="24" align="right" class="mainbg">備注:</td>
                        <td class="mainbg"> 
			  <textarea name="Note" cols="30"  rows="3">{$Note2}</textarea>
                        </td>
                      </tr>
                      <!-- tr> 
                        <td height="24" align="right" class="mainbg">允許模板:</td>
                        <td class="mainbg"> 
                          <input type="checkbox" name="allowTmpl" value="1" class="noborder" {$allowTmplChecked}>
                          允許模板中輸出此配置值；</td>
                      </tr -->
                    </table>
                  </td>
                </tr>
                <tr class="pformstrip"> 
                  <td align="center" class="pformstrip">
		    <input type="reset" name="reset" value="返回" OnClick="return history.back();">
                    <input name="module" type="hidden" value="twmj">
                    <input name="action" type="hidden" value="depEditForm">
                    <input name="Id" type="hidden" value="{$Id}">
                  </td>
                </tr>
              </form>
            </table></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<?
$this->loadTmplate(TEMPLATE_PATH."public/foot.tpl.php");
?>
</body>
</html>
