<?php
/***
 * 名称：PHP OOP 面向物件开发工具套件 v0.06.05
 * 功能：基本函数库
 * 历	史：
 * v0.06.05 Arnold 增加 getTime 函数，功能：获取格式化日期时间的UNIX时间戳记
 *
 * $Id: main.fun.php 11 2010-03-09 05:38:04Z hua $
 */

/***
 * GB2312 转换为 BIG5
 */
Function gbToBig5(& $fContents) {
	Return iconv("GB2312","BIG5",$fContents);
	//Return ncf_gb2Big5($fContents);
}

/***
 * BIG 转换为 GB2312
 */
Function big5ToGb(& $fContents) {
	Return iconv("GBK","GB2312",$fContents);
	//Return ncf_big52Gb($fContents);
}

/***
 * GBK 转换为 BIG5
 */
Function gbkToBig5(& $fContents) {
	Return iconv("GBK","BIG5",$fContents);
}

/***
 * BIG 转换为 GBK
 */
Function big5ToGbk(& $fContents) {
	Return iconv("BIG5","GBK",$fContents);
}

/***
 * GB2312 转换为 UTF-8
 */
Function gbToUtf8(& $fContents) {
	Return  iconv("GB2312","UTF-8",$fContents);
}

/***
 * BIG 转换为 UTF-8
 */
Function big5ToUtf8(& $fContents) {
	Return  iconv("BIG5","UTF-8",$fContents);
}

/***
 * UTF-8 转换为 GB2312  
 */
Function utf8ToGb(& $fContents) {
	Return iconv("UTF-8","GB2312",$fContents);
}

/***
 * UTF-8 转换为 BIG
 */
Function utf8ToBig5(& $fContents) {
	Return iconv("UTF-8","BIG5",$fContents);
}

/***
 * GB2312 转换为 BIG5 转换为 UTF-8
 */
Function gbToBig5ToUtf8(& $fContents) {
	$fContents = gbToBig5($fContents);
	$fContents = big5ToUtf8($fContents);
	Return $fContents;
}

/***
 * BIG 转换为 GB2312 转换为 UTF-8
 */
Function big5ToGbToUtf8(& $fContents) {
	$fContents = big5ToGb($fContents);
	$fContents = gbToUtf8($fContents);
	Return $fContents;
}

/***
 * UTF8 转换为 BIG5 转换为 GB2312
 */
Function Utf8ToBig5ToGb(& $fContents) {
	$fContents = utf8ToBig5($fContents);
	$fContents = big5ToGb($fContents);
	Return $fContents;
}

/***
 * UTF-8 转换为 GB2312 转换为 BIG5
 */
Function utf8ToGbToBig5(& $fContents) {
	$fContents = utf8ToGb($fContents);
	$fContents = gbToBig5($fContents);
	Return $fContents;
}

/***
 * GBK 转换为 UTF-8
 */
Function gbkToUtf8(& $fContents) {
	Return  iconv("GBK","UTF-8",$fContents);
}

/***
 * UTF-8 转换为 GBK
 */
Function utf8ToGbk(& $fContents) {
	Return  iconv("UTF-8","GBK",$fContents);
}

/***
 * UTF-8 转换为 GBK 转换为 BIG5
 */
Function utf8ToGbkToBig5(& $fContents) {
	$fContents = utf8ToGbk($fContents);
	$fContents = gbkToBig5($fContents);
	Return $fContents;

}

/***
 * UTF-8 编码错误过滤
 */
Function utf8ErrFilter(& $fContents) {
	$utf8ErrCode = chr(226).chr(150).chr(161);
	$fContents = str_replace($utf8ErrCode,"???",$fContents);
	Return $fContents;
}

/***
 * 自动判别自动实现简繁转换（仅 gb2312 <-> big5 的转换，不涉及 UTF-8）
 * $fTmplLang 默认为 CFG_TEMPLATE_LANGUAGE 的设置值，也可强行指定为 big5 或 gb2312
 */
Function autoGbBig5(& $fStr, $fTmplLang = "") {

	if ("" == $fTmplLang) $fTmplLang = strtolower(CFG_TEMPLATE_LANGUAGE);

	/* 模板为 gb2312 ，当前语言为 big5 时，转换为 gb2312 - big5 */
	if (("gb2312" == $fTmplLang)&&("big5" == CHAR_SET)) {
		$fStr = gbToBig5($fStr);
	}
	/* 模板为 big5 ，当前语言为 gb2312 时，转换为 big5 - gb2312 */
	if (("big5" == $fTmplLang)&&("gb2312" == CHAR_SET)) {
		$fStr = big5ToGb($fStr);
	}
	/* 模板为 gbk ，当前语言为 big5 时，转换为 gbk - big5 */
	if (("gbk" == $fTmplLang)&&("big5" == CHAR_SET)) {
		$fStr = gbkToBig5($fStr);
	}
	/* 模板为 big5 ，当前语言为 gbk 时，转换为 big5 - gbk */
	if (("big5" == $fTmplLang)&&("gbk" == CHAR_SET)) {
		$fStr = big5ToGbk($fStr);
	}
	Return $fStr;
}

/***
 * 自动判别自动实现语言编码转换
 * $fAction = True  : gb2312 或 big5 自动转换为 UTF-8
 * $fAction = False : UTF-8 自动转换为 gb2312 或 big5 
 * $fTmplLang 默认为 CFG_TEMPLATE_LANGUAGE 的设置值，也可强行指定为 big5 或 gb2312
 */
Function autoCharSet(& $fStr, $fAction = True, $fTmplLang = "") {
	
	if ("" == $fTmplLang) $fTmplLang = strtolower(CFG_TEMPLATE_LANGUAGE);
	
	if ($fAction) {	/* 正相操作，gb2312 或 big5 转换为 utf8 */
		/* 模板为 gb2312 ，当前语言为 big5 时，转换为 big5 - utf8 */
		if (("gb2312" == $fTmplLang)&&("big5" == CHAR_SET)) {
			$fStr = gbToBig5ToUtf8($fStr);
		}
		/* 模板为 gb2312 ，当前语言为 gb2312 时，转换为 gb2312 - utf8 */
		if (("gb2312" == $fTmplLang)&&("gb2312" == CHAR_SET)) {
			$fStr = gbToUtf8($fStr);
		}
		/* 模板为 big5 ，当前语言为 gb2312 时，转换为 gb2312 - utf8 */
		if (("big5" == $fTmplLang)&&("gb2312" == CHAR_SET)) {
			$fStr = big5ToGbToUtf8($fStr);
		}
		/* 模板为 big5 ，当前语言为 big5 时，转换为 big5 - utf8 */
		if (("big5" == $fTmplLang)&&("big5" == CHAR_SET)) {
			$fStr = big5ToUtf8($fStr);
		}
		/* 模板为 gbk ，当前语言为 big5 或 gb2312 时，转换为 gbk - utf8 */
		if (("gbk" == $fTmplLang)&&(("big5" == CHAR_SET)||("gb2312" == CHAR_SET))) {
			$fStr = gbkToUtf8($fStr);
		}
	} else {	/* 逆向操作，utf8 转换为 big5 或 gb2312 */
		/* 模板为 gb2312 ，当前语言为 big5 的 utf8 时，转换为 utf8 - big5 - gb2312 */
		if (("gb2312" == $fTmplLang)&&("big5" == CHAR_SET)) {
			$fStr = utf8ToBig5ToGb($fStr);
		}
		/* 模板为 gb2312 ，当前语言为 gb2312 的 utf8 时，转换为 utf8 - gb2312 */
		if (("gb2312" == $fTmplLang)&&("gb2312" == CHAR_SET)) {
			$fStr = utf8ToGb($fStr);
		}
		/* 模板为 big5 ，当前语言为 gb2312 的 utf8 时，转换为 utf8 - gb2312 - big5 */
		if (("big5" == $fTmplLang)&&("gb2312" == CHAR_SET)) {
			$fStr = utf8ToGbToBig5($fStr);
		}
		/* 模板为 big5 ，当前语言为 big5 的 utf8 时，转换为 utf8 - big5 */
		if (("big5" == $fTmplLang)&&("big5" == CHAR_SET)) {
			$fStr = utf8ToBig5($fStr);
		}
		/* 模板为 gbk ，当前语言为 gb2312 的 utf8 时，转换为 utf8 - gbk */
		if (("gbk" == $fTmplLang)&&("gb2312" == CHAR_SET)) {
			$fStr = utf8ToGbk($fStr);
		}
		/* 模板为 gbk ，当前语言为 big5 的 utf8 时，转换为 utf8 - gbk - big5 */
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
 * 自动判别自动实现语言编码转换(简易版函数，非引用方式)
 * GB2312 or BIG5 To UTF-8
 */
Function c($fStr) {
	Return autoCharSet($fStr);
}

/***
 * 变数过滤，使 $_GET 、 $_POST 、$q->record 等变数更安全
 */
Function varFilter (& $fStr) {
	$flag	= 0;
	if (get_magic_quotes_gpc()) {		/* 转义开关开? */
		$flag	= 1;
	}

	if (is_array($fStr)) {
		foreach ( $fStr AS $_arrykey => $_arryval ) {
			if ( is_string($_arryval) ) {
				if (!$flag)
					$fStr["$_arrykey"] = addslashes($fStr["$_arrykey"]);
				$fStr["$_arrykey"] = trim($fStr["$_arrykey"]);			/* 去除左右两端空格 */
				$fStr["$_arrykey"] = htmlspecialchars($fStr["$_arrykey"]);	/* 将特殊字元转成 HTML 格式 */
				$fStr["$_arrykey"] = str_replace("javascript", "javascript ", $fStr["$_arrykey"]);	/* 禁止 javascript */
			} elseif ( is_array($_arryval) ) {
				$fStr["$_arrykey"] = varFilter($_arryval);
			}
		}
	} else {
		if ( !$flag )
			$fStr	= addslashes($fStr);
		$fStr = trim($fStr);							/* 去除左右两端空格 */
		$fStr = htmlspecialchars($fStr);					/* 将特殊字元转成 HTML 格式 */
		$fStr = str_replace("javascript", "javascript ", $fStr);		/* 禁止 javascript */
	}
	Return $fStr;
}

/***
 * 恢复被过滤的变数
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
 * 转换变数支援HTML语法
 */
Function trueHtml (& $fStr) {
	$fStr = varResume($fStr);
	$fStr = StripSlashes($fStr);
	Return $fStr;
}

/***
 * 获得最小和最大值之间乱数，位数不足补零
 */
Function getRand ($fMin, $fMax) {
	srand((double)microtime()*1000000);
	$fLen = "%0".strlen($fMax)."d";
	Return sprintf($fLen, rand($fMin,$fMax));
}

/***
 * 显示错误并中止程式执行
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
 * 读取文件内容
 */
Function fileRead($fFileName) {
	return file_get_contents($fFileName);
}

/***
 * 写入文件内容
 */
Function fileWrite($fFileName,$fContent) {
	ignore_user_abort (TRUE);		/* 忽略用户关闭游览器动作，程式继续执行 */
	$fp = fopen($fFileName, 'w'); 
	if (flock($fp, LOCK_EX)) {
		fwrite($fp, $fContent); 
		flock($fp, LOCK_UN);
	}
	fclose($fp); 
	ignore_user_abort (FALSE);		/* 关闭忽略用户关闭游览器动作，程式随用户中止防问而停止 */
	return;
}

/***
 * 获得回圈列表间隔行颜色
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
 * 秒转换为分钟格式
 */
function s2m($second) {
	return floor($second/60)."分".($second%60)."秒";
}

/***
 * 秒转换为小时格式
 */
function s2h($second) {
	return floor($second/3600)."时".floor(($second%3600)/60)."分";
}

/***
 * 中文字串截取函数
 * 参数说明：
 * $fStr：需要截最的原始字串；
 * $fStart：从第几个汉字后开始载取，从头开始截取使用 0
 * $fLen：截取几个汉字
 * $fCode：原始字串的编码方式，默认为 gb2312 或 big5，UTF-8 按 UTF-8 编码方式截取
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
 * 根据阵列生成 select 选项
 * fSelectName：select 列表框的 Name
 * fSelectArray：列表内容阵列，key 为 option 的值，var 为 option 显示内容
 * fNowVal：当前选中的 option ，使用 key 值对应
 * fFirstOption：第一个 option
 * fJavaScript：所需要的 javascript 调用
 * fBgColorArr: 各 option 的颜色
 */
Function selectList ($fSelectName, & $fSelectArray, $fNowVal = "", $fFirstOption = "", $fJavaScript = "", $fBgColorArr = array()) {
	/* 将阵列指标指向第一个元素 */
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
 * 根据阵列生成 gameSelect 选项
 * fSelectName：select 列表框的 Name
 * fSelectArray：列表内容阵列，key 为 option 的值，var 为 option 显示内容
 * fNowVal：当前选中的 option ，使用 key 值对应
 * fFirstOption：第一个 option
 * fBgColorArr: 各 option 的颜色
 */
Function gameSelectList ($fSelectName, & $fSelectArray1, & $fSelectArray2, & $fSelectArray3, $fNowVal = "", $fFirstOption = "", $fJavaScript = "") {
	/* 将阵列指标指向第一个元素 */
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
 *  获得日期所在年份中的第几周
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
 * 获取格式化日期时间的UNIX时间戳记
 * fDateTime ：格式为 year-month-day hour:minute:second
 * fAction：+ 当前时间加上一个 fDateTime 时间，- 当前时间减去一个 fDateTime 时间
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
 * 对一个阵列中的值自动简繁转换
 * $fAction = True  : gb2312 或 big5 自动转换为 UTF-8
 * $fAction = False : UTF-8 自动转换为 gb2312 或 big5 
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
 * 检查是否一个合法的 $_SESSION 变数
 * $fStr：SESSION 变数下标
 * 如果 $_SESSION 变数已定义且值不为空返回 True，反之返回 False
 */
Function is_session ($fStr) {
	if ((isset($_SESSION[$fStr]))&&(!empty($_SESSION[$fStr]))) {
		return True;
	} else {
		return False;
	}
}

/***
 * 检查日志文件，并写入日志
 * $fLogPath：	日志文件的路径及档案名
 * $fLogMaxSize：日志文件的最大Size
 * $fLog：	日志内容
 */
Function doLog ($fLogPath,$fLogMaxSize,$fLog) {
	if (!file_exists($fLogPath)) fileWrite($fLogPath,'');
	if (filesize($fLogPath) > $fLogMaxSize) {
		fileWrite($fLogPath,'');
	}
	ignore_user_abort (TRUE);		/* 忽略用户关闭游览器动作，程式继续执行 */
	$fp = fopen($fLogPath, 'a'); 
	if (flock($fp, LOCK_EX)) {
		fwrite($fp, $fLog); 
		flock($fp, LOCK_UN);
	}
	fclose($fp); 
	ignore_user_abort (FALSE);		/* 关闭忽略用户关闭游览器动作，程式随用户中止防问而停止 */
}

/***
 * 获取格式化后的金额数位
 * fVal ：金额数位
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
 * 随机从数组中取N个组成新的数组
 * dealArray ：数组
 * num ：数量
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
 * 根据会员身份证号，获取格式化后的银行专属帐号
 * fVal ：身份证字号
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
 * 根据数组，获取radio列表
 * $fVal1:单选框名称;$fVal2:阵列;$fVal3:选中值(可缺省)
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
 * 获取SQL条件 in 语句中的列表
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
 * 判断当前会员语言
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
 * cookie 认证后才能看到后台
 * $outtime 论证超时时间, 默认为 100 天
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
 * 替换模板中的变数为变数内容
 * 不经过架构的程式也可以支持模板~
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
 * 记录日志
 * $log_file 日志文件的路径及档案名
 * $log_str 日志内容, $log_size 日志文件的最大Size, 默认为 4M (4194304字节)
 */
function wlog($log_file, $log_str, $log_size = 4194304) 
{
	/* 忽略用户关闭游览器，程式继续执行 */
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

	/* 关闭忽略用户关闭游览器动作，程式随用户中止防问而停止 (在 window 上好像无效) */
	ignore_user_abort(FALSE);
}

/***
 * 防止用户恶意刷新请求 
 * 当用户在连续 $time 秒内请求超过 $count 次时,
 * 表示用户在恶意请求, 此时将在 $time 秒内拒绝用户的请求
 * 如果对不同项目需要不同了防刷新方式, 则应该要指定 $cc_name
 */
function prevent_cc($count = 5, $time = 10, $cc_name = 'cc01') 
{
	if ($_SESSION[$cc_name] == 1 || isset($_COOKIE[$cc_name])) {	/* 这里表示用户确实访问过 */
		/*** 客户拒绝 cookie 将不能正常浏览, 这应该是可以接受的 */
		if ($_COOKIE[$cc_name."_flag"] == 1 && $_COOKIE[$cc_name] < $count) {	/* 接受请求 */
			setcookie($cc_name, $_COOKIE[$cc_name] + 1, time() + $time);
			return 0;
		} else {
			$_SESSION[$cc_name] == 0;
			header("HTTP/1.1 503 Service Unavailable");
		//	header("HTTP/1.0 404 Not Found");
		//	echo @c("您请求的页面过多, 请稍后再试!");
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
 * 初始化 $_GET 参数
 * 将这个函数放在架构里面在项目的开始运行一次
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
 * 将链接转换为静态页面形式, 
 * 如果 REWRITE 或者被链接的脚本未被配置, 则返回原链接
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
			$url_str = str_replace('--', '-0-', $url_str);			/* 空参数 */
			$url_str = ($url_str[strlen($url_str)-1] == '-') ? substr($url_str, 0, -1) : $url_str;
			// $url_mod = $url_mod ? $url_mod : "/".CFG_DEFAULT_MODULE;
			// $url_act = $url_act ? $url_act : "/".CFG_DEFAULT_ACTION;
			if ($url_mod && !$url_act) { 
				$url_act = "/".CFG_DEFAULT_ACTION;
			}
			$url_str = (strlen($url_str) < 3) ? "index-0" : $url_str;	/* 无参数 */
			$url_str = $url_mod.$url_act."/".$url_str.".html";

			return $url_str;
		} elseif (substr($get, 0, 1) != '/') {			/* 转换为绝对路径 */
			return CFG_APP_PATH.$get;	
		}
	}
		return $get;	
}

/***
 * 装载插件
 * 默认装载 tools/ 目录下的工具,
 * $plug_name = "public" 时装载 public/ 下的工具,
 * 否则装载 plugs/ 下的 $plug_name 目录里的插件文件
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
		echo c("<!-- 载入插件 ".$tool_file." 失败! -->");
		return -1;
	}
}

/***
 * 向 $url 以 POST 方式传递数据, 支持 ssl 和 http 方式, 支持 http 帐号验证.
 * $data 为要 POST 的数据, 为空时以 GET 方式传递 ($data['name'] = 'value')
 * 返回 $url 传回的数据, 
 * $return = "data" 以字符串方式返回, $return = "array" 以数组(行)方式返回
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

	$line = fgets($fp, 2048);	/* 出错? */
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
		/* 去掉第一次的空行 */
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
 * 用于 cookie 认证的算法 (可以随意)
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
 * 将数组 (SimpleXMLElement Object) 生成 xml 文件 
 * 由 simplexml_load_string() 生成的 SimpleXMLElement Object 数组
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
 * 在指定的时间后跳转到指定的 $url
 * 可用 header("Location: $url") 代替
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
 * 检测整数指定的位是否置位
 * 检测标志状态是否允许 ubb 或 html
 * $i 被检测的整数, $c 被检测的位
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
