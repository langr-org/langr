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
              <form name="answerFormadd" method="post" action="?">
                <tr> 
                  <td height="30" background="../../../images/admin/titlembg.jpg" class="bigwhite">&nbsp;編輯通话资料</td>
                </tr>
                <tr> 
                  <td height="30" valign="top"> 
                    <table width="100%" border="0" align="center" cellpadding="2" cellspacing="1">
                      <tr>
                        <td align="right">主叫：</td>
                        <td><input name="caller" type="text" value="{$caller}"></td>
                      </tr>
<!--
		      <tr>
                        <td align="right">住址：</td>
                        <td>{$cityList}
                            <input name="address" type="text" value="{$address}" size="50"></td>
                      </tr>
-->
		      <tr>
                        <td align="right">被叫：</td>
                        <td><input name="called" type="text" value="{$called}"></td>
                      </tr>
                      <tr>
                        <td align="right">通话时长：</td>
                        <td><input name="talk_time" type="text" value="{$talk_time}"></td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr class="pformstrip"> 
                  <td align="center" class="pformstrip">
                    <input type="submit" name="Submit" value="編 輯" id="button" tabindex="3"> 
                    <input name="module" type="hidden" value="ivrTalk">
                    <input name="action" type="hidden" value="editForm">
                    <input name="id" type="hidden" value="{$id}">
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
