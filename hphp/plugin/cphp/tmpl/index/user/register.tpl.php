<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>{$webSiteTitle}</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="./include/css/index.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.style4 {color: #FFFFFF}
.style8 {color: #FFFF00}
-->
</style>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<-- ����ͷ�� -->
<?
$this->loadTmplate(TEMPLATE_PATH."public/head.tpl.php");
?>
<br>
<div style="clear:both;"></div><form name="form1" method="post" action="?">
<table width="640" border="0" align="center">
  <tr> 
    <td>&nbsp;</td>
  </tr>

  <tr> 
    <td> 
      <div align="CENTER"> 
        <table id=Table5 cellspacing=0 cellpadding=0 width="100%" border=0>
          <tbody> 
          <tr> 
              <td bgcolor=#6699cc width="660">
                <table width="100%" border="0" cellpadding="4" cellspacing="1">
                  <tr bgcolor="#b1c6f2"> 
                    <td class=td3 width="30%"> 
                      <div align="CENTER"><font color="#FFFFFF">�] �� � Ŀ</font></div>
                    </td>

                    <td class=td3> 
                      <div align="CENTER"><font color="#FFFFFF">�� ��</font></div>
                    </td>
                  </tr>
                  <tr> 
                    <td class=td3 bgcolor="#ececea"><font color="#000000">��̖��</font>��<font color="#FF0000">��䛕rʹ��</font>��<br>
                      <font color="#206a97">ֻ������ĸ<font color="#206a97">��</font>���֣��L��<font color="#206a97"> <font color="#FF0000">4</font> - <font color="#FF0000">12</font> </font>����Ԫ��</font></font></td>

                    <td class=td3 bgcolor="#f7f7f9"> &nbsp;&nbsp; 
                      <input  name="user" class=input1 size="12" maxlength="12">
                      <input name="check_user" type="button"  onClick="window.open('/user/register/user-0'+this.form.user.value+'.html','','height=300, width=600,toolbar=no,scrollbars=no,menubar=no,resizable=1');" value="�z�y��̖">
                    </td>
                  </tr>
                  <tr> 
                    <td class=td3 bgcolor="#ececea">�ܴa��<br>
                      <font color="#206a97"><font color="#206a97">ֻ��</font>����ĸ�����֡���̖���L�� 
                      <font color="#FF0000">4</font> - <font color="#FF0000">12</font> 
                      ��λԪ<font color="#206a97">��</font></font></td>

                    <td class=td3 bgcolor="#f7f7f9">&nbsp;&nbsp; 
                      <input class=input1 style="WIDTH: 320px" 
            size=60 name='pwd' maxlength="12" type="PASSWORD">
                    </td>
                  </tr>
                  <tr> 
                    <td class=td3 bgcolor="#ececea"><font color="#000000">�_�J�ܴa��</font></td>
                    <td class=td3 bgcolor="#f7f7f9">&nbsp;&nbsp; 
                      <input class=input1 style="WIDTH: 320px" 
            size=60 name='check_pwd' maxlength="12" type="PASSWORD">
                    </td>
                  </tr>

                  <tr> 
                    <td class=td3 bgcolor="#ececea">�ܴa��ʾ���}��<br>
                      <font color="#000000"></font></td>
                    <td class=td3 bgcolor="#f7f7f9">&nbsp;&nbsp; 
                      <input type="radio" name="pwd_Q" value="����Є��Ԓ������a�Ƕ��٣�" checked>
                      ����Є��Ԓ������a�Ƕ��٣�<br>
                      &nbsp;&nbsp; 
                      <input type="radio" name="pwd_Q" value="�������^��̖������a�Ƕ��٣�">
                      �������^��̖������a�Ƕ��٣� <br>

                      &nbsp;&nbsp; 
                      <input type="radio" name="pwd_Q" value="2">
                      �Զ��x�� 
                      <input class=input1 style="WIDTH: 300px" 
            size=60 name="pwd_q" maxlength="40" type="text" value="����õ��������l��">
                    </td>
                  </tr>
                  <tr> 
                    <td class=td3 bgcolor="#ececea">�ܴa��ʾ�𰸣�<br>
                    </td>
                    <td class=td3 bgcolor="#f7f7f9">&nbsp;&nbsp; 
                      <input size=60 name="pwd_a"  maxlength="20" type="text">

                    </td>
                  </tr>
		<tr><td class=td3 bgcolor="#ececea">
		 ��֤��: </td><td class=td3 bgcolor="#f7f7f9"><input type=text name='verifyCode'> <img src="<?=url("?module=user&action=verifyCode&t=gif")?>">
		    </td>
		  </tr>
                </table>
              </td>
          </tr>
          </tbody> 
        </table>
          <table id=Table5 cellspacing=0 cellpadding=0 width="100%" border=0>
            <tbody> 
            <tr> 
              <td bgcolor=#6699cc width="660">

                <table width="100%" border="0" cellpadding="4" cellspacing="1">
                  <tr bgcolor="#b1c6f2"> 
                    <td class=td3 width="30%"> 
                      <div align="CENTER"><font color="#FFFFFF">�] �� � Ŀ</font></div>
                    </td>
                    <td class=td3> 
                      <div align="CENTER"><font color="#FFFFFF">�� ��</font></div>
                    </td>
                  </tr>

                  <tr> 
                    <td class=td3 bgcolor="#ececea"><font color="#000000">��Q��</font><br>
                      <font color="#206a97"> ֻ�������ġ���ĸ�����֣��L��<font color="#206a97"> <font color="#FF0000">4</font> - <font color="#FF0000">12</font> </font>����Ԫ��</font></font></td>

                    <td class=td3 bgcolor="#f7f7f9">&nbsp;&nbsp; 
                      <input class=input1 size=12 name="nike_name" maxlength="12">
                    </td>
                  </tr>
                  <tr> 
                    <td class=td3 bgcolor="#ececea"><font color="#000000">�Ԅe��</font>			</td>
                    <td class=td3 bgcolor="#f7f7f9">&nbsp;&nbsp; 
                      <input type="radio" name="sex" value="B" checked>

                      <font color="#000000">��</font> 
                      <input type="radio" name="sex" value="G">
                      <font color="#000000">Ů</font> </td>
                  </tr>
                  <tr>                     <td class=td3 bgcolor="#ececea"><font color="#000000">���գ�</font></td>

                    <td class=td3 bgcolor="#f7f7f9"><font color="#000000">&nbsp;&nbsp; 
                      <input class=input1 maxlength=4 size=4 
                        name="year">
                      �� 
                      <select class=input1 name="month">
                        <option value=-1 ></option>
                        <option value=01 >һ��</option>
                        <option 
                          value=02 >����</option>
                        <option value=03 >����</option>

                        <option value=04 >����</option>
                        <option 
                          value=05 >����</option>
                        <option value=06 >����</option>
                        <option value=07 >����</option>
                        <option 
                          value=08 >����</option>
                        <option value=09 >����</option>

                        <option value=10 >ʮ��</option>
                        <option 
                          value=11 >ʮһ��</option>
                        <option 
                        value=12 >ʮ����</option>
                      </select>
                      �� 
                      <select class=input1 name="day">
                        <option value=-1 selected></option>
                        <option value=01 >1</option>

                        <option value=02 >2</option>
                        <option value=03 >3</option>
                        <option value=04 >4</option>
                        <option value=05 >5</option>
                        <option value=06 >6</option>
                        <option value=07 >7</option>

                        <option value=08 >8</option>
                        <option value=09 >9</option>
                        <option 
                          value=10 >10</option>
                        <option value=11>11</option>
                        <option value=12 >12</option>
                        <option 
                          value=13 >13</option>

                        <option value=14 >14</option>
                        <option value=15 >15</option>
                        <option 
                          value=16 >16</option>
                        <option value=17 >17</option>
                        <option value=18 >18</option>
                        <option 
                          value=19 >19</option>

                        <option value=20 >20</option>
                        <option value=21 >21</option>
                        <option value=22 >22</option>
                        <option value=23 >23</option>
                        <option value=24 >24</option>
                        <option 
                          value=25 >25</option>

                        <option value=26 >26</option>
                        <option value=27 >27</option>
                        <option 
                          value=28 >28</option>
                        <option value=29 >29</option>
                        <option value=30 >30</option>
                        <option 
                          value=31 >31</option>

                      </select>
                      ��</font></td>
                  </tr>
                  <tr> 
                    <td class=td3 bgcolor="#ececea"><font color="#000000">����]����ַ��</font></td>
                    <td class=td3 bgcolor="#f7f7f9">&nbsp;&nbsp; 
                      <input class=input1 style="WIDTH: 320px" 
            size=60 name="e_mail" maxlength="50">
                    </td>
                  </tr>
		<!-- <tr><td>����ǩ�� sign</td></tr> -->
                </table>
              </td>
            </tr>
            </tbody> 
          </table>
          <div align="center"> 
             <p align="center"> 
              <input class=button1 type="submit" name="enter" value="���ɣ���Ҫ������T">
              <input type="hidden" name="module" value="user">
              <input type="hidden" name="action" value="register">
            </p>
          </div>
        </div>
    </td>
  </tr>

</table>
</form>
<?
$this->loadTmplate(TEMPLATE_PATH."public/foot.tpl.php");
?>
 </body>
</html>
