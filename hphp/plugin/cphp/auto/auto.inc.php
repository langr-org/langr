<?PHP
/***
 * 自动执行程序配置文件
 */

//error_reporting(E_ALL & ~E_NOTICE);

define('FILE_PATH', "/home/htdocs/betcity/");				// 项目文件路径 (相对此文件)
//** 网站语言设置
define('CFG_CHAR_SET', "BIG5");				// 网站语言设置(UTF-8 编码)，可选值：GB2312、BIG5
define('CFG_TEMPLATE_LANGUAGE', "GBK");			// 程序及模板的语言方式
define("CHAR_SET", "big5");				// 当前语言 (CFG_CHAR_SET)
define('CFG_DB_CHAR', "BIG5");				// 数据库编码

//** 定义客户端 IP
$cip	= getenv("REMOTE_ADDR");
$rcip	= getenv("HTTP_X_FORWARDED_FOR");
if ($rcip) $cip	= $rcip;		//使用了代理?
define('CLIENT_IP', $cip);

//** 服务器选择 
if (getenv("SERVER_ADDR") == "192.168.1.169") {	//内部
//** 数据库设置
	define('CFG_DB_HOST', "localhost");		// MySQL 主机地址
	define('CFG_DB_NAME', "twmj_test");			// 数据库名称
	define('CFG_DB_USER', "root");			// 连结用户
	define('CFG_DB_PWD', "123456");			// 连结密码
##
	define('CFG_DEBUG_MODE', True);			// 统计程序运行时间
	define('CFG_RUN_TIME', True);			// 统计程序运行时间
##
	define('CFG_IMG_PATH', "./images");		// 内部网站图片路径
} else {
//** 数据库设置
	define('CFG_DB_HOST', "220.247.145.226");		// MySQL 主机地址
	define('CFG_DB_NAME', "addwe");			// 数据库名称
	define('CFG_DB_USER', "addwe");			// 连结用户
	define('CFG_DB_PWD',  "yy22787789");			// 连结密码
##
	define('CFG_DEBUG_MODE', False);		// 统计程序运行时间
	define('CFG_RUN_TIME', False);			// 统计程序运行时间
	define('CFG_IMG_PATH', "./images");		// 外部网站图片路径
}

define('CFG_DB_COMPANY', "www.betcity.com.tw");		// MySQL 错误时显示的公司名称
define('CFG_DB_ADMINMAIL', "service@betcity.com.tw");		// MySQL 错误时寄出的邮箱地址

define('SMTP_HOST', "smtp.126.com");
define('SMTP_USER', "yuanlin31@126.com");
define('SMTP_PWD', "hh02.02.330");
define('SMTP_PORT', 25);
define('SMTP_DOMAIN', false);				// 当在一个服务器上使用多个域名的企业邮箱时, 要设置为 true
define('SMTP_SEND', "<service@betcity.com.tw>");	// 伪造的发送者

/* APP */
define('APP_DB_PREFIX', "");				// 项目资料库前缀
define('APP_LOG_PATH', "/home/htdocs/betcity/data/log/");			// 项目日志文件路径
define('APP_TMP_PATH', "/home/htdocs/betcity/data/tmp/");			// 项目临时文件路径

define('APP_ATM_NO', "59102000000848");			// 我们在上银的帐号
define('APP_BuySafe_NO', "b070214017");			// 我们在 红阳 BuySafe 刷卡 (信用卡支付) 的帐号 Id
define('APP_BuySafe_PWD', "aa2278");			// 密码
define('APP_ShopPay_NO', "b070214361");			// 我们在 红阳 24Payment (便利超商支付) 的帐号 Id
define('APP_Hinet3A_NO', "addwe1");			// 我们在 联盛 的商家 Id
define('APP_GwShop_NO', '4982');			// 我们在绿界的超商商家代码
define('APP_GwShop_Check', '00001348');			// 绿界超商检查码
define('APP_GwPay_NO', '4982');				// 我们在绿界的信用卡商家代码

//require_once("/home/htdocs/betcity/include/config/app.inc.php");
require_once("/home/htdocs/betcity/lib/main.fun.php");

/**
if ($_POST) {
	foreach ($_POST as $key => $val) {
		if (!is_array($$key)) {
			$$key = $val;
		 }
	}
}
if ($_GET) {
	foreach ($_GET as $key => $val) {
 		if (!is_array($$key)) {
			$$key = $val;
		}
	}
}
 */

/***
 * 载入资料库，由子类中根据是否需要资料库去调用。
 */
function loadDB() {
	if (!class_exists("MySql")) {
		include_once ("/home/htdocs/betcity/lib/mysql.class.php");  // 载入MySQL类
	}

	$Q = & New MySql();
	$Q->Host       = CFG_DB_HOST;
	$Q->Database   = CFG_DB_NAME;
	$Q->User       = CFG_DB_USER;
	$Q->Password   = CFG_DB_PWD;
	$Q->Company    = CFG_DB_COMPANY;
	$Q->AdminMail  = CFG_DB_ADMINMAIL;

	if ( !$Q->connect() ) {
		halt("错误：资料库连接失败！<br>Session halted.");
	}

	return $Q;
}

/***
 * 替换模板中的变数为变数内容
 * 不经过架构的程式也可以支持模板~
 */
function tmplVarReplace(& $tmplContent, & $tmpl)
{
	$d	= & $tmpl;
	$tmplContent = str_replace("\"", "\\\"", $tmplContent);
	$temp	= preg_replace('/(\{\$)(.+?)(\})/is', '".'.'$d[\'\\2\']'.'."', $tmplContent);
	eval("\$temp = \"$temp\";");
	$temp = str_replace("\\\"", "\"", $temp);
	$temp  = StripSlashes($temp);

	return $temp;
}
?>
