<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="5">&nbsp;</td>
          <td> 
            <table width="100%" border="0" cellpadding="0" cellspacing="0" class="lineborder">
              <form name="userinfo" method="post" action="?">
                <tr> 
                  <td height="30" background="../../../images/admin/titlembg.jpg" class="bigwhite">&nbsp;Ո������]���Y��</td>
		</tr>
                <tr> 
                  <td height="30">&nbsp;<img src="{$Img}" alt=""> ���x���� {$Nums} �����rֵ {$Money} ̨�ŵ� {$WaveName}, ���蠑����Ʒ֧�� {$Bonus} �c�t��</td>
		</tr>
                <tr> 
                  <td height="30" valign="top"> 
                    <table width="100%" border="0" align="center" cellpadding="2" cellspacing="1">
                      <tr> 
                        <td height="24" align="right" class="mainbg">�ռ�������:</td>
                        <td class="mainbg"> 
                          <input name="Name" type="text" id="value" tabindex="1" maxlength="200" value="{$Name}">
                        </td>
		      </tr>
                      <tr> 
                        <td height="24" align="right" class="mainbg">����C��̖:</td>
                        <td class="mainbg"> 
                          <input name="IdCard" type="text" id="value" tabindex="1" maxlength="20" value="{$IdCard}">
                        </td>
		      </tr>
                      <tr> 
                        <td height="24" align="right" class="mainbg">�j�Ԓ:</td>
			<td class="mainbg"> 
                          <input name="Tel" type="text" id="value" tabindex="1" maxlength="15" value="{$Tel}">
                        </td>
                      </tr>
                      <tr> 
                        <td height="24" align="right" class="mainbg">E-mail:</td>
                        <td class="mainbg"> 
                          <input name="Email" type="text" id="value" tabindex="1" maxlength="200" value="{$Email}">
                        </td>
		      </tr>
                      <tr> 
                        <td height="24" align="right" class="mainbg">�]�ĵ�ַ:</td>
                        <td class="mainbg"> 
                          <input name="Addr" type="text" tabindex="2" size=50 maxlength="200" value="{$Addr}">
                        </td>
		      </tr>
                      <tr> 
                        <td height="24" align="right" class="mainbg">�]�־��a:</td>
			<td class="mainbg"> 
                          <input name="PostCode" type="text" id="value" tabindex="1" maxlength="15" value="">
                        </td>
                      </tr>
                      <!-- tr> 
                        <td height="24" align="right" class="mainbg">��B:</td>
                        <td class="mainbg"> 
                          <input name="State" type="text" tabindex="2" maxlength="100" value="{$State}">
                        </td>
		      </tr -->
                    </table>
                  </td>
                </tr>
                <tr class="pformstrip"> 
                  <td align="center" class="pformstrip">
		    <input type="submit" name="Submit" value="�� ��" id="button" tabindex="3"> 
		    <input type="reset" name="reset" value="����" OnClick="return history.back();">
                    <input name="module" type="hidden" value="member">
                    <input name="action" type="hidden" value="doExchange">
                    <input name="Nums" type="hidden" value="{$Nums}">
                    <input name="WaveId" type="hidden" value="{$WaveId}">
                    <input name="WaveName" type="hidden" value="{$WaveName}">
		    <input name="Money" type="hidden" value="{$Money}">
                    <input name="Bonus" type="hidden" value="{$Bonus}">
                  </td>
                </tr>
              </form>
            </table></td>
        </tr>
      </table>
