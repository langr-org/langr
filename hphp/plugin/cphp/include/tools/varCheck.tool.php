<?
#============================================================================================================================================================
# 名    稱: VarCheck v 0.9.13
# 功    能: 變數驗證
# 作    者：Arnold, arnold@addwe.com
# 使用說明:
# $VarCheck->List	= array(
#	{變數名} => array('note'=>{變數中文注釋},
#					  'type'=>{變數檢查類型},
#					 {'minLen'|'minVal'}=>{變數最小長度|變數最小值},
#				     {'maxLen'|'maxVal'}=>{變數最大長度|變數最大值}),
#					  'allowNull'=>{True,False},
# );
#
# {變數名} ：$_POST 或 $_GET 變數中的下標，必須是字母加數位，和表單控制項上的名字對應。
# {變數中文注釋} ：變數的中文注釋，幫助自動構造完整的報錯資訊。
# {變數檢查類型} ：參數可省略，檢查規則，目前支援以下規
#				   accounts ：檢查字元是否帳號格式，只充許字母、數位、下劃線 "_"
#				   varname ：檢查字元是否變數名格式，只充許 a-z 的字母開始，其中可以包含數位和下劃線"_"
#				   letter ：檢查字元是否全部只是 a-z 的字母，不分大小寫
#				   number ：檢查字元是否全部只是 0-9 的數位
#				   password ：檢查字元是否爲密碼格式的字母，ASCII 碼從 33 - 126 ，包括除空格外的所有可輸入字元
#				   email ：檢查是否正確的Email格式
#				   ip：檢查是否正確的IP格式
#				   url：檢查是否正確的 URL 格式
#				   date：檢查字元是否爲正確的 日期 格式，參數必需爲 yyyy-mm-dd 格式
#				   twidcard：檢查臺灣的身份證字型大小是否合法
#
# {變數最小長度|變數最小值} ：參數可省略，檢查變數長度或值不能小於指定值。
# {變數最大長度|變數最大值} ：參數可省略，檢查變數長度或值不能大於指定值。
# {allowNull}：參數可省略，預設值 False，檢查時是否充許變數值爲空值，當充充空值時，變數值爲空時不給出錯誤提示。
#
# 例：
# $VarCheck = & new Tool_VarCheck;
# $VarCheck->List	= array(
#	'accounts' => array('note'=>'管理員帳號','type'=>'accounts','minLen'=>4,'maxLen'=>8),
#	'password' => array('note'=>'管理員密碼','type'=>'password','minLen'=>4,'maxLen'=>10),
# );
# if (!$VarCheck->check()) {
#	echo $VarCheck->ErrMsg;
# }
#
# 曆	史：
# v 0.5.26 2004-05-26 Arnold Arnold@addwe.com : 1、新增加 url 檢查類型。
# v 0.5.24 2004-05-24 Arnold Arnold@addwe.com : 1、新增加 allowNull 參數，預設值爲 False，當指定爲 True 					 
#                                                  時，變數值爲空不提示錯誤，不爲空值進行檢查，主要用於一些不要求用戶一定需要輸入的值的檢查。
#											    2、varname 類型檢查，充許值中有下劃線"_"符號。
# v 0.5.15 2004-05-15 Arnold Arnold@addwe.com : 1、修改：type 參數可省略，用於值任意，不用進行檢查，只檢查是否爲空或大小、長度的時候。
#												2、新增 number、email、ip 三種檢查類型。
# v 0.9.09 2004-09-09 Arnold Arnold@addwe.com : 1、修改：當某檢查專案 AllowNull 等於 True 時，後面的檢查專案會中止的BUG。
# v 0.9.13 2004-09-13 Arnold Arnold@addwe.com : 1、新增：twIDCard （臺灣身份證）檢查類型。
#------------------------------------------------------------------------------------------------------------------------------------------------------------

class Tool_VarCheck
{
	#=======================================================================================
	#  外部屬性，可通過外部指定
	#=======================================================================================
	var $List;		// 需要檢查的內容
	#=======================================================================================
	#  內部屬性，內部使用，無需外部指定
	#=======================================================================================
	var $ErrMsg;	// 錯誤提示資訊

	#=======================================================================================
	#  檢查
	#=======================================================================================
	function check()
	{
		foreach ($this->List as $key => $value) { 
			
			$var	   = isset($_POST[$key]) ? $_POST[$key] : $_GET[$key];
			$type	   = $value['type'];
			$allowNull = $value['allowNull'];
			$note	   = $value['note'];
			$minLen	   = $value['minLen'];
			$maxLen	   = $value['maxLen'];
			$minVal	   = $value['minVal'];
			$maxVal	   = $value['maxVal'];
			unset($value);
			if (empty($type)){
				$typeCheck = False;
			} else {
				$typeCheck = True;
			}

			if (empty($allowNull)) {
				$allowNull = False;
			} else {
				if (empty($var)){
					$allowNull = True;
				} else {
					$allowNull = False;
				}
			}
			
			if (!$this->checkKey($key)) return False;					// 檢查變數名是否合法
			if (!$this->checkNote($key, $note)) return False;			// 檢查變數名的注釋是否合法

			if ($typeCheck) {
				$method = "is".ucfirst(strtolower($type));			// 獲得檢查方法的格式，例如：isAccounts
				if (!$this->checkType($key, $method)) return False;			// 檢查變數名的要檢查的類型是否合法
			}
			
			if ((isset($var))&&((!$allowNull))) {							// 如果變數有定義值才進行檢查，注意，變數是否定義值和變數值爲空是兩個不同的概念
				$len = strlen($var);					// 獲取變數長度
				if ("" == $var) {
					$this->ErrMsg = "錯誤：請填寫 [".$note."] 欄位。";
					return False;
				}
				if ($typeCheck) {
					if (!$this->$method($var)) {
						$this->ErrMsg = "錯誤： [".$note."] 欄位元內容格式不正確。";
						return False;
					}
				}
				if ((isset($minLen)) && ($len < $minLen)){
					$this->ErrMsg = "錯誤： [".$note."] 欄位元內容長度不能小於 ".$minLen."。";
					return False;
				}
				if ((isset($maxLen)) && ($len > $maxLen)){
					$this->ErrMsg = "錯誤： [".$note."] 欄位元內容長度不能大於 ".$maxLen."。";
					return False;
				}
				if ((isset($minVal)) && ($var < $minVal)){
					$this->ErrMsg = "錯誤： [".$note."] 欄位元內容的值不能小於 ".$minVal."。";
					return False;
				}
				if ((isset($maxVal)) && ($var > $maxVal)){
					$this->ErrMsg = "錯誤： [".$note."] 欄位元內容的值不能大於 ".$maxVal."。";
					return False;
				}
			}
		} 
		return True;
	}

	#=======================================================================================
	#  檢查變數名是否合法
	#=======================================================================================
	function checkKey(& $fKey)
	{
		if (!$this->isVarname($fKey)){
			$this->ErrMsg = "程式錯誤：要檢查的變數名 $fKey 錯誤，變數名只能由字母、數位、下劃線組成，必需以字母開始。";
			return False;
		}
		return True;
	}

	#=======================================================================================
	#  檢查變數名的中文注釋是否合法
	#=======================================================================================
	function checkNote(& $fKey,& $fStr)
	{
		if ((!isset($fStr))||("" == $fStr)) {
			$this->ErrMsg = "程式錯誤：要檢查的變數 $fKey 的注釋沒有定義。";
			return False;
		}
		return True;
	}

	#=======================================================================================
	#  檢查變數名要檢查的類型是否合法
	#=======================================================================================
	function checkType(& $fKey,& $fStr)
	{
		if (!method_exists($this,$fStr)) {  // 檢查方法是否存在
			$this->ErrMsg = "程式錯誤：要檢查的變數 $fKey 所檢查的方法 $fStr 不存在。";
			return False;
		}
		return True;
	}

	#=======================================================================================
	#  檢查字元是否帳號格式，只充許字母、數位、下劃線 "_"
	#=======================================================================================
	function isAccounts(& $fStr)
	{
		if (!eregi("^([0-9a-z]+[0-9a-z_]*)$",$fStr)) return False;
		return True;
	}

	#=======================================================================================
	#  檢查字元是否變數名格式，只充許 a-z 的字母開始，其中可以包含數位
	#=======================================================================================
	function isVarname(& $fStr)
	{
		if (!eregi("^([a-z]+[0-9a-z_]*)$",$fStr)) return False;
		return True;
	}

	#=======================================================================================
	#  檢查字元是否全部只是 a-z 的字母
	#=======================================================================================
	function isLetter(& $fStr)
	{
		if (!eregi("^([a-z]*)$",$fStr)) return False;
		return True;
	}

	#=======================================================================================
	#  檢查字元是否全部只是 0-9 的數位
	#=======================================================================================
	function isNumber(& $fStr)
	{
		if (!eregi("^([0-9]*)$",$fStr)) return False;
		return True;
	}

	#=======================================================================================
	#  檢查字元是否爲密碼格式的字母，ASCII 碼從 33 - 126 ，包括除空格外的所有可輸入字元
	#=======================================================================================
	function isPassword(& $fStr)
	{
		$len = strlen($fStr); 
		for ($i = 0; $i < $len; $i++) { 
			$ord = ord(substr($fStr, $i, 1)); 
			if (($ord < 33)||($ord > 126)) return false; 
		}
		return True;
	}

	#=======================================================================================
	#  檢查字元是否爲 Email 格式
	#=======================================================================================
	function isEmail(& $fStr)
	{
		if (!eregi("^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3}$", $fStr)) return False;
		return True;
	}

	#=======================================================================================
	#  檢查字元是否爲 IP 位址格式
	#=======================================================================================
	function isIp(& $fStr)
	{
		if (!eregi("^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$", $fStr)) return False;
		return True;
	}

	#=======================================================================================
	#  檢查字元是否爲 URL 位址格式
	#=======================================================================================
	function isUrl(& $fStr)
	{
		if (!eregi("^(http|ftp|https)://[-A-Za-z0-9._]+(\/([A-Za-z0-9\-\_\.\!\~\*\'\(\)\%\?\=\&]+))*/?$", $fStr)) return False;
		return True;
	}

	#=======================================================================================
	#  檢查字元是否爲正確的 日期 格式，參數必需爲 yyyy-mm-dd 格式
	#=======================================================================================
	function isDate(& $fStr)
	{
		$year  = substr($fStr,0,4);
		$month = substr($fStr,5,2);
		$day   = substr($fStr,8,2);
		if (@checkdate($month, $day , $year)) {
			return True;
		} else {
			return False;
		}
	}

	#=======================================================================================
	#  檢查臺灣身份證字型大小是否合法
	#=======================================================================================
	function isTwIDCard(& $fStr)
	{
		$abcord = array("A"=>10,"B"=>11,"C"=>12,"D"=>13,"E"=>14,
						"F"=>15,"G"=>16,"H"=>17,"J"=>18,"K"=>19,
						"L"=>20,"M"=>21,"N"=>22,"P"=>23,"Q"=>24,
						"R"=>25,"S"=>26,"T"=>27,"U"=>28,"V"=>29,
						"X"=>30,"Y"=>31,"W"=>32,"Z"=>33,"I"=>34,
						"O"=>35,
						);

		// 第一步 驗證身份證長度
		if (strlen($fStr)!=10) return False;  

		for ($i=1;$i<=10;$i++) { 
			$N[$i] = substr($fStr,$i-1,1); 
		} 
		
		$N[1] = strtoupper($N[1]);
		$N[1] = $abcord[$N[1]]; 
		
		if ($N[1]<10 or $N[1]>35) return False;

		//第二步 驗證性別 
		if ($N[2]<1 or $N[2]>2) return False;
		
		//第三步 檢查驗證碼
		$N1  = substr($N[1],0,1); 
		$N12 = substr($N[1],1,1); 
		$express = ($N1+$N12*9+$N[2]*8+$N[3]*7+$N[4]*6+$N[5]*5+$N[6]*4+$N[7]*3+$N[8]*2+$N[9]*1+$N[10])%10; 
		if ($express != 0) { 
			return False; 
		} 

		return True; 
	}
}
?>
