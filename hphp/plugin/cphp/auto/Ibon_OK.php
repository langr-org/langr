<?php
/**
 * 7-11 Ibon �����ɹ��������
 * �� Ibon �Զ�����
 *
 * by: langr <langr@126.com> Oct 2007
 * $Id: Ibon_OK.php 6 2007-10-28 03:40:44Z langr $
 */

/***
 * ����˴������Ĳ���:
 * $_POST['xml'] = <OLTP><HEADER><VER></VER><FROM></FROM><TERMINO></TERMINO><TO></TO>
 * <BUSINESS></BUSINESS><DATE></DATE><TIME></TIME><STATCODE></STATCODE><STATDESC></STATDESC></HEADER>
 * <AP><TotalCount>n</TotalCount><TotalAmount>m</TotalAmount>
 * <Detail><SequenceNo>x</SequenceNo><OL_OI_NO>x</OL_OI_NO>
 * <OL_Code_1>x</OL_Code_1><OL_Code_2>x</OL_Code_2><OL_Code_3>x</OL_Code_3>
 * <OL_Amount>x</OL_Amount><OL_Print>Y/N</OL_Print></Detail>
 * <Detail>...</Detail>...</AP></OLTP>
 *
 * �������������� (ֱ�� echo ��ҳ��):
 * <OLTP><HEADER><VER></VER><FROM></FROM><TERMINO></TERMINO><TO></TO>
 * <BUSINESS></BUSINESS><DATE></DATE><TIME></TIME><STATCODE></STATCODE><STATDESC></STATDESC></HEADER>
 * <AP><TotalCount>n</TotalCount><TotalAmount>m</TotalAmount>
 * <Detail><SequenceNo>x</SequenceNo><OL_OI_NO>x</OL_OI_NO>
 * <OL_Code_1>x</OL_Code_1><OL_Code_2>x</OL_Code_2><OL_Code_3>x</OL_Code_3>
 * <OL_Amount>x</OL_Amount><OL_Print>Y/N</OL_Print><Status>S/F</Status><Description></Description></Detail>
 * <Detail>...</Detail>...</AP></OLTP>		   ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */
include_once('./auto.inc.php');
$today	= date("Ymd");
$now	= date("Y-m-d H:i:s");
$month	= date("Y-m");
$log	= "ibonshop_error.$month.log";
$log_ok	= "ibonshop_ok.$month.txt";

/***
if (CLIENT_IP != '192.168.1.169') {
	wlog($log, "�Ƿ�����! ".CLIENT_IP." ���ܾ���IP!");
	exit;
}
 */

$xml = $_POST['xml'];
if ( empty($xml) || strlen($xml) < 150 ) {
	echo "<msgno>2</msgno><describe>xml error</describe>";
	exit;
}

/* �� xml �ļ�����Ϊ���� (SimpleXMLElement Object) */
$data	= simplexml_load_string($xml);

/***
 * ������ 
 * ��һ��(9)���U���ֹ����(6)+eshop�S�̾�̖(3)
 * �ڶ���(16)���U�M��̖ (8)+����e(1)+XXXX(4)+��eshop�S�̾�̖(3)
 * ������(15)��XX(2)+�U���ֹ�r�g(4)+XX(2)+���ս��~(7)
 */
$q	= loadDB();
$c	= count($data->AP->Detail);
for ($i = 0; $i < $c; $i++) {
	$ShopNo	= substr($data->AP->Detail[$i]->OL_Code_1, 6, 3).substr($data->AP->Detail[$i]->OL_Code_2, 13, 3);
	$paymentno = substr($data->AP->Detail[$i]->OL_Code_2, 0, 8);
	$account = (int) substr($data->AP->Detail[$i]->OL_Code_3, 8, 7);

	/* ��ⶩ�� ���ɷѺ��Ƿ����, ���˱ʶ����Ƿ��Ѿ������ */
	$sql	= "select ds.Id,ds.UID,ds.OrderType,ds.Money,ds.State,oi.DepositId,oi.ShopNo from orderIbonShop as oi where oi.paymentno='$paymentno' and ds.Id=oi.DepositId";
	$q->query($sql);

	$uid	= $q->record['UID'];
	$orderId = $q->record['Id'];
	$orderType = $q->record['OrderType'];
	$logstr	= "uid:$uid orderId:$orderId ShopNo:$ShopNo paymentno:$paymentno account:$account ";
	if ( $q->nextRecord() ) {
		if ( $q->record['State'] == 1 ) {		/* ��������Ķ��� */
			wlog($log, "Error: ".$logstr."�����Ѵ���� State=".$q->record['State']);
			$data->AP->Detail[$i]->Status = 'F';
			$data->AP->Detail[$i]->Description = 'order: status=1';
			continue;
		} elseif ( $q->record['Money'] != $account ) {	/* �����Ķ��� */
			wlog($log, "Error: ".$logstr."�������� Money=".$q->record['Money']);
			$data->AP->Detail[$i]->Status = 'F';
			$data->AP->Detail[$i]->Description = 'order: money error';
			continue;
		}
		/* �����Ķ���, ��Ϊ�û�������� (���¶���, ���ӵ�) */
		$sql	= "update deposit as d,orderIbonShop as oi set d.SuccTime='$now',d.State='1',oi.OL_Code_1='".$data->AP->Detail[$i]->OL_Code_1."',oi.OL_Code_2='".$data->AP->Detail[$i]->OL_Code_2."',oi.OL_Code_3='".$data->AP->Detail[$i]->OL_Code_3."',oi.ReplyTime='$now',oi.State='1' where d.Id='$orderId' and oi.DepositId='$orderId'";
		$q->query($sql);
		/* Ϊ�û�������Ӧ������֪ͨ�û� */
		$sql	= "update userset as us,userinfo as ui,deposittype as dt,majong16set as ms set us.Point=(us.Point+dt.Point),us.Bonus=(us.Bonus+dt.IbonShopAddBonus),ms.Rank=(if(dt.AddLevel>ms.Rank,dt.AddLevel,ms.Rank)) where us.UserID='$uid' and ui.UID='$uid' and dt.Id='$orderType' and ms.UserID='$uid'";
		$logstr	= "OK: ".$logstr;
		/* �ɹ������û����ͼ�Ѷ */
		if ($q->query($sql)) {
			/**
			tool("sms");
			$sms	= new Tool_Sms();
			$sms->mobile = $tel;
		//	$sms->mobileInfo = "�ƶ�";
			$sms->message = "����addwe��̨���齫�������� $depositId ��֧���ɹ�!";
			if ($sms->send()) {
				$logstr	.= "�ѷ��ͼ�Ѷ! ";
			}
			*/

			/* ȡ�û�������Ϣ */
			$sql	= "select us.Account,ui.NikeName,ui.Email,dt.Money,dt.Point,dt.Bonus,d.Id,d.PayType from userset as us,userinfo as ui,deposittype as dt,deposit as d where d.Id='$orderId' and dt.Id=d.OrderType and us.UserID=d.UID and ui.UID=d.UID";
			$q->query($sql);
			if ($q->nextRecord()) {
				$tmpl['DepositId'] = $q->record['Id'];
				$tmpl['Account'] = $q->record['Account'];
				$tmpl['NikeName'] = $q->record['NikeName'];
				$tmpl['Money']	= $q->record['Money'];
				$tmpl['Point']	= $q->record['Point'];
				$tmpl['Bonus']	= $q->record['Bonus'];
				$tmpl['PayType'] = $q->record['PayType'];
				$email	= $q->record['Email'];
			}
	
			$mailTmpl = FILE_PATH."include/mail/depositSucc.html";
			$mailText = c(file_get_contents($mailTmpl));
			$mailText = tmplVarReplace($mailText, $tmpl);
	
			tool("smtp");
			$mail	= new smtp_mail(SMTP_HOST, SMTP_USER, SMTP_PWD, SMTP_PORT, SMTP_DOMAIN);
			$mail->from = SMTP_SEND;		/* ����İl���� */
			$mail->to = $email;
			$mail->subject = c("�錢���ӆ�γɹ�֪ͨ");
		//	$mail->text = "";
			$mail->html = $mailText;
			if ($mail->send() == 0) {
				$logstr	.= "�Ѱl��Email֪ͨ! ";
			}

			wlog($log_ok, "OK: ".$logstr);
			$data->AP->Detail[$i]->Status = 'S';
			$data->AP->Detail[$i]->Description = 'OK';
			continue;
		}
		wlog($log, "NOTE: ".$logstr."�ӵ�ʧ�� ");
	} else {						/* �����ڵĶ��� */
		wlog($log, "Error: ".$logstr."�����ڵĶ��� ");
		$data->AP->Detail[$i]->Status = 'F';
		$data->AP->Detail[$i]->Description = 'order: paymentno error';
		continue;
	}
}

/* �������, ���� Ibon echo xml ��Ϣ */
$xml	= simplexml_array2xml($data);
echo $xml;

/***
 * ������ (SimpleXMLElement Object) ���� xml �ļ� 
 * �� simplexml_load_string() ���ɵ� SimpleXMLElement Object ����
 */
function simplexml_array2xml($object)
{
	$dom_xml = dom_import_simplexml($object);
	if (!$dom_xml) {
	    return 'Error while converting XML';
	}

	$dom = new DOMDocument('1.0');
	$dom_xml = $dom->importNode($dom_xml, true);
	$dom_xml = $dom->appendChild($dom_xml);

	return $dom->saveXML();
}
?>
