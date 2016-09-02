<?PHP
/***
 * ��    ��: PHP OOP ����������������׼� MySql v0.06.03
 * ��    ��: ���Ͽ������
 * 
 * $Id: mysql.class.php 8 2009-10-20 10:05:34Z langr $
 */

Class MySql  {

	/***
	 * ��������: �ŷ������� ���e��Ĭ�����ã���������base.setup.php������
	 */
	Var $Host     = 'localhost';		/* database system host */
	Var $Database = 'dbName';		/* database */
	Var $User     = 'userName';		/* database user */
	Var $Password = 'password';		/* database password */

	/***
	 * ��������: ���ò���
	 */
	Var $AutoFree    = True;		/* True: �Զ��ͷ� */
	Var $Debug       = False;		/* True: ��ʾ������Ѷ */
	Var $HaltOnError = 'yes';		/* "yes"   : ��ʾ�����ж�ִ�� */
						/* "no"    : ���Դ��󣬼���ִ�� */
						/* "report": ��ʾ���󣬼���ִ�� */
	Var $ReportError = False;		/* True: ������ϸ������Ų�������Ա�� */
	Var $PconnectOn  = True;		/* True: ʹ�� pconnect ������ʹ�� connect */
	Var $ReadOnly	 = False;		/* True: ���Ͽ�ֻ��, ��������ͼд���Ͽ⶯��ʱ���س��� */

	/***
	 * ��������: ��ѯ������� �� ��ǰ����
	 */
	Var $record = Array();
	Var $Row;
	Var $QueryStr = '';

	/***
	 * ��������: ������� �� ������Ѷ
	 */
	Var $Errno = 0;
	Var $Error = '';

	/***
	 * ��������: �����Ͽ������� ������Ѷ
	 */
	Var $Type     = 'MySQL';
	Var $Revision = '1.41';
	Var $Company  = 'www.betcity.com.tw';
	Var $AdminMail= 'service@betcity.com.tw';

	/***
	 * ˽������: ����ID ��ѯID
	 */
	Var $LinkID  = 0;
	Var $QueryID = 0;

	/***
	 * ��������: ������
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
	 * ��������: һЩ����ı���
	 */
	Function getLinkID() {
		Return $this->LinkID;
	}

	Function getQueryID() {
		Return $this->QueryID;
	}

	/***
	 * ��������: �������Ͽ�
	 */
	Function connect() {
		/* �������ӣ�ѡ�����Ͽ� */
		if ( $this->LinkID == 0 ) {
			/* �������� */
			if ( $this->PconnectOn ) {
				$this->LinkID = @mysql_pconnect($this->Host, $this->User, $this->Password);
			} else {
				$this->LinkID = @mysql_connect($this->Host, $this->User, $this->Password);
			}
			/* ���Ӵ��� */
			if ( $this->LinkID == 0 ) {
				if ( $this->Debug ) {
					$msg = "connect('$this->Host', '$this->User', '$this->Password') ���ݿ�����ʧ�ܣ�";
				} else {
					$msg = '��ʱ�޷��������ݿ⣡';
				}
				$this->halt($msg);
				Return False;
			}
			/* ѡ�����Ͽ�ʱ���� */
			if ( !@mysql_select_db($this->Database, $this->LinkID) ) {
				$this->halt("�޷������Ͽ�'".$this->Database."'��");
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
	 * ��������: �ͷŲ�ѯ���
	 */
	Function free() {
		@mysql_free_result($this->QueryID);
		$this->QueryID = 0;
	}

	/***
	 * ��������: �ر����Ͽ�����
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
	 * ��������: ִ�в�ѯ
	 */
	Function query($str) {
		if ( $str == '' ) Return False;

		if ( !$this->connect() ) Return False;

		/* �²�ѯ���ͷ�ǰ�εĲ�ѯ��� */
		if ( $this->QueryID ) {
			$this->free();
		}
		
		$str = varResume($str);			/* �ָ������˵ı���,��ԭ��ʵ��ֵ */
		
	//	$str = $this->autoDBLanguage($str);	/* �Զ��� SQL ���Ը�ʽ����ת�� */
		$str = $this->autoDBChar($str);		/* �Զ��� SQL ���Ը�ʽ����ת�� */
		if ( $this->ReadOnly ) {		/* ֻ����ѯ */
			if ( $this->checkStr($str) ) {
			//	$this->halt("<b>Database error:</b> DataBase read-only: ".autoCharSet($str, false));
				halt("<b>Database error:</b> DataBase read-only: ".autoCharSet($str, false)."<br />ϵͳ���Ͽⷢ�����󣬵�ǰ�����ѱ���ֹ��");
				Return False;
			}
		}
		$this->QueryStr = $str;
		
		$debugMsg = "����: ���";
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
			$this->halt("�����ѯ:".$this->QueryStr);
			Return False;
		} else {
			Return $this->QueryID;
		}
	}

	/***
	 * ��������: ��ò�ѯ���
	 */
	Function nextRecord() {
		if ( !$this->QueryID ) {
			$this->halt('ִ�д��󣺲�ѯ��Ч��');
			Return False;
		}

		$this->record = @mysql_fetch_array($this->QueryID);
	  		if ("" != $this->record){
			foreach($this->record as $key => $val) { 
			//	$this->record[$key] = $this->autoDBLanguage($val,False);	/* �Զ��� ���� ���Ը�ʽ����ת�� */
				$this->record[$key] = $this->autoDBChar($val, 'out');		/* �Զ��� ���� ���Ը�ʽ����ת�� */
			}
			$this->record = varFilter($this->record);				/* ȡ����ֵ���а�ȫ���� */
		}

		$this->Row += 1;
		
		$stat = is_array($this->record);

		if ( !$stat && $this->AutoFree ) {
			$this->free();
		}
		Return $stat;
	}

	/***
	 * ��������: ��ò����ID
	 */
	Function insertId(){
		if( $result = mysql_insert_id($this->LinkID) ) {
			Return $result;
		} else {
			Return False;
		}
	}

	/***
	 * ��������: ѡ�����ݿ�
	 */
	Function selectDB($db /*= $this->Database*/) {
		if( $result = mysql_select_db($db, $this->LinkID) ) {
			Return $result;
		} else {
			Return False;
		}
	}

	/***
	 * ��������: ���Է���
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
	 * ��ѯ����������һ����¼���
	 *
	 * @param string ��ѯ���
	 * @return bool True ��ѯ�����ؽ���ɹ���False ��ѯ�����ؽ��ʧ��
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
	 * ��ѯ������������� limitLine �����ļ�¼��Ϊ��ֹ���������limit ��ֵ�ɶ�������ָ����sql ���������� limit �Ӿ䣬Ĭ��30��
	 *
	 * @param string ��ѯ���
	 * @param int limit ��ʼλ��
	 * @param int ���������
	 * 
	 * @return array �� bool ��ѯ�ɹ��������ʽ���ؽ����False ��ѯʧ��
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
	 * ��������: ���SQL���ִ�к���Ӱ�������
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
	 * ��������: ���Է���
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
	 * ��������: ���ұ�
	 */
	Function tableNames() {
		$this->connect();
		$h = @mysql_query("show tables", $this->LinkID);
		if ( $this->Debug ) printf("����: ��� = %s<br>\n", "'show tables'");
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
	 * ��������: ������
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
		if ( $this->HaltOnError != 'report' ) halt(' ϵͳ���Ͽⷢ�����󣬵�ǰ�����ѱ���ֹ��');
	}

	Function haltmsg($msg) {
		if ( $this->ReportError ) {
			$mailTitle = " ���Ͽ���ִ���";
			$mailMessage = "�� ".$this->Company." �ϵ����Ͽⷢ������: $msg\n";
			$mailMessage.= "MySQL ����Ĵ�����(MySQL return error message): ".$this->Error."\n";
			$mailMessage.= "MySQL ���صĴ��������(Error number): ".$this->Errno."\n";
			$mailMessage.="����ʱ��(date): ".date("Y-m-d l H:i:s")."\n";
			$mailMessage.="����ĵ�ַ(url): http://".getenv("HTTP_HOST").getenv("REQUEST_URI")."\n";
			$mailMessage.="ǰһ����ַ��(referer url): ".getenv("HTTP_REFERER")."\n";

			$mailTitle   = autoGbBig5($mailTitle);		/* �Զ���ת�������漰 UTF8 */
			$mailMessage = autoGbBig5($mailMessage);	/* �Զ���ת�������漰 UTF8 */
			@mail ($this->AdminMail, $this->Company."-".getenv("HTTP_HOST").$mailTitle,$mailMessage);

			$message = "\n<pre>$message </pre>\n";
			$message .="</td></tr></table>\n<p>���Ͽ��ŷ�������΢�Ĵ���\n";
			$message .="���Ժ���ˢ�³���һ�¡�</p>";
			$message .= "��ϵͳ�Ѿ����˴���ͨ��E-Mail���͸��� ".$this->Company." ��<a href=\"mailto:".$this->AdminMail."\">������Ա</a>�� ���������Ȼ, ��Ҳ����ֱ�����M����</p>";
			$message .= "<p>����Ϊ���δ������Ǹ�⣬ͬʱ��л����֧Ԯ��</p>";
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
	 * ��������: DB ���Ը�ʽת��
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
	 * �Զ�ת�����ݿ������ַ���Ϊ��վ UTF8 ����
	 * ��ת����վ UTF8 ����Ϊ���ݿ��ַ���
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
	 * ��� $str ����Ƿ���д���Ͽ���ص� sql
	 * ���򷵻� true
	 */
	function checkStr($str)
	{
		$s	= trim($str);
		$sub_str = substr(strtolower($s), 0, 10);
		$deny_str = array("insert", "update", "delete", "create");	/* ������ִ�е� sql �ؼ��� */
		$allow_str = array("select");					/* ������� sql �ؼ��� */
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
