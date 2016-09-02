<?PHP
/***
 * 名    稱: PHP OOP 面向物件開發工具套件 MySql v0.06.03
 * 功    能: 資料庫操作類
 * 
 * $Id: mysql.class.php 6 2007-10-28 03:40:44Z langr $
 */

Class MySql  {

	/***
	 * 公共屬性: 伺服器參數 這裡是默認設置，具體請在base.setup.php中設置
	 */
	Var $Host     = 'localhost';		/* database system host */
	Var $Database = 'dbName';		/* database */
	Var $User     = 'userName';		/* database user */
	Var $Password = 'password';		/* database password */

	/***
	 * 公共屬性: 設置參數
	 */
	Var $AutoFree    = True;		/* True: 自動釋放 */
	Var $Debug       = False;		/* True: 顯示調試資訊 */
	Var $HaltOnError = 'yes';		/* "yes"   : 顯示錯誤，中斷執行 */
						/* "no"    : 忽略錯誤，繼續執行 */
						/* "report": 顯示錯誤，繼續執行 */
	Var $ReportError = False;		/* True: 報告詳細錯誤寄信並給管理員。 */
	Var $PconnectOn  = True;		/* True: 使用 pconnect ，否則使用 connect */

	/***
	 * 公共屬性: 查詢結果陣列 和 當前行數
	 */
	Var $record = Array();
	Var $Row;
	Var $QueryStr = '';

	/***
	 * 公共屬性: 錯誤號碼 和 錯誤資訊
	 */
	Var $Errno = 0;
	Var $Error = '';

	/***
	 * 公共屬性: 本資料庫操作類的 資料資訊
	 */
	Var $Type     = 'MySQL';
	Var $Revision = '1.41';
	Var $Company  = 'www.betcity.com.tw';
	Var $AdminMail= 'service@betcity.com.tw';

	/***
	 * 私有屬性: 連接ID 查詢ID
	 */
	Var $LinkID  = 0;
	Var $QueryID = 0;

	/***
	 * 公共方法: 構造器
	 */
	Function MySql() {
	}

	/***
	 * 公共方法: 一些瑣碎的報告
	 */
	Function getLinkID() {
		Return $this->LinkID;
	}

	Function getQueryID() {
		Return $this->QueryID;
	}

	/***
	 * 公共方法: 連接資料庫
	 */
	Function connect() {
		/* 建立連接，選擇資料庫 */
		if ( $this->LinkID == 0 ) {
			/* 建立連接 */
			if ( $this->PconnectOn ) {
				$this->LinkID = @mysql_pconnect($this->Host, $this->User, $this->Password);
			} else {
				$this->LinkID = @mysql_connect($this->Host, $this->User, $this->Password);
			}
			/* 連接錯誤 */
			if ( $this->LinkID == 0 ) {
				if ( $this->Debug ) {
					$msg = "connect('$this->Host', '$this->User', '$this->Password') 數據庫連接失敗！";
				} else {
					$msg = '暫時無法連接數據庫！';
				}
				$this->halt($msg);
				Return False;
			}
			/* 選擇資料庫時錯誤 */
			if ( !@mysql_select_db($this->Database, $this->LinkID) ) {
				$this->halt("無法打開資料庫'".$this->Database."'！");
				Return False;
			}
			/*  */
			if (defined('CFG_DB_CHAR')) {
				mysql_query("set names ".CFG_DB_CHAR, $this->LinkID);
			}
		}

		Return $this->LinkID;
	}

	/***
	 * 公共方法: 釋放查詢結果
	 */
	Function free() {
		@mysql_free_result($this->QueryID);
		$this->QueryID = 0;
	}

	/***
	 * 公共方法: 執行查詢
	 */
	Function query($str) {
		if ( $str == '' ) Return False;

		if ( !$this->connect() ) Return False;

		/* 新查詢，釋放前次的查詢結果 */
		if ( $this->QueryID ) {
			$this->free();
		}
		
		$str = varResume($str);			/* 恢復被過濾的變數,還原真實的值 */
		
	//	$str = $this->autoDBLanguage($str);	/* 自動對 SQL 語言格式進行轉換 */
		$str = $this->autoDBChar($str);		/* 自動對 SQL 語言格式進行轉換 */
		$this->QueryStr = $str;
		
		$debugMsg = "調試: 語句";
		$debugMsg = autoCharSet($debugMsg);
		if ( $this->Debug ) printf($debugMsg." = %s<br>\n", $this->QueryStr);

		$this->QueryID = @mysql_query($this->QueryStr, $this->LinkID);
		$this->Row   = 0;
		if ( !$this->QueryID ) {
			if (("gb2312" == CHAR_SET) && ( "big5" == strtolower(CFG_DB_LANGUAGE))){
				$this->QueryStr = utf8ToBig5ToGb($this->QueryStr);
				$this->QueryStr = gbToUtf8($this->QueryStr);
			}
			if (("big5" == CHAR_SET) && ( "gb2312" == strtolower(CFG_DB_LANGUAGE))){
				$this->QueryStr = utf8ToGbToBig5($this->QueryStr);
				$this->QueryStr = big5ToUtf8($this->QueryStr);
			}

			$this->QueryStr = autoCharSet($this->QueryStr,False);
			$this->halt("錯誤查詢:".$this->QueryStr);
			Return False;
		} else {
			Return $this->QueryID;
		}
	}

	/***
	 * 公共方法: 獲得查詢結果
	 */
	Function nextRecord() {
		if ( !$this->QueryID ) {
			$this->halt('執行錯誤：查詢無效！');
			Return False;
		}

		$this->record = @mysql_fetch_array($this->QueryID);
	  		if ("" != $this->record){
			foreach($this->record as $key => $val) { 
			//	$this->record[$key] = $this->autoDBLanguage($val,False);	/* 自動對 資料 語言格式進行轉換 */
				$this->record[$key] = $this->autoDBChar($val, 'out');		/* 自動對 資料 語言格式進行轉換 */
			}
			$this->record = varFilter($this->record);				/* 取出的值進行安全過濾 */
		}

		$this->Row += 1;
		
		$stat = is_array($this->record);

		if ( !$stat && $this->AutoFree ) {
			$this->free();
		}
		Return $stat;
	}

	/***
	 * 公共方法: 獲得插入的ID
	 */
	Function insertId(){
		if( $result = mysql_insert_id($this->LinkID) ) {
			Return $result;
		} else {
			Return False;
		}
	}

	/***
	 * 公共方法: 選擇數據庫
	 */
	Function selectDB($db /*= $this->Database*/) {
		if( $result = mysql_select_db($db, $this->LinkID) ) {
			Return $result;
		} else {
			Return False;
		}
	}

	/***
	 * 公共方法: 縮略方法
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
	 * 公共方法: 獲得SQL語句執行後受影響的行數
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
	 * 公共方法: 縮略方法
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
		if ( $this->Debug ) printf("調試: 語句 = %s<br>\n", "'show tables'");
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
	 * 公共方法: 錯誤處理
	 */
	Function halt($msg) {
		$this->Error = @mysql_error($this->LinkID);
		$this->Errno = @mysql_errno($this->LinkID);
		if ( $this->HaltOnError == 'no' ) Return;

		$this->haltmsg($msg);
		if ( $this->HaltOnError != 'report' ) halt(' 系統資料庫發生錯誤，當前操作已被中止。');
	}

	Function haltmsg($msg) {
		if ( $this->ReportError ) {
			$mailTitle = " 資料庫出現錯誤！";
			$mailMessage = "在 ".$this->Company." 上的資料庫發生錯誤: $msg\n";
			$mailMessage.= "MySQL 報告的錯誤是(MySQL return error message): ".$this->Error."\n";
			$mailMessage.= "MySQL 返回的錯誤號碼是(Error number): ".$this->Errno."\n";
			$mailMessage.="出錯時間(date): ".date("Y-m-d l H:i:s")."\n";
			$mailMessage.="出錯的地址(url): http://".getenv("HTTP_HOST").getenv("REQUEST_URI")."\n";
			$mailMessage.="前一個地址是(referer url): ".getenv("HTTP_REFERER")."\n";

			$mailTitle   = autoGbBig5($mailTitle);		/* 自動簡繁轉換，不涉及 UTF8 */
			$mailMessage = autoGbBig5($mailMessage);	/* 自動簡繁轉換，不涉及 UTF8 */
			@mail ($this->AdminMail, $this->Company."-".getenv("HTTP_HOST").$mailTitle,$mailMessage);

			$message = "\n<pre>$message </pre>\n";
			$message .="</td></tr></table>\n<p>資料庫大概發生了輕微的錯誤，\n";
			$message .="請稍候再刷新嘗試一下。</p>";
			$message .= "本系統已經將此錯誤通過E-Mail發送給了 ".$this->Company." 的<a href=\"mailto:".$this->AdminMail."\">技術人員</a>， 如果問題依然, 您也可以直接聯繫她。</p>";
			$message .= "<p>我們為本次錯誤深表歉意，同時感謝您的支援！</p>";
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
	 * 公共方法: DB 語言格式轉換
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
	 * 自動轉換數據庫資料字符集為網站 UTF8 編碼
	 * 或轉換網站 UTF8 編碼為數據庫字符集
	 */
	function autoDBChar(& $str, $do = 'in')
	{
		if (!defined('CFG_DB_CHAR') || strtolower(CFG_DB_CHAR) == "utf-8") {
			return $str;
		}

		if ($do == 'in') {
			$str	= iconv('UTF-8', CFG_DB_CHAR, $str);
		} elseif ($do == 'out') {
			$str	= iconv(CFG_DB_CHAR, 'UTF-8', $str);
		}

		return $str;
	}
}
?>
