<?
#============================================================================================================================================================
# ��    �Q: Ubb v 0.5.24
# ��    ��: UBB �Z��̎��
# ��    �ߣ�Arnold, arnold@addwe.com
# ʹ���e����
# ��1���@ȡ UBB ���ߗl�� Javascript ���a��
#		include_once(FILE_PATH."include/tools/ubb.tool.php");
#		$Ubb = new Tool_Ubb;
#		$Ubb->ToolsImage = FILE_PATH."images/admin/ubbToolsButton.gif";
#		$this->Tmpl['ubbTools'] = autoCharSet($Ubb->getUbbTools($formname, $textareaname));	// �@ȡ UBB ���ߗl
#
# ��2��UBB ���a�D�Q
#			include_once(FILE_PATH."include/tools/ubb.tool.php");
#			$Ubb = new Tool_Ubb;
#			$Ubb->convert($this->Tmpl['content']);
#
# ��3��ֻ���S b,i,u ����UBB���a���D�Q
#			include_once(FILE_PATH."include/tools/ubb.tool.php");
#			$Ubb = new Tool_Ubb;
#			$Ubb->allowUbb("b,i,u");
#			$Ubb->convert($this->Tmpl['content']);
#
# ��4��ֻ��ֹ b,i,u ����UBB���a���D�Q
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
	var $ToolsImage;				// Ubb ���߈DƬ��·��

	var $AutoNewLine = true;		// �ԄӓQ��
	var $AutoSpace   = true;		// �Ԅ��D�Q�ո�
	var $AutoUbb	 = true;		// �Ԅ��D�Q UBB ���a
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

	var $UbbWidth	  = 800;		// image �� flash ����󌒶�
	var $UbbHeight	  = 600;		// flash �����߶�, image ����Ҫָ���߶ȣ���߶��S���ȱ���׃��

	#=======================================================================================
	# �Ȳ����ԣ�����Ҫ�ⲿָ�����޸ġ�
	#=======================================================================================

	#=======================================================================================
	# ��Ԫ�D�Q
	#=======================================================================================
	Function convert (& $fStr)
	{
		$this->newLine($fStr);		// �ԄӓQ��
		$this->space($fStr);		// �Ԅӿո�
		$this->ubb($fStr);			// �Ԅ� UBB �D�Q
		
		return $fStr;
	}

	#=======================================================================================
	# ��Ԫ�Q�У�/n �D�Q�� <br>
	#=======================================================================================
	Function newLine (& $fStr)
	{
		if ($this->AutoNewLine) $fStr = nl2br($fStr);
		return;
	}

	#=======================================================================================
	# ��Ԫ�ո񣬃ɂ��հ���Ԫ �D�Q�� &nbsp;&nbsp;
	#=======================================================================================
	Function space (& $fStr)
	{
		if ($this->AutoSpace) $fStr = eregi_replace("  ","&nbsp;&nbsp;",$fStr);
		return;
	}

	#=======================================================================================
	# ���S���� UBB ���a�D�Q��ʹ�ô˷��������� UBB �D�QĬ�J�� False��ֻ��ָ���� UBB ���a�� True
	# ���S�� UBB ���a֮�g��","̖�ָ�
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
	# �ܽ^���� UBB ���a�D�Q��ʹ�ô˷��������� UBB �D�QĬ�J�� True��ֻ��ָ���� UBB ���a�� False
	# �ܽ^�� UBB ���a֮�g��","̖�ָ�
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
	# UBB���a�D�Q
	#=======================================================================================
	Function ubb (& $fStr)
	{
		if ($this->AutoUbb){
			while(list($key,$val) = @each($this->UbbMap)){
				if (($val)&&(method_exists($this,$key))) {	// ��UBB���a���S���D�Q�K�Ҵ����D�Q�ķ���
					$this->$key($fStr);
				}
			}
		}
		reset($this->UbbMap);

		return;
	}

	#=======================================================================================
	# UBB - hr ���a�D�Q
	# �f����ˮƽ��
	# ��ʽ��[hr=�߶�(����),����(�ٷֱ�),����,�ɫ] ����[hr=80,#0000ff]
	#=======================================================================================
	Function hr (& $fStr)
	{
		$fStr = eregi_replace("\[hr=([0-9]*),([0-9]*),([left|center|right]{4,6}),([#0-9a-z]{7})\]","<hr size=\"\\1\" width=\"\\2%\" align=\"\\3\" color=\"\\4\">",$fStr);
		return;
	}

	#=======================================================================================
	# UBB - color ���a�D�Q
	# �f�������w�ɫ
	# ��ʽ��[color=�ɫ]����[/color] ����[color=red]����[/color]
	#=======================================================================================
	Function color (& $fStr)
	{
		$fStr = preg_replace("/\[color=(.+?)\](.+?)\[\/color\]/is", "<font color='\\1'>\\2</font>", $fStr);
		
		return;
	}

	#=======================================================================================
	# UBB - b ���a�D�Q
	# �f�������w�Ӵ�
	# ��ʽ��[b]��Ҫ�Ӵֵ�����[/b]
	#=======================================================================================
	Function b (& $fStr)
	{
		$fStr = preg_replace("/\[b\](.+?)\[\/b\]/is", "<b>\\1</b>", $fStr);
		
		return;
	}

	#=======================================================================================
	# UBB - i ���a�D�Q
	# �f�������wб�w
	# ��ʽ��[i]��Ҫб�w�@ʾ������[/i]
	#=======================================================================================
	Function i (& $fStr)
	{
		$fStr = preg_replace("/\[i\](.+?)\[\/i\]/is", "<i>\\1</i>", $fStr);

		return;
	}

	#=======================================================================================
	# UBB - u ���a�D�Q
	# �f�������w��������
	# ��ʽ��[u]��Ҫ�������@ʾ������[/u]
	#=======================================================================================
	Function u (& $fStr)
	{
		$fStr = preg_replace("/\[u\](.+?)\[\/u\]/is", "<u>\\1</u>", $fStr);

		return;
	}

	#=======================================================================================
	# UBB - url ���a�D�Q
	# �f�����B�Y
	# ��ʽ��[url=http://�Wַ]�@ʾ�ă���[/url]
	#=======================================================================================
	Function url (& $fStr)
	{
		$fStr = eregi_replace("\[url=http://([^[]*)\]{1}([^[]*)\[\/url\]", "<a href=\"http://\\1\" target=\"_blank\" onClick=\"return confirm(\'".c("�������_�������B�Y��ȷ���᣿\\\\n\\\\nhttp://\\1\\\\n\\\\n�����B�Y���ܰ���ľ�R��ʽ")."\')\">\\2</a>", $fStr);
		//$fStr = eregi_replace("\[url=http://([^[]*)\]{1}", "<a href=\"http://\\1\" target=_blank>", $fStr);
	  	//$fStr = eregi_replace("\[\/url\]","</a>",$fStr);
		
		return;
	}

	#=======================================================================================
	# UBB - email ���a�D�Q
	# �f�����B�Y
	# ��ʽ��[email=��]λַ]�@ʾ�ă���[/email]
	#=======================================================================================
	Function email (& $fStr)
	{
	    $fStr = eregi_replace("\[email=([^[]*)\]{1}([^[]*)\[\/email\]", "<a href=\"mailto:\\1\">\\2</a>", $fStr);

		//$fStr = eregi_replace("\[email=([^[]*)\]{1}","<a href=\"mailto:\\1\">", $fStr);
	    //$fStr = eregi_replace("\[\/email\]","</a>",$fStr);

		return;
	}

	#=======================================================================================
	# UBB - page ���a�D�Q
	# �f��������һ���W�
	# ��ʽ��[page]Ҫ����ľW퓵�ַ[/page]
	#=======================================================================================
	Function page (& $fStr)
	{
	    $fStr = eregi_replace("\[page\]([^\[]*)\[/page\]","<br><iframe frameborder=0 width=90% height=400 scrolling=auto src=\"\\1\"></iframe><br><br>",$fStr);

		return;
	}

	#=======================================================================================
	# UBB - code ���a�D�Q
	# �f��������һ�δ��a
	# ��ʽ��[code]���aƬ��[/code]
	#=======================================================================================
	Function code (& $fStr)
	{
	    //$fStr = eregi_replace("\[code\]([^\[]*)\[\/code\]", "<BLOCKQUOTE><font size=1 face=Arial>CODE:</font><HR><pre>\\1</pre><HR></BLOCKQUOTE><BR>", $fStr);
		
		$fStr = eregi_replace("\[code\]","<BLOCKQUOTE><font size=1 face=Arial>CODE:</font><HR><pre>",$fStr);
        $fStr = eregi_replace("\[\/code\]","</pre><HR></BLOCKQUOTE><BR>",$fStr);

		return;
	}

	#=======================================================================================
	# UBB - list ���a�D�Q
	# �f�����б�
	# ��ʽ��[list]���aƬ��[/list] , �� list �c PHP �� list �������nͻ�����Է��������� _list
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
	# UBB - quote ���a�D�Q
	# �f��������
	# ��ʽ��[quote]���õă���[/quote]
	#=======================================================================================
	Function quote (& $fStr)
	{
		//$fStr = eregi_replace("\[quote\]([^\[]*)\[/quote\]", "<blockquote>QUOTE:<hr>\\1<hr></blockquote>", $fStr);

		$fStr = eregi_replace("\[quote\]","<blockquote>QUOTE:<hr>",$fStr);
		$fStr = eregi_replace("\[/quote\]","<hr></blockquote>",$fStr);

		return;
	}

	#=======================================================================================
	# UBB - img ���a�D�Q
	# �f�����DƬ�������ƌ��Ⱥ͸߶ȣ�
	# ��ʽ��[img]�DƬλַ[/img]
	#=======================================================================================
	Function img (& $fStr)
	{
		$fStr = eregi_replace("\[img\]([^\[]*)\[/img\]","<img src=\"\\1\" border=\"0\">",$fStr);

		return;
	}

	#=======================================================================================
	# UBB - music ���a�D�Q
	# �f����������rm ��ʽ��
	# ��ʽ��[music]����λַ[/music]
	#=======================================================================================
	Function music (& $fStr)
	{
	    $fStr = eregi_replace("\[music\]([^\[]*)\[/music\]","<EMBED src=\"\\1\" width=\"200\" height=\"40\" type=\"audio/x-pn-realaudio-plugin\" autostart=\"false\" controls=\"ControlPanel\"></EMBED>",$fStr);

		return;
	}

	#=======================================================================================
	# UBB - image ���a�D�Q
	# �f�����DƬ�����ƌ��ȣ��߶Ȱ����ȱ����Ԅ��{����
	# ��ʽ��[image=����]�DƬλַ[/image]
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
	# UBB - flash ���a�D�Q
	# �f����Flash �Ӯ������ƌ��ȣ��߶Ȱ����ȱ����Ԅ��{����
	# ��ʽ��[flash=����,�߶�]swf�ļ���ַ[/flash]
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
	# UBB - move ���a�D�Q
	# �f�������������Ƅ�
	# ��ʽ��[move]Ҫ�Ƅӵ�����[/move]
	#=======================================================================================
	Function move (& $fStr)
	{
        $fStr = eregi_replace("\[move\]([^]]*)\[/move\]", "<MARQUEE width=90% scrollamount=3>\\1</MARQUEE>", $fStr);

		return;
	}

	#=======================================================================================
	# UBB - fly ���a�D�Q
	# �f���������Ƅ�
	# ��ʽ��[fly]Ҫ�Ƅӵ�����[/fly]
	#=======================================================================================
	Function fly (& $fStr)
	{
        $fStr = eregi_replace("\[fly\]([^]]*)\[/fly\]", "<MARQUEE width=90% behavior=\"alternate\" scrollamount=3>\\1</MARQUEE>", $fStr);

		return;
	}
	   
	#=======================================================================================
	# UBB - movie ���a�D�Q
	# �f����ӰƬ
	# ��ʽ��[movie]ӰƬ�ļ���ַ[/movie]
	#=======================================================================================
	Function movie (& $fStr)
	{
		$fStr = eregi_replace("\[movie\]([^]]*)\[/movie\]", "<OBJECT codeBase=http://www.microsoft.com/ntserver/netshow/download/en/nsmp2inf.cab#Version=5,1,51,415 type=application/x-oleobject classid=CLSID:22D6f312-B0F6-11D0-94AB-0080C74C7E95><PARAM NAME=\"AutoStart\" VALUE=\"false\"><PARAM NAME=\"FileName\" VALUE=\"\\1\"></OBJECT>", $fStr);

		return;
	}

	#=======================================================================================
	# �@ȡ UBB ���ߗl�ĈDƬ��JS
	#=======================================================================================
	Function getUbbTools ($form = 'form1', $textarea = 'content')
	{
		$UbbTools = "
			<script>
			function addText(para){ // ���Ӱ��������̖�ڃȵ��κ�UBB���a
				var para;
				var currentText = document.$form.$textarea.value;
				document.$form.$textarea.value=currentText + para;
				document.$form.$textarea.focus();
				return;
			}

			function bold() { // �Ӵ��@ʾ
			  var txt;
			  txt=prompt(\"�Ӵ��@ʾ\",\"ݔ����Ҫ�Ӵ��@ʾ���ı�\");     
			  if (txt!=null) {           
				 ubbCode	=\"[b]\"+txt+\"[/b]\";
				 addText(ubbCode);
			  }
			}

			function italicize() { // �O�à�б�w
			  var txt;
			  txt=prompt(\"�O�à�б�w\",\"ݔ����Ҫ�O�à�б�w���ı�\");     
			  if (txt!=null) {           
				 ubbCode=\"[i]\"+txt+\"[/i]\";
				 addText(ubbCode);
			  }	        
			}

			function underline() { // �����˺�
			  var txt;
			  txt=prompt(\"ݔ��������������ı�\",\"ݔ��������������ı�\");     
			  if (txt!=null) {           
				 ubbCode=\"[u]\"+txt+\"[/u]\";
				 addText(ubbCode);
			  }	        
			}

			function color() { // �����ɫ
			  var txt1;
			  var txt2;
			  txt1 = prompt(\"�x�����w�ɫ��������������_���M���Զ��x�ɫ��\\n1-��ɫ,2-���{ɫ,3-�tɫ,4-�ۼtɫ,5-��ɫ,6-�{ɫ,7-�Sɫ\",\"\");     
			  while ((txt1!=\"1\") && (txt1!=\"2\") && (txt1!=\"3\") && (txt1!=\"4\") && (txt1!=\"5\") && (txt1!=\"6\") && (txt1!=\"7\") && (txt1!=\"\") && (txt1!=null)) {
				txt1 = prompt(\"�e�`��\\n�ɫ���ֻ��ݔ��'1��7'\",\"\");
			  }
			  if (txt1!=null) {
				if (txt1==\"\"){
				   txt1=prompt(\"ݔ���Զ��x�ɫ����ʽ��'#000000'\",\"\");     
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
				   txt2=prompt(\"ݔ������ɫ���ı�\",\"\");     
				   if (txt2!=null){
					  ubbCode=\"[color=\"+txt1+\"]\"+txt2+\"[/color]\";
					  addText(ubbCode);
				   }
				}
			  }	        
			}

			function hr() { // ˮƽ��
			  var txt1;	// �߶�
			  var txt2;	// ����
			  var txt3;	// ����
			  var txt4;	// �ɫ
			  txt1 = prompt(\"Ոݔ��ˮƽ���߶ȣ���������� 1 �����@ʾ\",\"1\"); 
			  while ((checkNum(txt1) == 0) || (txt1<1) || (txt1>100)){
				txt1 = prompt(\"�e�`��\\nˮƽ���߶�ֻ���� 1-100 ֮�g�Ĕ�ֵ\",\"\"); 
			  }
			  if (txt1!=null) {
				  txt2 = prompt(\"Ոݔ��ˮƽ�����ȣ����������100%�@ʾ\",\"100\"); 
				  while ((checkNum(txt2) == 0) || (txt2<1) || (txt2>100)){
					txt2 = prompt(\"�e�`��\\nˮƽ������ֻ���� 1-100 ֮�g�Ĕ�ֵ\",\"\"); 
				  }
			  }
			  if (txt2!=null) {
				  txt3 = prompt(\"Ոݔ��ˮƽ�����з�ʽ(left|center|right)����������� left �@ʾ\",\"left\"); 
				  while ((txt3 != 'left') && (txt3 != 'center') && (txt3 != 'right')){
					txt3 = prompt(\"�e�`��\\nˮƽ�����з�ʽֻ���� left �� center �� right\",\"\"); 
				  }
			  }
			  if (txt3!=null) {
				 txt4=prompt(\"Ոݔ��ˮƽ���ɫ\\n��ʽ��'#000000'������������A�Oֵ�@ʾ\",\"\");
				 if (txt4!=null){
					if (txt4==\"\"){
					   txt4=\"#000000\";
					}
					ubbCode= \"[hr=\"+txt1+\",\"+txt2+\",\"+txt3+\",\"+txt4+\"]\";
					addText(ubbCode);
				 }
			   }
			}

			function hyperlink() { // �����B�Y�˺�
			  var txt1;
			  var txt2;
			  txt1=prompt(\"���@ʾ�ĳ����B�Y�YӍ���磺xxx �����\\n����ϣ��ֱ���@ʾ�Wַ���Ϳ�����Ҫ���Ȼ�ᰴ�_�����o��\",\"\"); 
			  txt2=prompt(\"Ոݔ�볬���B�Y�ľWַ.\",\"ݔ��HTTP����FTP��ַ\");      
			  if (txt2!=null) {
				 if (txt1!=\"\") {           
					ubbCode=\"[url=\"+txt2+\"]\"+txt1+\"[/url]\";
				 } else	{
					ubbCode=\"[url=\"+txt2+\"]\"+txt2+\"[/url]\";
				 }
				 addText(ubbCode);
			  }
			}

			function e_mail() { // Email�˺�
			  var txt1;
			  var txt2;
			  txt1=prompt(\"ݔ����Email�B�Y����Ҫ�@ʾ���YӍ���磺David��Email\\n����ϣ��ֱ���@ʾEmailλַ���Ϳ�����Ҫ���Ȼ���c�_�����o��\",\"\"); 
			  txt2=prompt(\"Ոݔ��Email��ַ.\",\"name@domain.com\");      
			  if (txt2!=null) {
				 if (txt1!=\"\") {           
					ubbCode=\"[email=\"+txt2+\"]\"+txt1+\"[/email]\";
				 } else	{
					ubbCode=\"[email=\"+txt2+\"]\"+txt2+\"[/email]\";
				 }
				 addText(ubbCode);
			  }
			}

			function move() { // �L������
			  var txt;
			  txt=prompt(\"�L������\",\"ݔ����Ҫ�L���@ʾ������\");     
			  if (txt!=null) {           
				 ubbCode=\"[move]\"+txt+\"[/move]\";
				 addText(ubbCode);
			  }	  
			}

			function showcode() { // ���a�˺�
			  var txt;
			  txt=prompt(\"ݔ����Ҫ��ԭʼ��ʽ�@ʾ�Ĵ��a����\",\"\");     
			  if (txt!=null) {           
				 ubbCode=\"[code]\"+txt+\"[/code]\";
				 addText(ubbCode);
			  }	  
			}

			function list() { // �б팣��
			  var txt;
			  txt=prompt(\"��̖���\\nݔ��'A'�@ʾ����ĸ��̖��'1'�@ʾ����λ��̖���հ��@ʾ���@�c��̖��\",\"\");               
			  while ((txt!=\"\") && (txt!=\"A\") && (txt!=\"a\") && (txt!=\"1\") && (txt!=null)) {
					txt=prompt(\"�e�`��\\n��̖���ֻ���ǿհף�����ݔ��'a'�� '1'.\",\"\");               
			  }
			  if (txt!=null) {
				 if (txt==\"\") {
					ubbCode=\"\\r[list]\\r\\n\";
				 } else {
					ubbCode=\"\\r[list=\"+txt+\"]\\r\";
				 } 
				 txt=\"1\";
				 while ((txt!=\"\") && (txt!=null)) {
				   txt=prompt(\"�б팣��\\n�հױ�ʾ�Y���б�\",\"\"); 
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
			  txt=prompt(\"����\",\"ݔ����Ҫ���õă���\");     
			  if (txt!=null) {           
				 ubbCode=\"\\r[quote]\\r\"+txt+\"\\r[/quote]\\r\";
				 addText(ubbCode);
			  }	  
			}

			function page() { // ���
			  var txt;
			  txt=prompt(\"���\",\"ݔ����Ҫ�@ʾ�ľW�λַ\");     
			  if (txt!=null) {           
				 ubbCode=\"[page]\"+txt+\"[/page]\";
				 addText(ubbCode);
			  }	  
			}

			function image() { // ����DƬ
			  var txt;
			  var width;
			  var height;
			  txt=prompt(\"Ոݔ��DƬ�B�Y�ľWַ\",\"http://\");    
			  if (txt!=null) {           
				 ubbCode=\"[img]\"+txt+\"[/img]\";
				 addText(ubbCode);
			  }	  
			}

			function flash() { // ����Flase
			  var txt;
			  var width;
			  var height;
			  txt=prompt(\"Ոݔ��Flash Swf �Ӯ��B�Y�ľWַ\",\"http://\");    
			  if (txt!=null) {           
				 width = prompt(\"Ոݔ��Flash Swf �Ӯ�����(0 - 500)\",\"500\");    
				 while (checkNum(width) == 0) {
				   width = prompt(\"�e�`��\\nFlash Swf �Ӯ����ȱ����ǔ�λ(0 - 500)\",\"500\");               
				 }
				 height = prompt(\"Ոݔ��Flash Swf �Ӯ��߶�(0 - 500)\",\"500\");    
				 while (checkNum(height) == 0) {
				   height = prompt(\"�e�`��\\nFlash Swf �Ӯ��߶ȱ����ǔ�λ(0 - 500)\",\"500\");               
				 }
				 ubbCode=\"[flash=\"+width+\",\"+height+\"]\"+txt+\"[/flash]\";
				 addText(ubbCode);
			  }	  
			}

			function music() {
			  var txt;
			  txt = prompt(\"Ոݔ�������B�Y�ľWַ\",\"http://\");    
			  if (txt!=null && txt!=\"\") {
				 ubbCode=\"[music]\"+txt+\"[/music]\";
				 addText(ubbCode);
			  }	
			}

			function checkNum(NUM) { // �z�� NUM �ǲ�����λ
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
				  <area shape=\"rect\" coords=\"-1,1,22,21\" href=\"#bottom\" onClick=bold() alt=\"���w\">
				  <area onClick=italicize() alt=б�w shape=\"rect\" coords=\"22,1,45,21\" href=\"#bottom\">
				  <area onClick=underline() alt=���� shape=\"rect\" coords=\"45,0,68,21\" href=\"#bottom\">
				  <area onClick=color() alt=\"�����ɫ\" shape=\"rect\" coords=\"68,1,91,21\" href=\"#bottom\">
				  <area onClick=hr() alt=\"ˮƽ��\" shape=\"rect\" coords=\"91,1,114,22\" href=\"#bottom\">
				  <area onClick=hyperlink() alt=���볬�B�Y shape=\"rect\" coords=\"114,1,137,21\" href=\"#bottom\">
				  <area onClick=e_mail() alt=����Email��ַ  shape=\"rect\" coords=\"137,0,160,21\" href=\"#bottom\">
				  <area onClick=move() alt=�L������ shape=\"rect\" coords=\"160,1,183,20\" href=\"#bottom\">
				  <area onClick=showcode() alt=������a shape=\"rect\" coords=\"182,0,206,20\" href=\"#bottom\">
				  <area onClick=list() alt=���뾎̖ shape=\"rect\" coords=\"206,0,230,21\" href=\"#bottom\">
				  <area onClick=quote() alt=��������  shape=\"rect\" coords=\"230,1,252,21\" href=\"#bottom\">
				  <area onClick=page() alt=������� shape=\"rect\" coords=\"252,0,275,21\" href=\"#bottom\">
				 <area onClick=image() alt=����DƬ shape=\"rect\" coords=\"274,1,298,21\" href=\"#bottom\">
				 <area onClick=flash() alt=����FLASH�Ӯ� shape=\"rect\" coords=\"298,1,321,20\" href=\"#bottom\">
				 <area onClick=music() alt=����RM���� shape=\"rect\" coords=\"321,0,345,21\" href=\"#bottom\">  
			</map>";
			return $UbbTools;
	}
}
?>
