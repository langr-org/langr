<?php
/***
 * 经销商后台管理登陆
 * 
 * $Id: index.class.php 6 2007-10-28 03:40:44Z langr $
 */

class Index extends Index_Public
{
	/***
	 * 使用 GD 库函数 创建验证码图片
	 */
	function showVerifyCode() 
	{
		header("Content-type: image/png");
		$im = @imagecreatetruecolor(40, 20)
			or die("Cannot Initialize new GD image stream"); /* x, y */
		$text_color = imagecolorallocate($im, 233, 14, 91);	/* red, green, blue */

		$num	= sprintf("%04d", rand(1, 9999));
		$_SESSION['verifyCode']	= $num;

		imagestring($im, 5, 3, 2,  $num, $text_color);		/* font, x, y */
		imagepng($im);			/* output */
		imagedestroy($im);		/* free */

		return;
	}

	function showIndex() 
	{
		$this->display();
		return;
	}

	function doLogin() 
	{
		if ( ($_POST['accounts'] == '') 
				|| ($_POST['password'] == '')
				|| ($_POST['verifyCode'] != $_SESSION['verifyCode']) ) {
			$_SESSION['verifyCode']	= 0;
			$this->ErrMsg	= "错误: 登陆失败! 请刷新页面重新登陆.";
		}

		$now	= date('Y-m-d H:i:s');
		$_POST['accounts'] = ucfirst($_POST['accounts']);
		$log	= "../login/agency/".$_POST['accounts'].".log";		/* 经销商后台登入日志文件 */

		if (empty($_POST['verifyCode']) || $_POST['verifyCode'] != $_SESSION['verifyCode']) {
			$_SESSION['verifyCode']	= 0;
			$logStr	= c("@<font color=#ff0000>$now</font>@帳號: <b>".$_POST['accounts']."</b> 管理後台登入錯誤, IP: ".CLIENT_IP." [<font color=#ff0000>驗證碼錯誤</font>]");
			wlog($log, $logStr);
			$this->ErrMsg	= "错误: 登陆失败! 请刷新页面重新登陆.";
			$this->promptMsg();
		}

		$q	= $this->loadDB();
	//	$_POST['password'] = md5($_POST['password']);
		$sql	= "select Id,DealerNo,Pwd from dealerinfo where DealerNo='".$_POST['accounts']."'";
		$q->query($sql);
		$q->nextRecord();

		if ( empty($q->record['Id']) || ($_POST['password'] != $q->record['Pwd']) ) {		/* md5() */
			$_SESSION['verifyCode']	= 0;
			$logStr	= c("@<font color=#ff0000>$now</font>@帳號: <b>".$_POST['accounts']."</b> 管理後台登入錯誤, IP: ".CLIENT_IP." [<font color=#ff0000>密碼錯誤</font>]");
			$this->ErrMsg	= "错误: 登陆失败! 请刷新页面重新登陆.";
		} else {
			$_SESSION['ag_admin'] = $q->record['DealerNo'];
			$_SESSION['ag_adminId'] = $q->record['Id'];
			setcookie("ag_admin", $q->record['DealerNo'], time()+60*60*24*30);
			$logStr	= c("@$now@帳號: <b>".$_POST['accounts']."</b> 成功登入管理後台, IP: ".CLIENT_IP);

			$this->PromptMsg = "提示: 登陆成功!";
			$this->UrlJump	= "?module=twmj";
		}

		wlog($log, $logStr);
		$this->promptMsg();
		return;
	}

	function showLogout()
	{
		$_SESSION['ag_admin'] = '';
		$this->PromptMsg = "提示: 登出成功!";
		$this->UrlJump	= "?";
		$this->promptMsg();
		
		return;
	}
}
?>
