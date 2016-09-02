<?PHP
/***
 * 名称：PHP OOP 面向对像开发工具套件  v0.06.03
 * 功能：项目-基本配置文件
 * 
 * $Id: main.inc.php 8 2009-10-20 10:05:34Z langr $
 */
  
/* 工程项目相关配置 */
define('CFG_PROJECT_NAME', "cphp");			/* 工程项目目录名称 */
define('CFG_APP_PATH', "/cphp");			/* 架构相对 Web 文档根目录绝对路径  */
define('CFG_DEFAULT_MODULE', "index");			/* 缺省类 */
define('CFG_DEFAULT_ACTION', "index");			/* 缺省操作 */

/* 模板项目相关配置 */
define('CFG_TEMPLATE_PATH', "tmpl");			/* 模板目录 */
define('CFG_TEMPLATE_CACHE_PREFIX', "tmplCache");	/* 模板缓存目录前缀，后面程序会自动加上 _big5 或 _gb2312 */

/* 网站语言设置 */
define('CFG_CHAR_SET', "GB2312");			/* 网站语言设置(UTF-8 编码)，可选值：GB2312、BIG5 */
define('CFG_TEMPLATE_LANGUAGE', "GBK");			/* 程序及模板的语言方式 */
define('CFG_DB_CHAR', "UTF8");				/* 数据库编码 */

/* ReWrite 支持 */
define('REWRITE', 0);
define('RW_SCRIPT', "index.php");			/* 支持 REWRITE 的脚本名, 在 apache 配置文件中应做相应的修改   */

/* 定义客户端 IP */
$cip	= getenv("REMOTE_ADDR");
$rcip	= getenv("HTTP_X_FORWARDED_FOR");
if ($rcip) $cip	= $rcip;				/* 使用了代理? */
define('CLIENT_IP', $cip);

/* 数据库服务器选择  */
if ( getenv("SERVER_ADDR") == "127.0.0.1" ) {		/* 内部 */
	/* 数据库设置 */
	define('CFG_DB_HOST', "localhost");		/* MySQL 主机地址 */
	define('CFG_DB_NAME', "pu");			/* 数据库名称 */
	define('CFG_DB_USER', "root");			/* 连结用户 */
	define('CFG_DB_PWD', "123456");			/* 连结密码 */
	/* 只读数据库设置 */
	define('CFG_RO_DB_HOST', CFG_DB_HOST);		/* 只读 MySQL 主机地址 */
	define('CFG_RO_DB_NAME', CFG_DB_NAME);		/* 数据库名称 */
	define('CFG_RO_DB_USER', CFG_DB_USER);		/* 连结用户 */
	define('CFG_RO_DB_PWD', CFG_DB_PWD);		/* 连结密码 */

	define('CFG_DEBUG_MODE', True);			/* 打开调试模式 */
	define('CFG_RUN_TIME', False);			/* 统计程序运行时间 */
	define('CFG_IMG_PATH', "./images");		/* 内部网站图片路径 */
} else {
	/* 数据库设置 */
	define('CFG_DB_HOST', "localhost");	/* MySQL 主机地址 */
	define('CFG_DB_NAME', "LangR");			/* 数据库名称 */
	define('CFG_DB_USER', "langr");			/* 连结用户 */
	define('CFG_DB_PWD',  "hua_db");		/* 连结密码 */
//	define('CFG_DB_HOST', "localhost");		/* MySQL 主机地址 */
//	define('CFG_DB_NAME', "twmj_test");		/* 数据库名称 */
//	define('CFG_DB_USER', "root");			/* 连结用户 */
//	define('CFG_DB_PWD', "bkMJ8*");			/* 连结密码 */
	/* 只读数据库设置 */
	define('CFG_RO_DB_HOST', CFG_DB_HOST);		/* 只读 MySQL 主机地址 */
	define('CFG_RO_DB_NAME', CFG_DB_NAME);		/* 数据库名称 */
	define('CFG_RO_DB_USER', CFG_DB_USER);		/* 连结用户 */
	define('CFG_RO_DB_PWD', CFG_DB_PWD);		/* 连结密码 */

	define('CFG_DEBUG_MODE', False);		/* 调试模式 */
	define('CFG_RUN_TIME', False);			/* 统计程序运行时间 */
	define('CFG_IMG_PATH', "./images");		/* 外部网站图片路径 */

}

define('CFG_DB_COMPANY', "www.com.cn");			/* MySQL 错误时显示的公司名称 */
define('CFG_DB_ADMINMAIL', "webmaster@com.cn");		/* MySQL 错误时寄出的邮箱地址 */

/* smtp 发送邮件主机帐号 */
define('SMTP_HOST', "smtp.126.com");
define('SMTP_USER', "yuanlin31@126.com");
define('SMTP_PWD', "hh02.02.330");
define('SMTP_PORT', 25);
define('SMTP_DOMAIN', false);				/* 当在一个服务器上使用多个域名的企业邮箱时, 要设置为 true */
define('SMTP_SEND', "<service@betcity.com.tw>");	/* 伪造的发送者 */

/* 其他配置 */
define('CFG_ADMIN_VERIFY', "ToDayIs0204");

/* PHP程序[慢速日志]设置 */
if (CFG_RUN_TIME) {						/* 此日志必需 CFG_RUN_TIME 参数为 True 时生效 */
	define('CFG_RUN_TIME_LOG_NAME', "log/phpRunTime.log");	/* 程序运行时间日志名称 */
	define('CFG_RUN_TIME_LOG_SIZE_MAXIMUM', 102400);	/* 日志文件的最大容量, 超过将清空日志, 单位byte(字节) */
	define('CFG_RUN_TIME_MAXIMUM', 0);			/* 程序运行最大时间, 超过此时间将写入日志, 单位second(秒) */
}

/* */
require_once("./include/config/app.inc.php");
?>
