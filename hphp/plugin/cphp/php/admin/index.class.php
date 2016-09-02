<?php
/**
 * ��̨
 *
 * by: langr <hua@langr.org> May 2007
 * $Id: index.class.php 6 2007-10-28 03:40:44Z langr $
 */

class Index extends Index_Public
{
	/***
	 * ʹ�� GD �⺯�� ������֤��ͼƬ
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
			$this->ErrMsg	= "����: ��½ʧ��! ��ˢ��ҳ�����µ�½.";
		} else {
			$_SESSION['admin'] = $_POST['accounts'];
			setcookie($_POST['accounts'], date("Y-m-d H-i-s"), time()+60*60*24*30);

			$this->PromptMsg = "��ʾ: ��½�ɹ�!";
			$this->UrlJump	= "?module=twmj";
		}

		$this->promptMsg();
		return;
	}

	function showLogout()
	{
		$_SESSION['admin'] = '';
		$this->PromptMsg = "��ʾ: �ǳ��ɹ�!";
		$this->UrlJump	= "?";
		$this->promptMsg();
		
		return;
	}
}
?>
