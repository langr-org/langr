<?PHP
/***
 * ���ƣ�PHP OOP ������񿪷������׼�  v0.06.03
 * ���ܣ���Ŀ-���������ļ�
 * 
 * $Id: main.inc.php 8 2009-10-20 10:05:34Z langr $
 */
  
/* ������Ŀ������� */
define('CFG_PROJECT_NAME', "cphp");			/* ������ĿĿ¼���� */
define('CFG_APP_PATH', "/cphp");			/* �ܹ���� Web �ĵ���Ŀ¼����·��  */
define('CFG_DEFAULT_MODULE', "index");			/* ȱʡ�� */
define('CFG_DEFAULT_ACTION', "index");			/* ȱʡ���� */

/* ģ����Ŀ������� */
define('CFG_TEMPLATE_PATH', "tmpl");			/* ģ��Ŀ¼ */
define('CFG_TEMPLATE_CACHE_PREFIX', "tmplCache");	/* ģ�建��Ŀ¼ǰ׺�����������Զ����� _big5 �� _gb2312 */

/* ��վ�������� */
define('CFG_CHAR_SET', "GB2312");			/* ��վ��������(UTF-8 ����)����ѡֵ��GB2312��BIG5 */
define('CFG_TEMPLATE_LANGUAGE', "GBK");			/* ����ģ������Է�ʽ */
define('CFG_DB_CHAR', "UTF8");				/* ���ݿ���� */

/* ReWrite ֧�� */
define('REWRITE', 0);
define('RW_SCRIPT', "index.php");			/* ֧�� REWRITE �Ľű���, �� apache �����ļ���Ӧ����Ӧ���޸�   */

/* ����ͻ��� IP */
$cip	= getenv("REMOTE_ADDR");
$rcip	= getenv("HTTP_X_FORWARDED_FOR");
if ($rcip) $cip	= $rcip;				/* ʹ���˴���? */
define('CLIENT_IP', $cip);

/* ���ݿ������ѡ��  */
if ( getenv("SERVER_ADDR") == "127.0.0.1" ) {		/* �ڲ� */
	/* ���ݿ����� */
	define('CFG_DB_HOST', "localhost");		/* MySQL ������ַ */
	define('CFG_DB_NAME', "pu");			/* ���ݿ����� */
	define('CFG_DB_USER', "root");			/* �����û� */
	define('CFG_DB_PWD', "123456");			/* �������� */
	/* ֻ�����ݿ����� */
	define('CFG_RO_DB_HOST', CFG_DB_HOST);		/* ֻ�� MySQL ������ַ */
	define('CFG_RO_DB_NAME', CFG_DB_NAME);		/* ���ݿ����� */
	define('CFG_RO_DB_USER', CFG_DB_USER);		/* �����û� */
	define('CFG_RO_DB_PWD', CFG_DB_PWD);		/* �������� */

	define('CFG_DEBUG_MODE', True);			/* �򿪵���ģʽ */
	define('CFG_RUN_TIME', False);			/* ͳ�Ƴ�������ʱ�� */
	define('CFG_IMG_PATH', "./images");		/* �ڲ���վͼƬ·�� */
} else {
	/* ���ݿ����� */
	define('CFG_DB_HOST', "localhost");	/* MySQL ������ַ */
	define('CFG_DB_NAME', "LangR");			/* ���ݿ����� */
	define('CFG_DB_USER', "langr");			/* �����û� */
	define('CFG_DB_PWD',  "hua_db");		/* �������� */
//	define('CFG_DB_HOST', "localhost");		/* MySQL ������ַ */
//	define('CFG_DB_NAME', "twmj_test");		/* ���ݿ����� */
//	define('CFG_DB_USER', "root");			/* �����û� */
//	define('CFG_DB_PWD', "bkMJ8*");			/* �������� */
	/* ֻ�����ݿ����� */
	define('CFG_RO_DB_HOST', CFG_DB_HOST);		/* ֻ�� MySQL ������ַ */
	define('CFG_RO_DB_NAME', CFG_DB_NAME);		/* ���ݿ����� */
	define('CFG_RO_DB_USER', CFG_DB_USER);		/* �����û� */
	define('CFG_RO_DB_PWD', CFG_DB_PWD);		/* �������� */

	define('CFG_DEBUG_MODE', False);		/* ����ģʽ */
	define('CFG_RUN_TIME', False);			/* ͳ�Ƴ�������ʱ�� */
	define('CFG_IMG_PATH', "./images");		/* �ⲿ��վͼƬ·�� */

}

define('CFG_DB_COMPANY', "www.com.cn");			/* MySQL ����ʱ��ʾ�Ĺ�˾���� */
define('CFG_DB_ADMINMAIL', "webmaster@com.cn");		/* MySQL ����ʱ�ĳ��������ַ */

/* smtp �����ʼ������ʺ� */
define('SMTP_HOST', "smtp.126.com");
define('SMTP_USER', "yuanlin31@126.com");
define('SMTP_PWD', "hh02.02.330");
define('SMTP_PORT', 25);
define('SMTP_DOMAIN', false);				/* ����һ����������ʹ�ö����������ҵ����ʱ, Ҫ����Ϊ true */
define('SMTP_SEND', "<service@betcity.com.tw>");	/* α��ķ����� */

/* �������� */
define('CFG_ADMIN_VERIFY', "ToDayIs0204");

/* PHP����[������־]���� */
if (CFG_RUN_TIME) {						/* ����־���� CFG_RUN_TIME ����Ϊ True ʱ��Ч */
	define('CFG_RUN_TIME_LOG_NAME', "log/phpRunTime.log");	/* ��������ʱ����־���� */
	define('CFG_RUN_TIME_LOG_SIZE_MAXIMUM', 102400);	/* ��־�ļ����������, �����������־, ��λbyte(�ֽ�) */
	define('CFG_RUN_TIME_MAXIMUM', 0);			/* �����������ʱ��, ������ʱ�佫д����־, ��λsecond(��) */
}

/* */
require_once("./include/config/app.inc.php");
?>
