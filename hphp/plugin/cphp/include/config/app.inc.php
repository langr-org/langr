<?php
/**
 * 项目配置文件
 */

define('APP_DB_PREFIX', "cphp_");			/* 项目资料库前缀 */
define('APP_LOG_PATH', "./data/log/");			/* 项目日志文件路径 */
define('APP_TMP_PATH', "./data/tmp/");			/* 项目临时文件路径 */

/* album */
define('APP_USER_PATH', "/album/data/user/");		/* 用户相簿相对 Web 根目录绝对路径  */
define('APP_FILE_PATH', "./data/user/");		/* 用户相簿相对 index.php 文件物理路径  */
//define('APP_PAGE_NUM', 20);				/* 相簿每页显示相片数 */
define('APP_MAX_SIZE', 2048);				/* 单张相片最大限定值 2048(K) */
define('APP_COVER_PX', 90);				/* 相簿封面缩略图尺寸 90*90 */
//define('APP_PREVIEW_PX', 90);				/* 相片预览缩略图尺寸 90*90 */
define('APP_PHOTO_PX', 720);				/* 相片保存最大尺寸 720*720 */
define('APP_PHOTO_TYPE', "gif|jpg|jpeg|png|bmp");	/* 允许的相片类型 */

/* twmj */
define('APP_IMG_PATH', "/data/wave/");			/* 商品图片相对 Web 根目录绝对路径  */
define('APP_WAVE_PATH', "./data/wave/");		/* 商品图片相对 index.php 文件物理路径  */
define('APP_PREVIEW_PX', 90);				/* 相片预览缩略图尺寸 90*90 */
define('APP_LOGOIMG_PATH', "/data/agency_logo/");	/* 经销商 LOGO 相对 Web 根目录绝对路径  */
define('APP_LOGOFILE_PATH', "./data/agency_logo/");	/* 经销商 LOGO 相对 index.php 文件物理路径  */
define('APP_DEALERLOGO_W', 240);			/* 经销商LOGO保存最大尺寸 240*100 */
define('APP_DEALERLOGO_H', 100);			/*  */
define('APP_HINET3A_MAX', 1000);			/* twmj Hinet3A 允许储值的最大限额 */
define('APP_DOWNLOAD_PATH', "ftp://down1.betcity.com.tw/pub/");	/* 麻将程式下载地址所在目录 */
if (!defined('APP_PAGE_NUM'))
	define('APP_PAGE_NUM', 10);			/* 每页显示商品数 */

/* 储值帐号 */
define('APP_ATM_NO', "59102000000848");			/* 上海银行 ATM 汇款帐号 */
define('APP_ATM_VNO', "838");				/* ATM 虚拟支付帐号前三位 */
define('APP_BuySafe_NO', "b070214017");			/* 我们在 红阳 BuySafe 刷卡 (信用卡支付) 的帐号 Id */
define('APP_ShopPay_NO', "b070214361");			/* 我们在 红阳 24Payment (便利超商支付) 的帐号 Id */
define('APP_Hinet3A_NO', "addwe1");			/* 我们在 联盛 的商家 Id */
define('APP_Hinet3A_WAVE', "addwe_");			/* 我们在联盛的商品编号开头部分,后接三位储值类型Id(ex:addwe_001) */
define('APP_GwShop_NO', '4982');			/* 我们在绿界的超商商家代码 */
define('APP_GwPay_NO', '4982');				/* 我们在绿界的信用卡商家代码 */
?>
