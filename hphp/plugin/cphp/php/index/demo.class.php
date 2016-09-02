<?php
/**
 * ��ڣ�index
 * �ܹ�ϵͳ�� Demo ����
 * 
 * by:
 * $Id: demo.class.php 8 2009-10-20 10:05:34Z langr $
 */
class Demo extends Index_Public
{
	/**
	 * ���ݿ����ݶ�ȡ��ͨ��ģ����ʾ����ʾһ
	 * show �� Action ǰ׺������� Action �Ǳ� GET ��������
	 * ����ʾͨ��������ģ����ʵ��ģ��ѭ��
	 *
	 */
	Function showDbTmpl1()
	{
		$q   = $this->loadDB();		 // ����MySQL����
		$sql = "SELECT * from demo"; // ����SQL���
		$q->query($sql);
		$tmplListArray		 = $this->getSubTmplFileContent("list"); //��ȡһ����ģ��
		$this->Tmpl['subContent'] = $tmplListArray[0];		
		while ($q->nextRecord()) {
			$this->Tmpl = array_merge ($this->Tmpl, $q->record); // �� $q->record �ϲ��� $this->Tmpl �У�ͬ����ֵ�Զ�����
			$this->Tmpl['subContent'] .= $this->TmplVarReplace($tmplListArray[1]);
		}
		$this->Tmpl['subContent'] .= $tmplListArray[2];
		$this->display();
		return;
	}
	/**
	 * ���ݿ����ݶ�ȡ��ͨ��ģ����ʾ����ʾ��
	 * ����ʾͨ��������ʵ��ģ��ѭ��
	 */
	Function showDbTmpl2()
	{
		$q   = $this->loadDB();		 // ����MySQL����
		$sql = "SELECT * from demo"; // ����SQL���
		$q->query($sql);
		$i = 0;
		while ($q->nextRecord()) {
			$this->TDat[$i] = array_merge ($this->Tmpl, $q->record); // �� $q->record �ϲ��� $this->Tmpl �У�ͬ����ֵ�Զ�����
			$i++;
		}
		$this->display();
		return;
	}
	/**
	 * ��ҳ��ʾ����ʾ
	 */
	Function showPage()
	{
		tool('page'); // �d���P���x����
		$p	= new Tool_Page($this);
		
		$q   = $this->loadDB();		 // ����MySQL����
		if ($_GET['totalRows']){
			$p->TotalRows = $_GET['totalRows'];
		}else{
			$sql = "SELECT count(id) FROM demo";
			$q->query($sql);
			$q->nextRecord();
		    $p->TotalRows = $q->record[0];
		}
		$p->ListRows = 2; // ÿҳ��ʾ����

		if ($_GET['firstRow']){
			$p->FirstRow = $_GET['firstRow'];
		}else{
			$p->FirstRow = 0;
		}
	    $p->Parameter  = ""; // ��ҳҪ���ݵı�����Module��Action�Զ�����
	    $this->Tmpl['page'] = $p->getPage();

		$sql = "SELECT * from demo LIMIT ".$p->FirstRow.",".$p->ListRows;
		$q->query($sql);
		$i = 0;

		/* ȡ��ģ�弰����� */
		$tmplListArray = $this->getSubTmplFileContent("list");
		$this->Tmpl['subContent'] = $this->TmplVarReplace($tmplListArray[0]);

		while ($q->nextRecord()) {
			$this->Tmpl = array_merge ($this->Tmpl, $q->record); // �� $q->record �ϲ��� $this->Tmpl �У�ͬ����ֵ�Զ�����
			$i++;

			$this->Tmpl['subContent'] .= $this->TmplVarReplace($tmplListArray[1]);
		}
		$this->Tmpl['subContent'] .= $this->TmplVarReplace($tmplListArray[2]);

		$this->display();
		return;
	}
	/**
	 * ��ҳ��ʾ����ʾ��
	 */
	Function showPage2()
	{
		tool('page'); // �d���P���x����
		$p	= new Tool_Page($this);
		$p->Sql = 'SELECT count(id) FROM demo';
		$p->ListRows = 2; // ÿҳ��ʾ����
		$p->Parameter  = ""; // ��ҳҪ���ݵı�����Module��Action�Զ�����
		$this->Tmpl['page'] = $p->fromSqlGetPage();
		##
		$q   = $this->loadDB();		 // ����MySQL����
		$sql = "SELECT * from demo";
		$this->TDat = $q->selectAll($sql, $p->FirstRow, $p->ListRows);
		$this->display();
		return;
	}
	/**
	 * ���ύ����ʾ
	 */
	Function showForm()
	{
		$this->Tmpl['noteTagStart'] = "<!--";
		$this->Tmpl['noteTagEnd'] = "-->";
		$this->display();
		return;
	}
	/**
	 * ������ύ����ʾ
	 * do �� Action ǰ׺������� Action �Ǳ� POST ��������
	 */
	Function doForm()
	{
		if (empty($_POST['name'])) {
			$this->ErrMsg = "��������д���ݡ�";
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
	 * ʹ�� GD �⺯�� ������֤��ͼƬ
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
