<?php
/***
 * 名称：PHP OOP 面向物件开发工具套件 Cortrol v0.06.03
 * 功能：控制器类，集成了网站设置处理、资料库初始化、模板处理、分页处理、错误（提示）资讯处理等功能
 * 
 * $Id: cortrol.class.php 8 2009-10-20 10:05:34Z langr $
 */
class Cortrol
{
	/***
	 * 外部属性，其值由外部指定。
	 */
	var $Setup	= array();		/* 资料库保存的配置参数 */
	var $ErrMsg	= "";			/* 错误资讯 */
	var $PromptMsg	= "";			/* 提示资讯 */
	var $UrlJump	= "";			/* 跳转地址 */
	var $Tmpl	= array();		/* 模板元素 */
	/***
	 * 内部属性，不需要外部指定和修改。
	 * $Cortrol['tmplFile']		:	/* 模板档案名及路径 /
	 * $Cortrol['tmplCacheFile']	:	/* 模板缓存档案名及路径 /
	 * $Cortrol['subTmplFile']	:	/* 模板档案名及路径（不含.tpl.php），方便调用子模板 /
	 */
	var $Q		= "";			/* 资料库实体物件 */
	var $Cortrol	= array();
	var $Page	= array();
	/***
	 * 分页属性
	 * $Page['firstRow']		:	/* 起始行 /
	 * $Page['listRows']		:	/* 列表行数 /
	 * $Page['parameter']		:	/* 页数跳转时要带的参数 /
	 * $Page['totalPages']		:	/* 总页数 /
	 * $Page['totalRows']		:	/* 总行数 /
	 * $Page['nowPage']		:	/* 当前页数 /
	 * $Page['coolPages']		:	/* 分页的栏的总页数 /
	 * $Page['rollPage']		:	/* 分页栏每页显示的页数 /
	 */
	
	/***
	 * 构造函数，定义类实体时自动执行所有操作
	 */
	function Cortrol()
	{
		$moduleAction = ACTION_PREFIX.ucfirst(ACTION_NAME); 

		if (!method_exists($this,$moduleAction)) {		/* 检查 do.Action 是否存在 */
			halt('错误：非法操作！');
		}

		$this->getTmplFile();					/* 自动获取模板的档案名 */
		$this->loadSetup();					/* 载入 ./include/config/system.inc.php 系统配置文件 */

		/* 判断是否存在 public 方法，如果不存在就直接执行方法，如果存在，由 public 方法中去执行 */
		if (!method_exists($this,"Index_Public")) {	
			$this->$moduleAction();
		}

		return;
	}

	/***
	 * 载入资料库，由子类中根据是否需要资料库去调用。
	 */
	function loadDB()
	{
		if (!class_exists("MySql")) {
			include_once (FILE_PATH."include/db/mysql.class.php");	/* 载入MySQL类 */
		}

		$this->Q = & New MySql();
		$this->Q->Host = CFG_DB_HOST;
		$this->Q->Database = CFG_DB_NAME;
		$this->Q->User = CFG_DB_USER;
		$this->Q->Password = CFG_DB_PWD;
		$this->Q->Company = CFG_DB_COMPANY;
		$this->Q->AdminMail = CFG_DB_ADMINMAIL;

		if ( !$this->Q->connect() ) {
			halt("错误：资料库连接失败！<br>Session halted.");
		}

		return $this->Q;
	}

	/***
	 * 载入只读资料库，由子类中根据是否需要资料库去调用。
	 * 在有对资料库写发生时, 则返回出错
	 */
	function loadRODB()
	{
		if (!class_exists("MySql")) {
			include_once (FILE_PATH."include/db/mysql.class.php");	/* 载入MySQL类 */
		}

		if ( defined("CFG_RO_DB_HOST") ) {
			$this->Q = & New MySql();
			$this->Q->Host = CFG_RO_DB_HOST;
			$this->Q->Database = CFG_RO_DB_NAME;
			$this->Q->User = CFG_RO_DB_USER;
			$this->Q->Password = CFG_RO_DB_PWD;
			$this->Q->Company = CFG_DB_COMPANY;
			$this->Q->AdminMail = CFG_DB_ADMINMAIL;
		
			if ( !$this->Q->connect() ) {
				halt("错误：资料库连接失败！<br>Session halted.");
			}
		} else {
			$this->Q = $this->loadDB();
		}

		$this->Q->ReadOnly = true;

		return $this->Q;
	}
	
	/***
	 * 载入系统配置参数
	 */
	function loadSetup(){
		if (file_exists(FILE_PATH."include/config/system.inc.php")) {
			include_once (FILE_PATH."include/config/system.inc.php");	/* 载入系统配置文件 */
			if (is_array($systemSetup)) {
				foreach  ($systemSetup as $key => $val) {
					if ("1" == $systemSetup[$key]['allowTmpl']) {
						$this->Tmpl[$key] = trueHtml($systemSetup[$key]['value']);
					} else {
						$this->Setup[$key] = $systemSetup[$key]['value'];
					}
				}
				if (isset($this->Setup['active']))	  $this->checkSetupActive();		/* 检查网站是否关闭 */
				if (isset($this->Setup['loadLimit'])) $this->checkSetupLoadLimit();		/* 检查系统负核 */
				if (isset($this->Setup['denyIp']))	  $this->checkSetupDenyIp();		/* 检查禁止IP地址 */
			}
		}
		return;
	}

	/***
	 * 根据系统参数 active 检查网站是否关闭
	 */
	function checkSetupActive(){
		if ( ("true" == strtolower($this->Setup['active'])) || ("admin.php" == PHP_SCRIPT) 
				|| ("finance.php" == PHP_SCRIPT) || ("dataCount" == MODULE_NAME) ) {
			return;
		} else {
			if ( ("false" == strtolower($this->Setup['active'])) || ("0" == strtolower($this->Setup['active'])) ) {
				halt("系统维护中，请稍候访问。");
			} else {
				halt(iconv("UTF-8","GBK",$this->Setup['active']));
			}
		}
		return;
	}

	/***
	 * 根据系统参数 loadLimit 检查系统负核是否过高（仅限 Linux 系统）
	 */
	function checkSetupLoadLimit(){
		if (($this->Setup['loadLimit'] > 0) && (PHP_OS == 'Linux') && (PHP_SCRIPT != "admin.php")) {
			if ( $fp = @fopen('/proc/loadavg', 'r') ) {
				$filestuff = @fread($fp, 6);
				fclose($fp);
				$loadavg = explode(' ', $filestuff);
				if ( trim($loadavg[0]) > $this->Setup['loadLimit'] ) {
					halt("系统忙，请稍候访问。");
				}
			}
		}
		return;
	}

	/***
	 * 根据系统参数 denyIp 检查当前 IP 是否被禁止
	 */
	function checkSetupDenyIp(){
		if ($this->Setup['denyIp'] != "0") {
			$ip1 = getenv('REMOTE_ADDR');
			$ip2 = substr($ip1,0,strrpos($ip1,'.'));
			$denyIPArray = split(",",$this->Setup['denyIp']);
			$denyIp		 = False;
			if ((!empty($ip1)) && (in_array($ip1,$denyIPArray))) $denyIp = True;
			if ((!empty($ip2)) && (in_array($ip2,$denyIPArray))) $denyIp = True;
			if ($denyIp) {
				$haltMsg = "<H1>Internal Server Error</H1>The server encountered an internal error or misconfiguration and was unable to complete your request.<P> Please contact the server administrator, webmaster@domain.com and inform them of the time the error occurred,and anything you might have done that may have caused the error.<P>More information about this error may be available in the server error log.<P><HR><ADDRESS>Apache/2.0.52 Server at ".$_SERVER["HTTP_HOST"]." Port 80</ADDRESS>";
				halt($haltMsg);
			}
		}
		return;
	}

	/***
	 * 显示错误资讯
	 */
	function promptMsg($type = 'jump') {
		if ($this->ErrMsg != "") {
			$this->Tmpl['message'] = "<b><font color=red>".$this->ErrMsg."</font></b>";
			$this->Tmpl['autoJumpUrl'] = $this->UrlJump ? $this->UrlJump : "javascript:history.back(-1);";
			$this->Tmpl['waitSecond']	 = "3";
		} else if ($this->PromptMsg != "") {
			$this->Tmpl['message'] = "<b><font color=blue>".$this->PromptMsg."</font></b>";
			$this->Tmpl['autoJumpUrl'] = $this->UrlJump ? $this->UrlJump : "javascript:history.back(-1);";
			$this->Tmpl['waitSecond'] = "1";
		}
		if ('jump' == $type) {
			$this->Tmpl['errorType'] = "请等待系统转向...<br> <br> (<a href='".$this->Tmpl['autoJumpUrl']."'>如果您不想等待，请点击此处链接</a>)";
		} elseif ('close' == $type) {
			$this->Tmpl['autoJumpUrl']	= "javascript:window.close();";
			$this->Tmpl['waitSecond']	= "5";
			$this->Tmpl['errorType']	= "请等待窗口关闭<br> <br> (<a href='".$this->Tmpl['autoJumpUrl']."'>如果您不想等待，请点击此处关闭</a>)";
		} elseif ('close_game' == $type) {
			$this->Tmpl['autoJumpUrl']	= "app:close";
			$this->Tmpl['waitSecond']	= "5";
			$this->Tmpl['errorType']	= "请等待窗口关闭<br> <br> (<a href='".$this->Tmpl['autoJumpUrl']."'>如果您不想等待，请点击此处关闭</a>)";
		}
		

		$this->Tmpl['message'] = autoCharSet($this->Tmpl['message']);
		$this->Tmpl['errorType'] = autoCharSet($this->Tmpl['errorType']);

		$this->loadTmplate(TEMPLATE_PATH."public/prompt.tpl.php");	/* 调用资讯显示模板 */
		exit;
	}

	/***
	 * 分页
	 * 可以使用 page.class.php 替代
	 */
	function page() {
		if ( !isset($this->Page['rollPage']) ) $this->Page['rollPage'] = 10;
		if ( (!isset($this->Page['parameter'])) || (empty($this->Page['parameter'])) ) {
			$this->Page['parameter'] = "module=".MODULE_NAME."&action=".ACTION_NAME;
		} else {
			$this->Page['parameter'] .= "&module=".MODULE_NAME."&action=".ACTION_NAME;
		}
		if(0 == $this->Page['totalRows']) {
			return;
		}
		$this->Page['totalPages'] = ceil($this->Page['totalRows']/$this->Page['listRows']); 	/*总页数 */
		$this->Page['coolPages'] = ceil($this->Page['totalPages']/$this->Page['rollPage']);
		$this->Page['nowPage']	= floor($this->Page['firstRow']/$this->Page['listRows']+1);	/*当前页号 */
		$nowCoolPage		= ceil($this->Page['nowPage']/$this->Page['rollPage']);

		/*上下翻頁字串 */
		$upRow	= $this->Page['firstRow']-$this->Page['listRows'];
		$downRow = $this->Page['firstRow']+$this->Page['listRows'];
		if ($upRow>=0){
			$upPage="<a href='".url(PHP_SCRIPT."?firstRow=$upRow&totalRows=".$this->Page['totalRows']."&".$this->Page['parameter'])."'>上一页</a>";
		}else{
			$upPage="";
		}
		if ($downRow<$this->Page['totalRows']){
			$downPage="<a href='".url(PHP_SCRIPT."?firstRow=$downRow&totalRows=".$this->Page['totalRows']."&".$this->Page['parameter'])."'>下一页</a>";
		}else{
			$downPage="";
		}
		/* << < > >> */
		if($nowCoolPage == 1){
			$theFirst = "";
			$prePage = "";
		}else{
			$preRow =  ($this->Page['rollPage']*($nowCoolPage-1)-1)*$this->Page['listRows'];
			$prePage = "<a href='".url(PHP_SCRIPT."?firstRow=$preRow&totalRows=".$this->Page['totalRows']."&".$this->Page['parameter'])."' title='上".$this->Page['rollPage']."页'>上".$this->Page['rollPage']."页</a>";
			$theFirst = "<a href='".url(PHP_SCRIPT."?firstRow=0&totalRows=".$this->Page['totalRows']."&".$this->Page['parameter'])."' title='第一页'>第一页</a>";
		}
		if($nowCoolPage == $this->Page['coolPages']){
			$nextPage = "";
			$theEnd="";
		}else{
			$nextRow = ($nowCoolPage*$this->Page['rollPage'])*$this->Page['listRows'];
			$theEndRow = ($this->Page['totalPages']-1)*$this->Page['listRows'];
			$nextPage = "<a href='".url(PHP_SCRIPT."?firstRow=$nextRow&totalRows=".$this->Page['totalRows']."&".$this->Page['parameter'])."' title='下".$this->Page['rollPage']."页'>下".$this->Page['rollPage']."页</a>";
			$theEnd = "<a href='".url(PHP_SCRIPT."?firstRow=$theEndRow&totalRows=".$this->Page['totalRows']."&".$this->Page['parameter'])."' title='最后一页'>最后一页</a>";
		}
		/* 1 2 3 4 5 */
		$linkPage = "";
		for($i=1;$i<=$this->Page['rollPage'];$i++){
			$page=($nowCoolPage-1)*$this->Page['rollPage']+$i;
			$rows=($page-1)*$this->Page['listRows'];
			if($page!=$this->Page['nowPage']){
				if($page<=$this->Page['totalPages']){
					$linkPage .= "&nbsp;<a href='".url(PHP_SCRIPT."?firstRow=$rows&totalRows=".$this->Page['totalRows']."&".$this->Page['parameter'])."'>&nbsp;".$page."&nbsp;</a>";
				}else{
					break;
				}
			}else{
				if($this->Page['totalPages'] != 1){
					$linkPage .= " ".$page."";
				}
			}
		}
		$pageStr = $upPage." ".$downPage." 共".$this->Page['totalPages']."页 ".$theFirst." ".$prePage." ".$linkPage." ".$nextPage." ".$theEnd; 
		$pageStr = autoCharSet($pageStr);
		return $pageStr;
	}

	/***
	 * 自动获取模板档案名
	 * CFG_TEMPLATE_CACHE_PATH	: 临时模板常量
	 * TEMPLATE_PATH		: 起始文件对应的模板路径，方便模板中调用其他模板
	 */
	function getTmplFile()
	{
		if ((!defined('CHAR_SET'))||(CHAR_SET != 'big5')&&(CHAR_SET != 'gb2312')) {
			halt("错误：不能识别网站语言。");
		}
		define('CFG_TEMPLATE_CACHE_PATH', CFG_TEMPLATE_CACHE_PREFIX);	/* 根据模板缓存目录字首和网站语言定义模板缓存目录 */
		$tmplModulePath					= substr(MODULE_PATH,strpos(MODULE_PATH,'/')).MODULE_NAME."/";
		$this->Cortrol['subTmplFile']	= FILE_PATH.CFG_TEMPLATE_PATH.$tmplModulePath.ACTION_NAME; 
		$this->Cortrol['tmplFile']		= FILE_PATH.CFG_TEMPLATE_PATH.$tmplModulePath.ACTION_NAME.".tpl.php"; 
		$this->Cortrol['tmplCacheFile'] = FILE_PATH.CFG_TEMPLATE_CACHE_PATH.$tmplModulePath.ACTION_NAME.".tpl.php"; 
		/* 定义模板路径，方便模板中调用其他模板 */
		define('TEMPLATE_PATH',FILE_PATH.CFG_TEMPLATE_CACHE_PATH.substr(MODULE_PATH,strpos(MODULE_PATH,'/')));	
		return;
	}
	
	/***
	 * 打开并切割子模板文件
	 */
	function getSubTmplFileContent($subTmplName, $mode = "sub")
	{
		if ($mode != "element") {
			$subTmplName	= $this->Cortrol['subTmplFile'].".".$subTmplName.".tpl.php";
		}
		if ( !file_exists($subTmplName) ) {
			return array("<!-- $subTmplName: No such file -->");
		}
		$subTmplContent = explode("<!--TMPL:Line-->",autoCharSet(fileRead($subTmplName)));
		$subTmplContent = $this->samepath($subTmplContent);	/* 同步子模板中的路径 */
		return $subTmplContent;
	}

	/***
	 * 替换模板中的变数为变数内容
	 */
	function tmplVarReplace(& $tmplContent)
	{
		/* 替换模板变数{$var} 为 $var 格式，方便替换变数值 */
		$tmplContent = preg_replace('/(\{\$)(.+?)(\})/is', '$\\2', $tmplContent);
		extract($this->Tmpl, EXTR_OVERWRITE);		/* 模板阵列变数分解成为独立变数 */

		preg_match_all('/(<\?=)(.+?)(\?>)/is', $tmplContent, $out);		/* */
		$j	= count($out[0]);
		$temp	= AddSlashes($tmplContent);
		for ($i = 0; $i < $j; $i++) {						/* */
			$temp	= preg_replace("/(<\?=)(.+?)(\?>)/is", $out[0][$i], $temp, 1);
			$temp	= preg_replace('/(<\?=)(.+?)(\?>)/is', '".'.'\\2'.'."', $temp, 1);	/* */
		}
		eval("\$temp = \"$temp\";");
		$temp	= StripSlashes($temp);
	//	$tmplContent = preg_replace('/(\{)(.+?)(\})/is', '\\2', $tmplContent);

		return $temp;
	}

	/***
	 * 获得一个模板元素
	 */
	function getElement($fileName)
	{
		$content = $this->getSubTmplFileContent($fileName, "element");
		$content = $this->tmplVarReplace($content[0]);

		return $content;
	}

	/***
	 * 检查模板是否变动，如果变动，要重新编译模板
	 */
	function checkCache(& $tmplCacheFile)
	{
		$tmplFile = str_replace(CFG_TEMPLATE_CACHE_PATH,CFG_TEMPLATE_PATH,$tmplCacheFile);
		/* 判断是否需要更新 */
		if (!file_exists($tmplCacheFile)) {			/* 缓存文件是否存在 */
			$tmplCahceFileDir	= substr($tmplCacheFile,0,strrpos($tmplCacheFile,"/"));
			$tmplCacheFileDirDown	= $tmplCahceFileDir;
			$tmplCacheFileDirUp	= substr($tmplCacheFileDirDown,0,strrpos($tmplCacheFileDirDown,"/"));
			if (!file_exists($tmplCacheFileDirUp)) {	/* 缓存文件目录是否存在 */
				mkdir($tmplCacheFileDirUp);
			}
			if (!file_exists($tmplCacheFileDirDown)) {
				mkdir($tmplCacheFileDirDown);
			}
			return True; 
		} elseif (filemtime($tmplFile) > filemtime($tmplCacheFile)) {	/* 源模板文件是否更新 */
			return True; 
		}
		return False;
	}

	/***
	 * 载入模板文件
	 */
	function readTmplate(& $tmplCacheFile) 
	{ 
		$tmplFile = str_replace(CFG_TEMPLATE_CACHE_PATH,CFG_TEMPLATE_PATH,$tmplCacheFile);
		$tmplContent = implode("", file($tmplFile));
		return $tmplContent;
	} 

	/***
	 * 替换变数,并且"编译"模板. 
	 */
	function writeCache(& $tmplCacheFile, & $tmplContent) 
	{ 
		$tmplContent = autoCharSet($this->compiler($tmplContent));	/* 编译模板文件 */

		if ( strlen($tmplContent) > 0) {
			fileWrite($tmplCacheFile,$tmplContent);
		}
		return;
	} 

	/***
	 * 编译模板文件. 
	 */
	function compiler (& $tmplContent)
	{

		/* 变数显示. 例：$tmpl[abc] -> <?=$tmpl[abc]? > */
		$tmplContent = preg_replace('/(\{\$)(.+?)(\})/is', '<'.'?=$\\2?'.'>', $tmplContent); 
		$tmplContent = preg_replace('/(charset=)(.+?)(\")/is', 'charset=UTF-8"', $tmplContent); 
		$tmplContent = str_replace(' *? *','?',$tmplContent);
		$tmplContent = $this->samepath($tmplContent);
		return $tmplContent;
	}

	/***
	 * 同步页面中的路径
	 */
	function samePath (&  $tmplContent)
	{
		$tmplContent = str_replace("../../../images",CFG_IMG_PATH,$tmplContent);	/* 转换模板中的图片路径 */
		$tmplContent = str_replace("../../../include","./include",$tmplContent);	/* 转换模板中的样式表路径 */
		return $tmplContent;
	}

	/***
	 * 载入模板文件. 
	 */
	function loadTmplate ($tmplCacheFile, $mode="sub")
	{
		/* 检查是否需要重新编译模板（1、编译模板不存在，2、原模板已更新） */
		if ($this->checkCache($tmplCacheFile)) {
			/* 根据编译模板档案名及路径，获得原模板档案名及路径，读出原模板内容 */
			$tmplContent = $this->readTmplate($tmplCacheFile);
			$this->writeCache($tmplCacheFile,$tmplContent);		/* 编译模板，并重写编译模板文件 */
		}
		extract($this->Tmpl, EXTR_OVERWRITE);		/* 模板阵列变数分解成为独立变数 */
		@extract($this->Tmpl, EXTR_OVERWRITE);		/* 模板陣列變數分解成爲獨立變數 */
		/*** */
		if ($mode == "element") {
			$element = fileRead($tmplCacheFile);
			return $element;
		}
		/*** */
		if ((defined('HTML_FILE'))&&("main" == $mode)) {
			ob_start();
		}
		include_once($tmplCacheFile);		/* 载入编译模板 */
		if ((defined('HTML_FILE'))&&("main" == $mode)) {
			$htmlContent = ob_get_contents();
			$this->writeHtml($htmlContent);	/* 写入HTML静态文件 */
		}
		return;
	}

	/***
	 * 写入HTML静态文件
	 */
	function writeHtml (& $htmlContent)
	{
		$dirArr = split("/",HTML_FILE);
		@array_pop($dirArr);
		$dir1 = implode("/",$dirArr);
		@array_pop($dirArr);
		$dir2 = implode("/",$dirArr);
		@array_pop($dirArr);
		$dir3 = implode("/",$dirArr);
		if (!file_exists($dir1)) {		/* 緩存文件目錄是否存在 */
			if (!file_exists($dir2)) {
				if (!file_exists($dir3)) {
					mkdir($dir3);
					mkdir($dir2);
					mkdir($dir1);
				} else {
					mkdir($dir2);
					mkdir($dir1);
				}
			} else {
				mkdir($dir1);
			}
		}
		fileWrite(HTML_FILE,$htmlContent);
		return;
	}

	/***
	 * 打开模板，替换变数元素，输出
	 */
	function display()
	{
		$this->loadTmplate($this->Cortrol['tmplCacheFile'], "main");	/* 載入編譯模板 */
		return;
	}
}
?>
