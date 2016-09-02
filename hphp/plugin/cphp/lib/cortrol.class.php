<?php
/***
 * ���ƣ�PHP OOP ����������������׼� Cortrol v0.06.03
 * ���ܣ��������࣬��������վ���ô������Ͽ��ʼ����ģ�崦����ҳ����������ʾ����Ѷ����ȹ���
 * 
 * $Id: cortrol.class.php 8 2009-10-20 10:05:34Z langr $
 */
class Cortrol
{
	/***
	 * �ⲿ���ԣ���ֵ���ⲿָ����
	 */
	var $Setup	= array();		/* ���ϿⱣ������ò��� */
	var $ErrMsg	= "";			/* ������Ѷ */
	var $PromptMsg	= "";			/* ��ʾ��Ѷ */
	var $UrlJump	= "";			/* ��ת��ַ */
	var $Tmpl	= array();		/* ģ��Ԫ�� */
	/***
	 * �ڲ����ԣ�����Ҫ�ⲿָ�����޸ġ�
	 * $Cortrol['tmplFile']		:	/* ģ�嵵������·�� /
	 * $Cortrol['tmplCacheFile']	:	/* ģ�建�浵������·�� /
	 * $Cortrol['subTmplFile']	:	/* ģ�嵵������·��������.tpl.php�������������ģ�� /
	 */
	var $Q		= "";			/* ���Ͽ�ʵ����� */
	var $Cortrol	= array();
	var $Page	= array();
	/***
	 * ��ҳ����
	 * $Page['firstRow']		:	/* ��ʼ�� /
	 * $Page['listRows']		:	/* �б����� /
	 * $Page['parameter']		:	/* ҳ����תʱҪ���Ĳ��� /
	 * $Page['totalPages']		:	/* ��ҳ�� /
	 * $Page['totalRows']		:	/* ������ /
	 * $Page['nowPage']		:	/* ��ǰҳ�� /
	 * $Page['coolPages']		:	/* ��ҳ��������ҳ�� /
	 * $Page['rollPage']		:	/* ��ҳ��ÿҳ��ʾ��ҳ�� /
	 */
	
	/***
	 * ���캯����������ʵ��ʱ�Զ�ִ�����в���
	 */
	function Cortrol()
	{
		$moduleAction = ACTION_PREFIX.ucfirst(ACTION_NAME); 

		if (!method_exists($this,$moduleAction)) {		/* ��� do.Action �Ƿ���� */
			halt('���󣺷Ƿ�������');
		}

		$this->getTmplFile();					/* �Զ���ȡģ��ĵ����� */
		$this->loadSetup();					/* ���� ./include/config/system.inc.php ϵͳ�����ļ� */

		/* �ж��Ƿ���� public ��������������ھ�ֱ��ִ�з�����������ڣ��� public ������ȥִ�� */
		if (!method_exists($this,"Index_Public")) {	
			$this->$moduleAction();
		}

		return;
	}

	/***
	 * �������Ͽ⣬�������и����Ƿ���Ҫ���Ͽ�ȥ���á�
	 */
	function loadDB()
	{
		if (!class_exists("MySql")) {
			include_once (FILE_PATH."include/db/mysql.class.php");	/* ����MySQL�� */
		}

		$this->Q = & New MySql();
		$this->Q->Host = CFG_DB_HOST;
		$this->Q->Database = CFG_DB_NAME;
		$this->Q->User = CFG_DB_USER;
		$this->Q->Password = CFG_DB_PWD;
		$this->Q->Company = CFG_DB_COMPANY;
		$this->Q->AdminMail = CFG_DB_ADMINMAIL;

		if ( !$this->Q->connect() ) {
			halt("�������Ͽ�����ʧ�ܣ�<br>Session halted.");
		}

		return $this->Q;
	}

	/***
	 * ����ֻ�����Ͽ⣬�������и����Ƿ���Ҫ���Ͽ�ȥ���á�
	 * ���ж����Ͽ�д����ʱ, �򷵻س���
	 */
	function loadRODB()
	{
		if (!class_exists("MySql")) {
			include_once (FILE_PATH."include/db/mysql.class.php");	/* ����MySQL�� */
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
				halt("�������Ͽ�����ʧ�ܣ�<br>Session halted.");
			}
		} else {
			$this->Q = $this->loadDB();
		}

		$this->Q->ReadOnly = true;

		return $this->Q;
	}
	
	/***
	 * ����ϵͳ���ò���
	 */
	function loadSetup(){
		if (file_exists(FILE_PATH."include/config/system.inc.php")) {
			include_once (FILE_PATH."include/config/system.inc.php");	/* ����ϵͳ�����ļ� */
			if (is_array($systemSetup)) {
				foreach  ($systemSetup as $key => $val) {
					if ("1" == $systemSetup[$key]['allowTmpl']) {
						$this->Tmpl[$key] = trueHtml($systemSetup[$key]['value']);
					} else {
						$this->Setup[$key] = $systemSetup[$key]['value'];
					}
				}
				if (isset($this->Setup['active']))	  $this->checkSetupActive();		/* �����վ�Ƿ�ر� */
				if (isset($this->Setup['loadLimit'])) $this->checkSetupLoadLimit();		/* ���ϵͳ���� */
				if (isset($this->Setup['denyIp']))	  $this->checkSetupDenyIp();		/* ����ֹIP��ַ */
			}
		}
		return;
	}

	/***
	 * ����ϵͳ���� active �����վ�Ƿ�ر�
	 */
	function checkSetupActive(){
		if ( ("true" == strtolower($this->Setup['active'])) || ("admin.php" == PHP_SCRIPT) 
				|| ("finance.php" == PHP_SCRIPT) || ("dataCount" == MODULE_NAME) ) {
			return;
		} else {
			if ( ("false" == strtolower($this->Setup['active'])) || ("0" == strtolower($this->Setup['active'])) ) {
				halt("ϵͳά���У����Ժ���ʡ�");
			} else {
				halt(iconv("UTF-8","GBK",$this->Setup['active']));
			}
		}
		return;
	}

	/***
	 * ����ϵͳ���� loadLimit ���ϵͳ�����Ƿ���ߣ����� Linux ϵͳ��
	 */
	function checkSetupLoadLimit(){
		if (($this->Setup['loadLimit'] > 0) && (PHP_OS == 'Linux') && (PHP_SCRIPT != "admin.php")) {
			if ( $fp = @fopen('/proc/loadavg', 'r') ) {
				$filestuff = @fread($fp, 6);
				fclose($fp);
				$loadavg = explode(' ', $filestuff);
				if ( trim($loadavg[0]) > $this->Setup['loadLimit'] ) {
					halt("ϵͳæ�����Ժ���ʡ�");
				}
			}
		}
		return;
	}

	/***
	 * ����ϵͳ���� denyIp ��鵱ǰ IP �Ƿ񱻽�ֹ
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
	 * ��ʾ������Ѷ
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
			$this->Tmpl['errorType'] = "��ȴ�ϵͳת��...<br> <br> (<a href='".$this->Tmpl['autoJumpUrl']."'>���������ȴ��������˴�����</a>)";
		} elseif ('close' == $type) {
			$this->Tmpl['autoJumpUrl']	= "javascript:window.close();";
			$this->Tmpl['waitSecond']	= "5";
			$this->Tmpl['errorType']	= "��ȴ����ڹر�<br> <br> (<a href='".$this->Tmpl['autoJumpUrl']."'>���������ȴ��������˴��ر�</a>)";
		} elseif ('close_game' == $type) {
			$this->Tmpl['autoJumpUrl']	= "app:close";
			$this->Tmpl['waitSecond']	= "5";
			$this->Tmpl['errorType']	= "��ȴ����ڹر�<br> <br> (<a href='".$this->Tmpl['autoJumpUrl']."'>���������ȴ��������˴��ر�</a>)";
		}
		

		$this->Tmpl['message'] = autoCharSet($this->Tmpl['message']);
		$this->Tmpl['errorType'] = autoCharSet($this->Tmpl['errorType']);

		$this->loadTmplate(TEMPLATE_PATH."public/prompt.tpl.php");	/* ������Ѷ��ʾģ�� */
		exit;
	}

	/***
	 * ��ҳ
	 * ����ʹ�� page.class.php ���
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
		$this->Page['totalPages'] = ceil($this->Page['totalRows']/$this->Page['listRows']); 	/*��ҳ�� */
		$this->Page['coolPages'] = ceil($this->Page['totalPages']/$this->Page['rollPage']);
		$this->Page['nowPage']	= floor($this->Page['firstRow']/$this->Page['listRows']+1);	/*��ǰҳ�� */
		$nowCoolPage		= ceil($this->Page['nowPage']/$this->Page['rollPage']);

		/*���·���ִ� */
		$upRow	= $this->Page['firstRow']-$this->Page['listRows'];
		$downRow = $this->Page['firstRow']+$this->Page['listRows'];
		if ($upRow>=0){
			$upPage="<a href='".url(PHP_SCRIPT."?firstRow=$upRow&totalRows=".$this->Page['totalRows']."&".$this->Page['parameter'])."'>��һҳ</a>";
		}else{
			$upPage="";
		}
		if ($downRow<$this->Page['totalRows']){
			$downPage="<a href='".url(PHP_SCRIPT."?firstRow=$downRow&totalRows=".$this->Page['totalRows']."&".$this->Page['parameter'])."'>��һҳ</a>";
		}else{
			$downPage="";
		}
		/* << < > >> */
		if($nowCoolPage == 1){
			$theFirst = "";
			$prePage = "";
		}else{
			$preRow =  ($this->Page['rollPage']*($nowCoolPage-1)-1)*$this->Page['listRows'];
			$prePage = "<a href='".url(PHP_SCRIPT."?firstRow=$preRow&totalRows=".$this->Page['totalRows']."&".$this->Page['parameter'])."' title='��".$this->Page['rollPage']."ҳ'>��".$this->Page['rollPage']."ҳ</a>";
			$theFirst = "<a href='".url(PHP_SCRIPT."?firstRow=0&totalRows=".$this->Page['totalRows']."&".$this->Page['parameter'])."' title='��һҳ'>��һҳ</a>";
		}
		if($nowCoolPage == $this->Page['coolPages']){
			$nextPage = "";
			$theEnd="";
		}else{
			$nextRow = ($nowCoolPage*$this->Page['rollPage'])*$this->Page['listRows'];
			$theEndRow = ($this->Page['totalPages']-1)*$this->Page['listRows'];
			$nextPage = "<a href='".url(PHP_SCRIPT."?firstRow=$nextRow&totalRows=".$this->Page['totalRows']."&".$this->Page['parameter'])."' title='��".$this->Page['rollPage']."ҳ'>��".$this->Page['rollPage']."ҳ</a>";
			$theEnd = "<a href='".url(PHP_SCRIPT."?firstRow=$theEndRow&totalRows=".$this->Page['totalRows']."&".$this->Page['parameter'])."' title='���һҳ'>���һҳ</a>";
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
		$pageStr = $upPage." ".$downPage." ��".$this->Page['totalPages']."ҳ ".$theFirst." ".$prePage." ".$linkPage." ".$nextPage." ".$theEnd; 
		$pageStr = autoCharSet($pageStr);
		return $pageStr;
	}

	/***
	 * �Զ���ȡģ�嵵����
	 * CFG_TEMPLATE_CACHE_PATH	: ��ʱģ�峣��
	 * TEMPLATE_PATH		: ��ʼ�ļ���Ӧ��ģ��·��������ģ���е�������ģ��
	 */
	function getTmplFile()
	{
		if ((!defined('CHAR_SET'))||(CHAR_SET != 'big5')&&(CHAR_SET != 'gb2312')) {
			halt("���󣺲���ʶ����վ���ԡ�");
		}
		define('CFG_TEMPLATE_CACHE_PATH', CFG_TEMPLATE_CACHE_PREFIX);	/* ����ģ�建��Ŀ¼���׺���վ���Զ���ģ�建��Ŀ¼ */
		$tmplModulePath					= substr(MODULE_PATH,strpos(MODULE_PATH,'/')).MODULE_NAME."/";
		$this->Cortrol['subTmplFile']	= FILE_PATH.CFG_TEMPLATE_PATH.$tmplModulePath.ACTION_NAME; 
		$this->Cortrol['tmplFile']		= FILE_PATH.CFG_TEMPLATE_PATH.$tmplModulePath.ACTION_NAME.".tpl.php"; 
		$this->Cortrol['tmplCacheFile'] = FILE_PATH.CFG_TEMPLATE_CACHE_PATH.$tmplModulePath.ACTION_NAME.".tpl.php"; 
		/* ����ģ��·��������ģ���е�������ģ�� */
		define('TEMPLATE_PATH',FILE_PATH.CFG_TEMPLATE_CACHE_PATH.substr(MODULE_PATH,strpos(MODULE_PATH,'/')));	
		return;
	}
	
	/***
	 * �򿪲��и���ģ���ļ�
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
		$subTmplContent = $this->samepath($subTmplContent);	/* ͬ����ģ���е�·�� */
		return $subTmplContent;
	}

	/***
	 * �滻ģ���еı���Ϊ��������
	 */
	function tmplVarReplace(& $tmplContent)
	{
		/* �滻ģ�����{$var} Ϊ $var ��ʽ�������滻����ֵ */
		$tmplContent = preg_replace('/(\{\$)(.+?)(\})/is', '$\\2', $tmplContent);
		extract($this->Tmpl, EXTR_OVERWRITE);		/* ģ�����б����ֽ��Ϊ�������� */

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
	 * ���һ��ģ��Ԫ��
	 */
	function getElement($fileName)
	{
		$content = $this->getSubTmplFileContent($fileName, "element");
		$content = $this->tmplVarReplace($content[0]);

		return $content;
	}

	/***
	 * ���ģ���Ƿ�䶯������䶯��Ҫ���±���ģ��
	 */
	function checkCache(& $tmplCacheFile)
	{
		$tmplFile = str_replace(CFG_TEMPLATE_CACHE_PATH,CFG_TEMPLATE_PATH,$tmplCacheFile);
		/* �ж��Ƿ���Ҫ���� */
		if (!file_exists($tmplCacheFile)) {			/* �����ļ��Ƿ���� */
			$tmplCahceFileDir	= substr($tmplCacheFile,0,strrpos($tmplCacheFile,"/"));
			$tmplCacheFileDirDown	= $tmplCahceFileDir;
			$tmplCacheFileDirUp	= substr($tmplCacheFileDirDown,0,strrpos($tmplCacheFileDirDown,"/"));
			if (!file_exists($tmplCacheFileDirUp)) {	/* �����ļ�Ŀ¼�Ƿ���� */
				mkdir($tmplCacheFileDirUp);
			}
			if (!file_exists($tmplCacheFileDirDown)) {
				mkdir($tmplCacheFileDirDown);
			}
			return True; 
		} elseif (filemtime($tmplFile) > filemtime($tmplCacheFile)) {	/* Դģ���ļ��Ƿ���� */
			return True; 
		}
		return False;
	}

	/***
	 * ����ģ���ļ�
	 */
	function readTmplate(& $tmplCacheFile) 
	{ 
		$tmplFile = str_replace(CFG_TEMPLATE_CACHE_PATH,CFG_TEMPLATE_PATH,$tmplCacheFile);
		$tmplContent = implode("", file($tmplFile));
		return $tmplContent;
	} 

	/***
	 * �滻����,����"����"ģ��. 
	 */
	function writeCache(& $tmplCacheFile, & $tmplContent) 
	{ 
		$tmplContent = autoCharSet($this->compiler($tmplContent));	/* ����ģ���ļ� */

		if ( strlen($tmplContent) > 0) {
			fileWrite($tmplCacheFile,$tmplContent);
		}
		return;
	} 

	/***
	 * ����ģ���ļ�. 
	 */
	function compiler (& $tmplContent)
	{

		/* ������ʾ. ����$tmpl[abc] -> <?=$tmpl[abc]? > */
		$tmplContent = preg_replace('/(\{\$)(.+?)(\})/is', '<'.'?=$\\2?'.'>', $tmplContent); 
		$tmplContent = preg_replace('/(charset=)(.+?)(\")/is', 'charset=UTF-8"', $tmplContent); 
		$tmplContent = str_replace(' *? *','?',$tmplContent);
		$tmplContent = $this->samepath($tmplContent);
		return $tmplContent;
	}

	/***
	 * ͬ��ҳ���е�·��
	 */
	function samePath (&  $tmplContent)
	{
		$tmplContent = str_replace("../../../images",CFG_IMG_PATH,$tmplContent);	/* ת��ģ���е�ͼƬ·�� */
		$tmplContent = str_replace("../../../include","./include",$tmplContent);	/* ת��ģ���е���ʽ��·�� */
		return $tmplContent;
	}

	/***
	 * ����ģ���ļ�. 
	 */
	function loadTmplate ($tmplCacheFile, $mode="sub")
	{
		/* ����Ƿ���Ҫ���±���ģ�壨1������ģ�岻���ڣ�2��ԭģ���Ѹ��£� */
		if ($this->checkCache($tmplCacheFile)) {
			/* ���ݱ���ģ�嵵������·�������ԭģ�嵵������·��������ԭģ������ */
			$tmplContent = $this->readTmplate($tmplCacheFile);
			$this->writeCache($tmplCacheFile,$tmplContent);		/* ����ģ�壬����д����ģ���ļ� */
		}
		extract($this->Tmpl, EXTR_OVERWRITE);		/* ģ�����б����ֽ��Ϊ�������� */
		@extract($this->Tmpl, EXTR_OVERWRITE);		/* ģ�����׃���ֽ�ɠ�����׃�� */
		/*** */
		if ($mode == "element") {
			$element = fileRead($tmplCacheFile);
			return $element;
		}
		/*** */
		if ((defined('HTML_FILE'))&&("main" == $mode)) {
			ob_start();
		}
		include_once($tmplCacheFile);		/* �������ģ�� */
		if ((defined('HTML_FILE'))&&("main" == $mode)) {
			$htmlContent = ob_get_contents();
			$this->writeHtml($htmlContent);	/* д��HTML��̬�ļ� */
		}
		return;
	}

	/***
	 * д��HTML��̬�ļ�
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
		if (!file_exists($dir1)) {		/* �����ļ�Ŀ��Ƿ���� */
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
	 * ��ģ�壬�滻����Ԫ�أ����
	 */
	function display()
	{
		$this->loadTmplate($this->Cortrol['tmplCacheFile'], "main");	/* �d�뾎�gģ�� */
		return;
	}
}
?>
