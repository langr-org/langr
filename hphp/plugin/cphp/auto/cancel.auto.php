#!/usr/local/bin/php
<?php
/***
 * 无效订单自动处理程序
 * 每隔一定时间自动清理数据库中过期的订单
 * 
 * by: langr <hua@langr.org> May 2007
 * $Id: cancel.auto.php 6 2007-10-28 03:40:44Z langr $
 */

include_once('./auto.inc.php');
$log	= "cancel.log";
$today	= date("Ymd");
$now	= date("Y-m-d H:i:s");
$month	= date("Y-m");
$log	= "cancel.$month.log";

/***
if (CLIENT_IP != '192.168.1.169') {
	wlog($log, "非法访问! ".CLIENT_IP." 被拒绝的IP!");
	exit;
}
 */

$q	= loadDB();

/***
 * 红阳 信用卡(BuySafe), 联盛 3A 订单
 * 应即时支付, 在 1 小时后过期
 */
$sql	= "delete deposit,orderHinet3A from deposit,orderHinet3A where deposit.Id=orderHinet3A.DepositId and orderHinet3A.Id='$ICPOrderID'";

$q->query($sql);
$i	= $q->affectedRows();

/***
 * 红阳 超商(24Payment) 订单
 * 在超过过期时间后过期
 */


/***
 * 上银 ATM 订单
 * 在 x天 后过期
 */

/* 取用户资料 */
$q	= loadDB();
$sql	= "select oh.UID,oh.State,ds.OrderType,ui.Name,ui.Sex,ui.Email,ui.Tel from orderHinet3A as oh,deposit as ds,userinfo as ui where oh.DepositId='$ICPOrderID' and ds.Id='$ICPOrderID' and ui.UID=oh.UID";
$q->query($sql);
$q->nextRecord();
$orderType = $q->record['OrderType'];
$email	= $q->record['Email'];
$tel	= $q->record['Tel'];
$name	= $q->record['Name'];

if ( $result['OrderConfirmResult']['RESULTCODE'] == '00000' ) {	/* 订单成功 */
	wlog($log_ok, "OK: $logstr");
	/* 更新此订单 */
	$sql	= "update deposit as d,orderHinet3A as oh set d.SuccTime='$now',d.State='1',oh.ReplyTime='$now',oh.State='1' where d.Id='$ICPOrderID' and oh.DepositId='$ICPOrderID'";
	$q->query($sql);

	/* 为用户增加相应点数并通知用户 */
	$sql	= "update userset as us,userinfo as ui,deposittype as dt,majong16set as ms set us.Point=(us.Point+dt.Point),ui.Bonus=(ui.Bonus+dt.ShopPayAddBonus),ms.Rank=(if(dt.AddLevel>ms.Rank,dt.AddLevel,ms.Rank)) where us.UID='$td' and ui.UID='$ICPOrderID' and dt.Id='$orderType' and ms.UID='$ICPOrderID'";
	$logstr	= "OK: $logstr ";
	/* 成功则向用户发送简讯 */
	if ($q->query($sql)) {
		/**
		tool("sms");
		$sms	= new Tool_Sms();
		$sms->mobile = $tel;
	//	$sms->mobileInfo = "移动";
		$sms->message = "";
		if ($sms->send()) {
			$logstr	.= "已发送简讯! ";
		}
		*/
		tool("smtp");
		$mail	= new smtp_mail(SMTP_HOST, SMTP_USER, SMTP_PWD);
		$mail->from = "<webmaster@addwe.com>";		/* 伪造的发送者 */
		$mail->to = $email;
		$mail->subject = c("addwe台湾麻将订单成功通知");
	//	$mail->text = "";
		$mail->html = c("订单详细内容: $logstr \r\n已支付成功!");
		if ($mail->send()) {
			$logstr	.= "已发送Email通知! ";
		}
	}
	wlog($log_ok, $logstr);
} else {	/* 订单失败 */
	wlog($log, "NOTE: $logstr"." 商家回传订单无效!");
}

?>
