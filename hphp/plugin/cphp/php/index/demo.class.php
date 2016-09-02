<?php
/**
 * 入口：index
 * 架构系统的 Demo 程序
 * 
 * by:
 * $Id: demo.class.php 8 2009-10-20 10:05:34Z langr $
 */
class Demo extends Index_Public
{
	/**
	 * 数据库内容读取并通过模板显示的演示一
	 * show 是 Action 前缀，代表此 Action 是被 GET 方法调用
	 * 本演示通过调用子模板来实现模板循环
	 *
	 */
	Function showDbTmpl1()
	{
		$q   = $this->loadDB();		 // 载入MySQL对象
		$sql = "SELECT * from demo"; // 定义SQL语句
		$q->query($sql);
		$tmplListArray		 = $this->getSubTmplFileContent("list"); //获取一个子模板
		$this->Tmpl['subContent'] = $tmplListArray[0];		
		while ($q->nextRecord()) {
			$this->Tmpl = array_merge ($this->Tmpl, $q->record); // 把 $q->record 合并到 $this->Tmpl 中，同名键值自动覆盖
			$this->Tmpl['subContent'] .= $this->TmplVarReplace($tmplListArray[1]);
		}
		$this->Tmpl['subContent'] .= $tmplListArray[2];
		$this->display();
		return;
	}
	/**
	 * 数据库内容读取并通过模板显示的演示二
	 * 本演示通过数组来实现模板循环
	 */
	Function showDbTmpl2()
	{
		$q   = $this->loadDB();		 // 载入MySQL对象
		$sql = "SELECT * from demo"; // 定义SQL语句
		$q->query($sql);
		$i = 0;
		while ($q->nextRecord()) {
			$this->TDat[$i] = array_merge ($this->Tmpl, $q->record); // 把 $q->record 合并到 $this->Tmpl 中，同名键值自动覆盖
			$i++;
		}
		$this->display();
		return;
	}
	/**
	 * 分页显示的演示
	 */
	Function showPage()
	{
		tool('page'); // 載入關定義分頁類
		$p	= new Tool_Page($this);
		
		$q   = $this->loadDB();		 // 载入MySQL对象
		if ($_GET['totalRows']){
			$p->TotalRows = $_GET['totalRows'];
		}else{
			$sql = "SELECT count(id) FROM demo";
			$q->query($sql);
			$q->nextRecord();
		    $p->TotalRows = $q->record[0];
		}
		$p->ListRows = 2; // 每页显示两行

		if ($_GET['firstRow']){
			$p->FirstRow = $_GET['firstRow'];
		}else{
			$p->FirstRow = 0;
		}
	    $p->Parameter  = ""; // 分页要传递的变量，Module和Action自动传递
	    $this->Tmpl['page'] = $p->getPage();

		$sql = "SELECT * from demo LIMIT ".$p->FirstRow.",".$p->ListRows;
		$q->query($sql);
		$i = 0;

		/* 取子模板及其变量 */
		$tmplListArray = $this->getSubTmplFileContent("list");
		$this->Tmpl['subContent'] = $this->TmplVarReplace($tmplListArray[0]);

		while ($q->nextRecord()) {
			$this->Tmpl = array_merge ($this->Tmpl, $q->record); // 把 $q->record 合并到 $this->Tmpl 中，同名键值自动覆盖
			$i++;

			$this->Tmpl['subContent'] .= $this->TmplVarReplace($tmplListArray[1]);
		}
		$this->Tmpl['subContent'] .= $this->TmplVarReplace($tmplListArray[2]);

		$this->display();
		return;
	}
	/**
	 * 分页显示的演示二
	 */
	Function showPage2()
	{
		tool('page'); // 載入關定義分頁類
		$p	= new Tool_Page($this);
		$p->Sql = 'SELECT count(id) FROM demo';
		$p->ListRows = 2; // 每页显示两行
		$p->Parameter  = ""; // 分页要传递的变量，Module和Action自动传递
		$this->Tmpl['page'] = $p->fromSqlGetPage();
		##
		$q   = $this->loadDB();		 // 载入MySQL对象
		$sql = "SELECT * from demo";
		$this->TDat = $q->selectAll($sql, $p->FirstRow, $p->ListRows);
		$this->display();
		return;
	}
	/**
	 * 表单提交的演示
	 */
	Function showForm()
	{
		$this->Tmpl['noteTagStart'] = "<!--";
		$this->Tmpl['noteTagEnd'] = "-->";
		$this->display();
		return;
	}
	/**
	 * 处理表单提交的演示
	 * do 是 Action 前缀，代表此 Action 是被 POST 方法调用
	 */
	Function doForm()
	{
		if (empty($_POST['name'])) {
			$this->ErrMsg = "错误：请填写内容。";
			$this->promptMsg();
		}
		$this->Tmpl['name'] = trueHtml($_POST['name']);
		$this->Tmpl['id']   = $_POST['id'];
		$this->Tmpl['noteTagStart'] = "";
		$this->Tmpl['noteTagEnd'] = "";
		$this->display();
		return;
	}

	/***
	 * 使用 GD 库函数 创建验证码图片
	 */
	function showVerifyCode()
	{
		$type = ($_GET['t'])?($_GET['t']):'png';
		$width = ($_GET['w'])?($_GET['w']):54;
		$height = ($_GET['h'])?($_GET['h']):22;

		Header("Content-type: image/".$type);

		srand((double)microtime()*1000000);
		$randval = sprintf("%04d", rand(1,9999));
		$_SESSION['verifyCode'] = $randval;

		$im = @imagecreate($width,$height);
		$r = Array(225,255,255,223);
		$g = Array(225,236,237,255);
		$b = Array(225,236,166,125);

		$key = rand(0,3);

		$backColor = ImageColorAllocate($im, $r[$key],$g[$key],$b[$key]); 
		$borderColor = ImageColorAllocate($im, 0, 0, 0);				  
		$pointColor = ImageColorAllocate($im, 0, 255, 255);				  

		imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);
		@imagerectangle($im, 0, 0, $width-1, $height-1, $borderColor);
		$stringColor = ImageColorAllocate($im, 255,51,153);
		for($i=0;$i<=10;$i++){
			$pointX = rand(2,$width-2);
			$pointY = rand(2,$height-2);
			@imagesetpixel($im, $pointX, $pointY, $pointColor);
		}

		imagestring($im, 5, 8, 3, $randval, $stringColor);
		$ImageFun='Image'.$type;
		$ImageFun($im);
		@ImageDestroy($im);	
		
		return;
	}
}
?>
