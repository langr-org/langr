<html>
<head>
<title>麻將大悶鍋經銷商系統</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style type="text/css">
<!--
.td1 {  font-family: "新細明體"; font-size: 9pt}
.button {  font-family: "Verdana"; font-size: 9pt; height: 22px; width: 75px}
.input {  font-family: "Verdana"; font-size: 9pt; height: 22px; width: 160px}
-->
</style>
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
</head>
<body bgcolor="#FFFFFF" onload="set_focus()">
<br>
<br>
<br>
<br>
<table border="1" cellspacing="0" cellpadding="0" align="center" width="450">
  <tr bgcolor="#C6C3C6">

    <td>
        <form name="login" method="post" action="?">
      <table width="450" border="0" cellspacing="0" cellpadding="1" align="center">
        <tr bgcolor="#400080">
          <td height="20" class="td1" bgcolor="#00561f"><b><font color="#FFFFFF">
            &nbsp;麻將大悶鍋經銷商系統</font></b></td>
          <td height="20" class="td1" align="right" bgcolor="#00561f">
            <a href="javascript:close();"><img src="./images/agency/cross.gif" width="16" height="14" border=0></a> </td>
        </tr>

        <tr align="center">
          <td colspan="2">
            <form method="post" name="pass" action="?">
              <table border="0" cellspacing="0" cellpadding="1" width="100%">
                <tr>
                  <td rowspan="4" valign="top" align="right" width="12%"><img src="./images/agency/key.png" width="79" height="40">
                  </td>
                  <td colspan="2" class="td1" height="42">請輸入登入帳號及密碼</td>

                </tr>
                <tr>
                  <td class="td1" width="18%" align=right>登入帳號 &nbsp; </td>
                  <td width="70%">
                    <input type="text" name="accounts" size="22" class="input">
                  </td>
                </tr>
                <tr>

                  <td class="td1" height="35" width="18%" align=right>密　　碼 &nbsp; </td>
                  <td height="35" width="70%">
                    <input type="password" name="password" size="22" class="input">
                    <input type="hidden" name="enter" value="1">
                  </td>
                </tr>
                <tr>
                  <td class="td1" width="18%" align=right>驗 &nbsp;證 &nbsp;碼 &nbsp; </td>

                  <td width="70%">
                    <input type="text" name="verifyCode" size="9" style="font-size:9pt; font-family:Century Gothic">　　<img src="./?module=member&action=verifyCode" border=0 align="absmiddle">
					<input name="action" type="hidden" value="login">
                  </td>
                </tr>
              </table>
              <table width="100%" border="0" cellspacing="0" cellpadding="1">
                <tr>

                  <td align="right" width="47%">
                    <input type="submit" value="   確定   " class="button">
                  </td>
                  <td align="right" width="1%">&nbsp;</td>
                  <td width="52%">
                    <input type="reset" value="重設" class="button">
                  </td>
                </tr>
              </table>

            </form>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
