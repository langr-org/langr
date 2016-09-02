<?php
/**
 * 后台
 *
 * by: langr <hua@langr.org> May 2007
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
		if (($_POST['accounts'] != $this->Setup['admin']) 
				|| ($_POST['password'] != $this->Setup['adminPwd'])
				|| ($_POST['verifyCode'] != $_SESSION['verifyCode']) ) {
			$_SESSION['verifyCode']	= 0;
			$this->ErrMsg	= "错误: 登陆失败! 请刷新页面重新登陆.";
		} else {
			$_SESSION['admin'] = $_POST['accounts'];
			setcookie($_POST['accounts'], date("Y-m-d H-i-s"), time()+60*60*24*30);

			$this->PromptMsg = "提示: 登陆成功!";
			$this->UrlJump	= "?module=twmj";
		}

		$this->promptMsg();
		return;
	}

	function showLogout()
	{
		$_SESSION['admin'] = '';
		$this->PromptMsg = "提示: 登出成功!";
		$this->UrlJump	= "?";
		$this->promptMsg();
		
		return;
	}
}
?>
