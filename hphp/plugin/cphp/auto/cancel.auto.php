#!/usr/local/bin/php
<?php
/***
 * ��Ч�����Զ��������
 * ÿ��һ��ʱ���Զ��������ݿ��й��ڵĶ���
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
	wlog($log, "�Ƿ�����! ".CLIENT_IP." ���ܾ���IP!");
	exit;
}
 */

$q	= loadDB();

/***
 * ���� ���ÿ�(BuySafe), ��ʢ 3A ����
 * Ӧ��ʱ֧��, �� 1 Сʱ�����
 */
$sql	= "delete deposit,orderHinet3A from deposit,orderHinet3A where deposit.Id=orderHinet3A.DepositId and orderHinet3A.Id='$ICPOrderID'";

$q->query($sql);
$i	= $q->affectedRows();

/***
 * ���� ����(24Payment) ����
 * �ڳ�������ʱ������
 */


/***
 * ���� ATM ����
 * �� x�� �����
 */

/* ȡ�û����� */
$q	= loadDB();
$sql	= "select oh.UID,oh.State,ds.OrderType,ui.Name,ui.Sex,ui.Email,ui.Tel from orderHinet3A as oh,deposit as ds,userinfo as ui where oh.DepositId='$ICPOrderID' and ds.Id='$ICPOrderID' and ui.UID=oh.UID";
$q->query($sql);
$q->nextRecord();
$orderType = $q->record['OrderType'];
$email	= $q->record['Email'];
$tel	= $q->record['Tel'];
$name	= $q->record['Name'];

if ( $result['OrderConfirmResult']['RESULTCODE'] == '00000' ) {	/* �����ɹ� */
	wlog($log_ok, "OK: $logstr");
	/* ���´˶��� */
	$sql	= "update deposit as d,orderHinet3A as oh set d.SuccTime='$now',d.State='1',oh.ReplyTime='$now',oh.State='1' where d.Id='$ICPOrderID' and oh.DepositId='$ICPOrderID'";
	$q->query($sql);

	/* Ϊ�û�������Ӧ������֪ͨ�û� */
	$sql	= "update userset as us,userinfo as ui,deposittype as dt,majong16set as ms set us.Point=(us.Point+dt.Point),ui.Bonus=(ui.Bonus+dt.ShopPayAddBonus),ms.Rank=(if(dt.AddLevel>ms.Rank,dt.AddLevel,ms.Rank)) where us.UID='$td' and ui.UID='$ICPOrderID' and dt.Id='$orderType' and ms.UID='$ICPOrderID'";
	$logstr	= "OK: $logstr ";
	/* �ɹ������û����ͼ�Ѷ */
	if ($q->query($sql)) {
		/**
		tool("sms");
		$sms	= new Tool_Sms();
		$sms->mobile = $tel;
	//	$sms->mobileInfo = "�ƶ�";
		$sms->message = "";
		if ($sms->send()) {
			$logstr	.= "�ѷ��ͼ�Ѷ! ";
		}
		*/
		tool("smtp");
		$mail	= new smtp_mail(SMTP_HOST, SMTP_USER, SMTP_PWD);
		$mail->from = "<webmaster@addwe.com>";		/* α��ķ����� */
		$mail->to = $email;
		$mail->subject = c("addwę���齫�����ɹ�֪ͨ");
	//	$mail->text = "";
		$mail->html = c("������ϸ����: $logstr \r\n��֧���ɹ�!");
		if ($mail->send()) {
			$logstr	.= "�ѷ���Email֪ͨ! ";
		}
	}
	wlog($log_ok, $logstr);
} else {	/* ����ʧ�� */
	wlog($log, "NOTE: $logstr"." �̼һش�������Ч!");
}

?>
