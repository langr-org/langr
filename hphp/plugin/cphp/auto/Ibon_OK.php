<?php
/**
 * 7-11 Ibon 订单成功处理程序
 * 由 Ibon 自动调用
 *
 * by: langr <langr@126.com> Oct 2007
 * $Id: Ibon_OK.php 6 2007-10-28 03:40:44Z langr $
 */

/***
 * 服务端传过来的参数:
 * $_POST['xml'] = <OLTP><HEADER><VER></VER><FROM></FROM><TERMINO></TERMINO><TO></TO>
 * <BUSINESS></BUSINESS><DATE></DATE><TIME></TIME><STATCODE></STATCODE><STATDESC></STATDESC></HEADER>
 * <AP><TotalCount>n</TotalCount><TotalAmount>m</TotalAmount>
 * <Detail><SequenceNo>x</SequenceNo><OL_OI_NO>x</OL_OI_NO>
 * <OL_Code_1>x</OL_Code_1><OL_Code_2>x</OL_Code_2><OL_Code_3>x</OL_Code_3>
 * <OL_Amount>x</OL_Amount><OL_Print>Y/N</OL_Print></Detail>
 * <Detail>...</Detail>...</AP></OLTP>
 *
 * 服务端请求的资料 (直接 echo 到页面):
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
	wlog($log, "非法访问! ".CLIENT_IP." 被拒绝的IP!");
	exit;
}
 */

$xml = $_POST['xml'];
if ( empty($xml) || strlen($xml) < 150 ) {
	echo "<msgno>2</msgno><describe>xml error</describe>";
	exit;
}

/* 将 xml 文件解析为数组 (SimpleXMLElement Object) */
$data	= simplexml_load_string($xml);

/***
 * 处理订单 
 * 第一段(9)：繳款截止日期(6)+eshop廠商編號(3)
 * 第二段(16)：繳費編號 (8)+服務類別(1)+XXXX(4)+子eshop廠商編號(3)
 * 第三段(15)：XX(2)+繳款截止時間(4)+XX(2)+代收金額(7)
 */
$q	= loadDB();
$c	= count($data->AP->Detail);
for ($i = 0; $i < $c; $i++) {
	$ShopNo	= substr($data->AP->Detail[$i]->OL_Code_1, 6, 3).substr($data->AP->Detail[$i]->OL_Code_2, 13, 3);
	$paymentno = substr($data->AP->Detail[$i]->OL_Code_2, 0, 8);
	$account = (int) substr($data->AP->Detail[$i]->OL_Code_3, 8, 7);

	/* 检测订单 检测缴费号是否存在, 及此笔订单是否已经处理过 */
	$sql	= "select ds.Id,ds.UID,ds.OrderType,ds.Money,ds.State,oi.DepositId,oi.ShopNo from orderIbonShop as oi where oi.paymentno='$paymentno' and ds.Id=oi.DepositId";
	$q->query($sql);

	$uid	= $q->record['UID'];
	$orderId = $q->record['Id'];
	$orderType = $q->record['OrderType'];
	$logstr	= "uid:$uid orderId:$orderId ShopNo:$ShopNo paymentno:$paymentno account:$account ";
	if ( $q->nextRecord() ) {
		if ( $q->record['State'] == 1 ) {		/* 被处理过的订单 */
			wlog($log, "Error: ".$logstr."订单已处理过 State=".$q->record['State']);
			$data->AP->Detail[$i]->Status = 'F';
			$data->AP->Detail[$i]->Description = 'order: status=1';
			continue;
		} elseif ( $q->record['Money'] != $account ) {	/* 金额不符的订单 */
			wlog($log, "Error: ".$logstr."订单金额不符 Money=".$q->record['Money']);
			$data->AP->Detail[$i]->Status = 'F';
			$data->AP->Detail[$i]->Description = 'order: money error';
			continue;
		}
		/* 正常的订单, 则为用户处理点数 (更新订单, 并加点) */
		$sql	= "update deposit as d,orderIbonShop as oi set d.SuccTime='$now',d.State='1',oi.OL_Code_1='".$data->AP->Detail[$i]->OL_Code_1."',oi.OL_Code_2='".$data->AP->Detail[$i]->OL_Code_2."',oi.OL_Code_3='".$data->AP->Detail[$i]->OL_Code_3."',oi.ReplyTime='$now',oi.State='1' where d.Id='$orderId' and oi.DepositId='$orderId'";
		$q->query($sql);
		/* 为用户增加相应点数并通知用户 */
		$sql	= "update userset as us,userinfo as ui,deposittype as dt,majong16set as ms set us.Point=(us.Point+dt.Point),us.Bonus=(us.Bonus+dt.IbonShopAddBonus),ms.Rank=(if(dt.AddLevel>ms.Rank,dt.AddLevel,ms.Rank)) where us.UserID='$uid' and ui.UID='$uid' and dt.Id='$orderType' and ms.UserID='$uid'";
		$logstr	= "OK: ".$logstr;
		/* 成功则向用户发送简讯 */
		if ($q->query($sql)) {
			/**
			tool("sms");
			$sms	= new Tool_Sms();
			$sms->mobile = $tel;
		//	$sms->mobileInfo = "移动";
			$sms->message = "您在addwe的台湾麻将点数订单 $depositId 已支付成功!";
			if ($sms->send()) {
				$logstr	.= "已发送简讯! ";
			}
			*/

			/* 取用户订单信息 */
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
			$mail->from = SMTP_SEND;		/* 偽造的發送者 */
			$mail->to = $email;
			$mail->subject = c("麻將大悶鍋訂單成功通知");
		//	$mail->text = "";
			$mail->html = $mailText;
			if ($mail->send() == 0) {
				$logstr	.= "已發送Email通知! ";
			}

			wlog($log_ok, "OK: ".$logstr);
			$data->AP->Detail[$i]->Status = 'S';
			$data->AP->Detail[$i]->Description = 'OK';
			continue;
		}
		wlog($log, "NOTE: ".$logstr."加点失败 ");
	} else {						/* 不存在的订单 */
		wlog($log, "Error: ".$logstr."不存在的订单 ");
		$data->AP->Detail[$i]->Status = 'F';
		$data->AP->Detail[$i]->Description = 'order: paymentno error';
		continue;
	}
}

/* 处理完毕, 则向 Ibon echo xml 信息 */
$xml	= simplexml_array2xml($data);
echo $xml;

/***
 * 将数组 (SimpleXMLElement Object) 生成 xml 文件 
 * 由 simplexml_load_string() 生成的 SimpleXMLElement Object 数组
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
