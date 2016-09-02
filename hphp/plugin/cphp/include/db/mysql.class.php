<?PHP
/***
 * 名    称: PHP OOP 面向物件开发工具套件 MySql v0.06.03
 * 功    能: 资料库操作类
 * 
 * $Id: mysql.class.php 8 2009-10-20 10:05:34Z langr $
 */

Class MySql  {

	/***
	 * 公共属性: 伺服器参数 这裡是默认设置，具体请在base.setup.php中设置
	 */
	Var $Host     = 'localhost';		/* database system host */
	Var $Database = 'dbName';		/* database */
	Var $User     = 'userName';		/* database user */
	Var $Password = 'password';		/* database password */

	/***
	 * 公共属性: 设置参数
	 */
	Var $AutoFree    = True;		/* True: 自动释放 */
	Var $Debug       = False;		/* True: 显示调试资讯 */
	Var $HaltOnError = 'yes';		/* "yes"   : 显示错误，中断执行 */
						/* "no"    : 忽略错误，继续执行 */
						/* "report": 显示错误，继续执行 */
	Var $ReportError = False;		/* True: 报告详细错误寄信并给管理员。 */
	Var $PconnectOn  = True;		/* True: 使用 pconnect ，否则使用 connect */
	Var $ReadOnly	 = False;		/* True: 资料库只读, 当遇到试图写资料库动作时返回出错 */

	/***
	 * 公共属性: 查询结果阵列 和 当前行数
	 */
	Var $record = Array();
	Var $Row;
	Var $QueryStr = '';

	/***
	 * 公共属性: 错误号码 和 错误资讯
	 */
	Var $Errno = 0;
	Var $Error = '';

	/***
	 * 公共属性: 本资料库操作类的 资料资讯
	 */
	Var $Type     = 'MySQL';
	Var $Revision = '1.41';
	Var $Company  = 'www.betcity.com.tw';
	Var $AdminMail= 'service@betcity.com.tw';

	/***
	 * 私有属性: 连接ID 查询ID
	 */
	Var $LinkID  = 0;
	Var $QueryID = 0;

	/***
	 * 公共方法: 构造器
	 */
	Function MySql() {
	//	if (method_exists($this, '__destruct')){
	//		register_shutdown_function(array(&$this, '__destruct'));
      	//	}
	}

	function __destruct() {
		$this->close();
	}

	/***
	 * 公共方法: 一些琐碎的报告
	 */
	Function getLinkID() {
		Return $this->LinkID;
	}

	Function getQueryID() {
		Return $this->QueryID;
	}

	/***
	 * 公共方法: 连接资料库
	 */
	Function connect() {
		/* 建立连接，选择资料库 */
		if ( $this->LinkID == 0 ) {
			/* 建立连接 */
			if ( $this->PconnectOn ) {
				$this->LinkID = @mysql_pconnect($this->Host, $this->User, $this->Password);
			} else {
				$this->LinkID = @mysql_connect($this->Host, $this->User, $this->Password);
			}
			/* 连接错误 */
			if ( $this->LinkID == 0 ) {
				if ( $this->Debug ) {
					$msg = "connect('$this->Host', '$this->User', '$this->Password') 数据库连接失败！";
				} else {
					$msg = '暂时无法连接数据库！';
				}
				$this->halt($msg);
				Return False;
			}
			/* 选择资料库时错误 */
			if ( !@mysql_select_db($this->Database, $this->LinkID) ) {
				$this->halt("无法打开资料库'".$this->Database."'！");
				Return False;
			}
			/*  */
			if (defined('CFG_DB_CHAR')) {
				@mysql_query("set names ".CFG_DB_CHAR, $this->LinkID);
			}
		}
		if ( !is_resource($this->LinkID) ) {
			halt("<b>Database error:</b> mysql resource error<br />");
			return false;
		}

		Return $this->LinkID;
	}

	/***
	 * 公共方法: 释放查询结果
	 */
	Function free() {
		@mysql_free_result($this->QueryID);
		$this->QueryID = 0;
	}

	/***
	 * 公共方法: 关闭资料库连接
	 */
	function close() {
		$this->free();
		if(!$this->PconnectOn) {
			if($this->LinkID != 0) {
				@mysql_close($this->LinkID);
				$this->LinkID = 0;
			}
		}
	}

	/***
	 * 公共方法: 执行查询
	 */
	Function query($str) {
		if ( $str == '' ) Return False;

		if ( !$this->connect() ) Return False;

		/* 新查询，释放前次的查询结果 */
		if ( $this->QueryID ) {
			$this->free();
		}
		
		$str = varResume($str);			/* 恢复被过滤的变数,还原真实的值 */
		
	//	$str = $this->autoDBLanguage($str);	/* 自动对 SQL 语言格式进行转换 */
		$str = $this->autoDBChar($str);		/* 自动对 SQL 语言格式进行转换 */
		if ( $this->ReadOnly ) {		/* 只读查询 */
			if ( $this->checkStr($str) ) {
			//	$this->halt("<b>Database error:</b> DataBase read-only: ".autoCharSet($str, false));
				halt("<b>Database error:</b> DataBase read-only: ".autoCharSet($str, false)."<br />系统资料库发生错误，当前操作已被中止。");
				Return False;
			}
		}
		$this->QueryStr = $str;
		
		$debugMsg = "调试: 语句";
		$debugMsg = autoCharSet($debugMsg);
		if ( $this->Debug ) printf($debugMsg." = %s<br>\n", $this->QueryStr);

		$this->QueryID = @mysql_query($this->QueryStr, $this->LinkID);
		$this->Row   = 0;
		if ( !$this->QueryID ) {
			if (("gb2312" == CHAR_SET) && ( "big5" == strtolower(CFG_DB_CHAR))){
				$this->QueryStr = utf8ToBig5ToGb($this->QueryStr);
				$this->QueryStr = gbToUtf8($this->QueryStr);
			}
			if (("big5" == CHAR_SET) && ( "gb2312" == strtolower(CFG_DB_CHAR))){
				$this->QueryStr = utf8ToGbToBig5($this->QueryStr);
				$this->QueryStr = big5ToUtf8($this->QueryStr);
			}

			$this->QueryStr = autoCharSet($this->QueryStr, False);
			$this->halt("错误查询:".$this->QueryStr);
			Return False;
		} else {
			Return $this->QueryID;
		}
	}

	/***
	 * 公共方法: 获得查询结果
	 */
	Function nextRecord() {
		if ( !$this->QueryID ) {
			$this->halt('执行错误：查询无效！');
			Return False;
		}

		$this->record = @mysql_fetch_array($this->QueryID);
	  		if ("" != $this->record){
			foreach($this->record as $key => $val) { 
			//	$this->record[$key] = $this->autoDBLanguage($val,False);	/* 自动对 资料 语言格式进行转换 */
				$this->record[$key] = $this->autoDBChar($val, 'out');		/* 自动对 资料 语言格式进行转换 */
			}
			$this->record = varFilter($this->record);				/* 取出的值进行安全过滤 */
		}

		$this->Row += 1;
		
		$stat = is_array($this->record);

		if ( !$stat && $this->AutoFree ) {
			$this->free();
		}
		Return $stat;
	}

	/***
	 * 公共方法: 获得插入的ID
	 */
	Function insertId(){
		if( $result = mysql_insert_id($this->LinkID) ) {
			Return $result;
		} else {
			Return False;
		}
	}

	/***
	 * 公共方法: 选择数据库
	 */
	Function selectDB($db /*= $this->Database*/) {
		if( $result = mysql_select_db($db, $this->LinkID) ) {
			Return $result;
		} else {
			Return False;
		}
	}

	/***
	 * 公共方法: 缩略方法
	 */
	Function insert($table, $field, $value) {
		$str = "insert into ".$table;
		if ( $field != "" ) $str .= "(".$field.")";
		$str .= " values(".$value.")";
		if ( $this->query($str) ) {
			Return True;
		} else {
			Return False;
		}
	}

	Function replace($table, $field, $value) {
		$str = "replace into ".$table;
		if ( $field != "" ) $str .= "(".$field.")";
		$str .= " values(".$value.")";
		if ( $this->query($str) ) {
			Return True;
		} else {
			Return False;
		}
	}

	Function select($table, $field="*", $condition="", $order="", $limit="") {
		$str = "select ".$field." from ".$table;
		if ( $condition != "" ) $str.=" where ".$condition;
		if ( $order != "" ) $str.=" order by ".$order;
		if ( $limit != "" ) $str.=" limit ".$limit;
		if ( $this->query($str) ) {
			Return True;
		} else {
			Return False;
		}
	}

	Function update($table, $value, $condition="") {
		$str = "update ".$table." set ".$value;
		if ( $condition != "" ) $str.=" where ".$condition;
		if ( $this->query($str) ) {
			Return True;
		} else {
			Return False;
		}
	}

	Function delete($table, $condition="") {
		$str = "delete from ".$table;
		if ( $condition != "" ) $str.=" where ".$condition;
		if ( $this->query($str) ) {
			Return True;
		} else {
			Return False;
		}
	}

	/**
	 * 查询并立即返回一条记录结果
	 *
	 * @param string 查询语句
	 * @return bool True 查询并返回结果成功，False 查询并返回结果失败
	 */
	Function selectOne($str) {
		if ( $this->query($str) ) {
			if ( $this->nextRecord() ) {
				Return $this->record;
			} else {
				Return False;
			}
		} else {
			Return False;
		}
	}

	/**
	 * 查询并立即返回最大 limitLine 数量的记录，为防止结果集过大，limit 的值由独立参数指定，sql 语句中请勿带 limit 子句，默认30笔
	 *
	 * @param string 查询语句
	 * @param int limit 起始位置
	 * @param int 结果集笔数
	 * 
	 * @return array 或 bool 查询成功以数组格式返回结果，False 查询失败
	 */
	Function selectAll($str, $limitStart = 0, $limitLine = 30) {
		$str .= ' LIMIT '.$limitStart.', '.$limitLine;
		if ( $this->query($str) ) {
			$resultArr = array();
			while ( $this->nextRecord() ) {
				$resultArr[] = $this->record;
			}
			if (count($resultArr)>0){
				return $resultArr;
			} else {
				Return False;
			}
		} else {
			Return False;
		}
	}

	Function count($table, $field="*", $condition="") {
		if ( $condition != "" ) $strC=" where ".$condition;
		$str = "select count(".$field.") from ".$table.$strC;
		$this->query($str);
		$this->nextRecord();
		Return $this->record[0];
	}

	Function nextData() {
		if ( $this->nextRecord() ) {
			Return $this->record;
		}else{
			Return False;
		}
	}

	/***
	 * 公共方法: 获得SQL语句执行后受影响的行数
	 */
	Function affectedRows() {
		Return @mysql_affected_rows($this->LinkID);
	}

	Function numRows() {
		Return @mysql_num_rows($this->QueryID);
	}

	Function numFields() {
		Return @mysql_num_fields($this->QueryID);
	}

	/***
	 * 公共方法: 缩略方法
	 */
	Function nr() {
		Return $this->numRows();
	}

	Function np() {
		print $this->numRows();
	}

	Function r($name) {
		if ( isset($this->record[$name]) ) {
			Return $this->record[$name];
		}
	}

	Function p($name) {
		if ( isset($this->record[$name]) ) {
			print $this->record[$name];
		}
	}

	/***
	 * 公共方法: 查找表
	 */
	Function tableNames() {
		$this->connect();
		$h = @mysql_query("show tables", $this->LinkID);
		if ( $this->Debug ) printf("调试: 语句 = %s<br>\n", "'show tables'");
		$i = 0;
		while ( $info = @mysql_fetch_row($h) ) {
			$return[$i]["table_name"]      = $info[0];
			$return[$i]["tablespace_name"] = $this->Database;
			$return[$i]["database"]        = $this->Database;
			$i++;
		}
		@mysql_free_result($h);
		Return $return;
	}

	/***
	 * 公共方法: 错误处理
	 */
	Function halt($msg) {
		$this->Error = @mysql_error($this->LinkID);
		$this->Errno = @mysql_errno($this->LinkID);
		if ( $this->HaltOnError == 'no' ) Return;

		/*$_err = array();
		$_err['errno'] = $this->Errno = @mysql_errno($this->LinkID);
		$_err['error'] = $this->Error = @mysql_error($this->LinkID);
		$_err['msg'] = $msg;
		if ( $this->HaltOnError != 'report' ) {
			//header("Location:/error.html");
			echo posttohost("/error.php", $_err);
			exit;
		}*/

		$this->haltmsg($msg);
		if ( $this->HaltOnError != 'report' ) halt(' 系统资料库发生错误，当前操作已被中止。');
	}

	Function haltmsg($msg) {
		if ( $this->ReportError ) {
			$mailTitle = " 资料库出现错误！";
			$mailMessage = "在 ".$this->Company." 上的资料库发生错误: $msg\n";
			$mailMessage.= "MySQL 报告的错误是(MySQL return error message): ".$this->Error."\n";
			$mailMessage.= "MySQL 返回的错误号码是(Error number): ".$this->Errno."\n";
			$mailMessage.="出错时间(date): ".date("Y-m-d l H:i:s")."\n";
			$mailMessage.="出错的地址(url): http://".getenv("HTTP_HOST").getenv("REQUEST_URI")."\n";
			$mailMessage.="前一个地址是(referer url): ".getenv("HTTP_REFERER")."\n";

			$mailTitle   = autoGbBig5($mailTitle);		/* 自动简繁转换，不涉及 UTF8 */
			$mailMessage = autoGbBig5($mailMessage);	/* 自动简繁转换，不涉及 UTF8 */
			@mail ($this->AdminMail, $this->Company."-".getenv("HTTP_HOST").$mailTitle,$mailMessage);

			$message = "\n<pre>$message </pre>\n";
			$message .="</td></tr></table>\n<p>资料库大概发生了轻微的错误，\n";
			$message .="请稍候再刷新尝试一下。</p>";
			$message .= "本系统已经将此错误通过E-Mail发送给了 ".$this->Company." 的<a href=\"mailto:".$this->AdminMail."\">技术人员</a>， 如果问题依然, 您也可以直接联繫她。</p>";
			$message .= "<p>我们为本次错误深表歉意，同时感谢您的支援！</p>";
			$message .= "<hr><p><b>There seems to have been a slight problem with the ".$this->Company." database.</b><br>\n";
			$message .= "Please try again by pressing the <a href=\"javascript:window.location=window.location;\">refresh</a> button in your browser.</p>";
			$message .= "An E-Mail has been dispatched to our <a href=\"mailto:".$this->AdminMail."\">Technical Staff</a>, who you can also contact if the problem persists.</p>";
		} else {
			$message  = "</td></tr></table><b>Database error:</b> ".$msg."<br>\n";
			$message .= "<b>MySQL Error</b>: ".$this->Errno." (".$this->Error.")<br>\n";
		}
		echo autoCharSet($message);
	}

	/***
	 * 公共方法: DB 语言格式转换
	 */
	Function autoDBLanguage(& $str,$do = True) {
		if (defined('CFG_CHAR_SET')){
			return $str;
		}
		if ($do) {
			if (("gb2312" == CHAR_SET) && ( "big5" == strtolower(CFG_DB_LANGUAGE))){
				$str = utf8ToGbToBig5($str);
				$str = big5ToUtf8($str);
			}
			if (("big5" == CHAR_SET) && ( "gb2312" == strtolower(CFG_DB_LANGUAGE))){
				$str = utf8ToBig5ToGb($str);
				$str = gbToUtf8($str);
			}
		} else {
			if (("gb2312" == CHAR_SET) && ( "big5" == strtolower(CFG_DB_LANGUAGE))){
				$str = utf8ToBig5ToGb($str);
				$str = gbToUtf8($str);
			}
			if (("big5" == CHAR_SET) && ( "gb2312" == strtolower(CFG_DB_LANGUAGE))){
				$str = utf8ToGbToBig5($str);
				$str = big5ToUtf8($str);
			}
		}
		return $str;
	}

	/***
	 * 自动转换数据库资料字符集为网站 UTF8 编码
	 * 或转换网站 UTF8 编码为数据库字符集
	 */
	function autoDBChar(& $str, $do = 'in')
	{
		if (!defined('CFG_DB_CHAR') || strtolower(CFG_DB_CHAR) == "utf-8" || strtolower(CFG_DB_CHAR) == "utf8") {
			return $str;
		}

		if ($do == 'in') {
			$str	= iconv('UTF-8', CFG_DB_CHAR, $str);
		} elseif ($do == 'out') {
			$str	= iconv(CFG_DB_CHAR, 'UTF-8', $str);
		}

		return $str;
	}

	/***
	 * 检测 $str 语句是否含有写资料库相关的 sql
	 * 有则返回 true
	 */
	function checkStr($str)
	{
		$s	= trim($str);
		$sub_str = substr(strtolower($s), 0, 10);
		$deny_str = array("insert", "update", "delete", "create");	/* 不允许执行的 sql 关键字 */
		$allow_str = array("select");					/* 被允许的 sql 关键字 */
		$c	= count($allow_str);
		for ( $i = 0; $i < $c; $i++ ) {
			$f	= strpos($sub_str, $allow_str[$i]);

			if ( $f === false ) {
				return true;
			}
		}

		return false;
	}
}
?>
