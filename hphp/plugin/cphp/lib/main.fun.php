<?php
/***
 * ���ƣ�PHP OOP ����������������׼� v0.06.05
 * ���ܣ�����������
 * ��	ʷ��
 * v0.06.05 Arnold ���� getTime ���������ܣ���ȡ��ʽ������ʱ���UNIXʱ�����
 *
 * $Id: main.fun.php 11 2010-03-09 05:38:04Z hua $
 */

/***
 * GB2312 ת��Ϊ BIG5
 */
Function gbToBig5(& $fContents) {
	Return iconv("GB2312","BIG5",$fContents);
	//Return ncf_gb2Big5($fContents);
}

/***
 * BIG ת��Ϊ GB2312
 */
Function big5ToGb(& $fContents) {
	Return iconv("GBK","GB2312",$fContents);
	//Return ncf_big52Gb($fContents);
}

/***
 * GBK ת��Ϊ BIG5
 */
Function gbkToBig5(& $fContents) {
	Return iconv("GBK","BIG5",$fContents);
}

/***
 * BIG ת��Ϊ GBK
 */
Function big5ToGbk(& $fContents) {
	Return iconv("BIG5","GBK",$fContents);
}

/***
 * GB2312 ת��Ϊ UTF-8
 */
Function gbToUtf8(& $fContents) {
	Return  iconv("GB2312","UTF-8",$fContents);
}

/***
 * BIG ת��Ϊ UTF-8
 */
Function big5ToUtf8(& $fContents) {
	Return  iconv("BIG5","UTF-8",$fContents);
}

/***
 * UTF-8 ת��Ϊ GB2312  
 */
Function utf8ToGb(& $fContents) {
	Return iconv("UTF-8","GB2312",$fContents);
}

/***
 * UTF-8 ת��Ϊ BIG
 */
Function utf8ToBig5(& $fContents) {
	Return iconv("UTF-8","BIG5",$fContents);
}

/***
 * GB2312 ת��Ϊ BIG5 ת��Ϊ UTF-8
 */
Function gbToBig5ToUtf8(& $fContents) {
	$fContents = gbToBig5($fContents);
	$fContents = big5ToUtf8($fContents);
	Return $fContents;
}

/***
 * BIG ת��Ϊ GB2312 ת��Ϊ UTF-8
 */
Function big5ToGbToUtf8(& $fContents) {
	$fContents = big5ToGb($fContents);
	$fContents = gbToUtf8($fContents);
	Return $fContents;
}

/***
 * UTF8 ת��Ϊ BIG5 ת��Ϊ GB2312
 */
Function Utf8ToBig5ToGb(& $fContents) {
	$fContents = utf8ToBig5($fContents);
	$fContents = big5ToGb($fContents);
	Return $fContents;
}

/***
 * UTF-8 ת��Ϊ GB2312 ת��Ϊ BIG5
 */
Function utf8ToGbToBig5(& $fContents) {
	$fContents = utf8ToGb($fContents);
	$fContents = gbToBig5($fContents);
	Return $fContents;
}

/***
 * GBK ת��Ϊ UTF-8
 */
Function gbkToUtf8(& $fContents) {
	Return  iconv("GBK","UTF-8",$fContents);
}

/***
 * UTF-8 ת��Ϊ GBK
 */
Function utf8ToGbk(& $fContents) {
	Return  iconv("UTF-8","GBK",$fContents);
}

/***
 * UTF-8 ת��Ϊ GBK ת��Ϊ BIG5
 */
Function utf8ToGbkToBig5(& $fContents) {
	$fContents = utf8ToGbk($fContents);
	$fContents = gbkToBig5($fContents);
	Return $fContents;

}

/***
 * UTF-8 ����������
 */
Function utf8ErrFilter(& $fContents) {
	$utf8ErrCode = chr(226).chr(150).chr(161);
	$fContents = str_replace($utf8ErrCode,"???",$fContents);
	Return $fContents;
}

/***
 * �Զ��б��Զ�ʵ�ּ�ת������ gb2312 <-> big5 ��ת�������漰 UTF-8��
 * $fTmplLang Ĭ��Ϊ CFG_TEMPLATE_LANGUAGE ������ֵ��Ҳ��ǿ��ָ��Ϊ big5 �� gb2312
 */
Function autoGbBig5(& $fStr, $fTmplLang = "") {

	if ("" == $fTmplLang) $fTmplLang = strtolower(CFG_TEMPLATE_LANGUAGE);

	/* ģ��Ϊ gb2312 ����ǰ����Ϊ big5 ʱ��ת��Ϊ gb2312 - big5 */
	if (("gb2312" == $fTmplLang)&&("big5" == CHAR_SET)) {
		$fStr = gbToBig5($fStr);
	}
	/* ģ��Ϊ big5 ����ǰ����Ϊ gb2312 ʱ��ת��Ϊ big5 - gb2312 */
	if (("big5" == $fTmplLang)&&("gb2312" == CHAR_SET)) {
		$fStr = big5ToGb($fStr);
	}
	/* ģ��Ϊ gbk ����ǰ����Ϊ big5 ʱ��ת��Ϊ gbk - big5 */
	if (("gbk" == $fTmplLang)&&("big5" == CHAR_SET)) {
		$fStr = gbkToBig5($fStr);
	}
	/* ģ��Ϊ big5 ����ǰ����Ϊ gbk ʱ��ת��Ϊ big5 - gbk */
	if (("big5" == $fTmplLang)&&("gbk" == CHAR_SET)) {
		$fStr = big5ToGbk($fStr);
	}
	Return $fStr;
}

/***
 * �Զ��б��Զ�ʵ�����Ա���ת��
 * $fAction = True  : gb2312 �� big5 �Զ�ת��Ϊ UTF-8
 * $fAction = False : UTF-8 �Զ�ת��Ϊ gb2312 �� big5 
 * $fTmplLang Ĭ��Ϊ CFG_TEMPLATE_LANGUAGE ������ֵ��Ҳ��ǿ��ָ��Ϊ big5 �� gb2312
 */
Function autoCharSet(& $fStr, $fAction = True, $fTmplLang = "") {
	
	if ("" == $fTmplLang) $fTmplLang = strtolower(CFG_TEMPLATE_LANGUAGE);
	
	if ($fAction) {	/* ���������gb2312 �� big5 ת��Ϊ utf8 */
		/* ģ��Ϊ gb2312 ����ǰ����Ϊ big5 ʱ��ת��Ϊ big5 - utf8 */
		if (("gb2312" == $fTmplLang)&&("big5" == CHAR_SET)) {
			$fStr = gbToBig5ToUtf8($fStr);
		}
		/* ģ��Ϊ gb2312 ����ǰ����Ϊ gb2312 ʱ��ת��Ϊ gb2312 - utf8 */
		if (("gb2312" == $fTmplLang)&&("gb2312" == CHAR_SET)) {
			$fStr = gbToUtf8($fStr);
		}
		/* ģ��Ϊ big5 ����ǰ����Ϊ gb2312 ʱ��ת��Ϊ gb2312 - utf8 */
		if (("big5" == $fTmplLang)&&("gb2312" == CHAR_SET)) {
			$fStr = big5ToGbToUtf8($fStr);
		}
		/* ģ��Ϊ big5 ����ǰ����Ϊ big5 ʱ��ת��Ϊ big5 - utf8 */
		if (("big5" == $fTmplLang)&&("big5" == CHAR_SET)) {
			$fStr = big5ToUtf8($fStr);
		}
		/* ģ��Ϊ gbk ����ǰ����Ϊ big5 �� gb2312 ʱ��ת��Ϊ gbk - utf8 */
		if (("gbk" == $fTmplLang)&&(("big5" == CHAR_SET)||("gb2312" == CHAR_SET))) {
			$fStr = gbkToUtf8($fStr);
		}
	} else {	/* ���������utf8 ת��Ϊ big5 �� gb2312 */
		/* ģ��Ϊ gb2312 ����ǰ����Ϊ big5 �� utf8 ʱ��ת��Ϊ utf8 - big5 - gb2312 */
		if (("gb2312" == $fTmplLang)&&("big5" == CHAR_SET)) {
			$fStr = utf8ToBig5ToGb($fStr);
		}
		/* ģ��Ϊ gb2312 ����ǰ����Ϊ gb2312 �� utf8 ʱ��ת��Ϊ utf8 - gb2312 */
		if (("gb2312" == $fTmplLang)&&("gb2312" == CHAR_SET)) {
			$fStr = utf8ToGb($fStr);
		}
		/* ģ��Ϊ big5 ����ǰ����Ϊ gb2312 �� utf8 ʱ��ת��Ϊ utf8 - gb2312 - big5 */
		if (("big5" == $fTmplLang)&&("gb2312" == CHAR_SET)) {
			$fStr = utf8ToGbToBig5($fStr);
		}
		/* ģ��Ϊ big5 ����ǰ����Ϊ big5 �� utf8 ʱ��ת��Ϊ utf8 - big5 */
		if (("big5" == $fTmplLang)&&("big5" == CHAR_SET)) {
			$fStr = utf8ToBig5($fStr);
		}
		/* ģ��Ϊ gbk ����ǰ����Ϊ gb2312 �� utf8 ʱ��ת��Ϊ utf8 - gbk */
		if (("gbk" == $fTmplLang)&&("gb2312" == CHAR_SET)) {
			$fStr = utf8ToGbk($fStr);
		}
		/* ģ��Ϊ gbk ����ǰ����Ϊ big5 �� utf8 ʱ��ת��Ϊ utf8 - gbk - big5 */
		if (("gbk" == $fTmplLang)&&("big5" == CHAR_SET)) {
			$fStr = utf8ToGbk($fStr);
		}
	}
	Return $fStr;
}

/*
Function autoCharSet($fStr, $fFrom = CFG_TEMPLATE_LANGUAGE, $fTo = CFG_CHAR_SET) {
	$fStr = iconv($fFrom, $fTo, $fStr);
	Return $fStr;
}
 */

/***
 * �Զ��б��Զ�ʵ�����Ա���ת��(���װ溯���������÷�ʽ)
 * GB2312 or BIG5 To UTF-8
 */
Function c($fStr) {
	Return autoCharSet($fStr);
}

/***
 * �������ˣ�ʹ $_GET �� $_POST ��$q->record �ȱ�������ȫ
 */
Function varFilter (& $fStr) {
	$flag	= 0;
	if (get_magic_quotes_gpc()) {		/* ת�忪�ؿ�? */
		$flag	= 1;
	}

	if (is_array($fStr)) {
		foreach ( $fStr AS $_arrykey => $_arryval ) {
			if ( is_string($_arryval) ) {
				if (!$flag)
					$fStr["$_arrykey"] = addslashes($fStr["$_arrykey"]);
				$fStr["$_arrykey"] = trim($fStr["$_arrykey"]);			/* ȥ���������˿ո� */
				$fStr["$_arrykey"] = htmlspecialchars($fStr["$_arrykey"]);	/* ��������Ԫת�� HTML ��ʽ */
				$fStr["$_arrykey"] = str_replace("javascript", "javascript ", $fStr["$_arrykey"]);	/* ��ֹ javascript */
			} elseif ( is_array($_arryval) ) {
				$fStr["$_arrykey"] = varFilter($_arryval);
			}
		}
	} else {
		if ( !$flag )
			$fStr	= addslashes($fStr);
		$fStr = trim($fStr);							/* ȥ���������˿ո� */
		$fStr = htmlspecialchars($fStr);					/* ��������Ԫת�� HTML ��ʽ */
		$fStr = str_replace("javascript", "javascript ", $fStr);		/* ��ֹ javascript */
	}
	Return $fStr;
}

/***
 * �ָ������˵ı���
 */
Function varResume (& $fStr) {
	if (is_array($fStr)) {
		foreach ( $fStr AS $_arrykey => $_arryval ) {
			if ( is_string($_arryval) ) {
				$fStr["$_arrykey"] = str_replace("&quot;", "\"", $fStr);
				$fStr["$_arrykey"] = str_replace("&lt;", "<", $fStr);
				$fStr["$_arrykey"] = str_replace("&gt;", ">", $fStr);
				$fStr["$_arrykey"] = str_replace("&amp;", "&", $fStr);
				$fStr["$_arrykey"] = str_replace("javascript ", "javascript", $fStr);
			}else if (is_array($_arryval)) {
				$fStr["$_arrykey"] = varResume($_arryval);
			}
		}
	} else {
		$fStr = str_replace("&quot;", "\"", $fStr);
		$fStr = str_replace("&lt;", "<", $fStr);
		$fStr = str_replace("&gt;", ">", $fStr);
		$fStr = str_replace("&amp;", "&", $fStr);
		$fStr = str_replace("javascript ", "javascript", $fStr);
	}
	Return $fStr;
}

/***
 * ת������֧ԮHTML�﷨
 */
Function trueHtml (& $fStr) {
	$fStr = varResume($fStr);
	$fStr = StripSlashes($fStr);
	Return $fStr;
}

/***
 * �����С�����ֵ֮��������λ�����㲹��
 */
Function getRand ($fMin, $fMax) {
	srand((double)microtime()*1000000);
	$fLen = "%0".strlen($fMax)."d";
	Return sprintf($fLen, rand($fMin,$fMax));
}

/***
 * ��ʾ������ֹ��ʽִ��
 */
Function halt($fStr) {
	echo "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>";
	echo "<html><head>";
	echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8>";
	echo "</head>";
	echo "<body leftmargin='20' topmargin='20' marginwidth='0' marginheight='0'>";
	echo autoCharSet($fStr);
	echo "</body>";
	echo "</html>";
	exit;
}

/***
 * ��ȡ�ļ�����
 */
Function fileRead($fFileName) {
	return file_get_contents($fFileName);
}

/***
 * д���ļ�����
 */
Function fileWrite($fFileName,$fContent) {
	ignore_user_abort (TRUE);		/* �����û��ر���������������ʽ����ִ�� */
	$fp = fopen($fFileName, 'w'); 
	if (flock($fp, LOCK_EX)) {
		fwrite($fp, $fContent); 
		flock($fp, LOCK_UN);
	}
	fclose($fp); 
	ignore_user_abort (FALSE);		/* �رպ����û��ر���������������ʽ���û���ֹ���ʶ�ֹͣ */
	return;
}

/***
 * ��û�Ȧ�б�������ɫ
 */
Function rowColor (& $fVar, $fColor1="", $fColor2="") {
	if (!isset($fVar)) {
		$fVar = $fColor1;
	} else {
		if ($fColor1 == $fVar) {
			$fVar = $fColor2;
		} else {
			$fVar = $fColor1;
		}
	}
	Return $fVar;
}

/***
 * ��ת��Ϊ���Ӹ�ʽ
 */
function s2m($second) {
	return floor($second/60)."��".($second%60)."��";
}

/***
 * ��ת��ΪСʱ��ʽ
 */
function s2h($second) {
	return floor($second/3600)."ʱ".floor(($second%3600)/60)."��";
}

/***
 * �����ִ���ȡ����
 * ����˵����
 * $fStr����Ҫ�����ԭʼ�ִ���
 * $fStart���ӵڼ������ֺ�ʼ��ȡ����ͷ��ʼ��ȡʹ�� 0
 * $fLen����ȡ��������
 * $fCode��ԭʼ�ִ��ı��뷽ʽ��Ĭ��Ϊ gb2312 �� big5��UTF-8 �� UTF-8 ���뷽ʽ��ȡ
 */
Function msubstr (& $fStr, $fStart, $fLen, $fCode = "") {
	$fCode = strtolower($fCode);
	switch ($fCode) {
		case "utf-8" : 
			preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $fStr, $ar);  
			if(func_num_args() >= 3) {  
				if (count($ar[0])>$fLen) {
					return join("",array_slice($ar[0],$fStart,$fLen))."..."; 
				}
				return join("",array_slice($ar[0],$fStart,$fLen)); 
			} else {  
				return join("",array_slice($ar[0],$fStart)); 
			} 
			break;
		default:
			$fStart = $fStart*2;
			$fLen   = $fLen*2;
			$strlen = strlen($fStr);
			for ( $i = 0; $i < $strlen; $i++ ) {
				if ( $i >= $fStart && $i < ( $fStart+$fLen ) ) {
					if ( ord(substr($fStr, $i, 1)) > 129 ) $tmpstr .= substr($fStr, $i, 2);
					else $tmpstr .= substr($fStr, $i, 1);
				}
				if ( ord(substr($fStr, $i, 1)) > 129 ) $i++;
			}
			if ( strlen($tmpstr) < $strlen ) $tmpstr .= "...";
			Return $tmpstr;
	}
}

/*
	function msubstr($str,$start,$end,$len=0) { //UTF-8 Cutting
		preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $str, $info);
		$lens=sizeof($info[0]);
		if ($len==1) return array(join("",array_slice($info[0],$start,$end)), $lens);
		else return join("",array_slice($info[0],$start,$end));
	}
 */

/***
 * ������������ select ѡ��
 * fSelectName��select �б��� Name
 * fSelectArray���б��������У�key Ϊ option ��ֵ��var Ϊ option ��ʾ����
 * fNowVal����ǰѡ�е� option ��ʹ�� key ֵ��Ӧ
 * fFirstOption����һ�� option
 * fJavaScript������Ҫ�� javascript ����
 * fBgColorArr: �� option ����ɫ
 */
Function selectList ($fSelectName, & $fSelectArray, $fNowVal = "", $fFirstOption = "", $fJavaScript = "", $fBgColorArr = array()) {
	/* ������ָ��ָ���һ��Ԫ�� */
	if (is_array($fSelectArray)) reset($fSelectArray); 
	
	//if (!empty($fJavaScript)) $fJavaScript = " onClick=\"".$fJavaScript."\"";
	$fSelectStr = "<SELECT ID=\"".$fSelectName."\" NAME=\"".$fSelectName."\" ".$fJavaScript.">";
	if (!empty($fFirstOption)) {
		$fFirstOption = autoCharSet($fFirstOption);
		$fSelectStr .= "<option value=\"\">".$fFirstOption."</option>";
	}
	while ( list($key, $val) = @each($fSelectArray) ) {
		$gbColor  = "";
		$selected = "";
		if (!empty($fBgColorArr[$key])) $gbColor = "style=\"COLOR: #".$fBgColorArr[$key]."\" ";
		if (( "$fNowVal" == "$key" ) && ( "$fNowVal" !== "" )) $selected = "SELECTED";
		$fSelectStr .= "<option value=\"".$key."\" ".$gbColor.$selected.">".$val."</option>\n";
	}
	$fSelectStr .= "</SELECT>";
	Return $fSelectStr;
}

/***
 * ������������ gameSelect ѡ��
 * fSelectName��select �б��� Name
 * fSelectArray���б��������У�key Ϊ option ��ֵ��var Ϊ option ��ʾ����
 * fNowVal����ǰѡ�е� option ��ʹ�� key ֵ��Ӧ
 * fFirstOption����һ�� option
 * fBgColorArr: �� option ����ɫ
 */
Function gameSelectList ($fSelectName, & $fSelectArray1, & $fSelectArray2, & $fSelectArray3, $fNowVal = "", $fFirstOption = "", $fJavaScript = "") {
	/* ������ָ��ָ���һ��Ԫ�� */
	if (is_array($fSelectArray1)) reset($fSelectArray1); 
	if (is_array($fSelectArray2)) reset($fSelectArray2); 
	if (is_array($fSelectArray3)) reset($fSelectArray3); 
	
	if (!empty($fJavaScript)) $fJavaScript = " onClick=\"".$fJavaScript."\"";
	$fSelectStr = "<SELECT NAME = ".$fSelectName." ".$fJavaScript.">";
	if (!empty($fFirstOption)) {
		$fFirstOption = autoCharSet($fFirstOption);
		$fSelectStr .= "<option value=\"\">".$fFirstOption."</option>";
	}
	$selectAction = False;
	while ( list($key, $val) = @each($fSelectArray2) ) {
		$selected = "";
		if (( $fNowVal == $key ) && ( $fNowVal !== "" ) && (!$selectAction)) {
			$selectAction = True;
			$selected = "SELECTED";
		}
		$fSelectStr .= "<option value=\"".$key."\" style=\"COLOR: GREEN\" ".$selected.">".$val."</option>\n";
	}
	while ( list($key, $val) = @each($fSelectArray3) ) {
		$selected = "";
		if (( $fNowVal == $key ) && ( $fNowVal !== "" ) && (!$selectAction)) {
			$selectAction = True;
			$selected = "SELECTED";
		}
		$fSelectStr .= "<option value=\"".$key."\" style=\"COLOR: Blue\" ".$selected.">".$val."</option>\n";
	}
	while ( list($key, $val) = @each($fSelectArray1) ) {
		$selected = "";
		if (( $fNowVal == $key ) && ( $fNowVal !== "" ) && (!$selectAction)) {
			$selectAction = True;
			$selected = "SELECTED";
		}
		$fSelectStr .= "<option value=\"".$key."\" ".$selected.">".$val."</option>\n";
	}
	$fSelectStr .= "</SELECT>";
	Return $fSelectStr;
}

/***
 *  ���������������еĵڼ���
 */
function weekOfTheYear ($day='', $month='', $year='')
{
	if ( $day == '' ) $day = date('d');
	if ( $month == '' ) $month = date('m');
	if ( $year == '' ) $year = date('Y');
	$day0101 = mktime('0','0','0', '01', '01', $year);
	$today = mktime('0','0','0', $month, $day, $year);
	$week2sunday = 8-date("w", $day0101);
	$week2sun = mktime('0','0','0','01',$week2sunday,$year);
	$result = sprintf("%02d",floor((date("z",$today)-date("z",$week2sun))/7)+2);
	Return $result;
}

/***
 * ��ȡ��ʽ������ʱ���UNIXʱ�����
 * fDateTime ����ʽΪ year-month-day hour:minute:second
 * fAction��+ ��ǰʱ�����һ�� fDateTime ʱ�䣬- ��ǰʱ���ȥһ�� fDateTime ʱ��
 */
Function getTime ($fDateTime, $fAction = "") {
	$year   = substr($fDateTime,0,4);
	$month  = substr($fDateTime,5,2);
	$day    = substr($fDateTime,8,2);
	$hour   = substr($fDateTime,11,2);
	$minute = substr($fDateTime,14,2);
	$second = substr($fDateTime,17,2);
	if ("+" == $fAction) {
		$year   = date("Y",NOW_TIME)+substr($fDateTime,0,4);
		$month  = date("m",NOW_TIME)+substr($fDateTime,5,2);
		$day    = date("d",NOW_TIME)+substr($fDateTime,8,2);
		$hour   = date("H",NOW_TIME)+substr($fDateTime,11,2);
		$minute = date("i",NOW_TIME)+substr($fDateTime,14,2);
		$second = date("s",NOW_TIME)+substr($fDateTime,17,2);
	} else if ("-" == $fAction) {
		$year   = date("Y",NOW_TIME)-substr($fDateTime,0,4);
		$month  = date("m",NOW_TIME)-substr($fDateTime,5,2);
		$day    = date("d",NOW_TIME)-substr($fDateTime,8,2);
		$hour   = date("H",NOW_TIME)-substr($fDateTime,11,2);
		$minute = date("i",NOW_TIME)-substr($fDateTime,14,2);
		$second = date("s",NOW_TIME)-substr($fDateTime,17,2);
	}
	
	Return mktime($hour, $minute, $second, $month, $day, $year);
}

/***
 * ��һ�������е�ֵ�Զ���ת��
 * $fAction = True  : gb2312 �� big5 �Զ�ת��Ϊ UTF-8
 * $fAction = False : UTF-8 �Զ�ת��Ϊ gb2312 �� big5 
 */
Function arrayAutoCharSet (& $fArray, $fAction = True) {
	if (is_array($fArray)) {
		foreach ( $fArray AS $_arrykey => $_arryval ) {
			if ( is_string($_arryval) ) {
				$fArray[$_arrykey] = autoCharSet($fArray[$_arrykey], $fAction);
			}else if (is_array($_arryval)) {
				$fArray[$_arrykey] = arrayAutoCharSet($_arryval);
			}
		}
	}
	reset($fArray);
	Return;
}

/***
 * ����Ƿ�һ���Ϸ��� $_SESSION ����
 * $fStr��SESSION �����±�
 * ��� $_SESSION �����Ѷ�����ֵ��Ϊ�շ��� True����֮���� False
 */
Function is_session ($fStr) {
	if ((isset($_SESSION[$fStr]))&&(!empty($_SESSION[$fStr]))) {
		return True;
	} else {
		return False;
	}
}

/***
 * �����־�ļ�����д����־
 * $fLogPath��	��־�ļ���·����������
 * $fLogMaxSize����־�ļ������Size
 * $fLog��	��־����
 */
Function doLog ($fLogPath,$fLogMaxSize,$fLog) {
	if (!file_exists($fLogPath)) fileWrite($fLogPath,'');
	if (filesize($fLogPath) > $fLogMaxSize) {
		fileWrite($fLogPath,'');
	}
	ignore_user_abort (TRUE);		/* �����û��ر���������������ʽ����ִ�� */
	$fp = fopen($fLogPath, 'a'); 
	if (flock($fp, LOCK_EX)) {
		fwrite($fp, $fLog); 
		flock($fp, LOCK_UN);
	}
	fclose($fp); 
	ignore_user_abort (FALSE);		/* �رպ����û��ر���������������ʽ���û���ֹ���ʶ�ֹͣ */
}

/***
 * ��ȡ��ʽ����Ľ����λ
 * fVal �������λ
 */
Function numToMoney ($fVal) {
	$len = strlen($fVal);
	if ($len>3) {
		$num = "";
		for ($i=0; $i<$len; $i++) {
			$num .= substr($fVal,$i,1);
			if (0 == (($len-$i-1)%3)) {
				if (($len-$i-1) >0) $num .= ",";
			}
		}
	} else {
		$num = $fVal;
	}
	Return $num;
}

/***
 * �����������ȡN������µ�����
 * dealArray ������
 * num ������
 */
function randArray($dealArray,$num) {
	if ( !is_array($dealArray) ) Return "";
	if ( $num > count($dealArray) ) Return $dealArray;
	if ( $num <= 0 ) Return "";
	srand((float) microtime() * 10000000);
	$rand_keys = array_rand($dealArray, $num);
	if ( $num == 1 ) {
		$resultArray[$rand_keys] = $dealArray[$rand_keys];
	} else {
		for($j=0;$j<$num;$j++) {
			$resultArray[$rand_keys[$j]] = $dealArray[$rand_keys[$j]];
		}
	}
	return $resultArray;
}

/***
 * ���ݻ�Ա���֤�ţ���ȡ��ʽ���������ר���ʺ�
 * fVal �����֤�ֺ�
 */
Function bankAccounts ($fVal) {
	if (strlen($fVal) != 10) return False;
	$one = strtoupper(substr($fVal,0,1));
	$one = ord($one)-64;
	if (($one <1)||($one >26)) return False;
	$one = sprintf("%02d",$one);
	$bankAccounts = $one.substr($fVal,1);
	Return $bankAccounts;
	/*
	if (strlen($fVal) != 9) return False;
	Return $fVal;
	*/
}

/***
 * �������飬��ȡradio�б�
 * $fVal1:��ѡ������;$fVal2:����;$fVal3:ѡ��ֵ(��ȱʡ)
 */
Function arrToRadioList ($fVal1,$fVal2,$fVal3 = 0) {
	$result = "";
	if (is_array($fVal2)) {
		while ( list($key, $val) = @each($fVal2) ) {
			if ($fVal3 == $key) {
				$checked = "checked";
			} else {
				$checked = "";
			}
			$result .= "<input type=\"radio\" name=\"".$fVal1."\" value=\"".$key."\" class=\"noborder\" ".$checked.">".$val." ";
		}
	}
	return $result;
}

/***
 * ��ȡSQL���� in ����е��б�
 */
Function getSQLWhereInList ($fVal) {
	if (!is_array($fVal)) {
		return False;
	}
	reset($fVal);
	$theWhere = "(";
	while (list($key,$val)=@each($fVal)) {
		$theWhere .= $key.",";
	}
	$theWhere = substr($theWhere,0,-1);
	if ("" == $theWhere) {
		return False;
	}
	$theWhere .= ")";
	return $theWhere;
}

/***
 * �жϵ�ǰ��Ա����
 */
Function chkLang ($fVal1, $fVal2="") {
	if (empty($fVal2)) {
		$language = $_SERVER["HTTP_ACCEPT_LANGUAGE"];
	} else {
		$language = $fVal2;
	}
	$len1      = strlen($language);
	$language  = str_replace($fVal1,"",$language);
	$len2      = strlen($language);
	if ($len1 == $len2) {
		return False;
	} else {
		return True;
	}
}

/*** by $Id: main.fun.php 11 2010-03-09 05:38:04Z hua $ */

/***
 * cookie ��֤����ܿ�����̨
 * $outtime ��֤��ʱʱ��, Ĭ��Ϊ 100 ��
 */
function cookie_auth($outtime = 8640000)
{
	if (isset($_COOKIE['admin_CookieAuth']) && $_COOKIE['admin_CookieAuth'] == CFG_ADMIN_VERIFY) {
		return;
	}

	if (isset($_GET['s']) && $_GET['s'] == CFG_ADMIN_VERIFY) {
		setcookie('admin_CookieAuth', CFG_ADMIN_VERIFY, time() + $outtime, "/");
	} else {
		header("Location: /");
	}

	return;
}

/***
 * �滻ģ���еı���Ϊ��������
 * �������ܹ��ĳ�ʽҲ����֧��ģ��~
 */
function _tmplVarReplace(& $tmplContent, & $tmpl)
{
	$d	= & $tmpl;
	$tmplContent = str_replace("\"", "\\\"", $tmplContent);
	$temp	= preg_replace('/(\{\$)(.+?)(\})/is', '".'.'$d[\'\\2\']'.'."', $tmplContent);
	eval("\$temp = \"$temp\";");
	$temp = str_replace("\\\"", "\"", $temp);
	$temp  = StripSlashes($temp);

	return $temp;
}

/***
 * ��¼��־
 * $log_file ��־�ļ���·����������
 * $log_str ��־����, $log_size ��־�ļ������Size, Ĭ��Ϊ 4M (4194304�ֽ�)
 */
function wlog($log_file, $log_str, $log_size = 4194304) 
{
	/* �����û��ر�����������ʽ����ִ�� */
	ignore_user_abort(TRUE);

	if ( empty($log_file) ) {
		$log_file = 'log_file';
	}
	if (defined("APP_LOG_PATH") /*&& substr($log_file, 0, 1) != '/'*/)
		$log_file = APP_LOG_PATH.$log_file;

	if (!file_exists($log_file)) { 
		$fp = fopen($log_file, 'a');
	} elseif (@filesize($log_file) > $log_size) {
		$fp = fopen($log_file, 'w');
	} else
		$fp = fopen($log_file, 'a');

	if (flock($fp, LOCK_EX)) {
		$cip	= defined("CLIENT_IP") ? "[".CLIENT_IP."] " : '';
		$log_str = "[".date('Y-m-d H:i:s')."] ".$cip.$log_str."\r\n";
		fwrite($fp, $log_str);
		flock($fp, LOCK_UN);
	}
	fclose($fp);

	/* �رպ����û��ر���������������ʽ���û���ֹ���ʶ�ֹͣ (�� window �Ϻ�����Ч) */
	ignore_user_abort(FALSE);
}

/***
 * ��ֹ�û�����ˢ������ 
 * ���û������� $time �������󳬹� $count ��ʱ,
 * ��ʾ�û��ڶ�������, ��ʱ���� $time ���ھܾ��û�������
 * ����Բ�ͬ��Ŀ��Ҫ��ͬ�˷�ˢ�·�ʽ, ��Ӧ��Ҫָ�� $cc_name
 */
function prevent_cc($count = 5, $time = 10, $cc_name = 'cc01') 
{
	if ($_SESSION[$cc_name] == 1 || isset($_COOKIE[$cc_name])) {	/* �����ʾ�û�ȷʵ���ʹ� */
		/*** �ͻ��ܾ� cookie �������������, ��Ӧ���ǿ��Խ��ܵ� */
		if ($_COOKIE[$cc_name."_flag"] == 1 && $_COOKIE[$cc_name] < $count) {	/* �������� */
			setcookie($cc_name, $_COOKIE[$cc_name] + 1, time() + $time);
			return 0;
		} else {
			$_SESSION[$cc_name] == 0;
			header("HTTP/1.1 503 Service Unavailable");
		//	header("HTTP/1.0 404 Not Found");
		//	echo @c("�������ҳ�����, ���Ժ�����!");
			exit;
		}
	} else {
		$_SESSION[$cc_name] = 1;
		setcookie($cc_name, 1, time() + $time, "/");
		setcookie($cc_name."_flag", 1, 0, "/");
	}

	return 0;
}

/***
 * ��ʼ�� $_GET ����
 * ������������ڼܹ���������Ŀ�Ŀ�ʼ����һ��
 */
function args()
{
	if (isset($_GET['args'])) {
		$str	= $_GET['args'];
		$arg	= explode("-", $str);
		for ($i = 0; $arg[$i] != ''; $i += 2) {
			$_GET[$arg[$i]] = $arg[$i+1];
		}
	}
	return 0;
}

/***
 * url like: 
 * ?module=mod&action=act&args=arg | index.php?module=mod&args=arg | ?action=act | ?
 * => /mod/act/arg.html | /mod/index/arg.html | /act/index-0.html | /index-0.html
 * ������ת��Ϊ��̬ҳ����ʽ, 
 * ��� REWRITE ���߱����ӵĽű�δ������, �򷵻�ԭ����
 */
function url($get) /* $script = "index.php" */
{
	if (REWRITE) {
		$url	= explode('?', $get);

		if ($url[0] == RW_SCRIPT || ($url[0] == '' && PHP_SCRIPT == RW_SCRIPT)) {
			$url	= str_replace('=', '-', $url[1]);
			$url	= explode('&', $url);
			$i = 0;
			while ($url[$i]) {
				if (substr($url[$i], 0, 7) == "module-")
					$url_mod = "/".substr($url[$i], 7);
				else if (substr($url[$i], 0, 7) == "action-")
					$url_act = "/".substr($url[$i], 7);
				else if (substr($url[$i], 0, 5) == "args-")
					$url_str = "".substr($url[$i], 5);
				else {
					$url_str .= $url[$i]."-";
				}
				$i++ ;
			}
			$url_str = str_replace('--', '-0-', $url_str);			/* �ղ��� */
			$url_str = ($url_str[strlen($url_str)-1] == '-') ? substr($url_str, 0, -1) : $url_str;
			// $url_mod = $url_mod ? $url_mod : "/".CFG_DEFAULT_MODULE;
			// $url_act = $url_act ? $url_act : "/".CFG_DEFAULT_ACTION;
			if ($url_mod && !$url_act) { 
				$url_act = "/".CFG_DEFAULT_ACTION;
			}
			$url_str = (strlen($url_str) < 3) ? "index-0" : $url_str;	/* �޲��� */
			$url_str = $url_mod.$url_act."/".$url_str.".html";

			return $url_str;
		} elseif (substr($get, 0, 1) != '/') {			/* ת��Ϊ����·�� */
			return CFG_APP_PATH.$get;	
		}
	}
		return $get;	
}

/***
 * װ�ز��
 * Ĭ��װ�� tools/ Ŀ¼�µĹ���,
 * $plug_name = "public" ʱװ�� public/ �µĹ���,
 * ����װ�� plugs/ �µ� $plug_name Ŀ¼��Ĳ���ļ�
 */
function tool($tool, $plug_name = "tools") 
{
	if ($plug_name == "tools")
		$tool_file = FILE_PATH."include/tools/".$tool.".tool.php";
	elseif ($plug_name == "public")
		$tool_file = FILE_PATH."include/".$plug_name."/".$tool.".".$plug_name.".php";
	else
		$tool_file = FILE_PATH."plugs/".$plug_name."/".$tool;
		//$tool_file = FILE_PATH."include/".$plug_name."/".$tool.".".$plug_name.".php";

	if (file_exists($tool_file)) {
		include_once($tool_file);
	} else {
		echo c("<!-- ������ ".$tool_file." ʧ��! -->");
		return -1;
	}
}

/***
 * �� $url �� POST ��ʽ��������, ֧�� ssl �� http ��ʽ, ֧�� http �ʺ���֤.
 * $data ΪҪ POST ������, Ϊ��ʱ�� GET ��ʽ���� ($data['name'] = 'value')
 * ���� $url ���ص�����, 
 * $return = "data" ���ַ�����ʽ����, $return = "array" ������(��)��ʽ����
 */
function posttohost($url, $data = array(), $return = "data") {
	$url = parse_url($url);
	if (!$url) 
		return "couldn't parse url";
	if (!isset($url['port'])) { $url['port'] = ""; }
	if (!isset($url['query'])) { $url['query'] = ""; }

	$encoded = "";
	$post	= "POST";

	if (count($data) == 0) {
		$post	= "GET";
	} else {
		while (list($k, $v) = each($data)) {
			$encoded .= ($encoded ? "&" : "");
			$encoded .= rawurlencode($k)."=".rawurlencode($v);
		}
	}

	$m	= '';
	if ($url['scheme'] == 'ssl' || $url['scheme'] == 'udp') 
		$m = $url['scheme']."://";
	$url['port'] = $url['port'] ? $url['port'] : 80;
	$fp = fsockopen($m.$url['host'], $url['port']);
	if (!$fp) {
		return "Failed to open socket to $url[host]:$url[port]";
	}

	fputs($fp, sprintf($post." %s%s%s HTTP/1.1\n", $url['path'], $url['query'] ? "?" : "", $url['query']));
	fputs($fp, "Host: $url[host]\n");
	if ( !empty($url['user']) ) {
		fputs($fp, "Authorization: Basic ".base64_encode($url['user'].':'.$url['pass'])."\n");
	}
	fputs($fp, "User-Agent: Mozilla/5.0 hua@langr.org\n");
	fputs($fp, "Content-type: application/x-www-form-urlencoded\n");
	if ($post == "POST") {
		fputs($fp, "Content-length: " . strlen($encoded) . "\n");
	}
	fputs($fp, "Connection: close\n\n");

	if ($post == "POST") {
		fputs($fp, "$encoded\n");
	}

	$line = fgets($fp, 2048);	/* ����? */
	if (!eregi("^HTTP/1\.. 200", $line)) {
		return 0;
	}

	if ($return == "array") {
		$results = array();
	} else {
		$results = "";
	}
	$inheader = 1;
	$i	= 0;
	while(!feof($fp)) {
		$line = fgets($fp, 2048);
		/* ȥ����һ�εĿ��� */
		if ($inheader && ($line == "\n" || $line == "\r\n")) {
			$inheader = 0;
		} elseif (!$inheader) {
			if ($return == "array") {
				$results[$i] = $line;
				$i++;
			} else {
				$results .= $line;
			}
		}
	}
	fclose($fp);

	return $results;
}

/***
 * ���� cookie ��֤���㷨 (��������)
 */
function cookie_hash($account, $timestamp, $from_ip) {
	$keys = array(
		'c9f8330802d55ce888d6267a6b80387ba23041a4',
		'5108f0d1c17c312a1dcf159836514b5e2ba74977',
		'77a2a37da57decf7a048da0767dbe992a0751d97',
		'68674d71f80f8e8832bf2ecc43be12ec22d87d9a',
		'5d18f37e3e5bbe84e0eae1ac6f83cd920bce4d86');
	$a1 = strlen($account); 
	$a2 = ($timestamp)% 5;
	$a3 = $timestamp % $a1;
	$a4 = ord($account[$a3]);
	
	$key = $keys[$a2];
	$ord1 = $keys[$a2][$a4 % 40];
//	$ord2 = $keys[$a2][$$a3 % 40];
	$ord3 = $keys[$a2][$timestamp % 40];
	
	$str = sha1($key.$account.$timestamp.$from_ip.$ord1.$ord3);

	return $str;
}

function cookie_enc($key, $account, $timestamp) {
	return base64_encode($key."\t".$timestamp."\t".$account);
	
}

function cookie_dec($key) {
	return split("\t", base64_decode($key));
}

/***
 * ������ (SimpleXMLElement Object) ���� xml �ļ� 
 * �� simplexml_load_string() ���ɵ� SimpleXMLElement Object ����
 */
function simplexml_load_array($object)
{
	$dom_xml = dom_import_simplexml($object);
	if (!$dom_xml) {
	    return 'Error while converting XML';
	}

	$dom = new DOMDocument('1.0');
	$dom_xml = $dom->importNode($dom_xml, true);
	$dom_xml = $dom->appendChild($dom_xml);

	return $dom->saveXML();
}

function xml2array($xml)
{
	return simplexml_load_string($xml);
}

function array2xml($arr)
{
	return simplexml_load_array($arr);
}

/***
 * ��ָ����ʱ�����ת��ָ���� $url
 * ���� header("Location: $url") ����
 */
function jump_url($url, $time = 0) {
	if ($time > 0) {
		echo "<script language=javascript>
			function jump_url() {window.location.replace(\"$url\");}
				back = setTimeout(\"jump_url()\", $time*1000);
			</script>";
	}
	else
		echo "<script language=javascript>window.location.replace(\"$url\");</script>";
}

/***
 * �������ָ����λ�Ƿ���λ
 * ����־״̬�Ƿ����� ubb �� html
 * $i ����������, $c ������λ
 */
function isallow($state, $c)
{
	$i	= 1;
	$i	= $i << $c;

	if ( $state & $i ) {
		return true;
	} else {
		return false;
	}
}

function allow_ubb($state)
{
	return isallow($state, 0);
}

function allow_html($state)
{
	return isallow($state, 1);
}
?>
