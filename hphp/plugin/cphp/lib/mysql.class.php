<?PHP
/***
 * ��    �Q: PHP OOP ��������_�l�����׼� MySql v0.06.03
 * ��    ��: �Y�ώ�����
 * 
 * $Id: mysql.class.php 6 2007-10-28 03:40:44Z langr $
 */

Class MySql  {

	/***
	 * ��������: �ŷ������� �@�e��Ĭ�J�O�ã����wՈ��base.setup.php���O��
	 */
	Var $Host     = 'localhost';		/* database system host */
	Var $Database = 'dbName';		/* database */
	Var $User     = 'userName';		/* database user */
	Var $Password = 'password';		/* database password */

	/***
	 * ��������: �O�Å���
	 */
	Var $AutoFree    = True;		/* True: �Ԅ�ጷ� */
	Var $Debug       = False;		/* True: �@ʾ�{ԇ�YӍ */
	Var $HaltOnError = 'yes';		/* "yes"   : �@ʾ�e�`���Д����� */
						/* "no"    : �����e�`���^�m���� */
						/* "report": �@ʾ�e�`���^�m���� */
	Var $ReportError = False;		/* True: ���Ԕ���e�`���ŁK�o����T�� */
	Var $PconnectOn  = True;		/* True: ʹ�� pconnect ����tʹ�� connect */

	/***
	 * ��������: ��ԃ�Y����� �� ��ǰ�Д�
	 */
	Var $record = Array();
	Var $Row;
	Var $QueryStr = '';

	/***
	 * ��������: �e�`̖�a �� �e�`�YӍ
	 */
	Var $Errno = 0;
	Var $Error = '';

	/***
	 * ��������: ���Y�ώ����� �Y���YӍ
	 */
	Var $Type     = 'MySQL';
	Var $Revision = '1.41';
	Var $Company  = 'www.betcity.com.tw';
	Var $AdminMail= 'service@betcity.com.tw';

	/***
	 * ˽�Ќ���: �B��ID ��ԃID
	 */
	Var $LinkID  = 0;
	Var $QueryID = 0;

	/***
	 * ��������: ������
	 */
	Function MySql() {
	}

	/***
	 * ��������: һЩ����Ĉ��
	 */
	Function getLinkID() {
		Return $this->LinkID;
	}

	Function getQueryID() {
		Return $this->QueryID;
	}

	/***
	 * ��������: �B���Y�ώ�
	 */
	Function connect() {
		/* �����B�ӣ��x���Y�ώ� */
		if ( $this->LinkID == 0 ) {
			/* �����B�� */
			if ( $this->PconnectOn ) {
				$this->LinkID = @mysql_pconnect($this->Host, $this->User, $this->Password);
			} else {
				$this->LinkID = @mysql_connect($this->Host, $this->User, $this->Password);
			}
			/* �B���e�` */
			if ( $this->LinkID == 0 ) {
				if ( $this->Debug ) {
					$msg = "connect('$this->Host', '$this->User', '$this->Password') �������B��ʧ����";
				} else {
					$msg = '���r�o���B�Ӕ����죡';
				}
				$this->halt($msg);
				Return False;
			}
			/* �x���Y�ώ�r�e�` */
			if ( !@mysql_select_db($this->Database, $this->LinkID) ) {
				$this->halt("�o�����_�Y�ώ�'".$this->Database."'��");
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
	 * ��������: ጷŲ�ԃ�Y��
	 */
	Function free() {
		@mysql_free_result($this->QueryID);
		$this->QueryID = 0;
	}

	/***
	 * ��������: ���в�ԃ
	 */
	Function query($str) {
		if ( $str == '' ) Return False;

		if ( !$this->connect() ) Return False;

		/* �²�ԃ��ጷ�ǰ�εĲ�ԃ�Y�� */
		if ( $this->QueryID ) {
			$this->free();
		}
		
		$str = varResume($str);			/* �֏ͱ��^�V��׃��,߀ԭ�挍��ֵ */
		
	//	$str = $this->autoDBLanguage($str);	/* �Ԅӌ� SQL �Z�Ը�ʽ�M���D�Q */
		$str = $this->autoDBChar($str);		/* �Ԅӌ� SQL �Z�Ը�ʽ�M���D�Q */
		$this->QueryStr = $str;
		
		$debugMsg = "�{ԇ: �Z��";
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
			$this->halt("�e�`��ԃ:".$this->QueryStr);
			Return False;
		} else {
			Return $this->QueryID;
		}
	}

	/***
	 * ��������: �@�ò�ԃ�Y��
	 */
	Function nextRecord() {
		if ( !$this->QueryID ) {
			$this->halt('�����e�`����ԃ�oЧ��');
			Return False;
		}

		$this->record = @mysql_fetch_array($this->QueryID);
	  		if ("" != $this->record){
			foreach($this->record as $key => $val) { 
			//	$this->record[$key] = $this->autoDBLanguage($val,False);	/* �Ԅӌ� �Y�� �Z�Ը�ʽ�M���D�Q */
				$this->record[$key] = $this->autoDBChar($val, 'out');		/* �Ԅӌ� �Y�� �Z�Ը�ʽ�M���D�Q */
			}
			$this->record = varFilter($this->record);				/* ȡ����ֵ�M�а�ȫ�^�V */
		}

		$this->Row += 1;
		
		$stat = is_array($this->record);

		if ( !$stat && $this->AutoFree ) {
			$this->free();
		}
		Return $stat;
	}

	/***
	 * ��������: �@�ò����ID
	 */
	Function insertId(){
		if( $result = mysql_insert_id($this->LinkID) ) {
			Return $result;
		} else {
			Return False;
		}
	}

	/***
	 * ��������: �x�񔵓���
	 */
	Function selectDB($db /*= $this->Database*/) {
		if( $result = mysql_select_db($db, $this->LinkID) ) {
			Return $result;
		} else {
			Return False;
		}
	}

	/***
	 * ��������: �s�Է���
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
	 * ��������: �@��SQL�Z���������Ӱ푵��Д�
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
	 * ��������: �s�Է���
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
		if ( $this->Debug ) printf("�{ԇ: �Z�� = %s<br>\n", "'show tables'");
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
	 * ��������: �e�`̎��
	 */
	Function halt($msg) {
		$this->Error = @mysql_error($this->LinkID);
		$this->Errno = @mysql_errno($this->LinkID);
		if ( $this->HaltOnError == 'no' ) Return;

		$this->haltmsg($msg);
		if ( $this->HaltOnError != 'report' ) halt(' ϵ�y�Y�ώ�l���e�`����ǰ�����ѱ���ֹ��');
	}

	Function haltmsg($msg) {
		if ( $this->ReportError ) {
			$mailTitle = " �Y�ώ���F�e�`��";
			$mailMessage = "�� ".$this->Company." �ϵ��Y�ώ�l���e�`: $msg\n";
			$mailMessage.= "MySQL �����e�`��(MySQL return error message): ".$this->Error."\n";
			$mailMessage.= "MySQL ���ص��e�`̖�a��(Error number): ".$this->Errno."\n";
			$mailMessage.="���e�r�g(date): ".date("Y-m-d l H:i:s")."\n";
			$mailMessage.="���e�ĵ�ַ(url): http://".getenv("HTTP_HOST").getenv("REQUEST_URI")."\n";
			$mailMessage.="ǰһ����ַ��(referer url): ".getenv("HTTP_REFERER")."\n";

			$mailTitle   = autoGbBig5($mailTitle);		/* �ԄӺ����D�Q�����漰 UTF8 */
			$mailMessage = autoGbBig5($mailMessage);	/* �ԄӺ����D�Q�����漰 UTF8 */
			@mail ($this->AdminMail, $this->Company."-".getenv("HTTP_HOST").$mailTitle,$mailMessage);

			$message = "\n<pre>$message </pre>\n";
			$message .="</td></tr></table>\n<p>�Y�ώ��Űl�����p΢���e�`��\n";
			$message .="Ո�Ժ���ˢ�Lԇһ�¡�</p>";
			$message .= "��ϵ�y�ѽ������e�`ͨ�^E-Mail�l�ͽo�� ".$this->Company." ��<a href=\"mailto:".$this->AdminMail."\">���g�ˆT</a>�� ������}��Ȼ, ��Ҳ����ֱ���M����</p>";
			$message .= "<p>�҂��鱾���e�`���Ǹ�⣬ͬ�r���x����֧Ԯ��</p>";
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
	 * ��������: DB �Z�Ը�ʽ�D�Q
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
	 * �Ԅ��D�Q�������Y���ַ�����Wվ UTF8 ���a
	 * ���D�Q�Wվ UTF8 ���a�锵�����ַ���
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
