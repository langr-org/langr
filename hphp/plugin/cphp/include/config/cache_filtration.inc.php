<?php
/**
 * filtration.inc.php
 * Account, NikeName, Domain Name ����
 * date: 2007-08-06 10:33:44
 */

/* �ʺŹ��� */
$fil_ac[0]	= '/^gm[0-9_]*$/i';		/* gm �_�^������Ӕ��ֻ� _ */
$fil_ac[1]	= '/.*system.*$/i';		/* �Y���� system */
$fil_ac[2]	= '/.*addwe.*$/i';		/* ������ addwe */
$fil_ac[3]	= '/.*admin.*$/i';		/* ������ admin */
$fil_ac[4]	= '/.*game_*master.*$/i';	/* �Y���� gamemaster or game_master */
$fil_ac[5]	= '/.*root.*$/i';		/* ������ root */
//$fil_ac[6]	= '/.*[\&\%\(\)\<\>\/\\\'\"]*$/i';	/* ������ &%()<>/\'" */

/* ��ƹ��� (����Ҫ�ͳ�ʽ������ͬŶ, ������ GBK ����) */
$fil_nn[0]	= c('/.*ϵ�y����T.*$/i');
$fil_nn[1]	= c('/.*��̖����T.*$/i');
$fil_nn[2]	= c('/.*�[�����T.*$/i');
$fil_nn[3]	= c('/.*�ͷ�.*$/i');
$fil_nn[4]	= c('/.*ϵ�y����.*$/i');
$fil_nn[5]	= c('/.*���¾W.*$/i');
//$fil_nn[6]  = "/[\&%()<>\/\\\'\"]/i";
//$fil_nn[6]	= '/.*[\&\%\(\)\<\>\/\\\'\"]*$/i';	/* ������ &%()<>/\'" */

/* �����̴μ��������� */
$fil_dn[0]	= '/^www$/i';			/* www */
$fil_dn[1]	= '/^ftp$/i';			/* ftp */
$fil_dn[2]	= '/^mail$/i';			/* mail */
$fil_dn[3]	= '/^betcity$/i';		/* betcity */
?>
