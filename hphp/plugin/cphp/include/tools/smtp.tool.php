<?php
/**
 * smtp.tool.php v0.3.2
 * 使用 smtp 协议发送邮件工具类, 一般需要认证
 * 
 * ps:在网上找的好多 php 写的邮件发送工具基本都用不了, 
 *    于是就找了些 smtp 协议资料自己动手...
 *
 * Last modify: hua <hua@langr.org> Sep 2007
 * by langr <langr@126.com> Feb 2007
 * $Id: smtp.tool.php 8 2009-10-20 10:05:34Z langr $
 */

/***
 * Usage:
 *	include_once(FILE_PATH."include/tools/smtp.tool.php");
 * 	$mail	= new smtp_mail(smtp host, user, password, port, user domain);	// default => localhost, '', '', 25, false
 * 	(1): $mail	= new smtp_mail('smtp.126.com', 'langr@126.com', 'password');
 * 	(2): $mail	= new smtp_mail('ssl://smtp.gmail.com', 'loghua@gmail.com', 'password', 465);
 * 	(3): $mail	= new smtp_mail('smtp.langr.org', 'hua@langr.org', 'password', 25, true);
 * 	(4): $mail	= new smtp_mail('ssl://smtp.gmail.com', 'web@betcity.com.tw', 'password', 465, true);
 * 	(5): $mail	= new smtp_mail('ms.betcity.tw', 'server@ms.betcity.tw', '', 25, false);		// 不需要认证的邮件服务器
 * ||	$mail->from	= "langr<langr@126.com>";
 * 	$mail->to	= "loghua<loghua@gmail.com>,your<your@163.com>";
 * 	$mail->subject	= "test";
 * *	$mail->text	= "Hello, world!";
 * *	$mail->html	= "<html><body><font size=20>hi</font></body></html>";
 * *	$mail->attachment = "a.txt,b.jpg";
 * 	$mail->send();		// do not encrypt
 * ||	$mail->charset	= "gb2312"	// default => utf-8, 
 * ||	$mail->mail['user']	= base64_encode($user);
 * ||	$mail->mail['pw']	= base64_encode($password);
 * ||	$mail->send();		// do encrypt (base64_encode or other)
 * 一些不太好的用法:
 * 	$mail->from	= "addcn<webmaster@addcn.com>";	// 伪造的发送者
 * || 	$mail->mail['from'] = "<langr@126.com>";	// 真正的发送者: <email>
 * 	$mail->mail['to'] = "<langr@126.com>";		// 秘密的收信者: <email1>,<email2>
 */

/***
 * smtp 发邮件的基础类
 * NOET: 以 "_" 打头的函数应小心使用...
 */
class _mail
{
#	var	$connect['host']	= "localhost";
#	var	$connect['port']	= 25;
#	var	$connect['timeout']	= 20;
#	var	$connect['sock_fd']	= -1;
	var	$islogin	= 0;
	var	$errno	= 0;
	var	$errmsg	= array();
#	var	$mail['auth']	= True;
#	var	$mail['user']	= "";		/* base64 */
#	var	$mail['pw']	= "";		/* base64 */
#	var	$mail['from']	= "";
#	var	$mail['to']	= "";
#	var	$mail['body']	= "";		/* 已构造好的邮件源文件 */

	function _mail()
	{
		define('BUF_SIZE', 512);

		$this->mail['auth']	= True;

		return 0;
	}

	/***
	 * connect smtp
	 * return: sockfd - 成功, -1 - 失败
	 */
	function connect($h = "localhost", $p = 25, $tt = 20)
	{
		$this->connect['host'] = $h;
		$this->connect['port'] = $p;
		$this->connect['timeout'] = $tt;

		if ($this->connect['host'] == "") {
			$this->errmsg[0] = "Error: no host";
			return -1;
		}

		$this->connect['sock_fd'] = @fsockopen($this->connect['host'], 
						$this->connect['port'], 
						$errno, 
						$errmsg, 
						$this->connect['timeout']);
		$this->errmsg[0] = "host:".$this->connect['host']." port:".$this->connect['port']." sockfd:".$this->connect['sock_fd']."\r\n";
		if (!$this->connect['sock_fd']) {
			$this->errno	= 1;
			$this->errmsg[$this->errno] = $errno." Error: ".$errmsg;
			return -1;
		}

		$recv = fgets($this->connect['sock_fd'], BUF_SIZE);
		if (substr($recv, 0, 1) != "2") {	/* return 220 ... */
			$this->errno	= 1;
			$this->errmsg[$this->errno] = "connect error by service";
			return -1;
		}

		return $this->connect['sock_fd'];
	}

	/**
	 * _command()
	 * 直接调用时应在每个命令后需加上 "\r\n"
	 * return: 服务器返回码 - 成功, -1 - 失败
	 */
	function _command($command, $code = "2", $code_len = 1)
	{
		if ($this->connect['sock_fd'] <= 0) {
			$this->errno	= 2;
			$this->errmsg[$this->errno] = "Error: not connect smtp";
			return -1;
		}
	
		if (!@fputs($this->connect['sock_fd'], $command)) {
			$this->errno	= 3;
			$this->errmsg[$this->errno] = "Error: can not send command: ".$command;
			return -1;
		}
		//if ($this->debug) echo "command:".$command."<br>";
		$recv = @fread($this->connect['sock_fd'], BUF_SIZE);
		$s_code = substr($recv, 0, $code_len);
		//if ($this->debug) echo "return:".$recv."<p>";
		if ( $s_code != $code ) {
			$this->errno	= 4;
			$this->errmsg[$this->errno] .= "\r\nlatest error: ".$recv;
		}
		
		return $s_code;
	}

	/**
	 * 发送 smtp 指令
	 * 如果指令没正常执行, 则重复发送, 最多 3 次
	 * 这是我根据自己用 telnet 发送邮件时产生的一点想法, 
	 * 对于比较繁忙的服务器或许有用 ...
	 * return: 0 - 成功, -1 - 失败
	 */
	function command($com, $str, $code = "2", $code_len = 1)
	{
		if ($this->connect['sock_fd'] <= 0) {
			$this->errno	= 2;
			$this->errmsg[$this->errno] = "Error: not connect smtp";
		}

		if (empty($str)) 
			$command = $com."\r\n";
		else 
			$command = $com." ".$str."\r\n";

		for ($i = 0; $i < 3; $i++) {
			if ( ($r_code=$this->_command($command, $code, $code_len)) == $code )
				return 0;
			//if ($this->debug) echo "return:".$r_code."q:".$code."<br>";
		}
		
		$this->errno	= 4;
		$this->errmsg[$this->errno] .= "\r\nError: command can not implemented: ".$command;
		return -1;
	}

	/**
	 * 向服务器发起 EHLO 登陆, 并认证
	 * 这里发送已经过加密的用户和密码
	 * return: 0 - 成功, -1 - 失败
	 */
	function login($user, $pw)
	{
		if (!$this->mail['auth']) {
			if ( $this->command("HELO", $this->connect['host'], "250", 3) < 0 )
				return -1;
		} else {
			if ( $this->command("EHLO", $this->connect['host'], "250", 3) < 0 )	/* 服务端有多行返回 */
				return -1;
			if ( $this->command("AUTH LOGIN", "", "334", 3) < 0 )
				return -1;
			if ( $this->command($user, "", "334", 3) < 0 )
				return -1;
			if ( $this->_command($pw."\r\n", "235", 3) != "235" ) {
				$this->errno	= 5;
				$this->errmsg[$this->errno] = "Error: login failed";
				return -1;
			}
		}
		
		$this->islogin = 1;
		return 0;
	}
	
	/** _send() 发送已构造好的 mail 源文件 */
	function _send()
	{
		if ($this->mail['auth'] && !$this->islogin) {
			$this->errno	= 6;
			$this->errmsg[$this->errno] = "Error: not login";
			return -1;
		}
		
		if ( $this->command("MAIL FROM:", $this->mail['from'], "250", 3) < 0 )
			return -1;
		$mail	= explode(",", $this->mail['to']);
		for ($i = 0; $i < count($mail); $i++) {
			if ( $this->command("RCPT TO:", $mail[$i], "250", 3) < 0 )
				return -1;
		}
		if ( $this->command("DATA", "", "354", 3) < 0 )
			return -1;

		/** send mail body ... */
		$end	= "\r\n.\r\n";
		if ( $this->_command($this->mail['body'].$end, "250", 3) != "250" ) {
			$this->errno	= 7;
			$this->errmsg[$this->errno] = "Error: send mail failed";
			return -1;
		}

		return 0;
	}

	function quit()
	{
		if ($this->connect['sock_fd'] > 0) {
			$this->_command("QUIT\r\n", "221", 3);
			fclose($this->connect['sock_fd']);
			$this->connect['sock_fd'] = -1;
			$this->islogin = 0;
			$this->errno	= 2;
			$this->errmsg[$this->errno] = "quit by client";
		}
		return 0;
	}
}

class smtp_mail extends _mail
{
	var	$subject = "Welcome to the addcn.com";	//
	var	$from	= "";				//
	var	$to	= "";				//
	var	$header	= "";
	var	$text	= "This mail send by betcity.com.tw\r\n";	//
	var	$html	= "";				//
	var	$attachment = "";			//
	var	$att_encode = array();
	var	$charset = "utf-8";			//

	/***
	 * $host: smtp 主机, 如果使用 ssl 加密主机, 则用如: ssl://smtp.xxx.xxx
	 * $user: 用户名, $pw 密码, $port 端口, $tt 超时
	 * $user_domain: 使用独立的域名, 如在 smtp.gmail.com 主机上使用 @langr.org 为后缀的邮箱
	 */
	function smtp_mail($host = "localhost", $user = "", $pw = "", $port = 25, $user_domain = false, $tt = 20)
	{
		$this->_mail();				/* ... */

		define('ATT_MAXSIZE', 4194304);		/* 附件大小限制: 4M */

		$this->connect['host'] = $host;
		$this->connect['port'] = $port;
		$this->connect['timeout'] = $tt;
		$this->from = $user;
		$this->mail['from'] = "<".$user.">";
		$u	= explode("@", $user);
		if ( empty($pw) )
			$this->mail['auth'] = false;
		$u[0]	= $user_domain ? $user : $u[0];
		$this->mail['user'] = base64_encode($u[0]);
		$this->mail['pw'] = base64_encode($pw);

		return 0;
	}

	function _build_header($from, $to, $subject)
	{
		$this->header = "From: ".$from."\r\n";
		$this->header .= "To: ".$to."\r\n";
		$this->header .= "Subject: ".$subject."\r\n";
		$this->header .= "MIME-Version: 1.0\r\n";

		return 0;
	}

	/**
	 * 构造正文及附件各部分
	 * return: 0 - 正常, 正数 - 附件超过规定大小
	 */
	function _build_content()
	{
		if ($this->text != "") {
			$type	= "Content-Type: text/plain; charset=\"".$this->charset."\"\r\n";
			$type	.= "Content-Transfer-Encoding: 7bit\r\n"; /* stripslashes() */
			$this->text = $type."\r\n".$this->text;
		}

		if ($this->html != "") {
			$type	= "Content-Type: text/html; charset=\"".$this->charset."\"\r\n";
			$type	.= "Content-Transfer-Encoding: 7bit\r\n";
			$this->html = $type."\r\n".$this->html;
		}

		if ($this->attachment != "") {
			$file	= explode(",", $this->attachment);
			$i	= 0;
			$att_size = 0;
			while ($file[$i]) {
				$name	= substr(strrchr($file[$i], "/|\\"), 1);
				$name	= $name ? $name : $file[$i];
				$type	= "Content-Type: ".$this->get_file_type($file[$i])."; name=\"".$name."\"\r\n";
				$type	.= "Content-Transfer-Encoding: base64\r\n";
				$type	.= "Content-Disposition: attachment; filename=\"".$name."\"\r\n";
				$att_size += filesize($file[$i]);
				$con	= file_get_contents($file[$i]);
				$this->att_encode[$i] = $type."\r\n".chunk_split(base64_encode($con));
				$i++;
			}

			if ($att_size > ATT_MAXSIZE) {
				$this->errno	= 9;
				$this->errmsg[$this->errno] = "Error: attachment is too big: ".ceil($att_size/1024)."K > ".round(ATT_MAXSIZE/1024)."K";
				return $att_size;
			}
		}

		return 0;
	}
	
	/**
	 * 构造完整的 email 源文件 => $this->mail['body']
	 * return: 0 - 正常, 正数 - 附件过大
	 */
	function build_mail($from, $to, $subject)
	{
		$this->_build_header($from, $to, $subject);
		$flag	= $this->_build_content();	
		$boundary = md5(rand());
		$this->mail['body'] = $this->header;
		$this->mail['body'] .= "Content-Type: multipart/mixed; boundary=\"".$boundary."\"\r\n";
		$this->mail['body'] .= "\r\n--".$boundary."\r\n";
		/* 正文 */
		if ( $this->text != "" && $this->html != "" ) {
			$t_boundary = "Boundary-1=".$boundary;
			$part	= "Content-Type: multipart/alternative; boundary=\"".$t_boundary."\"\r\n";
			$part	.= "\r\n--".$t_boundary."\r\n".$this->text."\r\n--".$t_boundary."\r\n";
			$part	.= $this->html."\r\n--".$t_boundary."--\r\n";
			
		} else {
			$part	= empty($this->text) ? $this->html : $this->text;
		}
		$this->mail['body'] .= $part."\r\n--".$boundary;
		/* 附件 */
		if ($this->attachment != "") {
			$i = 0;
			while ($this->att_encode[$i]) {
				$this->mail['body'] .= "\r\n".$this->att_encode[$i];
				$this->mail['body'] .= "\r\n--".$boundary;
				$i++;
			}
		}
		$this->mail['body'] .= "--\r\n";

		return $flag;
	}
	
	function send()
	{
		if ($this->check_args() < 0) 
			return -1;

		if ($this->build_mail($this->from, $this->to, $this->subject) > 0)
			return -1;

		if ($this->connect['sock_fd'] <= 0) 
			$this->connect($this->connect['host'], $this->connect['port']);
		if ($this->islogin != 1) 
			$this->login($this->mail['user'], $this->mail['pw']);

		if ($this->_send() < 0)
			return -1;

		$this->quit();

		return 0;
	}

	function get_file_type($file_name) {
		$extension = strrchr($file_name, "."); 
		switch($extension){ 
			case ".jpeg":	
			case ".jpg":	return  "image/jpeg";
			case ".gif":	return  "image/gif"; 
			case ".png":	return  "image/x-png"; 
			case ".tif":	return  "image/tif";
	
			case ".htm":
			case ".php":
			case ".shtml":  
			case ".html":	return  "text/html";
			case ".txt":	return  "text/plain"; 
	
			case ".gz":	return  "application/x-gzip"; 
			case ".tar":	return  "application/x-tar"; 
			case ".zip":	return  "application/zip"; 
			case ".pdf":	return  "application/pdf"; 
			default:	return  "application/octet-stream"; 
		}
	}

	function check_args()
	{
		if ($this->mail['auth'] && (!$this->mail['user'] || !$this->mail['pw'])) {
			$this->errno	= 8;
			$this->errmsg[$this->errno] = "Error: user or password is empty";
			return -1;
		}

		/* 提取 mail addr ... */
		preg_match_all("/[a-zA-Z0-9\._-]*@[a-zA-Z0-9\._-]*/", $this->from, $matches);
		if (!$this->mail['from']) 		/* 可以伪造发件人 $this->from */
			$this->mail['from'] = "<".$matches[0][0].">";
		preg_match_all("/[a-zA-Z0-9\._-]*@[a-zA-Z0-9\._-]*/", $this->to, $matches_to);
		for ($i = 0; $i < count($matches_to[0]); $i++) {
			$this->mail['to'] .= ",<".$matches_to[0][$i].">";
		}
		if ($this->mail['to'][0] == ',')
			$this->mail['to'] = substr($this->mail['to'], 1);
		if (count($matches[0]) < 1 || count($matches_to[0]) < 1) {
			$this->errno	= 8;
			$this->errmsg[$this->errno] = "Error: mail addr invalidation";
			return -1;
		}

		return 0;
	}
}
?>
