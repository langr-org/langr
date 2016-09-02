<?
#============================================================================================================================================================
# 名    稱: Ubb v 0.5.24
# 功    能: UBB 語法處理
# 作    者：Arnold, arnold@addwe.com
# 使用舉例：
# 例1：獲取 UBB 工具條及 Javascript 代碼：
#		include_once(FILE_PATH."include/tools/ubb.tool.php");
#		$Ubb = new Tool_Ubb;
#		$Ubb->ToolsImage = FILE_PATH."images/admin/ubbToolsButton.gif";
#		$this->Tmpl['ubbTools'] = autoCharSet($Ubb->getUbbTools($formname, $textareaname));	// 獲取 UBB 工具條
#
# 例2：UBB 代碼轉換
#			include_once(FILE_PATH."include/tools/ubb.tool.php");
#			$Ubb = new Tool_Ubb;
#			$Ubb->convert($this->Tmpl['content']);
#
# 例3：只充許 b,i,u 三個UBB代碼的轉換
#			include_once(FILE_PATH."include/tools/ubb.tool.php");
#			$Ubb = new Tool_Ubb;
#			$Ubb->allowUbb("b,i,u");
#			$Ubb->convert($this->Tmpl['content']);
#
# 例4：只禁止 b,i,u 三個UBB代碼的轉換
#			include_once(FILE_PATH."include/tools/ubb.tool.php");
#			$Ubb = new Tool_Ubb;
#			$Ubb->denyUbb("b,i,u");
#			$Ubb->convert($this->Tmpl['content']);
#------------------------------------------------------------------------------------------------------------------------------------------------------------

class Tool_Ubb
{
	#=======================================================================================
	# 外部屬性，其值由外部指定。
	#=======================================================================================
	var $ToolsImage;				// Ubb 工具圖片的路徑

	var $AutoNewLine = true;		// 自動換行
	var $AutoSpace   = true;		// 自動轉換空格
	var $AutoUbb	 = true;		// 自動轉換 UBB 代碼
	var $UbbMap		 = array(
								'hr'	=> True,
								'color' => True,
								'b'		=> True,
								'i'		=> True,
								'u'		=> True,
								'url'	=> True,
								'email' => True,
								'page'  => False,
								'code'  => True,
								'_list' => True,
								'quote' => True,
								'img'	=> True,
								'music' => True,
								'image' => True,
								'flash' => True,
								'move'  => True,
								'fly'	=> True,
								'movie'	=> True,
							);

	var $UbbWidth	  = 800;		// image 和 flash 的最大寬度
	var $UbbHeight	  = 600;		// flash 的最大高度, image 不需要指定高度，其高度隨寬度比例變化

	#=======================================================================================
	# 內部屬性，不需要外部指定和修改。
	#=======================================================================================

	#=======================================================================================
	# 字元轉換
	#=======================================================================================
	Function convert (& $fStr)
	{
		$this->newLine($fStr);		// 自動換行
		$this->space($fStr);		// 自動空格
		$this->ubb($fStr);			// 自動 UBB 轉換
		
		return $fStr;
	}

	#=======================================================================================
	# 字元換行，/n 轉換爲 <br>
	#=======================================================================================
	Function newLine (& $fStr)
	{
		if ($this->AutoNewLine) $fStr = nl2br($fStr);
		return;
	}

	#=======================================================================================
	# 字元空格，兩個空白字元 轉換爲 &nbsp;&nbsp;
	#=======================================================================================
	Function space (& $fStr)
	{
		if ($this->AutoSpace) $fStr = eregi_replace("  ","&nbsp;&nbsp;",$fStr);
		return;
	}

	#=======================================================================================
	# 充許部分 UBB 代碼轉換，使用此方法，所有 UBB 轉換默認爲 False，只有指定的 UBB 代碼爲 True
	# 充許的 UBB 代碼之間用","號分隔
	#=======================================================================================
	Function allowUbb ($fPower = "")
	{
		$fPowerArray = split(",",$fPower);
		
		while(list($key,$val) = @each($this->UbbMap)){
			if (!in_array($key,$fPowerArray)) {
				$this->UbbMap[$key] = False;
			}
		}
		reset($this->UbbMap);

		return;
	}

	#=======================================================================================
	# 拒絕部分 UBB 代碼轉換，使用此方法，所有 UBB 轉換默認爲 True，只有指定的 UBB 代碼爲 False
	# 拒絕的 UBB 代碼之間用","號分隔
	#=======================================================================================
	Function denyUbb ($fPower = "")
	{
		$fPowerArray = split(",",$fPower);
		
		while(list($key,$val) = @each($this->UbbMap)){
			if (in_array($key,$fPowerArray)) {
				$this->UbbMap[$key] = False;
			}
		}
		reset($this->UbbMap);

		return;
	}

	#=======================================================================================
	# UBB代碼轉換
	#=======================================================================================
	Function ubb (& $fStr)
	{
		if ($this->AutoUbb){
			while(list($key,$val) = @each($this->UbbMap)){
				if (($val)&&(method_exists($this,$key))) {	// 此UBB代碼充許被轉換並且存在轉換的方法
					$this->$key($fStr);
				}
			}
		}
		reset($this->UbbMap);

		return;
	}

	#=======================================================================================
	# UBB - hr 代碼轉換
	# 說明：水平線
	# 格式：[hr=高度(象素),寬度(百分比),排列,顔色] 例：[hr=80,#0000ff]
	#=======================================================================================
	Function hr (& $fStr)
	{
		$fStr = eregi_replace("\[hr=([0-9]*),([0-9]*),([left|center|right]{4,6}),([#0-9a-z]{7})\]","<hr size=\"\\1\" width=\"\\2%\" align=\"\\3\" color=\"\\4\">",$fStr);
		return;
	}

	#=======================================================================================
	# UBB - color 代碼轉換
	# 說明：字體顔色
	# 格式：[color=顔色]文字[/color] 例：[color=red]文字[/color]
	#=======================================================================================
	Function color (& $fStr)
	{
		$fStr = preg_replace("/\[color=(.+?)\](.+?)\[\/color\]/is", "<font color='\\1'>\\2</font>", $fStr);
		
		return;
	}

	#=======================================================================================
	# UBB - b 代碼轉換
	# 說明：字體加粗
	# 格式：[b]需要加粗的文字[/b]
	#=======================================================================================
	Function b (& $fStr)
	{
		$fStr = preg_replace("/\[b\](.+?)\[\/b\]/is", "<b>\\1</b>", $fStr);
		
		return;
	}

	#=======================================================================================
	# UBB - i 代碼轉換
	# 說明：字體斜體
	# 格式：[i]需要斜體顯示的文字[/i]
	#=======================================================================================
	Function i (& $fStr)
	{
		$fStr = preg_replace("/\[i\](.+?)\[\/i\]/is", "<i>\\1</i>", $fStr);

		return;
	}

	#=======================================================================================
	# UBB - u 代碼轉換
	# 說明：字體加上下劃線
	# 格式：[u]需要加下劃線顯示的文字[/u]
	#=======================================================================================
	Function u (& $fStr)
	{
		$fStr = preg_replace("/\[u\](.+?)\[\/u\]/is", "<u>\\1</u>", $fStr);

		return;
	}

	#=======================================================================================
	# UBB - url 代碼轉換
	# 說明：連結
	# 格式：[url=http://網址]顯示的內容[/url]
	#=======================================================================================
	Function url (& $fStr)
	{
		$fStr = eregi_replace("\[url=http://([^[]*)\]{1}([^[]*)\[\/url\]", "<a href=\"http://\\1\" target=\"_blank\" onClick=\"return confirm(\'".c("您即將開啟下列連結，确定嗎？\\\\n\\\\nhttp://\\1\\\\n\\\\n不明連結可能包含木馬程式")."\')\">\\2</a>", $fStr);
		//$fStr = eregi_replace("\[url=http://([^[]*)\]{1}", "<a href=\"http://\\1\" target=_blank>", $fStr);
	  	//$fStr = eregi_replace("\[\/url\]","</a>",$fStr);
		
		return;
	}

	#=======================================================================================
	# UBB - email 代碼轉換
	# 說明：連結
	# 格式：[email=電郵位址]顯示的內容[/email]
	#=======================================================================================
	Function email (& $fStr)
	{
	    $fStr = eregi_replace("\[email=([^[]*)\]{1}([^[]*)\[\/email\]", "<a href=\"mailto:\\1\">\\2</a>", $fStr);

		//$fStr = eregi_replace("\[email=([^[]*)\]{1}","<a href=\"mailto:\\1\">", $fStr);
	    //$fStr = eregi_replace("\[\/email\]","</a>",$fStr);

		return;
	}

	#=======================================================================================
	# UBB - page 代碼轉換
	# 說明：引入一個網頁
	# 格式：[page]要引入的網頁地址[/page]
	#=======================================================================================
	Function page (& $fStr)
	{
	    $fStr = eregi_replace("\[page\]([^\[]*)\[/page\]","<br><iframe frameborder=0 width=90% height=400 scrolling=auto src=\"\\1\"></iframe><br><br>",$fStr);

		return;
	}

	#=======================================================================================
	# UBB - code 代碼轉換
	# 說明：引入一段代碼
	# 格式：[code]代碼片段[/code]
	#=======================================================================================
	Function code (& $fStr)
	{
	    //$fStr = eregi_replace("\[code\]([^\[]*)\[\/code\]", "<BLOCKQUOTE><font size=1 face=Arial>CODE:</font><HR><pre>\\1</pre><HR></BLOCKQUOTE><BR>", $fStr);
		
		$fStr = eregi_replace("\[code\]","<BLOCKQUOTE><font size=1 face=Arial>CODE:</font><HR><pre>",$fStr);
        $fStr = eregi_replace("\[\/code\]","</pre><HR></BLOCKQUOTE><BR>",$fStr);

		return;
	}

	#=======================================================================================
	# UBB - list 代碼轉換
	# 說明：列表
	# 格式：[list]代碼片段[/list] , 因爲 list 與 PHP 的 list 函數名衝突，所以方法改名爲 _list
	#=======================================================================================
	Function _list (& $fStr)
	{
		$fStr = eregi_replace("\[\*\]","<LI>",$fStr);
		$fStr = eregi_replace("\[list\]([^\[]*)\[/list\]"," \\1 ",$fStr);
		$fStr = eregi_replace("\[list=1\]([^\[]*)\[/list\]","<OL TYPE=1>\\1</OL>",$fStr);
		$fStr = eregi_replace("\[list=A\]([^\[]*)\[/list\]","<OL TYPE=A>\\1</OL>",$fStr);
		$fStr = eregi_replace("\[list=a\]([^\[]*)\[/list\]","<OL TYPE=A>\\1</OL>",$fStr);

		return;
	}

	#=======================================================================================
	# UBB - quote 代碼轉換
	# 說明：引用
	# 格式：[quote]引用的內容[/quote]
	#=======================================================================================
	Function quote (& $fStr)
	{
		//$fStr = eregi_replace("\[quote\]([^\[]*)\[/quote\]", "<blockquote>QUOTE:<hr>\\1<hr></blockquote>", $fStr);

		$fStr = eregi_replace("\[quote\]","<blockquote>QUOTE:<hr>",$fStr);
		$fStr = eregi_replace("\[/quote\]","<hr></blockquote>",$fStr);

		return;
	}

	#=======================================================================================
	# UBB - img 代碼轉換
	# 說明：圖片（不限制寬度和高度）
	# 格式：[img]圖片位址[/img]
	#=======================================================================================
	Function img (& $fStr)
	{
		$fStr = eregi_replace("\[img\]([^\[]*)\[/img\]","<img src=\"\\1\" border=\"0\">",$fStr);

		return;
	}

	#=======================================================================================
	# UBB - music 代碼轉換
	# 說明：音樂（rm 格式）
	# 格式：[music]音樂位址[/music]
	#=======================================================================================
	Function music (& $fStr)
	{
	    $fStr = eregi_replace("\[music\]([^\[]*)\[/music\]","<EMBED src=\"\\1\" width=\"200\" height=\"40\" type=\"audio/x-pn-realaudio-plugin\" autostart=\"false\" controls=\"ControlPanel\"></EMBED>",$fStr);

		return;
	}

	#=======================================================================================
	# UBB - image 代碼轉換
	# 說明：圖片（限制寬度，高度按寬度比例自動調整）
	# 格式：[image=寬度]圖片位址[/image]
	#=======================================================================================
	Function image (& $fStr)
	{
		$fStr_array		  = explode("[/image]",$fStr);
		$fStr_array_count = count($fStr_array)-1;
	    if ($fStr_array_count > 0){
			 $fStr = "";
			 for ($i=0;$i<$fStr_array_count;$i++){
		         $fStr_array[$i] = $fStr_array[$i]."[/image]";
		         $temp = eregi("\[image=([^\"]*)\]([^\[]*)\[/image\]",$fStr_array[$i],$resArray);
				 if ($resArray[1] <= $this->UbbWidth and eregi("[0-9]",$resArray[1])) {
                    $width  = $resArray[1];
				 }else{
                    $width  = $this->UbbWidth;
				 }
				 $fStr_array[$i] = eregi_replace("\[image=([^\"]*)\]([^\\[]*)\[/image\]","<img src=\"\\2\" border=0 width=\"$width\">",$fStr_array[$i]);
				 $fStr.=$fStr_array[$i];
	  	     }
			 $fStr .= $fStr_array[$fStr_array_count];
		  }

		return;
	}

	#=======================================================================================
	# UBB - flash 代碼轉換
	# 說明：Flash 動畫（限制寬度，高度按寬度比例自動調整）
	# 格式：[flash=寬度,高度]swf文件地址[/flash]
	#=======================================================================================
	Function flash (& $fStr)
	{
		$fStr_array		  = explode("[/flash]",$fStr);
		$fStr_array_count = count($fStr_array)-1;
	    if ($fStr_array_count > 0){
			 $fStr = "";
			 for ($i=0;$i<$fStr_array_count;$i++){
		         $fStr_array[$i] = $fStr_array[$i]."[/flash]";
		         $temp = eregi("\[flash=([^\"]*),([^\"]*)\]([^\[]*)\[/flash\]",$fStr_array[$i],$resArray);
				 if ($resArray[1] <= $this->UbbWidth and eregi("[0-9]",$resArray[1])) {
                    $width  = $resArray[1];
				 }else{
                    $width  = $this->UbbWidth;
				 }
 	             if ($resArray[2] <= $this->UbbHeight and eregi("[0-9]",$resArray[2])) {
					$height = $resArray[2];
				 }else{
					$height = $this->UbbHeight;
				 }

				 $fStr_array[$i] = eregi_replace("\[flash=([^\"]*),([^\"]*)\]([^\[]*)\[/flash\]","<EMBED src=\"\\3\" width=\"$width\" height=\"$height\" type=\"audio/x-pn-realaudio-plugin\" autostart=\"false\" controls=\"ControlPanel\"></EMBED>",$fStr_array[$i]);
				 $fStr.=$fStr_array[$i];
	  	     }
			 $fStr .= $fStr_array[$fStr_array_count];
		  }

		return;
	}

	#=======================================================================================
	# UBB - move 代碼轉換
	# 說明：從右向左移動
	# 格式：[move]要移動的文字[/move]
	#=======================================================================================
	Function move (& $fStr)
	{
        $fStr = eregi_replace("\[move\]([^]]*)\[/move\]", "<MARQUEE width=90% scrollamount=3>\\1</MARQUEE>", $fStr);

		return;
	}

	#=======================================================================================
	# UBB - fly 代碼轉換
	# 說明：左右移動
	# 格式：[fly]要移動的文字[/fly]
	#=======================================================================================
	Function fly (& $fStr)
	{
        $fStr = eregi_replace("\[fly\]([^]]*)\[/fly\]", "<MARQUEE width=90% behavior=\"alternate\" scrollamount=3>\\1</MARQUEE>", $fStr);

		return;
	}
	   
	#=======================================================================================
	# UBB - movie 代碼轉換
	# 說明：影片
	# 格式：[movie]影片文件地址[/movie]
	#=======================================================================================
	Function movie (& $fStr)
	{
		$fStr = eregi_replace("\[movie\]([^]]*)\[/movie\]", "<OBJECT codeBase=http://www.microsoft.com/ntserver/netshow/download/en/nsmp2inf.cab#Version=5,1,51,415 type=application/x-oleobject classid=CLSID:22D6f312-B0F6-11D0-94AB-0080C74C7E95><PARAM NAME=\"AutoStart\" VALUE=\"false\"><PARAM NAME=\"FileName\" VALUE=\"\\1\"></OBJECT>", $fStr);

		return;
	}

	#=======================================================================================
	# 獲取 UBB 工具條的圖片和JS
	#=======================================================================================
	Function getUbbTools ($form = 'form1', $textarea = 'content')
	{
		$UbbTools = "
			<script>
			function addText(para){ // 增加包括表情符號在內的任何UBB代碼
				var para;
				var currentText = document.$form.$textarea.value;
				document.$form.$textarea.value=currentText + para;
				document.$form.$textarea.focus();
				return;
			}

			function bold() { // 加粗顯示
			  var txt;
			  txt=prompt(\"加粗顯示\",\"輸入想要加粗顯示的文本\");     
			  if (txt!=null) {           
				 ubbCode	=\"[b]\"+txt+\"[/b]\";
				 addText(ubbCode);
			  }
			}

			function italicize() { // 設置爲斜體
			  var txt;
			  txt=prompt(\"設置爲斜體\",\"輸入需要設置爲斜體的文本\");     
			  if (txt!=null) {           
				 ubbCode=\"[i]\"+txt+\"[/i]\";
				 addText(ubbCode);
			  }	        
			}

			function underline() { // 下劃線標簽
			  var txt;
			  txt=prompt(\"輸入需加上下劃線的文本\",\"輸入需加上下劃線的文本\");     
			  if (txt!=null) {           
				 ubbCode=\"[u]\"+txt+\"[/u]\";
				 addText(ubbCode);
			  }	        
			}

			function color() { // 文字顔色
			  var txt1;
			  var txt2;
			  txt1 = prompt(\"選擇字體顔色，空著不填寫，按確定進入自定義顔色！\\n1-褐色,2-深藍色,3-紅色,4-粉紅色,5-紫色,6-藍色,7-黃色\",\"\");     
			  while ((txt1!=\"1\") && (txt1!=\"2\") && (txt1!=\"3\") && (txt1!=\"4\") && (txt1!=\"5\") && (txt1!=\"6\") && (txt1!=\"7\") && (txt1!=\"\") && (txt1!=null)) {
				txt1 = prompt(\"錯誤！\\n顔色類型只能輸入'1－7'\",\"\");
			  }
			  if (txt1!=null) {
				if (txt1==\"\"){
				   txt1=prompt(\"輸入自定義顔色，格式如'#000000'\",\"\");     
				}else{
				   switch (txt1){
					 case \"1\":txt1=\"#800000\";break;
					 case \"2\":txt1=\"#000080\";break;
					 case \"3\":txt1=\"#FF0000\";break;
					 case \"4\":txt1=\"#FF00FF\";break;
					 case \"5\":txt1=\"#800080\";break;
					 case \"6\":txt1=\"#0000FF\";break;
					 case \"7\":txt1=\"#FFFF00\";break;
				   }
				}
				if (txt1!=null){
				   txt2=prompt(\"輸入需加顔色的文本\",\"\");     
				   if (txt2!=null){
					  ubbCode=\"[color=\"+txt1+\"]\"+txt2+\"[/color]\";
					  addText(ubbCode);
				   }
				}
			  }	        
			}

			function hr() { // 水平線
			  var txt1;	// 高度
			  var txt2;	// 寬度
			  var txt3;	// 排列
			  var txt4;	// 顔色
			  txt1 = prompt(\"請輸入水平線高度，空著不填寫按 1 象素顯示\",\"1\"); 
			  while ((checkNum(txt1) == 0) || (txt1<1) || (txt1>100)){
				txt1 = prompt(\"錯誤！\\n水平線高度只能是 1-100 之間的數值\",\"\"); 
			  }
			  if (txt1!=null) {
				  txt2 = prompt(\"請輸入水平線寬度，空著不填寫按100%顯示\",\"100\"); 
				  while ((checkNum(txt2) == 0) || (txt2<1) || (txt2>100)){
					txt2 = prompt(\"錯誤！\\n水平線寬度只能是 1-100 之間的數值\",\"\"); 
				  }
			  }
			  if (txt2!=null) {
				  txt3 = prompt(\"請輸入水平線排列方式(left|center|right)，空著不填寫按 left 顯示\",\"left\"); 
				  while ((txt3 != 'left') && (txt3 != 'center') && (txt3 != 'right')){
					txt3 = prompt(\"錯誤！\\n水平線排列方式只能是 left 或 center 或 right\",\"\"); 
				  }
			  }
			  if (txt3!=null) {
				 txt4=prompt(\"請輸入水平線顔色\\n格式如'#000000'，空著不填寫按預設值顯示\",\"\");
				 if (txt4!=null){
					if (txt4==\"\"){
					   txt4=\"#000000\";
					}
					ubbCode= \"[hr=\"+txt1+\",\"+txt2+\",\"+txt3+\",\"+txt4+\"]\";
					addText(ubbCode);
				 }
			   }
			}

			function hyperlink() { // 超級連結標簽
			  var txt1;
			  var txt2;
			  txt1=prompt(\"需顯示的超級連結資訊，如：xxx 的主頁\\n您若希望直接顯示網址，就空著不要填寫，然後按確定按鈕！\",\"\"); 
			  txt2=prompt(\"請輸入超級連結的網址.\",\"輸入HTTP或者FTP地址\");      
			  if (txt2!=null) {
				 if (txt1!=\"\") {           
					ubbCode=\"[url=\"+txt2+\"]\"+txt1+\"[/url]\";
				 } else	{
					ubbCode=\"[url=\"+txt2+\"]\"+txt2+\"[/url]\";
				 }
				 addText(ubbCode);
			  }
			}

			function e_mail() { // Email標簽
			  var txt1;
			  var txt2;
			  txt1=prompt(\"輸入在Email連結中想要顯示的資訊，如：David的Email\\n您若希望直接顯示Email位址，就空著不要填寫，然後點確定按鈕！\",\"\"); 
			  txt2=prompt(\"請輸入Email地址.\",\"name@domain.com\");      
			  if (txt2!=null) {
				 if (txt1!=\"\") {           
					ubbCode=\"[email=\"+txt2+\"]\"+txt1+\"[/email]\";
				 } else	{
					ubbCode=\"[email=\"+txt2+\"]\"+txt2+\"[/email]\";
				 }
				 addText(ubbCode);
			  }
			}

			function move() { // 滾動文字
			  var txt;
			  txt=prompt(\"滾動文字\",\"輸入您要滾動顯示的文字\");     
			  if (txt!=null) {           
				 ubbCode=\"[move]\"+txt+\"[/move]\";
				 addText(ubbCode);
			  }	  
			}

			function showcode() { // 代碼標簽
			  var txt;
			  txt=prompt(\"輸入需要以原始格式顯示的代碼內容\",\"\");     
			  if (txt!=null) {           
				 ubbCode=\"[code]\"+txt+\"[/code]\";
				 addText(ubbCode);
			  }	  
			}

			function list() { // 列表專案
			  var txt;
			  txt=prompt(\"編號類型\\n輸入'A'顯示爲字母編號，'1'顯示爲數位編號，空白顯示爲園點編號。\",\"\");               
			  while ((txt!=\"\") && (txt!=\"A\") && (txt!=\"a\") && (txt!=\"1\") && (txt!=null)) {
					txt=prompt(\"錯誤！\\n編號類型只能是空白，或者輸入'a'和 '1'.\",\"\");               
			  }
			  if (txt!=null) {
				 if (txt==\"\") {
					ubbCode=\"\\r[list]\\r\\n\";
				 } else {
					ubbCode=\"\\r[list=\"+txt+\"]\\r\";
				 } 
				 txt=\"1\";
				 while ((txt!=\"\") && (txt!=null)) {
				   txt=prompt(\"列表專案\\n空白表示結束列表\",\"\"); 
				   if (txt!=\"\") {             
					  ubbCode+=\"[*]\"+txt;
					  addText(ubbCode);
					  ubbCode=\"\\r\";
					}                   
				 } 
				 ubbCode+=\"[/list]\\r\\n\";
				 addText(ubbCode); 
			  }
			}

			function quote() { // 引用
			  var txt;
			  txt=prompt(\"引用\",\"輸入需要引用的內容\");     
			  if (txt!=null) {           
				 ubbCode=\"\\r[quote]\\r\"+txt+\"\\r[/quote]\\r\";
				 addText(ubbCode);
			  }	  
			}

			function page() { // 引頁
			  var txt;
			  txt=prompt(\"引頁\",\"輸入您要顯示的網頁位址\");     
			  if (txt!=null) {           
				 ubbCode=\"[page]\"+txt+\"[/page]\";
				 addText(ubbCode);
			  }	  
			}

			function image() { // 插入圖片
			  var txt;
			  var width;
			  var height;
			  txt=prompt(\"請輸入圖片連結的網址\",\"http://\");    
			  if (txt!=null) {           
				 ubbCode=\"[img]\"+txt+\"[/img]\";
				 addText(ubbCode);
			  }	  
			}

			function flash() { // 插入Flase
			  var txt;
			  var width;
			  var height;
			  txt=prompt(\"請輸入Flash Swf 動畫連結的網址\",\"http://\");    
			  if (txt!=null) {           
				 width = prompt(\"請輸入Flash Swf 動畫寬度(0 - 500)\",\"500\");    
				 while (checkNum(width) == 0) {
				   width = prompt(\"錯誤！\\nFlash Swf 動畫寬度必需是數位(0 - 500)\",\"500\");               
				 }
				 height = prompt(\"請輸入Flash Swf 動畫高度(0 - 500)\",\"500\");    
				 while (checkNum(height) == 0) {
				   height = prompt(\"錯誤！\\nFlash Swf 動畫高度必需是數位(0 - 500)\",\"500\");               
				 }
				 ubbCode=\"[flash=\"+width+\",\"+height+\"]\"+txt+\"[/flash]\";
				 addText(ubbCode);
			  }	  
			}

			function music() {
			  var txt;
			  txt = prompt(\"請輸入音像連結的網址\",\"http://\");    
			  if (txt!=null && txt!=\"\") {
				 ubbCode=\"[music]\"+txt+\"[/music]\";
				 addText(ubbCode);
			  }	
			}

			function checkNum(NUM) { // 檢查 NUM 是不爲數位
				var i,j,strTemp;
				strTemp=\"0123456789\";
				if ( NUM.length == 0)
					return 0
				for (i=0;i<NUM.length;i++)
				{
					j=strTemp.indexOf(NUM.charAt(i));	
					if (j==-1)
					{
						return 0;
					}
				}
				return 1;
			}  
			</script>
			<img src=\"".$this->ToolsImage."\" width=\"345\" height=\"22\" usemap=\"#Map\" border=\"0\"> 
			<map name=\"Map\"> 
				  <area shape=\"rect\" coords=\"-1,1,22,21\" href=\"#bottom\" onClick=bold() alt=\"粗體\">
				  <area onClick=italicize() alt=斜體 shape=\"rect\" coords=\"22,1,45,21\" href=\"#bottom\">
				  <area onClick=underline() alt=下劃線 shape=\"rect\" coords=\"45,0,68,21\" href=\"#bottom\">
				  <area onClick=color() alt=\"文字顔色\" shape=\"rect\" coords=\"68,1,91,21\" href=\"#bottom\">
				  <area onClick=hr() alt=\"水平線\" shape=\"rect\" coords=\"91,1,114,22\" href=\"#bottom\">
				  <area onClick=hyperlink() alt=插入超連結 shape=\"rect\" coords=\"114,1,137,21\" href=\"#bottom\">
				  <area onClick=e_mail() alt=插入Email地址  shape=\"rect\" coords=\"137,0,160,21\" href=\"#bottom\">
				  <area onClick=move() alt=滾動文字 shape=\"rect\" coords=\"160,1,183,20\" href=\"#bottom\">
				  <area onClick=showcode() alt=插入代碼 shape=\"rect\" coords=\"182,0,206,20\" href=\"#bottom\">
				  <area onClick=list() alt=插入編號 shape=\"rect\" coords=\"206,0,230,21\" href=\"#bottom\">
				  <area onClick=quote() alt=插入引用  shape=\"rect\" coords=\"230,1,252,21\" href=\"#bottom\">
				  <area onClick=page() alt=引入頁面 shape=\"rect\" coords=\"252,0,275,21\" href=\"#bottom\">
				 <area onClick=image() alt=插入圖片 shape=\"rect\" coords=\"274,1,298,21\" href=\"#bottom\">
				 <area onClick=flash() alt=插入FLASH動畫 shape=\"rect\" coords=\"298,1,321,20\" href=\"#bottom\">
				 <area onClick=music() alt=插入RM音樂 shape=\"rect\" coords=\"321,0,345,21\" href=\"#bottom\">  
			</map>";
			return $UbbTools;
	}
}
?>
