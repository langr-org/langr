<?php
/**
 * smtp.tool.php v0.3.2
 * ʹ�� smtp Э�鷢���ʼ�������, һ����Ҫ��֤
 * 
 * ps:�������ҵĺö� php д���ʼ����͹��߻������ò���, 
 *    ���Ǿ�����Щ smtp Э�������Լ�����...
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
 * 	(5): $mail	= new smtp_mail('ms.betcity.tw', 'server@ms.betcity.tw', '', 25, false);		// ����Ҫ��֤���ʼ�������
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
 * һЩ��̫�õ��÷�:
 * 	$mail->from	= "addcn<webmaster@addcn.com>";	// α��ķ�����
 * || 	$mail->mail['from'] = "<langr@126.com>";	// �����ķ�����: <email>
 * 	$mail->mail['to'] = "<langr@126.com>";		// ���ܵ�������: <email1>,<email2>
 */

/***
 * smtp ���ʼ��Ļ�����
 * NOET: �� "_" ��ͷ�ĺ���ӦС��ʹ��...
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
#	var	$mail['body']	= "";		/* �ѹ���õ��ʼ�Դ�ļ� */

	function _mail()
	{
		define('BUF_SIZE', 512);

		$this->mail['auth']	= True;

		return 0;
	}

	/***
	 * connect smtp
	 * return: sockfd - �ɹ�, -1 - ʧ��
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
	 * ֱ�ӵ���ʱӦ��ÿ������������ "\r\n"
	 * return: ������������ - �ɹ�, -1 - ʧ��
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
	 * ���� smtp ָ��
	 * ���ָ��û����ִ��, ���ظ�����, ��� 3 ��
	 * �����Ҹ����Լ��� telnet �����ʼ�ʱ������һ���뷨, 
	 * ���ڱȽϷ�æ�ķ������������� ...
	 * return: 0 - �ɹ�, -1 - ʧ��
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
	 * ����������� EHLO ��½, ����֤
	 * ���﷢���Ѿ������ܵ��û�������
	 * return: 0 - �ɹ�, -1 - ʧ��
	 */
	function login($user, $pw)
	{
		if (!$this->mail['auth']) {
			if ( $this->command("HELO", $this->connect['host'], "250", 3) < 0 )
				return -1;
		} else {
			if ( $this->command("EHLO", $this->connect['host'], "250", 3) < 0 )	/* ������ж��з��� */
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
	
	/** _send() �����ѹ���õ� mail Դ�ļ� */
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
	 * $host: smtp ����, ���ʹ�� ssl ��������, ������: ssl://smtp.xxx.xxx
	 * $user: �û���, $pw ����, $port �˿�, $tt ��ʱ
	 * $user_domain: ʹ�ö���������, ���� smtp.gmail.com ������ʹ�� @langr.org Ϊ��׺������
	 */
	function smtp_mail($host = "localhost", $user = "", $pw = "", $port = 25, $user_domain = false, $tt = 20)
	{
		$this->_mail();				/* ... */

		define('ATT_MAXSIZE', 4194304);		/* ������С����: 4M */

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
	 * �������ļ�����������
	 * return: 0 - ����, ���� - ���������涨��С
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
	 * ���������� email Դ�ļ� => $this->mail['body']
	 * return: 0 - ����, ���� - ��������
	 */
	function build_mail($from, $to, $subject)
	{
		$this->_build_header($from, $to, $subject);
		$flag	= $this->_build_content();	
		$boundary = md5(rand());
		$this->mail['body'] = $this->header;
		$this->mail['body'] .= "Content-Type: multipart/mixed; boundary=\"".$boundary."\"\r\n";
		$this->mail['body'] .= "\r\n--".$boundary."\r\n";
		/* ���� */
		if ( $this->text != "" && $this->html != "" ) {
			$t_boundary = "Boundary-1=".$boundary;
			$part	= "Content-Type: multipart/alternative; boundary=\"".$t_boundary."\"\r\n";
			$part	.= "\r\n--".$t_boundary."\r\n".$this->text."\r\n--".$t_boundary."\r\n";
			$part	.= $this->html."\r\n--".$t_boundary."--\r\n";
			
		} else {
			$part	= empty($this->text) ? $this->html : $this->text;
		}
		$this->mail['body'] .= $part."\r\n--".$boundary;
		/* ���� */
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

		/* ��ȡ mail addr ... */
		preg_match_all("/[a-zA-Z0-9\._-]*@[a-zA-Z0-9\._-]*/", $this->from, $matches);
		if (!$this->mail['from']) 		/* ����α�췢���� $this->from */
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
