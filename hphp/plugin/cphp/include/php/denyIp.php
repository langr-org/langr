<?php
	@include ("./include/config/denyIp.inc.php");
	$ip1   = getenv('REMOTE_ADDR');
	$ipArr = split('\.',$ip1);
	$ip2   = $ipArr[0].".".$ipArr[1];
	$ip3   = $ipArr[0].".".$ipArr[1].".".$ipArr[2];
	$f_ip1  = getenv('HTTP_X_FORWARDED_FOR');
	if (!empty($f_ip1)) {
		$fIpArr = split('\.',$f_ip1);
		$f_ip2   = $fIpArr[0].".".$fIpArr[1];
		$f_ip3   = $fIpArr[0].".".$fIpArr[1].".".$fIpArr[2];
	} else {
		$f_ip1   = 9999;
		$f_ip2   = 9999;
		$f_ip3   = 9999;
	}
	$c_ip1  = getenv('CLIENT_IP');
	if (!empty($c_ip1)) {
		$cIpArr = split('\.',$c_ip1);
		$c_ip2   = $cIpArr[0].".".$cIpArr[1];
		$c_ip3   = $cIpArr[0].".".$cIpArr[1].".".$cIpArr[2];
	} else {
		$c_ip1   = 9999;
		$c_ip2   = 9999;
		$c_ip3   = 9999;
	}
	$denyIpAction = False;
	if (isset($_COOKIE['denyAccess'])) {
		while (list($key,$val)=@each($denyIp)) {
			if (($ip1 == $val['ip'])||($ip2 == $val['ip'])||($ip3 == $val['ip'])) {
				if ($val['cookieTime']<0) {
					setcookie("denyAccess","",time()-36000,"/");
				}
			}
		}
		$denyIpAction = True;
	} elseif (is_array($denyIp)){
		while (list($key,$val)=@each($denyIp)) {
			if (($ip1 == $val['ip'])||($ip2 == $val['ip'])||($ip3 == $val['ip'])) {
				$denyIpAction = True;
			} elseif (($f_ip1 == $val['ip'])||($f_ip2 == $val['ip'])||($f_ip3 == $val['ip'])) {
				$denyIpAction = True;
			} elseif (($c_ip1 == $val['ip'])||($c_ip2 == $val['ip'])||($c_ip3 == $val['ip'])) {
				$denyIpAction = True;
			}
			if ($denyIpAction) {
				if ($val['cookieTime']>0) {
					$cookieTime = $val['cookieTime']*3600;
					$cookieTime = time()+$cookieTime;
					setcookie("denyAccess", "1", $cookieTime, "/");
				}
				
			}
		}
	}
	if ($denyIpAction) {
		$haltMsg = "<H1>Internal Server Error</H1>The server encountered an internal error or misconfiguration and was unable to complete your request.<P> Please contact the server administrator, webmaster@domain.com and inform them of the time the error occurred,and anything you might have done that may have caused the error.<P>More information about this error may be available in the server error log.<P><HR><ADDRESS>Apache/2.0.52 Server at ".getenv('HTTP_HOST')." Port 80</ADDRESS>";
		echo "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>";
		echo "<html><head>";
		echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>";
		echo "</head>";
		echo "<body leftmargin='20' topmargin='20' marginwidth='0' marginheight='0'>";
		echo $haltMsg;
		echo "</body>";
		echo "</html>";
		exit;
	}
	unset($denyIp);
?>
