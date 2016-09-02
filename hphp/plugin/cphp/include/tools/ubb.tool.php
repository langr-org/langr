<?
#============================================================================================================================================================
# 名    称: Ubb v 0.5.24
# 功    能: UBB 语法处理
# 作    者：Arnold, arnold@addwe.com
# 使用举例：
# 例1：获取 UBB 工具条及 Javascript 代码：
#		include_once(FILE_PATH."include/tools/ubb.tool.php");
#		$Ubb = new Tool_Ubb;
#		$Ubb->ToolsImage = FILE_PATH."images/admin/ubbToolsButton.gif";
#		$this->Tmpl['ubbTools'] = autoCharSet($Ubb->getUbbTools($formname, $textareaname));	// 获取 UBB 工具条
#
# 例2：UBB 代码转换
#			include_once(FILE_PATH."include/tools/ubb.tool.php");
#			$Ubb = new Tool_Ubb;
#			$Ubb->convert($this->Tmpl['content']);
#
# 例3：只充许 b,i,u 三个UBB代码的转换
#			include_once(FILE_PATH."include/tools/ubb.tool.php");
#			$Ubb = new Tool_Ubb;
#			$Ubb->allowUbb("b,i,u");
#			$Ubb->convert($this->Tmpl['content']);
#
# 例4：只禁止 b,i,u 三个UBB代码的转换
#			include_once(FILE_PATH."include/tools/ubb.tool.php");
#			$Ubb = new Tool_Ubb;
#			$Ubb->denyUbb("b,i,u");
#			$Ubb->convert($this->Tmpl['content']);
#------------------------------------------------------------------------------------------------------------------------------------------------------------

class Tool_Ubb
{
	#=======================================================================================
	# 外部属性，其值由外部指定。
	#=======================================================================================
	var $webname	= "cphp";
	var $ToolsImage;				// Ubb 工具图片的路径

	var $allowHtml	= false;		// 允许 html ?
	var $AutoNewLine = true;		// 自动换行
	var $AutoSpace   = true;		// 自动转换空格
	var $AutoUbb	 = true;		// 自动转换 UBB 代码
	var $UbbMap	 = array(
				'hr'	=> True,
				'color' => True,
				'b'	=> True,
				'i'	=> True,
				'u'	=> True,
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

	var $UbbWidth	  = 800;		// image 和 flash 的最大宽度
	var $UbbHeight	  = 600;		// flash 的最大高度, image 不需要指定高度，其高度随宽度比例变化

	#=======================================================================================
	# 内部属性，不需要外部指定和修改。
	#=======================================================================================

	#=======================================================================================
	# 字元转换
	#=======================================================================================
	Function convert (& $fStr)
	{
		$this->html($fStr);		// 转换为 html, 如果被支持
		$this->newLine($fStr);		// 自动换行
		$this->space($fStr);		// 自动空格
		$this->ubb($fStr);		// 自动 UBB 转换
		
		return $fStr;
	}

	Function html(& $fStr)
	{
		if ($this->allowHtml) {
			$fStr	= html_entity_decode($fStr);
		}

		return ;
	}

	#=======================================================================================
	# 字元换行，/n 转换为 <br>
	#=======================================================================================
	Function newLine (& $fStr)
	{
		if ($this->AutoNewLine) $fStr = nl2br($fStr);
		return;
	}

	#=======================================================================================
	# 字元空格，两个空白字元 转换为 &nbsp;&nbsp;
	#=======================================================================================
	Function space (& $fStr)
	{
		if ($this->AutoSpace) $fStr = eregi_replace("  ","&nbsp;&nbsp;",$fStr);
		return;
	}

	#=======================================================================================
	# 充许部分 UBB 代码转换，使用此方法，所有 UBB 转换默认为 False，只有指定的 UBB 代码为 True
	# 充许的 UBB 代码之间用","号分隔
	#=======================================================================================
	Function allowUbb ($fPower = "")
	{
		$fPowerArray = split(",",$fPower);
		
		while(list($key,$val) = @each($this->UbbMap)){
			if (!in_array($key,$fPowerArray)) {
				$this->UbbMap[$key] = False;
			} else {
				$this->UbbMap[$key] = True;
			}
		}
		reset($this->UbbMap);

		return;
	}

	#=======================================================================================
	# 拒绝部分 UBB 代码转换，使用此方法，所有 UBB 转换默认为 True，只有指定的 UBB 代码为 False
	# 拒绝的 UBB 代码之间用","号分隔
	#=======================================================================================
	Function denyUbb ($fPower = "")
	{
		$fPowerArray = split(",",$fPower);
		
		while(list($key,$val) = @each($this->UbbMap)){
			if (in_array($key,$fPowerArray)) {
				$this->UbbMap[$key] = False;
			} else {
				$this->UbbMap[$key] = True;
			}
		}
		reset($this->UbbMap);

		return;
	}

	#=======================================================================================
	# UBB代码转换
	#=======================================================================================
	Function ubb (& $fStr)
	{
		if ($this->AutoUbb){
			while(list($key,$val) = @each($this->UbbMap)){
				if (($val)&&(method_exists($this,$key))) {	// 此UBB代码充许被转换并且存在转换的方法
					$this->$key($fStr);
				}
			}
		}
		reset($this->UbbMap);

		return;
	}

	#=======================================================================================
	# UBB - hr 代码转换
	# 说明：水平线
	# 格式：[hr=高度(象素),宽度(百分比),排列,颜色] 例：[hr=80,#0000ff]
	#=======================================================================================
	Function hr (& $fStr)
	{
		$fStr = eregi_replace("\[hr=([0-9]*),([0-9]*),([left|center|right]{4,6}),([#0-9a-z]{7})\]","<hr size=\"\\1\" width=\"\\2%\" align=\"\\3\" color=\"\\4\">",$fStr);
		return;
	}

	#=======================================================================================
	# UBB - color 代码转换
	# 说明：字体颜色
	# 格式：[color=颜色]文字[/color] 例：[color=red]文字[/color]
	#=======================================================================================
	Function color (& $fStr)
	{
		$fStr = preg_replace("/\[color=(.+?)\](.+?)\[\/color\]/is", "<font color='\\1'>\\2</font>", $fStr);
		
		return;
	}

	#=======================================================================================
	# UBB - b 代码转换
	# 说明：字体加粗
	# 格式：[b]需要加粗的文字[/b]
	#=======================================================================================
	Function b (& $fStr)
	{
		$fStr = preg_replace("/\[b\](.+?)\[\/b\]/is", "<b>\\1</b>", $fStr);
		
		return;
	}

	#=======================================================================================
	# UBB - i 代码转换
	# 说明：字体斜体
	# 格式：[i]需要斜体显示的文字[/i]
	#=======================================================================================
	Function i (& $fStr)
	{
		$fStr = preg_replace("/\[i\](.+?)\[\/i\]/is", "<i>\\1</i>", $fStr);

		return;
	}

	#=======================================================================================
	# UBB - u 代码转换
	# 说明：字体加上下划线
	# 格式：[u]需要加下划线显示的文字[/u]
	#=======================================================================================
	Function u (& $fStr)
	{
		$fStr = preg_replace("/\[u\](.+?)\[\/u\]/is", "<u>\\1</u>", $fStr);

		return;
	}

	#=======================================================================================
	# UBB - url 代码转换
	# 说明：连结
	# 格式：[url=http://网址]显示的内容[/url]
	#=======================================================================================
	Function url (& $fStr)
	{
		$fStr = eregi_replace("\[url=http://([^[]*)\]{1}([^[]*)\[\/url\]", "<a href=\"http://\\1\" target=\"_blank\" onClick=\"return confirm(\'".c("您即将开放下列链接, 确定吗? \\\\n\\\\nhttp://\\1\\\\n\\\\n".$this->webname." 提醒您, 不明链接可能包含木马程序")."\')\">\\2</a>", $fStr);
		//$fStr = eregi_replace("\[url=http://([^[]*)\]{1}", "<a href=\"http://\\1\" target=_blank>", $fStr);
	  	//$fStr = eregi_replace("\[\/url\]","</a>",$fStr);
		
		return;
	}

	#=======================================================================================
	# UBB - email 代码转换
	# 说明：连结
	# 格式：[email=电邮位址]显示的内容[/email]
	#=======================================================================================
	Function email (& $fStr)
	{
	    $fStr = eregi_replace("\[email=([^[]*)\]{1}([^[]*)\[\/email\]", "<a href=\"mailto:\\1\">\\2</a>", $fStr);

		//$fStr = eregi_replace("\[email=([^[]*)\]{1}","<a href=\"mailto:\\1\">", $fStr);
	    //$fStr = eregi_replace("\[\/email\]","</a>",$fStr);

		return;
	}

	#=======================================================================================
	# UBB - page 代码转换
	# 说明：引入一个网页
	# 格式：[page]要引入的网页地址[/page]
	#=======================================================================================
	Function page (& $fStr)
	{
	    $fStr = eregi_replace("\[page\]([^\[]*)\[/page\]","<br><iframe frameborder=0 width=90% height=400 scrolling=auto src=\"\\1\"></iframe><br><br>",$fStr);

		return;
	}

	#=======================================================================================
	# UBB - code 代码转换
	# 说明：引入一段代码
	# 格式：[code]代码片段[/code]
	#=======================================================================================
	Function code (& $fStr)
	{
	    //$fStr = eregi_replace("\[code\]([^\[]*)\[\/code\]", "<BLOCKQUOTE><font size=1 face=Arial>CODE:</font><HR><pre>\\1</pre><HR></BLOCKQUOTE><BR>", $fStr);
		
		$fStr = eregi_replace("\[code\]","<BLOCKQUOTE><font size=1 face=Arial>CODE:</font><HR><pre>",$fStr);
        $fStr = eregi_replace("\[\/code\]","</pre><HR></BLOCKQUOTE><BR>",$fStr);

		return;
	}

	#=======================================================================================
	# UBB - list 代码转换
	# 说明：列表
	# 格式：[list]代码片段[/list] , 因为 list 与 PHP 的 list 函数名衝突，所以方法改名为 _list
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
	# UBB - quote 代码转换
	# 说明：引用
	# 格式：[quote]引用的内容[/quote]
	#=======================================================================================
	Function quote (& $fStr)
	{
		//$fStr = eregi_replace("\[quote\]([^\[]*)\[/quote\]", "<blockquote>QUOTE:<hr>\\1<hr></blockquote>", $fStr);

		$fStr = eregi_replace("\[quote\]","<blockquote>QUOTE:<hr>",$fStr);
		$fStr = eregi_replace("\[/quote\]","<hr></blockquote>",$fStr);

		return;
	}

	#=======================================================================================
	# UBB - img 代码转换
	# 说明：图片（不限制宽度和高度）
	# 格式：[img]图片位址[/img]
	#=======================================================================================
	Function img (& $fStr)
	{
		$fStr = eregi_replace("\[img\]([^\[]*)\[/img\]","<img src=\"\\1\" border=\"0\">",$fStr);

		return;
	}

	#=======================================================================================
	# UBB - music 代码转换
	# 说明：音乐（rm 格式）
	# 格式：[music]音乐位址[/music]
	#=======================================================================================
	Function music (& $fStr)
	{
	    $fStr = eregi_replace("\[music\]([^\[]*)\[/music\]","<EMBED src=\"\\1\" width=\"200\" height=\"40\" type=\"audio/x-pn-realaudio-plugin\" autostart=\"false\" controls=\"ControlPanel\"></EMBED>",$fStr);

		return;
	}

	#=======================================================================================
	# UBB - image 代码转换
	# 说明：图片（限制宽度，高度按宽度比例自动调整）
	# 格式：[image=宽度]图片位址[/image]
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
	# UBB - flash 代码转换
	# 说明：Flash 动画（限制宽度，高度按宽度比例自动调整）
	# 格式：[flash=宽度,高度]swf文件地址[/flash]
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
	# UBB - move 代码转换
	# 说明：从右向左移动
	# 格式：[move]要移动的文字[/move]
	#=======================================================================================
	Function move (& $fStr)
	{
        $fStr = eregi_replace("\[move\]([^]]*)\[/move\]", "<MARQUEE width=90% scrollamount=3>\\1</MARQUEE>", $fStr);

		return;
	}

	#=======================================================================================
	# UBB - fly 代码转换
	# 说明：左右移动
	# 格式：[fly]要移动的文字[/fly]
	#=======================================================================================
	Function fly (& $fStr)
	{
        $fStr = eregi_replace("\[fly\]([^]]*)\[/fly\]", "<MARQUEE width=90% behavior=\"alternate\" scrollamount=3>\\1</MARQUEE>", $fStr);

		return;
	}
	   
	#=======================================================================================
	# UBB - movie 代码转换
	# 说明：影片
	# 格式：[movie]影片文件地址[/movie]
	#=======================================================================================
	Function movie (& $fStr)
	{
		$fStr = eregi_replace("\[movie\]([^]]*)\[/movie\]", "<OBJECT codeBase=http://www.microsoft.com/ntserver/netshow/download/en/nsmp2inf.cab#Version=5,1,51,415 type=application/x-oleobject classid=CLSID:22D6f312-B0F6-11D0-94AB-0080C74C7E95><PARAM NAME=\"AutoStart\" VALUE=\"false\"><PARAM NAME=\"FileName\" VALUE=\"\\1\"></OBJECT>", $fStr);

		return;
	}

	#=======================================================================================
	# 获取 UBB 工具条的图片和JS
	#=======================================================================================
	Function getUbbTools ($form = 'form1', $textarea = 'content')
	{
		$UbbTools = "
			<script>
			function addText(para){ // 增加包括表情符号在内的任何UBB代码
				var para;
				var currentText = document.$form.$textarea.value;
				document.$form.$textarea.value=currentText + para;
				document.$form.$textarea.focus();
				return;
			}

			function bold() { // 加粗显示
			  var txt;
			  txt=prompt(\"加粗显示\",\"输入想要加粗显示的文本\");     
			  if (txt!=null) {           
				 ubbCode	=\"[b]\"+txt+\"[/b]\";
				 addText(ubbCode);
			  }
			}

			function italicize() { // 设置为斜体
			  var txt;
			  txt=prompt(\"设置为斜体\",\"输入需要设置为斜体的文本\");     
			  if (txt!=null) {           
				 ubbCode=\"[i]\"+txt+\"[/i]\";
				 addText(ubbCode);
			  }	        
			}

			function underline() { // 下划线标签
			  var txt;
			  txt=prompt(\"输入需加上下划线的文本\",\"输入需加上下划线的文本\");     
			  if (txt!=null) {           
				 ubbCode=\"[u]\"+txt+\"[/u]\";
				 addText(ubbCode);
			  }	        
			}

			function color() { // 文字颜色
			  var txt1;
			  var txt2;
			  txt1 = prompt(\"选择字体颜色，空著不填写，按确定进入自定义颜色！\\n1-褐色,2-深蓝色,3-红色,4-粉红色,5-紫色,6-蓝色,7-黄色\",\"\");     
			  while ((txt1!=\"1\") && (txt1!=\"2\") && (txt1!=\"3\") && (txt1!=\"4\") && (txt1!=\"5\") && (txt1!=\"6\") && (txt1!=\"7\") && (txt1!=\"\") && (txt1!=null)) {
				txt1 = prompt(\"错误！\\n颜色类型只能输入'1－7'\",\"\");
			  }
			  if (txt1!=null) {
				if (txt1==\"\"){
				   txt1=prompt(\"输入自定义颜色，格式如'#000000'\",\"\");     
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
				   txt2=prompt(\"输入需加颜色的文本\",\"\");     
				   if (txt2!=null){
					  ubbCode=\"[color=\"+txt1+\"]\"+txt2+\"[/color]\";
					  addText(ubbCode);
				   }
				}
			  }	        
			}

			function hr() { // 水平线
			  var txt1;	// 高度
			  var txt2;	// 宽度
			  var txt3;	// 排列
			  var txt4;	// 颜色
			  txt1 = prompt(\"请输入水平线高度，空著不填写按 1 象素显示\",\"1\"); 
			  while ((checkNum(txt1) == 0) || (txt1<1) || (txt1>100)){
				txt1 = prompt(\"错误！\\n水平线高度只能是 1-100 之间的数值\",\"\"); 
			  }
			  if (txt1!=null) {
				  txt2 = prompt(\"请输入水平线宽度，空著不填写按100%显示\",\"100\"); 
				  while ((checkNum(txt2) == 0) || (txt2<1) || (txt2>100)){
					txt2 = prompt(\"错误！\\n水平线宽度只能是 1-100 之间的数值\",\"\"); 
				  }
			  }
			  if (txt2!=null) {
				  txt3 = prompt(\"请输入水平线排列方式(left|center|right)，空著不填写按 left 显示\",\"left\"); 
				  while ((txt3 != 'left') && (txt3 != 'center') && (txt3 != 'right')){
					txt3 = prompt(\"错误！\\n水平线排列方式只能是 left 或 center 或 right\",\"\"); 
				  }
			  }
			  if (txt3!=null) {
				 txt4=prompt(\"请输入水平线颜色\\n格式如'#000000'，空著不填写按预设值显示\",\"\");
				 if (txt4!=null){
					if (txt4==\"\"){
					   txt4=\"#000000\";
					}
					ubbCode= \"[hr=\"+txt1+\",\"+txt2+\",\"+txt3+\",\"+txt4+\"]\";
					addText(ubbCode);
				 }
			   }
			}

			function hyperlink() { // 超级连结标签
			  var txt1;
			  var txt2;
			  txt1=prompt(\"需显示的超级连结资讯，如：xxx 的主页\\n您若希望直接显示网址，就空著不要填写，然后按确定按钮！\",\"\"); 
			  txt2=prompt(\"请输入超级连结的网址.\",\"输入HTTP或者FTP地址\");      
			  if (txt2!=null) {
				 if (txt1!=\"\") {           
					ubbCode=\"[url=\"+txt2+\"]\"+txt1+\"[/url]\";
				 } else	{
					ubbCode=\"[url=\"+txt2+\"]\"+txt2+\"[/url]\";
				 }
				 addText(ubbCode);
			  }
			}

			function e_mail() { // Email标签
			  var txt1;
			  var txt2;
			  txt1=prompt(\"输入在Email连结中想要显示的资讯，如：David的Email\\n您若希望直接显示Email位址，就空著不要填写，然后点确定按钮！\",\"\"); 
			  txt2=prompt(\"请输入Email地址.\",\"name@domain.com\");      
			  if (txt2!=null) {
				 if (txt1!=\"\") {           
					ubbCode=\"[email=\"+txt2+\"]\"+txt1+\"[/email]\";
				 } else	{
					ubbCode=\"[email=\"+txt2+\"]\"+txt2+\"[/email]\";
				 }
				 addText(ubbCode);
			  }
			}

			function move() { // 滚动文字
			  var txt;
			  txt=prompt(\"滚动文字\",\"输入您要滚动显示的文字\");     
			  if (txt!=null) {           
				 ubbCode=\"[move]\"+txt+\"[/move]\";
				 addText(ubbCode);
			  }	  
			}

			function showcode() { // 代码标签
			  var txt;
			  txt=prompt(\"输入需要以原始格式显示的代码内容\",\"\");     
			  if (txt!=null) {           
				 ubbCode=\"[code]\"+txt+\"[/code]\";
				 addText(ubbCode);
			  }	  
			}

			function list() { // 列表专案
			  var txt;
			  txt=prompt(\"编号类型\\n输入'A'显示为字母编号，'1'显示为数位编号，空白显示为园点编号。\",\"\");               
			  while ((txt!=\"\") && (txt!=\"A\") && (txt!=\"a\") && (txt!=\"1\") && (txt!=null)) {
					txt=prompt(\"错误！\\n编号类型只能是空白，或者输入'a'和 '1'.\",\"\");               
			  }
			  if (txt!=null) {
				 if (txt==\"\") {
					ubbCode=\"\\r[list]\\r\\n\";
				 } else {
					ubbCode=\"\\r[list=\"+txt+\"]\\r\";
				 } 
				 txt=\"1\";
				 while ((txt!=\"\") && (txt!=null)) {
				   txt=prompt(\"列表专案\\n空白表示结束列表\",\"\"); 
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
			  txt=prompt(\"引用\",\"输入需要引用的内容\");     
			  if (txt!=null) {           
				 ubbCode=\"\\r[quote]\\r\"+txt+\"\\r[/quote]\\r\";
				 addText(ubbCode);
			  }	  
			}

			function page() { // 引页
			  var txt;
			  txt=prompt(\"引页\",\"输入您要显示的网页位址\");     
			  if (txt!=null) {           
				 ubbCode=\"[page]\"+txt+\"[/page]\";
				 addText(ubbCode);
			  }	  
			}

			function image() { // 插入图片
			  var txt;
			  var width;
			  var height;
			  txt=prompt(\"请输入图片连结的网址\",\"http://\");    
			  if (txt!=null) {           
				 ubbCode=\"[img]\"+txt+\"[/img]\";
				 addText(ubbCode);
			  }	  
			}

			function flash() { // 插入Flase
			  var txt;
			  var width;
			  var height;
			  txt=prompt(\"请输入Flash Swf 动画连结的网址\",\"http://\");    
			  if (txt!=null) {           
				 width = prompt(\"请输入Flash Swf 动画宽度(0 - 500)\",\"500\");    
				 while (checkNum(width) == 0) {
				   width = prompt(\"错误！\\nFlash Swf 动画宽度必需是数位(0 - 500)\",\"500\");               
				 }
				 height = prompt(\"请输入Flash Swf 动画高度(0 - 500)\",\"500\");    
				 while (checkNum(height) == 0) {
				   height = prompt(\"错误！\\nFlash Swf 动画高度必需是数位(0 - 500)\",\"500\");               
				 }
				 ubbCode=\"[flash=\"+width+\",\"+height+\"]\"+txt+\"[/flash]\";
				 addText(ubbCode);
			  }	  
			}

			function music() {
			  var txt;
			  txt = prompt(\"请输入音像连结的网址\",\"http://\");    
			  if (txt!=null && txt!=\"\") {
				 ubbCode=\"[music]\"+txt+\"[/music]\";
				 addText(ubbCode);
			  }	
			}

			function checkNum(NUM) { // 检查 NUM 是不为数位
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
				  <area shape=\"rect\" coords=\"-1,1,22,21\" href=\"#bottom\" onClick=bold() alt=\"粗体\">
				  <area onClick=italicize() alt=斜体 shape=\"rect\" coords=\"22,1,45,21\" href=\"#bottom\">
				  <area onClick=underline() alt=下划线 shape=\"rect\" coords=\"45,0,68,21\" href=\"#bottom\">
				  <area onClick=color() alt=\"文字颜色\" shape=\"rect\" coords=\"68,1,91,21\" href=\"#bottom\">
				  <area onClick=hr() alt=\"水平线\" shape=\"rect\" coords=\"91,1,114,22\" href=\"#bottom\">
				  <area onClick=hyperlink() alt=插入超连结 shape=\"rect\" coords=\"114,1,137,21\" href=\"#bottom\">
				  <area onClick=e_mail() alt=插入Email地址  shape=\"rect\" coords=\"137,0,160,21\" href=\"#bottom\">
				  <area onClick=move() alt=滚动文字 shape=\"rect\" coords=\"160,1,183,20\" href=\"#bottom\">
				  <area onClick=showcode() alt=插入代码 shape=\"rect\" coords=\"182,0,206,20\" href=\"#bottom\">
				  <area onClick=list() alt=插入编号 shape=\"rect\" coords=\"206,0,230,21\" href=\"#bottom\">
				  <area onClick=quote() alt=插入引用  shape=\"rect\" coords=\"230,1,252,21\" href=\"#bottom\">
				  <area onClick=page() alt=引入页面 shape=\"rect\" coords=\"252,0,275,21\" href=\"#bottom\">
				 <area onClick=image() alt=插入图片 shape=\"rect\" coords=\"274,1,298,21\" href=\"#bottom\">
				 <area onClick=flash() alt=插入FLASH动画 shape=\"rect\" coords=\"298,1,321,20\" href=\"#bottom\">
				 <area onClick=music() alt=插入RM音乐 shape=\"rect\" coords=\"321,0,345,21\" href=\"#bottom\">  
			</map>";
			return $UbbTools;
	}
}
?>
