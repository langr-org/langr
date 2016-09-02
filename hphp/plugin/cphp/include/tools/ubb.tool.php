<?
#============================================================================================================================================================
# ��    ��: Ubb v 0.5.24
# ��    ��: UBB �﷨����
# ��    �ߣ�Arnold, arnold@addwe.com
# ʹ�þ�����
# ��1����ȡ UBB �������� Javascript ���룺
#		include_once(FILE_PATH."include/tools/ubb.tool.php");
#		$Ubb = new Tool_Ubb;
#		$Ubb->ToolsImage = FILE_PATH."images/admin/ubbToolsButton.gif";
#		$this->Tmpl['ubbTools'] = autoCharSet($Ubb->getUbbTools($formname, $textareaname));	// ��ȡ UBB ������
#
# ��2��UBB ����ת��
#			include_once(FILE_PATH."include/tools/ubb.tool.php");
#			$Ubb = new Tool_Ubb;
#			$Ubb->convert($this->Tmpl['content']);
#
# ��3��ֻ���� b,i,u ����UBB�����ת��
#			include_once(FILE_PATH."include/tools/ubb.tool.php");
#			$Ubb = new Tool_Ubb;
#			$Ubb->allowUbb("b,i,u");
#			$Ubb->convert($this->Tmpl['content']);
#
# ��4��ֻ��ֹ b,i,u ����UBB�����ת��
#			include_once(FILE_PATH."include/tools/ubb.tool.php");
#			$Ubb = new Tool_Ubb;
#			$Ubb->denyUbb("b,i,u");
#			$Ubb->convert($this->Tmpl['content']);
#------------------------------------------------------------------------------------------------------------------------------------------------------------

class Tool_Ubb
{
	#=======================================================================================
	# �ⲿ���ԣ���ֵ���ⲿָ����
	#=======================================================================================
	var $webname	= "cphp";
	var $ToolsImage;				// Ubb ����ͼƬ��·��

	var $allowHtml	= false;		// ���� html ?
	var $AutoNewLine = true;		// �Զ�����
	var $AutoSpace   = true;		// �Զ�ת���ո�
	var $AutoUbb	 = true;		// �Զ�ת�� UBB ����
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

	var $UbbWidth	  = 800;		// image �� flash �������
	var $UbbHeight	  = 600;		// flash �����߶�, image ����Ҫָ���߶ȣ���߶����ȱ����仯

	#=======================================================================================
	# �ڲ����ԣ�����Ҫ�ⲿָ�����޸ġ�
	#=======================================================================================

	#=======================================================================================
	# ��Ԫת��
	#=======================================================================================
	Function convert (& $fStr)
	{
		$this->html($fStr);		// ת��Ϊ html, �����֧��
		$this->newLine($fStr);		// �Զ�����
		$this->space($fStr);		// �Զ��ո�
		$this->ubb($fStr);		// �Զ� UBB ת��
		
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
	# ��Ԫ���У�/n ת��Ϊ <br>
	#=======================================================================================
	Function newLine (& $fStr)
	{
		if ($this->AutoNewLine) $fStr = nl2br($fStr);
		return;
	}

	#=======================================================================================
	# ��Ԫ�ո������հ���Ԫ ת��Ϊ &nbsp;&nbsp;
	#=======================================================================================
	Function space (& $fStr)
	{
		if ($this->AutoSpace) $fStr = eregi_replace("  ","&nbsp;&nbsp;",$fStr);
		return;
	}

	#=======================================================================================
	# ������ UBB ����ת����ʹ�ô˷��������� UBB ת��Ĭ��Ϊ False��ֻ��ָ���� UBB ����Ϊ True
	# ����� UBB ����֮����","�ŷָ�
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
	# �ܾ����� UBB ����ת����ʹ�ô˷��������� UBB ת��Ĭ��Ϊ True��ֻ��ָ���� UBB ����Ϊ False
	# �ܾ��� UBB ����֮����","�ŷָ�
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
	# UBB����ת��
	#=======================================================================================
	Function ubb (& $fStr)
	{
		if ($this->AutoUbb){
			while(list($key,$val) = @each($this->UbbMap)){
				if (($val)&&(method_exists($this,$key))) {	// ��UBB�������ת�����Ҵ���ת���ķ���
					$this->$key($fStr);
				}
			}
		}
		reset($this->UbbMap);

		return;
	}

	#=======================================================================================
	# UBB - hr ����ת��
	# ˵����ˮƽ��
	# ��ʽ��[hr=�߶�(����),���(�ٷֱ�),����,��ɫ] ����[hr=80,#0000ff]
	#=======================================================================================
	Function hr (& $fStr)
	{
		$fStr = eregi_replace("\[hr=([0-9]*),([0-9]*),([left|center|right]{4,6}),([#0-9a-z]{7})\]","<hr size=\"\\1\" width=\"\\2%\" align=\"\\3\" color=\"\\4\">",$fStr);
		return;
	}

	#=======================================================================================
	# UBB - color ����ת��
	# ˵����������ɫ
	# ��ʽ��[color=��ɫ]����[/color] ����[color=red]����[/color]
	#=======================================================================================
	Function color (& $fStr)
	{
		$fStr = preg_replace("/\[color=(.+?)\](.+?)\[\/color\]/is", "<font color='\\1'>\\2</font>", $fStr);
		
		return;
	}

	#=======================================================================================
	# UBB - b ����ת��
	# ˵��������Ӵ�
	# ��ʽ��[b]��Ҫ�Ӵֵ�����[/b]
	#=======================================================================================
	Function b (& $fStr)
	{
		$fStr = preg_replace("/\[b\](.+?)\[\/b\]/is", "<b>\\1</b>", $fStr);
		
		return;
	}

	#=======================================================================================
	# UBB - i ����ת��
	# ˵��������б��
	# ��ʽ��[i]��Ҫб����ʾ������[/i]
	#=======================================================================================
	Function i (& $fStr)
	{
		$fStr = preg_replace("/\[i\](.+?)\[\/i\]/is", "<i>\\1</i>", $fStr);

		return;
	}

	#=======================================================================================
	# UBB - u ����ת��
	# ˵������������»���
	# ��ʽ��[u]��Ҫ���»�����ʾ������[/u]
	#=======================================================================================
	Function u (& $fStr)
	{
		$fStr = preg_replace("/\[u\](.+?)\[\/u\]/is", "<u>\\1</u>", $fStr);

		return;
	}

	#=======================================================================================
	# UBB - url ����ת��
	# ˵��������
	# ��ʽ��[url=http://��ַ]��ʾ������[/url]
	#=======================================================================================
	Function url (& $fStr)
	{
		$fStr = eregi_replace("\[url=http://([^[]*)\]{1}([^[]*)\[\/url\]", "<a href=\"http://\\1\" target=\"_blank\" onClick=\"return confirm(\'".c("������������������, ȷ����? \\\\n\\\\nhttp://\\1\\\\n\\\\n".$this->webname." ������, �������ӿ��ܰ���ľ�����")."\')\">\\2</a>", $fStr);
		//$fStr = eregi_replace("\[url=http://([^[]*)\]{1}", "<a href=\"http://\\1\" target=_blank>", $fStr);
	  	//$fStr = eregi_replace("\[\/url\]","</a>",$fStr);
		
		return;
	}

	#=======================================================================================
	# UBB - email ����ת��
	# ˵��������
	# ��ʽ��[email=����λַ]��ʾ������[/email]
	#=======================================================================================
	Function email (& $fStr)
	{
	    $fStr = eregi_replace("\[email=([^[]*)\]{1}([^[]*)\[\/email\]", "<a href=\"mailto:\\1\">\\2</a>", $fStr);

		//$fStr = eregi_replace("\[email=([^[]*)\]{1}","<a href=\"mailto:\\1\">", $fStr);
	    //$fStr = eregi_replace("\[\/email\]","</a>",$fStr);

		return;
	}

	#=======================================================================================
	# UBB - page ����ת��
	# ˵��������һ����ҳ
	# ��ʽ��[page]Ҫ�������ҳ��ַ[/page]
	#=======================================================================================
	Function page (& $fStr)
	{
	    $fStr = eregi_replace("\[page\]([^\[]*)\[/page\]","<br><iframe frameborder=0 width=90% height=400 scrolling=auto src=\"\\1\"></iframe><br><br>",$fStr);

		return;
	}

	#=======================================================================================
	# UBB - code ����ת��
	# ˵��������һ�δ���
	# ��ʽ��[code]����Ƭ��[/code]
	#=======================================================================================
	Function code (& $fStr)
	{
	    //$fStr = eregi_replace("\[code\]([^\[]*)\[\/code\]", "<BLOCKQUOTE><font size=1 face=Arial>CODE:</font><HR><pre>\\1</pre><HR></BLOCKQUOTE><BR>", $fStr);
		
		$fStr = eregi_replace("\[code\]","<BLOCKQUOTE><font size=1 face=Arial>CODE:</font><HR><pre>",$fStr);
        $fStr = eregi_replace("\[\/code\]","</pre><HR></BLOCKQUOTE><BR>",$fStr);

		return;
	}

	#=======================================================================================
	# UBB - list ����ת��
	# ˵�����б�
	# ��ʽ��[list]����Ƭ��[/list] , ��Ϊ list �� PHP �� list �������nͻ�����Է�������Ϊ _list
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
	# UBB - quote ����ת��
	# ˵��������
	# ��ʽ��[quote]���õ�����[/quote]
	#=======================================================================================
	Function quote (& $fStr)
	{
		//$fStr = eregi_replace("\[quote\]([^\[]*)\[/quote\]", "<blockquote>QUOTE:<hr>\\1<hr></blockquote>", $fStr);

		$fStr = eregi_replace("\[quote\]","<blockquote>QUOTE:<hr>",$fStr);
		$fStr = eregi_replace("\[/quote\]","<hr></blockquote>",$fStr);

		return;
	}

	#=======================================================================================
	# UBB - img ����ת��
	# ˵����ͼƬ�������ƿ�Ⱥ͸߶ȣ�
	# ��ʽ��[img]ͼƬλַ[/img]
	#=======================================================================================
	Function img (& $fStr)
	{
		$fStr = eregi_replace("\[img\]([^\[]*)\[/img\]","<img src=\"\\1\" border=\"0\">",$fStr);

		return;
	}

	#=======================================================================================
	# UBB - music ����ת��
	# ˵�������֣�rm ��ʽ��
	# ��ʽ��[music]����λַ[/music]
	#=======================================================================================
	Function music (& $fStr)
	{
	    $fStr = eregi_replace("\[music\]([^\[]*)\[/music\]","<EMBED src=\"\\1\" width=\"200\" height=\"40\" type=\"audio/x-pn-realaudio-plugin\" autostart=\"false\" controls=\"ControlPanel\"></EMBED>",$fStr);

		return;
	}

	#=======================================================================================
	# UBB - image ����ת��
	# ˵����ͼƬ�����ƿ�ȣ��߶Ȱ���ȱ����Զ�������
	# ��ʽ��[image=���]ͼƬλַ[/image]
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
	# UBB - flash ����ת��
	# ˵����Flash ���������ƿ�ȣ��߶Ȱ���ȱ����Զ�������
	# ��ʽ��[flash=���,�߶�]swf�ļ���ַ[/flash]
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
	# UBB - move ����ת��
	# ˵�������������ƶ�
	# ��ʽ��[move]Ҫ�ƶ�������[/move]
	#=======================================================================================
	Function move (& $fStr)
	{
        $fStr = eregi_replace("\[move\]([^]]*)\[/move\]", "<MARQUEE width=90% scrollamount=3>\\1</MARQUEE>", $fStr);

		return;
	}

	#=======================================================================================
	# UBB - fly ����ת��
	# ˵���������ƶ�
	# ��ʽ��[fly]Ҫ�ƶ�������[/fly]
	#=======================================================================================
	Function fly (& $fStr)
	{
        $fStr = eregi_replace("\[fly\]([^]]*)\[/fly\]", "<MARQUEE width=90% behavior=\"alternate\" scrollamount=3>\\1</MARQUEE>", $fStr);

		return;
	}
	   
	#=======================================================================================
	# UBB - movie ����ת��
	# ˵����ӰƬ
	# ��ʽ��[movie]ӰƬ�ļ���ַ[/movie]
	#=======================================================================================
	Function movie (& $fStr)
	{
		$fStr = eregi_replace("\[movie\]([^]]*)\[/movie\]", "<OBJECT codeBase=http://www.microsoft.com/ntserver/netshow/download/en/nsmp2inf.cab#Version=5,1,51,415 type=application/x-oleobject classid=CLSID:22D6f312-B0F6-11D0-94AB-0080C74C7E95><PARAM NAME=\"AutoStart\" VALUE=\"false\"><PARAM NAME=\"FileName\" VALUE=\"\\1\"></OBJECT>", $fStr);

		return;
	}

	#=======================================================================================
	# ��ȡ UBB ��������ͼƬ��JS
	#=======================================================================================
	Function getUbbTools ($form = 'form1', $textarea = 'content')
	{
		$UbbTools = "
			<script>
			function addText(para){ // ���Ӱ�������������ڵ��κ�UBB����
				var para;
				var currentText = document.$form.$textarea.value;
				document.$form.$textarea.value=currentText + para;
				document.$form.$textarea.focus();
				return;
			}

			function bold() { // �Ӵ���ʾ
			  var txt;
			  txt=prompt(\"�Ӵ���ʾ\",\"������Ҫ�Ӵ���ʾ���ı�\");     
			  if (txt!=null) {           
				 ubbCode	=\"[b]\"+txt+\"[/b]\";
				 addText(ubbCode);
			  }
			}

			function italicize() { // ����Ϊб��
			  var txt;
			  txt=prompt(\"����Ϊб��\",\"������Ҫ����Ϊб����ı�\");     
			  if (txt!=null) {           
				 ubbCode=\"[i]\"+txt+\"[/i]\";
				 addText(ubbCode);
			  }	        
			}

			function underline() { // �»��߱�ǩ
			  var txt;
			  txt=prompt(\"����������»��ߵ��ı�\",\"����������»��ߵ��ı�\");     
			  if (txt!=null) {           
				 ubbCode=\"[u]\"+txt+\"[/u]\";
				 addText(ubbCode);
			  }	        
			}

			function color() { // ������ɫ
			  var txt1;
			  var txt2;
			  txt1 = prompt(\"ѡ��������ɫ����������д����ȷ�������Զ�����ɫ��\\n1-��ɫ,2-����ɫ,3-��ɫ,4-�ۺ�ɫ,5-��ɫ,6-��ɫ,7-��ɫ\",\"\");     
			  while ((txt1!=\"1\") && (txt1!=\"2\") && (txt1!=\"3\") && (txt1!=\"4\") && (txt1!=\"5\") && (txt1!=\"6\") && (txt1!=\"7\") && (txt1!=\"\") && (txt1!=null)) {
				txt1 = prompt(\"����\\n��ɫ����ֻ������'1��7'\",\"\");
			  }
			  if (txt1!=null) {
				if (txt1==\"\"){
				   txt1=prompt(\"�����Զ�����ɫ����ʽ��'#000000'\",\"\");     
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
				   txt2=prompt(\"���������ɫ���ı�\",\"\");     
				   if (txt2!=null){
					  ubbCode=\"[color=\"+txt1+\"]\"+txt2+\"[/color]\";
					  addText(ubbCode);
				   }
				}
			  }	        
			}

			function hr() { // ˮƽ��
			  var txt1;	// �߶�
			  var txt2;	// ���
			  var txt3;	// ����
			  var txt4;	// ��ɫ
			  txt1 = prompt(\"������ˮƽ�߸߶ȣ���������д�� 1 ������ʾ\",\"1\"); 
			  while ((checkNum(txt1) == 0) || (txt1<1) || (txt1>100)){
				txt1 = prompt(\"����\\nˮƽ�߸߶�ֻ���� 1-100 ֮�����ֵ\",\"\"); 
			  }
			  if (txt1!=null) {
				  txt2 = prompt(\"������ˮƽ�߿�ȣ���������д��100%��ʾ\",\"100\"); 
				  while ((checkNum(txt2) == 0) || (txt2<1) || (txt2>100)){
					txt2 = prompt(\"����\\nˮƽ�߿��ֻ���� 1-100 ֮�����ֵ\",\"\"); 
				  }
			  }
			  if (txt2!=null) {
				  txt3 = prompt(\"������ˮƽ�����з�ʽ(left|center|right)����������д�� left ��ʾ\",\"left\"); 
				  while ((txt3 != 'left') && (txt3 != 'center') && (txt3 != 'right')){
					txt3 = prompt(\"����\\nˮƽ�����з�ʽֻ���� left �� center �� right\",\"\"); 
				  }
			  }
			  if (txt3!=null) {
				 txt4=prompt(\"������ˮƽ����ɫ\\n��ʽ��'#000000'����������д��Ԥ��ֵ��ʾ\",\"\");
				 if (txt4!=null){
					if (txt4==\"\"){
					   txt4=\"#000000\";
					}
					ubbCode= \"[hr=\"+txt1+\",\"+txt2+\",\"+txt3+\",\"+txt4+\"]\";
					addText(ubbCode);
				 }
			   }
			}

			function hyperlink() { // ���������ǩ
			  var txt1;
			  var txt2;
			  txt1=prompt(\"����ʾ�ĳ���������Ѷ���磺xxx ����ҳ\\n����ϣ��ֱ����ʾ��ַ���Ϳ�����Ҫ��д��Ȼ��ȷ����ť��\",\"\"); 
			  txt2=prompt(\"�����볬���������ַ.\",\"����HTTP����FTP��ַ\");      
			  if (txt2!=null) {
				 if (txt1!=\"\") {           
					ubbCode=\"[url=\"+txt2+\"]\"+txt1+\"[/url]\";
				 } else	{
					ubbCode=\"[url=\"+txt2+\"]\"+txt2+\"[/url]\";
				 }
				 addText(ubbCode);
			  }
			}

			function e_mail() { // Email��ǩ
			  var txt1;
			  var txt2;
			  txt1=prompt(\"������Email��������Ҫ��ʾ����Ѷ���磺David��Email\\n����ϣ��ֱ����ʾEmailλַ���Ϳ�����Ҫ��д��Ȼ���ȷ����ť��\",\"\"); 
			  txt2=prompt(\"������Email��ַ.\",\"name@domain.com\");      
			  if (txt2!=null) {
				 if (txt1!=\"\") {           
					ubbCode=\"[email=\"+txt2+\"]\"+txt1+\"[/email]\";
				 } else	{
					ubbCode=\"[email=\"+txt2+\"]\"+txt2+\"[/email]\";
				 }
				 addText(ubbCode);
			  }
			}

			function move() { // ��������
			  var txt;
			  txt=prompt(\"��������\",\"������Ҫ������ʾ������\");     
			  if (txt!=null) {           
				 ubbCode=\"[move]\"+txt+\"[/move]\";
				 addText(ubbCode);
			  }	  
			}

			function showcode() { // �����ǩ
			  var txt;
			  txt=prompt(\"������Ҫ��ԭʼ��ʽ��ʾ�Ĵ�������\",\"\");     
			  if (txt!=null) {           
				 ubbCode=\"[code]\"+txt+\"[/code]\";
				 addText(ubbCode);
			  }	  
			}

			function list() { // �б�ר��
			  var txt;
			  txt=prompt(\"�������\\n����'A'��ʾΪ��ĸ��ţ�'1'��ʾΪ��λ��ţ��հ���ʾΪ԰���š�\",\"\");               
			  while ((txt!=\"\") && (txt!=\"A\") && (txt!=\"a\") && (txt!=\"1\") && (txt!=null)) {
					txt=prompt(\"����\\n�������ֻ���ǿհף���������'a'�� '1'.\",\"\");               
			  }
			  if (txt!=null) {
				 if (txt==\"\") {
					ubbCode=\"\\r[list]\\r\\n\";
				 } else {
					ubbCode=\"\\r[list=\"+txt+\"]\\r\";
				 } 
				 txt=\"1\";
				 while ((txt!=\"\") && (txt!=null)) {
				   txt=prompt(\"�б�ר��\\n�հױ�ʾ�����б�\",\"\"); 
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

			function quote() { // ����
			  var txt;
			  txt=prompt(\"����\",\"������Ҫ���õ�����\");     
			  if (txt!=null) {           
				 ubbCode=\"\\r[quote]\\r\"+txt+\"\\r[/quote]\\r\";
				 addText(ubbCode);
			  }	  
			}

			function page() { // ��ҳ
			  var txt;
			  txt=prompt(\"��ҳ\",\"������Ҫ��ʾ����ҳλַ\");     
			  if (txt!=null) {           
				 ubbCode=\"[page]\"+txt+\"[/page]\";
				 addText(ubbCode);
			  }	  
			}

			function image() { // ����ͼƬ
			  var txt;
			  var width;
			  var height;
			  txt=prompt(\"������ͼƬ�������ַ\",\"http://\");    
			  if (txt!=null) {           
				 ubbCode=\"[img]\"+txt+\"[/img]\";
				 addText(ubbCode);
			  }	  
			}

			function flash() { // ����Flase
			  var txt;
			  var width;
			  var height;
			  txt=prompt(\"������Flash Swf �����������ַ\",\"http://\");    
			  if (txt!=null) {           
				 width = prompt(\"������Flash Swf �������(0 - 500)\",\"500\");    
				 while (checkNum(width) == 0) {
				   width = prompt(\"����\\nFlash Swf ������ȱ�������λ(0 - 500)\",\"500\");               
				 }
				 height = prompt(\"������Flash Swf �����߶�(0 - 500)\",\"500\");    
				 while (checkNum(height) == 0) {
				   height = prompt(\"����\\nFlash Swf �����߶ȱ�������λ(0 - 500)\",\"500\");               
				 }
				 ubbCode=\"[flash=\"+width+\",\"+height+\"]\"+txt+\"[/flash]\";
				 addText(ubbCode);
			  }	  
			}

			function music() {
			  var txt;
			  txt = prompt(\"�����������������ַ\",\"http://\");    
			  if (txt!=null && txt!=\"\") {
				 ubbCode=\"[music]\"+txt+\"[/music]\";
				 addText(ubbCode);
			  }	
			}

			function checkNum(NUM) { // ��� NUM �ǲ�Ϊ��λ
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
				  <area shape=\"rect\" coords=\"-1,1,22,21\" href=\"#bottom\" onClick=bold() alt=\"����\">
				  <area onClick=italicize() alt=б�� shape=\"rect\" coords=\"22,1,45,21\" href=\"#bottom\">
				  <area onClick=underline() alt=�»��� shape=\"rect\" coords=\"45,0,68,21\" href=\"#bottom\">
				  <area onClick=color() alt=\"������ɫ\" shape=\"rect\" coords=\"68,1,91,21\" href=\"#bottom\">
				  <area onClick=hr() alt=\"ˮƽ��\" shape=\"rect\" coords=\"91,1,114,22\" href=\"#bottom\">
				  <area onClick=hyperlink() alt=���볬���� shape=\"rect\" coords=\"114,1,137,21\" href=\"#bottom\">
				  <area onClick=e_mail() alt=����Email��ַ  shape=\"rect\" coords=\"137,0,160,21\" href=\"#bottom\">
				  <area onClick=move() alt=�������� shape=\"rect\" coords=\"160,1,183,20\" href=\"#bottom\">
				  <area onClick=showcode() alt=������� shape=\"rect\" coords=\"182,0,206,20\" href=\"#bottom\">
				  <area onClick=list() alt=������ shape=\"rect\" coords=\"206,0,230,21\" href=\"#bottom\">
				  <area onClick=quote() alt=��������  shape=\"rect\" coords=\"230,1,252,21\" href=\"#bottom\">
				  <area onClick=page() alt=����ҳ�� shape=\"rect\" coords=\"252,0,275,21\" href=\"#bottom\">
				 <area onClick=image() alt=����ͼƬ shape=\"rect\" coords=\"274,1,298,21\" href=\"#bottom\">
				 <area onClick=flash() alt=����FLASH���� shape=\"rect\" coords=\"298,1,321,20\" href=\"#bottom\">
				 <area onClick=music() alt=����RM���� shape=\"rect\" coords=\"321,0,345,21\" href=\"#bottom\">  
			</map>";
			return $UbbTools;
	}
}
?>
