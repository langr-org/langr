<?php
/**
 * 聯盛 3A 訂單成功處理程序
 * 由聯盛自動調用
 *
 * by: langr <hua@langr.org> May 2007
 * $Id: Hinet3A_OK.php 6 2007-10-28 03:40:44Z langr $
 */

/***
 * 服務端傳過來的參數
 * http://langr.org/twmj/auto/Hinet3A_OK.php?
 * ICPOrderID=t1122334455&ICPID=addwe&ICPProdID=addwe_t001&MPID=839OTP&Price=1
 * &Memo=Test%20order&ICPIP=210.72.225.57&OrderDateTime=2007/5/30%20%e4%b8%8a%e5%8d%88%2009:28:43
 * &AuthCode=20f9cc4326323dcb9b252026a03744&RESULTCODE=00000&RESULTMESG=%e6%88%90%e5%8a%9f 
 */
include_once('./auto.inc.php');

$today	= date("Ymd");
$now	= date("Y-m-d H:i:s");
$month	= date("Y-m");
$log	= "hinet3a_error.$month.log";
$log_ok	= "hinet3a_ok.$month.txt";
$tmpl	= array();

/***
if (CLIENT_IP != '192.168.1.169') {
	wlog($log, "非法訪問! ".CLIENT_IP." 被拒絕的IP!");
	exit;
}
 */

/*** ... */
$err	= 0;
if (isset($_GET['ICPOrderID'])) {
	$ICPOrderID = $_GET['ICPOrderID'];
} else {
	$err	= 1;
}
$ICPID	= $_GET['ICPID'];
$ICPProdID = $_GET['ICPProdID'];
$MPID	= $_GET['MPID'];
$Price	= $_GET['Price'];
$Memo	= $_GET['Memo'];
$ICPIP	= $_GET['ICPIP'];
$OrderDateTime = $_GET['OrderDateTime'];
if (isset($_GET['AuthCode'])) {
	$AuthCode = $_GET['AuthCode'];
} else {
	$err	= 1;
}
$RESULTMESG = $_GET['RESULTMESG'];

if ($err) {
	wlog($log, "Error: null parameter: ".$_SERVER["QUERY_STRING"]);
	exit;
}
/***/

/* 通過 soap 向服務商提交訂單並取認證號 */
//$server	= "http://test.payment.net.tw/mpwebservice/main.asmx?WSDL";
$server	= "https://www.payment.net.tw/MPWebService/Main.asmx?WSDL";
//$interface = "http://test.payment.net.tw/MPOrder/OrderSend2.aspx";
$interface = "https://www.payment.net.tw/MPOrder/OrderSend2.aspx";
$dealerId = "addwe";			/* 我們在聯盛的商家 Id */

tool('nusoap.php', 'soap');		/* 加載 nusoap 插件 */
tool('class.wsdlcache.php', 'soap');

$cache	= new wsdlcache(APP_TMP_PATH, FALSE);
$wsdl	= $cache->get($server);		/* 在緩存中請求 WSDL */
if (is_null($wsdl)) {			/* 如果沒有緩存則從服務器端加載並緩存 */
	$wsdl	= new wsdl($server);
	$cache->put($wsdl);
} else {
	$wsdl->debug_str = '';
	$wsdl->debug('Retrieved from cache');
}
$client	= new soapclient($wsdl, TRUE);

/* 確認訂單 */
$params['ICPID'] = $ICPID;		/* $_POST or $_GET */
$params['ICPOrderID'] = $ICPOrderID;	

$result = $client->call('OrderConfirm', array("parameters"=>$params), '', '', false, true);	/* call soap method */

if ($client->fault) {				/* 調用失敗? */
	wlog($log, "Fault: ".$result['OrderAuthResult']['RESULTCODE']." ".$result['OrderAuthResult']['RESULTMESG']);
} else {
	$err	= $client->getError();		/* 檢查是否出錯? */
	if ($err) {
		wlog($log, " Error: ".$err);
	} else {				/* 沒有出錯, 則記錄訂單 */
		$logstr	= $ICPID." ".$ICPOrderID." ".$ICPProdID." ".$AuthCode." ".$result['OrderConfirmResult']['RESULTCODE']." ".$result['OrderConfirmResult']['RESULTMESG']." ".$result['OrderConfirmResult']['ICPID']." ".$result['OrderConfirmResult']['ICPOrderID']." ".$result['OrderConfirmResult']['Result'];
	}
}

/* 取用戶資料 */
$q	= loadDB();
$sql	= "select oh.UID,oh.State,ds.OrderType,ui.Name,ui.Sex,ui.Email,ui.Tel from orderHinet3A as oh,deposit as ds,userinfo as ui where oh.DepositId='$ICPOrderID' and oh.AUTHCODE='$AuthCode' and ds.Id='$ICPOrderID' and ui.UID=oh.UID";
$q->query($sql);
$q->nextRecord();
$orderType = $q->record['OrderType'];
$uid	= $q->record['UID'];
$email	= $q->record['Email'];
$tel	= $q->record['Tel'];
$name	= $q->record['Name'];

if ( $q->record['State'] == '1') {
	echo c("訂單 $ICPOrderID 已經被處理過!");
} elseif ( $result['OrderConfirmResult']['RESULTCODE'] == '00000' ) {	/* 訂單成功 */
	/* 更新此訂單 */
	$sql	= "update deposit as d,orderHinet3A as oh set d.SuccTime='$now',d.State='1',oh.ReplyTime='$now',oh.State='1' where d.Id='$ICPOrderID' and oh.DepositId='$ICPOrderID'";
	$q->query($sql);

	/* 為用戶增加相應點數並通知用戶 */
	$sql	= "update userset as us,userinfo as ui,deposittype as dt,majong16set as ms set us.Point=(us.Point+dt.Point),us.Bonus=(us.Bonus+dt.Hinet3AAddBonus),ms.Rank=(if(dt.AddLevel>ms.Rank,dt.AddLevel,ms.Rank)) where us.UserID='$uid' and ui.UID='$uid' and dt.Id='$orderType' and ms.UserID='$uid'";

	/* 成功則向用戶發送簡訊 */
	if ($q->query($sql)) {
		/**
		tool("sms");
		$sms	= new Tool_Sms();
		$sms->mobile = $tel;
	//	$sms->mobileInfo = "移動";
		$sms->message = "您在addwe的台灣麻將點數訂單 $ICPOrderID 已支付成功!";
		if ($sms->send()) {
			$logstr	.= "已發送簡訊! ";
		}
		 */

		/* 取用户订单信息 */
		$sql	= "select us.Account,ui.NikeName,dt.Money,dt.Point,dt.Bonus,d.Id,d.PayType from userset as us,userinfo as ui,deposittype as dt,deposit as d where d.Id='$ICPOrderID' and dt.Id=d.OrderType and us.UserID=d.UID and ui.UID=d.UID";
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
		$mail	= new smtp_mail(SMTP_HOST, SMTP_USER, SMTP_PWD);
		$mail->from = SMTP_SEND;		/* 偽造的發送者 */
		$mail->to = $email;
		$mail->subject = c("麻將大悶鍋訂單成功通知");
	//	$mail->text = "";
		$mail->html = $mailText;
		if ($mail->send() == 0) {
			$logstr	.= "已發送Email通知! ";
		}
	}
	wlog($log_ok, "OK: $logstr");
	/* 转到架构去显示成功信息~~ */
	$url	= "/?module=member&action=depositResult&id=$ICPOrderID&flag=1";
	header("Location: ".$url);
//	echo c("<font size=5 color=blue><b>點數儲值付款成功！</b></font><p>訂單編號：<b>$ICPOrderID</b>");
} else {	/* 訂單失敗 */
	wlog($log, "NOTE: $logstr"." 商家回傳訂單無效!");
	$url	= "/?module=member&action=depositResult&id=$ICPOrderID&flag=2";
	header("Location: ".$url);
}
//jump_url("/", 10);
?>
