<?php
		define("FILE_PATH", '../');
error_reporting(E_ALL & ~E_NOTICE);
		$now	= date('Y-m-d H:i:s');
		$email	= $_GET['e'] ? $_GET['e'] : "langr@126.com";

		$mailTmpl = FILE_PATH."include/mail/bugReply.html";
		$mailText = file_get_contents($mailTmpl);
	
		include_once(FILE_PATH."include/tools/smtp.tool.php");

		$mail	= new smtp_mail("smtp.langr.org", "hua@langr.org", "111111");
		$mail->from = "hua@langr.org";		/* ����İl���� */
		$mail->to = $email;
		$mail->subject = "test 007";
	//	$mail->text = "";
		$mail->html = $mailText;
		if ($mail->send() == 0) {
			echo "Email�ظ��ɹ�! ";
	
		} else {
			echo "Email�ظ�ʧ��, Ո�_�J�͑��]�������������!";
			echo "<br>";
			print_r($mail->errmsg);
		}

?>
