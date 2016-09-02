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
<-- 公共头部 -->
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
                      <div align="CENTER"><font color="#FFFFFF">註 冊 項 目</font></div>
                    </td>

                    <td class=td3> 
                      <div align="CENTER"><font color="#FFFFFF">內 容</font></div>
                    </td>
                  </tr>
                  <tr> 
                    <td class=td3 bgcolor="#ececea"><font color="#000000">帳號：</font>（<font color="#FF0000">登錄時使用</font>）<br>
                      <font color="#206a97">只能是字母<font color="#206a97">、</font>數字，長度<font color="#206a97"> <font color="#FF0000">4</font> - <font color="#FF0000">12</font> </font>個字元。</font></font></td>

                    <td class=td3 bgcolor="#f7f7f9"> &nbsp;&nbsp; 
                      <input  name="user" class=input1 size="12" maxlength="12">
                      <input name="check_user" type="button"  onClick="window.open('/user/register/user-0'+this.form.user.value+'.html','','height=300, width=600,toolbar=no,scrollbars=no,menubar=no,resizable=1');" value="檢測帳號">
                    </td>
                  </tr>
                  <tr> 
                    <td class=td3 bgcolor="#ececea">密碼：<br>
                      <font color="#206a97"><font color="#206a97">只能</font>是字母、數字、符號，長度 
                      <font color="#FF0000">4</font> - <font color="#FF0000">12</font> 
                      個位元<font color="#206a97">。</font></font></td>

                    <td class=td3 bgcolor="#f7f7f9">&nbsp;&nbsp; 
                      <input class=input1 style="WIDTH: 320px" 
            size=60 name='pwd' maxlength="12" type="PASSWORD">
                    </td>
                  </tr>
                  <tr> 
                    <td class=td3 bgcolor="#ececea"><font color="#000000">確認密碼：</font></td>
                    <td class=td3 bgcolor="#f7f7f9">&nbsp;&nbsp; 
                      <input class=input1 style="WIDTH: 320px" 
            size=60 name='check_pwd' maxlength="12" type="PASSWORD">
                    </td>
                  </tr>

                  <tr> 
                    <td class=td3 bgcolor="#ececea">密碼提示問題：<br>
                      <font color="#000000"></font></td>
                    <td class=td3 bgcolor="#f7f7f9">&nbsp;&nbsp; 
                      <input type="radio" name="pwd_Q" value="你的行動電話最後五碼是多少？" checked>
                      你的行動電話最後五碼是多少？<br>
                      &nbsp;&nbsp; 
                      <input type="radio" name="pwd_Q" value="你的身份証字號最後五碼是多少？">
                      你的身份証字號最後五碼是多少？ <br>

                      &nbsp;&nbsp; 
                      <input type="radio" name="pwd_Q" value="2">
                      自定義： 
                      <input class=input1 style="WIDTH: 300px" 
            size=60 name="pwd_q" maxlength="40" type="text" value="你最好的朋友是誰？">
                    </td>
                  </tr>
                  <tr> 
                    <td class=td3 bgcolor="#ececea">密碼提示答案：<br>
                    </td>
                    <td class=td3 bgcolor="#f7f7f9">&nbsp;&nbsp; 
                      <input size=60 name="pwd_a"  maxlength="20" type="text">

                    </td>
                  </tr>
		<tr><td class=td3 bgcolor="#ececea">
		 验证码: </td><td class=td3 bgcolor="#f7f7f9"><input type=text name='verifyCode'> <img src="<?=url("?module=user&action=verifyCode&t=gif")?>">
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
                      <div align="CENTER"><font color="#FFFFFF">註 冊 項 目</font></div>
                    </td>
                    <td class=td3> 
                      <div align="CENTER"><font color="#FFFFFF">內 容</font></div>
                    </td>
                  </tr>

                  <tr> 
                    <td class=td3 bgcolor="#ececea"><font color="#000000">匿稱：</font><br>
                      <font color="#206a97"> 只能是中文、字母、數字，長度<font color="#206a97"> <font color="#FF0000">4</font> - <font color="#FF0000">12</font> </font>個字元。</font></font></td>

                    <td class=td3 bgcolor="#f7f7f9">&nbsp;&nbsp; 
                      <input class=input1 size=12 name="nike_name" maxlength="12">
                    </td>
                  </tr>
                  <tr> 
                    <td class=td3 bgcolor="#ececea"><font color="#000000">性別：</font>			</td>
                    <td class=td3 bgcolor="#f7f7f9">&nbsp;&nbsp; 
                      <input type="radio" name="sex" value="B" checked>

                      <font color="#000000">男</font> 
                      <input type="radio" name="sex" value="G">
                      <font color="#000000">女</font> </td>
                  </tr>
                  <tr>                     <td class=td3 bgcolor="#ececea"><font color="#000000">生日：</font></td>

                    <td class=td3 bgcolor="#f7f7f9"><font color="#000000">&nbsp;&nbsp; 
                      <input class=input1 maxlength=4 size=4 
                        name="year">
                      年 
                      <select class=input1 name="month">
                        <option value=-1 ></option>
                        <option value=01 >一月</option>
                        <option 
                          value=02 >二月</option>
                        <option value=03 >三月</option>

                        <option value=04 >四月</option>
                        <option 
                          value=05 >五月</option>
                        <option value=06 >六月</option>
                        <option value=07 >七月</option>
                        <option 
                          value=08 >八月</option>
                        <option value=09 >九月</option>

                        <option value=10 >十月</option>
                        <option 
                          value=11 >十一月</option>
                        <option 
                        value=12 >十二月</option>
                      </select>
                      月 
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
                      日</font></td>
                  </tr>
                  <tr> 
                    <td class=td3 bgcolor="#ececea"><font color="#000000">電子郵件地址：</font></td>
                    <td class=td3 bgcolor="#f7f7f9">&nbsp;&nbsp; 
                      <input class=input1 style="WIDTH: 320px" 
            size=60 name="e_mail" maxlength="50">
                    </td>
                  </tr>
		<!-- <tr><td>个性签名 sign</td></tr> -->
                </table>
              </td>
            </tr>
            </tbody> 
          </table>
          <div align="center"> 
             <p align="center"> 
              <input class=button1 type="submit" name="enter" value="填寫完成，我要加入會員">
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
