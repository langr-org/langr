<?php
/**
 * ��Ŀ�����ļ�
 */

define('APP_DB_PREFIX', "cphp_");			/* ��Ŀ���Ͽ�ǰ׺ */
define('APP_LOG_PATH', "./data/log/");			/* ��Ŀ��־�ļ�·�� */
define('APP_TMP_PATH', "./data/tmp/");			/* ��Ŀ��ʱ�ļ�·�� */

/* album */
define('APP_USER_PATH', "/album/data/user/");		/* �û��ಾ��� Web ��Ŀ¼����·��  */
define('APP_FILE_PATH', "./data/user/");		/* �û��ಾ��� index.php �ļ�����·��  */
//define('APP_PAGE_NUM', 20);				/* �ಾÿҳ��ʾ��Ƭ�� */
define('APP_MAX_SIZE', 2048);				/* ������Ƭ����޶�ֵ 2048(K) */
define('APP_COVER_PX', 90);				/* �ಾ��������ͼ�ߴ� 90*90 */
//define('APP_PREVIEW_PX', 90);				/* ��ƬԤ������ͼ�ߴ� 90*90 */
define('APP_PHOTO_PX', 720);				/* ��Ƭ�������ߴ� 720*720 */
define('APP_PHOTO_TYPE', "gif|jpg|jpeg|png|bmp");	/* �������Ƭ���� */

/* twmj */
define('APP_IMG_PATH', "/data/wave/");			/* ��ƷͼƬ��� Web ��Ŀ¼����·��  */
define('APP_WAVE_PATH', "./data/wave/");		/* ��ƷͼƬ��� index.php �ļ�����·��  */
define('APP_PREVIEW_PX', 90);				/* ��ƬԤ������ͼ�ߴ� 90*90 */
define('APP_LOGOIMG_PATH', "/data/agency_logo/");	/* ������ LOGO ��� Web ��Ŀ¼����·��  */
define('APP_LOGOFILE_PATH', "./data/agency_logo/");	/* ������ LOGO ��� index.php �ļ�����·��  */
define('APP_DEALERLOGO_W', 240);			/* ������LOGO�������ߴ� 240*100 */
define('APP_DEALERLOGO_H', 100);			/*  */
define('APP_HINET3A_MAX', 1000);			/* twmj Hinet3A ����ֵ������޶� */
define('APP_DOWNLOAD_PATH', "ftp://down1.betcity.com.tw/pub/");	/* �齫��ʽ���ص�ַ����Ŀ¼ */
if (!defined('APP_PAGE_NUM'))
	define('APP_PAGE_NUM', 10);			/* ÿҳ��ʾ��Ʒ�� */

/* ��ֵ�ʺ� */
define('APP_ATM_NO', "59102000000848");			/* �Ϻ����� ATM ����ʺ� */
define('APP_ATM_VNO', "838");				/* ATM ����֧���ʺ�ǰ��λ */
define('APP_BuySafe_NO', "b070214017");			/* ������ ���� BuySafe ˢ�� (���ÿ�֧��) ���ʺ� Id */
define('APP_ShopPay_NO', "b070214361");			/* ������ ���� 24Payment (��������֧��) ���ʺ� Id */
define('APP_Hinet3A_NO', "addwe1");			/* ������ ��ʢ ���̼� Id */
define('APP_Hinet3A_WAVE', "addwe_");			/* ��������ʢ����Ʒ��ſ�ͷ����,�����λ��ֵ����Id(ex:addwe_001) */
define('APP_GwShop_NO', '4982');			/* �������̽�ĳ����̼Ҵ��� */
define('APP_GwPay_NO', '4982');				/* �������̽�����ÿ��̼Ҵ��� */
?>
