#!/usr/local/php/bin/php -q
<?php
/**
 * 红阳信用卡 (BuySafe) 订单成功处理补订程序
 * 需要定时自动调用
 *
 * by: langr <langr@126.com> Sep 2007
 * $Id: BuySafe_OK.auto.php 6 2007-10-28 03:40:44Z langr $
 */

include_once('/home/htdocs/betcity/auto/auto.inc.php');

/***
 * 传到服务端的参数
 * http://www.esafe.com.tw/ez/check/double_check.asp?
 * storekind=2&payno=b070214017&pwd=aa2278&web=3&startdate=2007/09/29&enddate=2007/09/29
 */

$today	= isset($_GET['day']) ? $_GET['day'] : date("Y/m/d");
$time	= date("His");
$now	= date("Y-m-d H:i:s");
$month	= date("Y-m");
//$log	= "creditcard_error.$month.log";
$log	= "creditcard_manual_ok.$month.txt";
$log_data = "creditcard_data.$month.log";
$log_ok	= "creditcard_manual_ok.$month.txt";
$dealerId = APP_BuySafe_NO;		/* 我们在红阳的信用卡商家 ID */
$pwd	= APP_BuySafe_PWD;		/* 密码 */
$interface = "http://www.esafe.com.tw/ez/check/double_check.asp?storekind=2&payno=$dealerId&pwd=$pwd&web=3&startdate=$today&enddate=$today";

if (isset($_GET['day'])) {
	$log	= $log_data = $log_ok = "creditcard_manual_web_ok.$month.log";
}

/***
 * 以 GET 方式取红阳主机资料 
 * 以字符串形式保存
 */
$str	= posttohost($interface);
$data	= explode("<br>", $str);

$q	= loadDB();

/***
 * get 资料范例:
 * orderno 1000000709294729108## name 吳浚程## price 1000## note1 26668## note2 t1a0e3bf5c3119bed0a## Result 成功
 * orderno 1000000709294727654## name 吳浚程## price 1000## note1 26668## note2 t1a0e3bf5c3119bed0a##Result 失敗 ## errorcode:.
 * orderno 1000000709294700914## name 吳浚程## price 1000## note1 26665## note2 t95cf409b20d842e5bb##Result 失敗 ## errorcode:12 .41
 * orderno 1000000709294549476## name 蔡宜芬## price 500## note1 26656## note2 t46a8342b95415c4a24## Result 成功
 * orderno 1000000709294482942## name 林建男## price 1000## note1 48574## note2 1000## Result 成功
 */
$line	= count($data);
for ($i = 0; $i < $line; $i++) {
	$data[$i] = trim($data[$i]);
	if (strlen($data[$i]) < 50)
		continue;

	$logstr	= $data[$i];
	$get_argv = explode("##", $data[$i]);
	/* 去掉预付卡订单 */
	if (substr(trim($get_argv[4]), 6, 1) != 't')
		continue;
	/* 去掉失败订单 */
	if (count($get_argv) > 6 && substr(trim($get_argv[6]), 0, 9) == "errorcode")
		continue;
//	wlog($log_data, $logstr);

	$argv	= explode(" ", trim($get_argv[0]));
	$buysafeno = $argv[1];
	$argv	= explode(" ", trim($get_argv[1]));
	$Name	= $argv[1];
	$argv	= explode(" ", trim($get_argv[2]));
	$MN	= $argv[1];
	$argv	= explode(" ", trim($get_argv[3]));
	$note1	= $argv[1];
	$argv	= explode(" ", trim($get_argv[4]));
	$note2	= $argv[1];

	/***
	 * 核对数据库, 并取订单信息
	 */
	$sql	= "select oc.UID,oc.State,oc.Note,ds.OrderType,ds.Money,ui.Name,ui.Sex,ui.Email,ui.Tel from orderCreditCard as oc,deposit as ds,userinfo as ui where oc.DepositId='$note1' and oc.Note='$note2' and ds.Id='$note1' and ui.UID=oc.UID";

	$q->query($sql);
	$q->nextRecord();
	$uid	= $q->record['UID'];
	$orderType = $q->record['OrderType'];
	$email	= $q->record['Email'];
	$tel	= $q->record['Tel'];
	$name	= $q->record['Name'];

	if (!$q->record['UID']) {		/* 无此订单 */
		$logstr	= "Note: ".$data[$i]." 无此订单记录!";
		wlog($log, $logstr);
		/* 新建此订单~~ */
		continue;
	} elseif ($q->record['State'] == 1) {	/* 重复的订单 */
	//	$logstr	= "Note: ".$data[$i]." DepositId:$note1 State:".$q->record['State']." 已处理过的订单!";
	//	wlog($log, $logstr);
		continue;
	} elseif ($MN == $q->record['Money']) {
		/* 更新此订单 */
		$sql	= "update deposit as d,orderCreditCard as oc set d.SuccTime='$now',d.State='1',oc.ReplyTime='$now',oc.State='1',oc.BuysafeNo='$buysafeno' where d.Id='$note1' and oc.DepositId='$note1'";
		$q->query($sql);
	
		/* 为用户增加相应点数并通知用户 */
		$sql	= "update userset as us,userinfo as ui,deposittype as dt,majong16set as ms set us.Point=(us.Point+dt.Point),us.Bonus=(us.Bonus+dt.CreditCardAddBonus),ms.Rank=(if(dt.AddLevel>ms.Rank,dt.AddLevel,ms.Rank)) where us.UserID='$uid' and ui.UID='$uid' and dt.Id='$orderType' and ms.UserID='$uid'";
		$logstr	= "OK: $logstr 补加的订单. ";
		/* 成功则向用户发送简讯 */
		if ($q->query($sql)) {
			/**
			tool("sms");
			$sms	= new Tool_Sms();
			$sms->mobile = $tel;
		//	$sms->mobileInfo = "移动";
			$sms->message = "您在addwe的台湾麻将点数订单 $note1 已支付成功!";
			if ($sms->send()) {
				$logstr	.= "已发送简讯! ";
			}
			*/
	
			/* 取用户订单信息 */
			$sql	= "select us.Account,ui.NikeName,dt.Money,dt.Point,dt.Bonus,d.Id,d.PayType from userset as us,userinfo as ui,deposittype as dt,deposit as d where d.Id='$note1' and dt.Id=d.OrderType and us.UserID=d.UID and ui.UID=d.UID";
			$q->query($sql);
			if ($q->nextRecord()) {
				$tmpl['DepositId'] = $q->record['Id'];
				$tmpl['Account'] = $q->record['Account'];
				$tmpl['NikeName'] = $q->record['NikeName'];
				$tmpl['Money']	= $q->record['Money'];
				$tmpl['Point']	= $q->record['Point'];
				$tmpl['Bonus']	= $q->record['Bonus'];
				$tmpl['PayType'] = $q->record['PayType'];
			}
	
			$mailTmpl = FILE_PATH."include/mail/depositSucc.html";
			$mailText = c(file_get_contents($mailTmpl));
			$mailText = tmplVarReplace($mailText, $tmpl);
	
			tool("smtp");
			$mail	= new smtp_mail(SMTP_HOST, SMTP_USER, SMTP_PWD, SMTP_PORT, SMTP_DOMAIN);
			$mail->from = SMTP_SEND;		/* 偽造的發送者 */
			$mail->to = $email;
			$mail->subject = c("麻將大悶鍋訂單成功通知");
		//	$mail->text = "";
			$mail->html = $mailText;
			if ($mail->send() == 0) {
				$logstr	.= "已發送Email通知! ";
			}
		}
		wlog($log_ok, $logstr);
	} else {	/* 因金额不对而储值失败 */
		wlog($log, "Note: $logstr 此訂單因金額不對(".$q->record['Money'].",$MN), 未儲值成功!");
	}
}
?>
