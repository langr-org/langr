<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>{$webSiteTitle}</title>
<meta http-equiv="Content-Type" content="text/html; charset=$charSet">
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
                  <td height="30" background="../../../images/admin/titlembg.jpg" class="bigwhite">&nbsp;新增廣告商</td>
                </tr>
                <tr> 
                  <td height="30" valign="top"> 
                    <table width="100%" border="0" align="center" cellpadding="2" cellspacing="1">
                      <tr>
                        <td width="30%" height="24" align="right" class="mainbg">廣告商名稱：</td>
                        <td width="70%" class="mainbg"><input name="adAgency" type="text" tabindex="1" maxlength="200">
                        </td>
                      </tr>
                      <tr>
                        <td height="24" align="right" class="mainbg">廣告商密碼：</td>
                        <td class="mainbg"><input name="password" type="password" tabindex="1" maxlength="200"></td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr class="pformstrip"> 
                  <td align="center" class="pformstrip"><input name="module" type="hidden" value="adAgency">
                  <input type="submit" name="Submit" value="新增廣告商" id="button" tabindex="3"> 
                    <input name="action" type="hidden" value="insert">
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
