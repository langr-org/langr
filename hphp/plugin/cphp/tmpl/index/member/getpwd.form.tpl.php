<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="5">&nbsp;</td>
          <td> 
            <table width="100%" border="0" cellpadding="0" cellspacing="0" class="lineborder">
              <form name="getpwd" method="post" action="?">
                <tr> 
                  <td height="30" class="bigwhite">&nbsp;取回密碼</td>
                </tr>
                <tr> 
                  <td height="30" valign="top"> 
                    <table width="100%" border="0" align="center" cellpadding="2" cellspacing="1">
                      <tr> 
                        <td height="24" align="right" class="mainbg">用戶帳號:</td>
                        <td class="mainbg"> 
			  <input name="Account" type="text" id="value">
                        </td>
		      </tr>
                      <tr> 
                        <td height="24" align="right" class="mainbg">真實姓名:</td>
                        <td class="mainbg"> 
                          <input name="Name" type="text" id="value" tabindex="1" maxlength="200" value="">
                        </td>
		      </tr>
                      <tr> 
                        <td height="24" align="right" class="mainbg">身份證字號:</td>
                        <td class="mainbg"> 
                          <input name="IdCard" type="text" id="value" tabindex="1" maxlength="200" value="">
                        </td>
		      </tr>
                      <tr> 
                        <td height="24" align="right" class="mainbg">E-mail:</td>
                        <td class="mainbg"> 
                          <input name="Email" type="text" id="value" tabindex="1" maxlength="200" value="">
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr class="pformstrip"> 
                  <td align="center" class="pformstrip">
		    <input type="submit" name="Submit" value="提 交" id="button" tabindex="3"> 
		    <input type="reset" name="reset" value="返回" OnClick="return history.back();">
                    <input name="module" type="hidden" value="member">
                    <input name="action" type="hidden" value="getpwd">
                  </td>
                </tr>
              </form>
            </table></td>
        </tr>
      </table>
