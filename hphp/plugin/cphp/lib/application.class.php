<?php
/***
 * PHP OOP 面向物件开发工具套件 Application v0.06.03
 * 导向器类，部分基本初始化，根据 MODULE 、ACTION 将程式导向具体的功能
 * 
 * $Id: application.class.php 6 2007-10-28 03:40:44Z langr $
 */
class Application
{
	/***
	 * 内部属性，不需要外部指定和修改。
	 * $App['starttime'] 程式执行时间统计开始时间
	 */
	var $App	= array();
	
	/***
	 * 构造函数，定义类实体时自动执行所有操作
	 */
	function Application()
	{
		if (CFG_RUN_TIME) 					/* 统计程式运行时间开始 */
			$this->startTimer();	
		$this->setup();						/* 设定网站资讯 */
		$this->language();					/* 设定网站语言 */			
		$this->getPathFileInfo();				/* 获取路径及文件资讯 */
		$this->loadLib();					/* 载入架构套件 */
		$this->varFilter();					/* 变数过滤 */

		return;
	}

	/***
	 *
	 * NOW_TIME : 取得当前时间戳记
	 */
	function setup() 
	{
		error_reporting(7);					/* 设定错误讯息回报的等级 */
		define('NOW_TIME', time());				/* 取得当前时间戳记 */
		/* 取得当前用户的IP地址 */
	//	define('CLIENT_IP', getenv('HTTP_CLIENT_IP'));
	//	define('FORWARDED_IP', getenv('HTTP_X_FORWARDED_FOR'));
	//	define('IP', getenv('REMOTE_ADDR'));
	}

	/***
	 * 
	 * PHP_SCRIPT		: 脚本档案名
	 * PHP_SCRIPT_PATH	: 脚本目录名
	 * PHP_SCRIPT_FULL_NAME	: 脚本文件全名
	 * FILE_PATH		: 当前脚本文件到最上层工作脚本目录的相对路径
	 */
	function getPathFileInfo() 
	{
		define('SCRIPT_FILENAME', $_SERVER['SCRIPT_FILENAME']);	/* 脚本档案路径及文件名 如 /home/htdocs/app/index.php */
		define('PHP_SCRIPT', basename(SCRIPT_FILENAME));	/* 脚本档案名 如 index.php */
		$strpos	= strpos(SCRIPT_FILENAME, CFG_PROJECT_NAME);	/* 脚本目录名 -> /home/htdocs/app */

		if ( !$strpos ){
			die("错误：在工程工作目录中不能找到配置参数指定的工程目录名称");
		}
		if ( strspn(CFG_PROJECT_NAME,SCRIPT_FILENAME) != strlen(CFG_PROJECT_NAME) ){
			 die("错误：工程目录名称的配置参数设定与脚本目录中的其他目录名称重复。");
		}
		define('PHP_SCRIPT_PATH', substr(SCRIPT_FILENAME, 0, $strpos+strlen(CFG_PROJECT_NAME)));

		/* 脚本文件全名 如 /php/index.php */
		define('PHP_SCRIPT_FULL_NAME', str_replace(PHP_SCRIPT_PATH, '', SCRIPT_FILENAME));

		/* 当前脚本到最上一层工作脚本目录的相对路径 */
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
	 * 载入架构套件
	 */
	function loadLib() 
	{
		require_once(FILE_PATH."lib/cortrol.class.php");	/* 载入控制类 */
		require_once(FILE_PATH."lib/main.fun.php");		/* 载入函数库 */

		return;
	}

	/***
	 * 设定网站语言
	 * CHAR_SET_URL	: 简繁转换的连结地址
	 * CHAR_SET	: 网站语言设置
	 */
	function language() 
	{
		if ( !extension_loaded('iconv') ) {			/* 判断当前PHP系统是否支援ICONV */
			define("CHAR_SET", "gb2312");
			die("错误：当前系统不支援 ICONV 模组。");
		}
		if ( defined('CFG_CHAR_SET') ) {			/* 判断是否有指定 CHAR_SET */
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

			$queryStr = getenv("QUERY_STRING");		/* 获取其他 url 后的 get 方式变数字串 */
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
	 * 变量过滤
	 * 1、去除左右两端空格；
	 * 2、将特殊字元转成 HTML 格式；
	 * 3、禁止 javascript；
	 */
	function varFilter() 
	{
		$_GET	= varFilter($_GET);	/* 过滤 _GET 阵列 */
		$_POST	= varFilter($_POST);	/* 过滤 _POST 阵列 */

		return;
	}

	/***
	 * 根据当前脚本档案名自动获取 Path 变数 (全小写字元)
	 * path 为要执行的类的路径，例如：admin.php 为脚本名，调用类的路径即为 ./php/admin/
	 */
	function getPath()
	{
		$path	= strtolower("php/".substr(PHP_SCRIPT,0,strpos(PHP_SCRIPT,'.'))."/"); 
		return $path; 
	}

	/***
	 * 获取 Module 变数 Action 为要执行的类（首字元大写，其他全小写）
	 */
	function getModule()
	{
		$module = isset($_POST['module']) ? $_POST['module'] : $_GET['module'];
		if (empty($module)) { 
			$module = CFG_DEFAULT_MODULE;	/* 如果 $module 为空，则赋予预设值 */
		} 
		return $module; 
	}

	/***
	 * 获取 Action 变数 Action 为类要执行的操作（会自动转换成 do$action 的格式，$action 首字母自动大写）
	 */
	function getAction()
	{
		$action	= isset($_POST['action']) ? $_POST['action'] : $_GET['action'];
		$actionPrefix = isset($_POST['action']) ? "do" : "show";
		if (empty($action)) {
			$action = CFG_DEFAULT_ACTION;
		}
		define('ACTION_PREFIX', $actionPrefix);	/* 定义 Action 字首，post 方式时为 do，get 方式时为 show */
		return $action; 
	}

	/***
	 * 载入控制器，导向具体指定的执行操作
	 * MODULE_PATH		: 模组的路径
	 * MODULE_NAME		: 模组的名称
	 * ACTION_NAME		: 操作的名称
	 */
	function run()
	{
		define('MODULE_PATH', $this->getPath());			/* Module 的路径 */
		define('MODULE_NAME', $this->getModule());			/* Module 的档案名称 */
		define('ACTION_NAME', $this->getAction());			/* Action 的 Module 需要调用的方法名称 */

		/* 载入公共类 */
		$publicClassFile = FILE_PATH.MODULE_PATH."public.class.php";
		if (file_exists($publicClassFile)) {
			require_once ($publicClassFile);
		}

		/* 载入具体功能的执行类 */
		$moduleClassFile = FILE_PATH.MODULE_PATH.MODULE_NAME.'.class.php';
		if (file_exists($moduleClassFile)) {
			require_once($moduleClassFile);
		}
		if (!class_exists(MODULE_NAME)) {
			halt("错误：不能载入 ".MODULE_NAME." 模组。");
		}
		$moduleClass = MODULE_NAME;
		$module	= & new $moduleClass();					/* 对具体功能的类定义实体 */
		if (CFG_RUN_TIME) {
			if (0 == CFG_RUN_TIME_MAXIMUM) {
				echo "<br>RunTime:".$this->endTimer();		/* 统计程式运行时间结束并显示 */
			} else {
				$this->runTimeLog();				/* 判断超时，并记录到 RunTime 日志中 */
			}
		}
		exit();
	}

	/***
	 * 程式执行时间统计开始
	 */
	function startTimer() {
		$mtime = microtime ();
		$mtime = explode (' ', $mtime);
		$mtime = $mtime[1] + $mtime[0];
		$this->App['starttime'] = $mtime;
	}

	/***
	 * 程式执行时间统计结束
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
	 * 程式运行时间日志
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
