<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="5">&nbsp;</td>
          <td> 
            <table width="100%" border="0" cellpadding="0" cellspacing="0" class="lineborder">
              <form name="userinfo" method="post" action="?">
                <tr> 
                  <td height="30" background="../../../images/admin/titlembg.jpg" class="bigwhite">&nbsp;請確認您的郵寄資料</td>
		</tr>
                <tr> 
                  <td height="30">&nbsp;<img src="{$Img}" alt=""> 您選擇了 {$Nums} 件總價值 {$Money} 台幣的 {$WaveName}, 您需爲此商品支付 {$Bonus} 點紅利</td>
		</tr>
                <tr> 
                  <td height="30" valign="top"> 
                    <table width="100%" border="0" align="center" cellpadding="2" cellspacing="1">
                      <tr> 
                        <td height="24" align="right" class="mainbg">收件人姓名:</td>
                        <td class="mainbg"> 
                          {$Name}
                    	<input name="Name" type="hidden" value="{$Name}">
                        </td>
		      </tr>
                      <tr> 
                        <td height="24" align="right" class="mainbg">身份證字號:</td>
                        <td class="mainbg"> 
                          {$IdCard}
                    	<input name="IdCard" type="hidden" value="{$IdCard}">
                        </td>
		      </tr>
                      <tr> 
                        <td height="24" align="right" class="mainbg">聯絡電話:</td>
			<td class="mainbg"> 
                          {$Tel}
                    	<input name="Tel" type="hidden" value="{$Tel}">
                        </td>
                      </tr>
                      <tr> 
                        <td height="24" align="right" class="mainbg">E-mail:</td>
                        <td class="mainbg"> 
                          {$Email}
                    	<input name="Email" type="hidden" value="{$Email}">
                        </td>
		      </tr>
                      <tr> 
                        <td height="24" align="right" class="mainbg">郵寄地址:</td>
                        <td class="mainbg"> 
                          {$Addr}
                    	<input name="Addr" type="hidden" value="{$Addr}">
                        </td>
		      </tr>
                      <tr> 
                        <td height="24" align="right" class="mainbg">郵局編碼:</td>
			<td class="mainbg"> 
			  {$PostCode}
                    	<input name="PostCode" type="hidden" value="{$PostCode}">
                        </td>
                      </tr>
                      <!-- tr> 
                        <td height="24" align="right" class="mainbg">狀態:</td>
                        <td class="mainbg"> 
                          <input name="State" type="text" tabindex="2" maxlength="100" value="{$State}">
                        </td>
		      </tr -->
                    </table>
                  </td>
                </tr>
                <tr class="pformstrip"> 
                  <td align="center" class="pformstrip">
		    <input type="submit" name="enter" value="確 認" id="button" tabindex="3"> 
		    <input type="reset" name="reset" value="返回" OnClick="return history.back();">
                    <input name="module" type="hidden" value="member">
                    <input name="action" type="hidden" value="doExchange">
                    <input name="Nums" type="hidden" value="{$Nums}">
                    <input name="WaveId" type="hidden" value="{$WaveId}">
		    <input name="Money" type="hidden" value="{$Money}">
                    <input name="Bonus" type="hidden" value="{$Bonus}">
                  </td>
                </tr>
              </form>
            </table></td>
        </tr>
      </table>
