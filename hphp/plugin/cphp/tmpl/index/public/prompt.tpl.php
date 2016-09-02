<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="gb2312">

<html>
<head>
	<title>等待跳转 - {$webSiteTitle}</title>
	<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
	<meta http-equiv='Refresh' content='{$waitSecond}; URL={$autoJumpUrl}'>
	<link rel="StyleSheet" href="../../../include/css/index.css" type="Text/css">
<script language="JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
</head>
<body>
<?
$this->loadTmplate(TEMPLATE_PATH."public/head.tpl.php");
?>
<table cellspacing=0 cellpadding=0 width=750 border=0 align="center">
  <tbody> 
  <tr> 
    <td><br>
      <br>
      <br>
      <br>
    </td>
  </tr>
  <tr> 
    <td> 
      <div align=center>
        <table cellspacing=0 cellpadding=2 width="80%" border=0>
          <tbody> 
          <tr bgcolor=#cccccc> 
            <td height="26" bgcolor="#6699FF" align="left"><b class=small1210>　系统提示</b></td>
          </tr>
          <tr bgcolor=#cccccc> 
            <td bgcolor="#6699FF">
              <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
                <tr> 
                  <td height="160" valign="middle"> 
                    <table height="80%" cellspacing=5 cellpadding=0 width="80%" 
                  align=center border=0>
                      <tbody> 
                      <tr> 
                        <td height="70%"> 
                          <div align=center>{$message}</div>
                        </td>
                      </tr>
                      <tr> 
                        <td align="center" height="30%">{$errorType}</td>
                      </tr>
                      </tbody> 
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          </tbody> 
        </table>
        
      </div>
    </td>
  </tr>
  <tr> 
    <td><br>
      <br>
      <br>
      <br>
    </td>
  </tr>
  </tbody> 
</table>
<?
$this->loadTmplate(TEMPLATE_PATH."public/foot.tpl.php");
?>
</body>
</html>