<?php
/***
 * PHP OOP ����������������׼� Application v0.06.03
 * �������࣬���ֻ�����ʼ�������� MODULE ��ACTION ����ʽ�������Ĺ���
 * 
 * $Id: application.class.php 6 2007-10-28 03:40:44Z langr $
 */
class Application
{
	/***
	 * �ڲ����ԣ�����Ҫ�ⲿָ�����޸ġ�
	 * $App['starttime'] ��ʽִ��ʱ��ͳ�ƿ�ʼʱ��
	 */
	var $App	= array();
	
	/***
	 * ���캯����������ʵ��ʱ�Զ�ִ�����в���
	 */
	function Application()
	{
		if (CFG_RUN_TIME) 					/* ͳ�Ƴ�ʽ����ʱ�俪ʼ */
			$this->startTimer();	
		$this->setup();						/* �趨��վ��Ѷ */
		$this->language();					/* �趨��վ���� */			
		$this->getPathFileInfo();				/* ��ȡ·�����ļ���Ѷ */
		$this->loadLib();					/* ����ܹ��׼� */
		$this->varFilter();					/* �������� */

		return;
	}

	/***
	 *
	 * NOW_TIME : ȡ�õ�ǰʱ�����
	 */
	function setup() 
	{
		error_reporting(7);					/* �趨����ѶϢ�ر��ĵȼ� */
		define('NOW_TIME', time());				/* ȡ�õ�ǰʱ����� */
		/* ȡ�õ�ǰ�û���IP��ַ */
	//	define('CLIENT_IP', getenv('HTTP_CLIENT_IP'));
	//	define('FORWARDED_IP', getenv('HTTP_X_FORWARDED_FOR'));
	//	define('IP', getenv('REMOTE_ADDR'));
	}

	/***
	 * 
	 * PHP_SCRIPT		: �ű�������
	 * PHP_SCRIPT_PATH	: �ű�Ŀ¼��
	 * PHP_SCRIPT_FULL_NAME	: �ű��ļ�ȫ��
	 * FILE_PATH		: ��ǰ�ű��ļ������ϲ㹤���ű�Ŀ¼�����·��
	 */
	function getPathFileInfo() 
	{
		define('SCRIPT_FILENAME', $_SERVER['SCRIPT_FILENAME']);	/* �ű�����·�����ļ��� �� /home/htdocs/app/index.php */
		define('PHP_SCRIPT', basename(SCRIPT_FILENAME));	/* �ű������� �� index.php */
		$strpos	= strpos(SCRIPT_FILENAME, CFG_PROJECT_NAME);	/* �ű�Ŀ¼�� -> /home/htdocs/app */

		if ( !$strpos ){
			die("�����ڹ��̹���Ŀ¼�в����ҵ����ò���ָ���Ĺ���Ŀ¼����");
		}
		if ( strspn(CFG_PROJECT_NAME,SCRIPT_FILENAME) != strlen(CFG_PROJECT_NAME) ){
			 die("���󣺹���Ŀ¼���Ƶ����ò����趨��ű�Ŀ¼�е�����Ŀ¼�����ظ���");
		}
		define('PHP_SCRIPT_PATH', substr(SCRIPT_FILENAME, 0, $strpos+strlen(CFG_PROJECT_NAME)));

		/* �ű��ļ�ȫ�� �� /php/index.php */
		define('PHP_SCRIPT_FULL_NAME', str_replace(PHP_SCRIPT_PATH, '', SCRIPT_FILENAME));

		/* ��ǰ�ű�������һ�㹤���ű�Ŀ¼�����·�� */
		$scriptArray	= split("/", PHP_SCRIPT_FULL_NAME);
		$scriptArrayCount = count($scriptArray);
		$scriptLevel	= $scriptArrayCount - 2;
		$filePath	= '';
		for ( $i = 1; $i <= $scriptLevel; $i++ ) {
			$filePath .= '../';
		}
		if ( '' == $filePath ) 
			$filePath = "./";
		define('FILE_PATH', $filePath);
		
		return;
	}
	
	/***
	 * ����ܹ��׼�
	 */
	function loadLib() 
	{
		require_once(FILE_PATH."lib/cortrol.class.php");	/* ��������� */
		require_once(FILE_PATH."lib/main.fun.php");		/* ���뺯���� */

		return;
	}

	/***
	 * �趨��վ����
	 * CHAR_SET_URL	: ��ת���������ַ
	 * CHAR_SET	: ��վ��������
	 */
	function language() 
	{
		if ( !extension_loaded('iconv') ) {			/* �жϵ�ǰPHPϵͳ�Ƿ�֧ԮICONV */
			define("CHAR_SET", "gb2312");
			die("���󣺵�ǰϵͳ��֧Ԯ ICONV ģ�顣");
		}
		if ( defined('CFG_CHAR_SET') ) {			/* �ж��Ƿ���ָ�� CHAR_SET */
			$charSet = strtolower(CFG_CHAR_SET);
		} else {
			$charSet = $_COOKIE["charSet"];			
			if ( 'big5' == strtolower($_GET['setChar']) || 'gb2312' == strtolower($_GET['setChar']) ) {
				setcookie("charSet", strtolower($_GET['setChar']), 1, "/");
				$charSet = strtolower($_GET['setChar']);
			} else {
				if ( '' == $charSet ) {
					if ( 'zh-cn' == getenv("HTTP_ACCEPT_LANGUAGE") ) {
						$charSet = 'gb2312';
					} else {
						$charSet = 'big5';
					}
				}
			}

			$queryStr = getenv("QUERY_STRING");		/* ��ȡ���� url ��� get ��ʽ�����ִ� */
			if ( $queryStr ) {
				$queryStr .= "&";
				$queryStr = eregi_replace("setChar=[a_zA-Z0-9]{4,6}&","", $queryStr);
			}
			if ( $charSet == "big5" ) {
				define("CHAR_SET_URL", basename(getenv('SCRIPT_NAME'))."?".$queryStr."setChar=gb2312");
			} else {
				define("CHAR_SET_URL", basename(getenv('SCRIPT_NAME'))."?".$queryStr."setChar=big5");
			}
		}
		define("CHAR_SET", $charSet);	

		return;
	}

	/**
	 * ��������
	 * 1��ȥ���������˿ո�
	 * 2����������Ԫת�� HTML ��ʽ��
	 * 3����ֹ javascript��
	 */
	function varFilter() 
	{
		$_GET	= varFilter($_GET);	/* ���� _GET ���� */
		$_POST	= varFilter($_POST);	/* ���� _POST ���� */

		return;
	}

	/***
	 * ���ݵ�ǰ�ű��������Զ���ȡ Path ���� (ȫСд��Ԫ)
	 * path ΪҪִ�е����·�������磺admin.php Ϊ�ű������������·����Ϊ ./php/admin/
	 */
	function getPath()
	{
		$path	= strtolower("php/".substr(PHP_SCRIPT,0,strpos(PHP_SCRIPT,'.'))."/"); 
		return $path; 
	}

	/***
	 * ��ȡ Module ���� Action ΪҪִ�е��ࣨ����Ԫ��д������ȫСд��
	 */
	function getModule()
	{
		$module = isset($_POST['module']) ? $_POST['module'] : $_GET['module'];
		if (empty($module)) { 
			$module = CFG_DEFAULT_MODULE;	/* ��� $module Ϊ�գ�����Ԥ��ֵ */
		} 
		return $module; 
	}

	/***
	 * ��ȡ Action ���� Action Ϊ��Ҫִ�еĲ��������Զ�ת���� do$action �ĸ�ʽ��$action ����ĸ�Զ���д��
	 */
	function getAction()
	{
		$action	= isset($_POST['action']) ? $_POST['action'] : $_GET['action'];
		$actionPrefix = isset($_POST['action']) ? "do" : "show";
		if (empty($action)) {
			$action = CFG_DEFAULT_ACTION;
		}
		define('ACTION_PREFIX', $actionPrefix);	/* ���� Action ���ף�post ��ʽʱΪ do��get ��ʽʱΪ show */
		return $action; 
	}

	/***
	 * ������������������ָ����ִ�в���
	 * MODULE_PATH		: ģ���·��
	 * MODULE_NAME		: ģ�������
	 * ACTION_NAME		: ����������
	 */
	function run()
	{
		define('MODULE_PATH', $this->getPath());			/* Module ��·�� */
		define('MODULE_NAME', $this->getModule());			/* Module �ĵ������� */
		define('ACTION_NAME', $this->getAction());			/* Action �� Module ��Ҫ���õķ������� */

		/* ���빫���� */
		$publicClassFile = FILE_PATH.MODULE_PATH."public.class.php";
		if (file_exists($publicClassFile)) {
			require_once ($publicClassFile);
		}

		/* ������幦�ܵ�ִ���� */
		$moduleClassFile = FILE_PATH.MODULE_PATH.MODULE_NAME.'.class.php';
		if (file_exists($moduleClassFile)) {
			require_once($moduleClassFile);
		}
		if (!class_exists(MODULE_NAME)) {
			halt("���󣺲������� ".MODULE_NAME." ģ�顣");
		}
		$moduleClass = MODULE_NAME;
		$module	= & new $moduleClass();					/* �Ծ��幦�ܵ��ඨ��ʵ�� */
		if (CFG_RUN_TIME) {
			if (0 == CFG_RUN_TIME_MAXIMUM) {
				echo "<br>RunTime:".$this->endTimer();		/* ͳ�Ƴ�ʽ����ʱ���������ʾ */
			} else {
				$this->runTimeLog();				/* �жϳ�ʱ������¼�� RunTime ��־�� */
			}
		}
		exit();
	}

	/***
	 * ��ʽִ��ʱ��ͳ�ƿ�ʼ
	 */
	function startTimer() {
		$mtime = microtime ();
		$mtime = explode (' ', $mtime);
		$mtime = $mtime[1] + $mtime[0];
		$this->App['starttime'] = $mtime;
	}

	/***
	 * ��ʽִ��ʱ��ͳ�ƽ���
	 */
	function endTimer() {
		$mtime = microtime ();
		$mtime = explode (' ', $mtime);
		$mtime = $mtime[1] + $mtime[0];
		$endtime = $mtime;
		$totaltime = round (($endtime - $this->App['starttime']), 5);
		return $totaltime;
	}

	/***
	 * ��ʽ����ʱ����־
	 */
	function runTimeLog() {
		if ($this->endTimer() > CFG_RUN_TIME_MAXIMUM) {
			$logNewEntry = $_SERVER['REQUEST_URI'].' '.$this->endTimer().' '.$_SERVER['REMOTE_ADDR'].' '.date('Y-m-d h:i:s')."\n";
			dolog(FILE_PATH.CFG_RUN_TIME_LOG_NAME,CFG_RUN_TIME_LOG_SIZE_MAXIMUM,$logNewEntry);
		}
		return;
	}
}
?>
