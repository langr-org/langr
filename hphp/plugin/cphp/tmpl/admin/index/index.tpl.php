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
    <td><img src="../../../images/admin/null.gif" width="10" height="10"></td>
  </tr>
  <tr> 
    <td valign="top">
        <table width="50%" border="0" align="center" cellpadding="0" cellspacing="0" class="lineborder">
        <form name="login" method="post" action="?">
          <tr align="center"> 
            <td width="100%" height="30" background="../../../images/admin/titlembg.jpg" class="linetd"><span class="bigwhite">管 
              理 員 登 入</span></td>
          </tr>
          <tr>
            <td align="center"><table width="100%" border="0" align="center" cellpadding="2" cellspacing="1">
                <tr> 
                  <td width="44%" height="24" align="right" class="mainbg">管理員帳號：</td>
                  <td width="56%" class="mainbg"><input name="accounts" type="text" tabindex="1" size="10">
                    <font face="Arial"></font></td>
                </tr>
                <tr> 
                  <td height="24" align="right" class="mainbg">登錄密碼：</td>
                  <td class="mainbg"><input name="password" type="password" tabindex="2"></td>
                </tr>
                <tr> 
                  <td height="24" align="right" class="mainbg">輸入驗證：</td>
                  <td class="mainbg"><input name="verifyCode" type="text" tabindex="3" autocomplete="off"></td>
                </tr>
                <tr> 
                  <td height="24" align="right" class="mainbg">驗證號碼：</td>
                  <td class="mainbg"><img src="./admin.php?action=verifyCode"> <input name="action" type="hidden" value="login">
                  </td>
                </tr>
              </table></td>
          </tr>
          <tr class="pformstrip"> 
            <td align="center" class="pformstrip"><input type="submit" name="Submit" value="登入管理" id="button" tabindex="4">
            </td>
          </tr>
        </form>
      </table>
    </td>
  </tr>
</table>
<?
$this->loadTmplate(TEMPLATE_PATH."public/foot.tpl.php");
?>
</body>
</html>
