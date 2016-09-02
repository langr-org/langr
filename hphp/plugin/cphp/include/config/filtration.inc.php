<?php
/**
 * filtration.inc.php
 * Account, NikeName, Domain Name 过滤
 * date: 2007-08-06 10:33:44
 */

/* 帐号过滤 */
$fil_ac[0]	= '/^gm[0-9_]*$/i';		/* gm 開頭，後面接數字或 _ */
$fil_ac[1]	= '/.*system.*$/i';		/* 裏面有 system */
$fil_ac[2]	= '/.*addwe.*$/i';		/* 里面有 addwe */
$fil_ac[3]	= '/.*admin.*$/i';		/* 里面有 admin */
$fil_ac[4]	= '/.*game_*master.*$/i';	/* 裏面有 gamemaster or game_master */
$fil_ac[5]	= '/.*root.*$/i';		/* 里面有 root */
//$fil_ac[6]	= '/.*[\&\%\(\)\<\>\/\\\'\"]*$/i';	/* 里面有 &%()<>/\'" */

/* 匿称过滤 (编码要和程式编码相同哦, 这里是 GBK 编码) */
$fil_nn[0]	= c('/.*系統管理員.*$/i');
$fil_nn[1]	= c('/.*帳號管理員.*$/i');
$fil_nn[2]	= c('/.*遊戲管理員.*$/i');
$fil_nn[3]	= c('/.*客服.*$/i');
$fil_nn[4]	= c('/.*系統公告.*$/i');
$fil_nn[5]	= c('/.*艾德網.*$/i');
//$fil_nn[6]  = "/[\&%()<>\/\\\'\"]/i";
//$fil_nn[6]	= '/.*[\&\%\(\)\<\>\/\\\'\"]*$/i';	/* 里面有 &%()<>/\'" */

/* 经销商次级域名过滤 */
$fil_dn[0]	= '/^www$/i';			/* www */
$fil_dn[1]	= '/^ftp$/i';			/* ftp */
$fil_dn[2]	= '/^mail$/i';			/* mail */
$fil_dn[3]	= '/^betcity$/i';		/* betcity */
?>
