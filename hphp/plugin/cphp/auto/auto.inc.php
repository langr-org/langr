<?PHP
/***
 * �Զ�ִ�г��������ļ�
 */

//error_reporting(E_ALL & ~E_NOTICE);

define('FILE_PATH', "/home/htdocs/betcity/");				// ��Ŀ�ļ�·�� (��Դ��ļ�)
//** ��վ��������
define('CFG_CHAR_SET', "BIG5");				// ��վ��������(UTF-8 ����)����ѡֵ��GB2312��BIG5
define('CFG_TEMPLATE_LANGUAGE', "GBK");			// ����ģ������Է�ʽ
define("CHAR_SET", "big5");				// ��ǰ���� (CFG_CHAR_SET)
define('CFG_DB_CHAR', "BIG5");				// ���ݿ����

//** ����ͻ��� IP
$cip	= getenv("REMOTE_ADDR");
$rcip	= getenv("HTTP_X_FORWARDED_FOR");
if ($rcip) $cip	= $rcip;		//ʹ���˴���?
define('CLIENT_IP', $cip);

//** ������ѡ�� 
if (getenv("SERVER_ADDR") == "192.168.1.169") {	//�ڲ�
//** ���ݿ�����
	define('CFG_DB_HOST', "localhost");		// MySQL ������ַ
	define('CFG_DB_NAME', "twmj_test");			// ���ݿ�����
	define('CFG_DB_USER', "root");			// �����û�
	define('CFG_DB_PWD', "123456");			// ��������
##
	define('CFG_DEBUG_MODE', True);			// ͳ�Ƴ�������ʱ��
	define('CFG_RUN_TIME', True);			// ͳ�Ƴ�������ʱ��
##
	define('CFG_IMG_PATH', "./images");		// �ڲ���վͼƬ·��
} else {
//** ���ݿ�����
	define('CFG_DB_HOST', "220.247.145.226");		// MySQL ������ַ
	define('CFG_DB_NAME', "addwe");			// ���ݿ�����
	define('CFG_DB_USER', "addwe");			// �����û�
	define('CFG_DB_PWD',  "yy22787789");			// ��������
##
	define('CFG_DEBUG_MODE', False);		// ͳ�Ƴ�������ʱ��
	define('CFG_RUN_TIME', False);			// ͳ�Ƴ�������ʱ��
	define('CFG_IMG_PATH', "./images");		// �ⲿ��վͼƬ·��
}

define('CFG_DB_COMPANY', "www.betcity.com.tw");		// MySQL ����ʱ��ʾ�Ĺ�˾����
define('CFG_DB_ADMINMAIL', "service@betcity.com.tw");		// MySQL ����ʱ�ĳ��������ַ

define('SMTP_HOST', "smtp.126.com");
define('SMTP_USER', "yuanlin31@126.com");
define('SMTP_PWD', "hh02.02.330");
define('SMTP_PORT', 25);
define('SMTP_DOMAIN', false);				// ����һ����������ʹ�ö����������ҵ����ʱ, Ҫ����Ϊ true
define('SMTP_SEND', "<service@betcity.com.tw>");	// α��ķ�����

/* APP */
define('APP_DB_PREFIX', "");				// ��Ŀ���Ͽ�ǰ׺
define('APP_LOG_PATH', "/home/htdocs/betcity/data/log/");			// ��Ŀ��־�ļ�·��
define('APP_TMP_PATH', "/home/htdocs/betcity/data/tmp/");			// ��Ŀ��ʱ�ļ�·��

define('APP_ATM_NO', "59102000000848");			// �������������ʺ�
define('APP_BuySafe_NO', "b070214017");			// ������ ���� BuySafe ˢ�� (���ÿ�֧��) ���ʺ� Id
define('APP_BuySafe_PWD', "aa2278");			// ����
define('APP_ShopPay_NO', "b070214361");			// ������ ���� 24Payment (��������֧��) ���ʺ� Id
define('APP_Hinet3A_NO', "addwe1");			// ������ ��ʢ ���̼� Id
define('APP_GwShop_NO', '4982');			// �������̽�ĳ����̼Ҵ���
define('APP_GwShop_Check', '00001348');			// �̽糬�̼����
define('APP_GwPay_NO', '4982');				// �������̽�����ÿ��̼Ҵ���

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
 * �������Ͽ⣬�������и����Ƿ���Ҫ���Ͽ�ȥ���á�
 */
function loadDB() {
	if (!class_exists("MySql")) {
		include_once ("/home/htdocs/betcity/lib/mysql.class.php");  // ����MySQL��
	}

	$Q = & New MySql();
	$Q->Host       = CFG_DB_HOST;
	$Q->Database   = CFG_DB_NAME;
	$Q->User       = CFG_DB_USER;
	$Q->Password   = CFG_DB_PWD;
	$Q->Company    = CFG_DB_COMPANY;
	$Q->AdminMail  = CFG_DB_ADMINMAIL;

	if ( !$Q->connect() ) {
		halt("�������Ͽ�����ʧ�ܣ�<br>Session halted.");
	}

	return $Q;
}

/***
 * �滻ģ���еı���Ϊ��������
 * �������ܹ��ĳ�ʽҲ����֧��ģ��~
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
