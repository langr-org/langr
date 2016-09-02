<?php
/**
 * ��퓹����
 *
 * @author Arnold 2007/04/30
 * @package Tool
 */
class Tool_Page
{
	/**
	 * ��ȡ�ܹ�����ʵ��
	 *
	 * @var object
	 */
	var $M	= '';

	/**
	 * ��ҳ��������
	 * $FirstRow	:	// ��ʼ��
	 * $ListRows	:	// �б�����
	 * $Parameter	:	// ҳ����תʱҪ���Ĳ���
	 * $TotalPages	:	// ��ҳ��
	 * $TotalRows	:	// ������
	 * $NowPage		:	// ��ǰҳ��
	 * $CoolPages	:	// ��ҳ��������ҳ��
	 * $RollPage	:	// ��ҳ��ÿҳ��ʾ��ҳ��
	 * 
	 * @var array
	 */
	var $FirstRow      = 0;
	var $ListRows      = 30;
	var $Parameter     = '';
	var $TotalPages    = 0;
	var $TotalRows     = 0;	
	var $NowPage       = 0;	
	var $CoolPages     = 0;	
	var $RollPage      = 10;	
	
	/**
	 * ��ȡ��ҳ���ϵ�Sql���
	 *
	 * @var string
	 */
	var $Sql	= '';

	/**
	 * ���캯����������ʵ��ʱ���ܹ����󴫵ݸ� $this->M,������üܹ��Ķ���ͷ���
	 *
	 * @param unknown_type $m
	 * @return Tool_Page
	 */
	function Tool_Page(& $m){
		$this->M = & $m;
		return;
	}
	/**
	 * �@ȡ����Y��
	 *
	 * @return string ����Y��
	 */
	function getPage(){
		$this->Parameter .= "&module=".MODULE_NAME."&action=".ACTION_NAME;
		if(0 == $this->TotalRows) {
			return;
		}
 	    $this->TotalPages = ceil($this->TotalRows/$this->ListRows); 	//��ҳ��
		$this->CoolPages  = ceil($this->TotalPages/$this->RollPage);
	    $this->NowPage	  = floor($this->FirstRow/$this->ListRows+1);    //��ǰҳ��
		$nowCoolPage	  = ceil($this->NowPage/$this->RollPage);

		//���·�ҳ�ִ�
		$upRow   = $this->FirstRow-$this->ListRows;
		$downRow = $this->FirstRow+$this->ListRows;
		if ($upRow>=0){
			$upPage="[<a href='".PHP_SCRIPT."?firstRow=$upRow&totalRows=".$this->TotalRows."&".$this->Parameter."'>��һҳ</a>]";
		}else{
			$upPage="";
		}
		if ($downRow<$this->TotalRows){
			$downPage="[<a href='".PHP_SCRIPT."?firstRow=$downRow&totalRows=".$this->TotalRows."&".$this->Parameter."'>��һҳ</a>]";
		}else{
			$downPage="";
		}
		// << < > >>
		if($nowCoolPage == 1){
			$theFirst = "";
			$prePage = "";
		}else{
			$preRow =  ($this->RollPage*($nowCoolPage-1)-1)*$this->ListRows;
			$prePage = "[<a href='".PHP_SCRIPT."?firstRow=$preRow&totalRows=".$this->TotalRows."&".$this->Parameter."' title='��".$this->RollPage."ҳ'>��".$this->RollPage."ҳ</a>]";
			$theFirst = "[<a href='".PHP_SCRIPT."?firstRow=0&totalRows=".$this->TotalRows."&".$this->Parameter."' title='��һҳ'>��һҳ</a>]";
		}
		if($nowCoolPage == $this->CoolPages){
			$nextPage = "";
			$theEnd="";
		}else{
			$nextRow = ($nowCoolPage*$this->RollPage)*$this->ListRows;
			$theEndRow = ($this->TotalPages-1)*$this->ListRows;
			$nextPage = "[<a href='".PHP_SCRIPT."?firstRow=$nextRow&totalRows=".$this->TotalRows."&".$this->Parameter."' title='��".$this->RollPage."ҳ'>��".$this->RollPage."ҳ</a>]";
			$theEnd = "[<a href='".PHP_SCRIPT."?firstRow=$theEndRow&totalRows=".$this->TotalRows."&".$this->Parameter."' title='���һҳ'>���һҳ</a>]";
		}
		// 1 2 3 4 5
		$linkPage = "";
		for($i=1;$i<=$this->RollPage;$i++){
			$page=($nowCoolPage-1)*$this->RollPage+$i;
			$rows=($page-1)*$this->ListRows;
			if($page!=$this->NowPage){
				if($page<=$this->TotalPages){
					$linkPage .= "&nbsp;<a href='".PHP_SCRIPT."?firstRow=$rows&totalRows=".$this->TotalRows."&".$this->Parameter."'>&nbsp;".$page."&nbsp;</a>";
				}else{
					break;
				}
			}else{
				if($this->TotalPages != 1){
					$linkPage .= " [".$page."]";
				}
			}
		}
		$pageStr = $upPage." ".$downPage." ��".$this->TotalPages."ҳ ".$theFirst." ".$prePage." ".$linkPage." ".$nextPage." ".$theEnd; 
		$pageStr = c($pageStr);
		return $pageStr;
	}
	/**
	 * ����SQL��䣬�@ȡ����Y�ϣ��������Ҫ��ǰָ�� Sql��ListRows��Parameter���Ե�ֵ
	 *
	 * @return string ����Y��
	 */
	function fromSqlGetPage(){
		if (empty($this->Sql)) return;
		$q   = $this->M->loadDB();		 // ����MySQL����
		if ($_GET['totalRows']){
			$this->TotalRows = $_GET['totalRows'];
		}else{
			$sql = $this->Sql;
			$q->query($sql);
			$q->nextRecord();
		    $this->TotalRows = $q->record[0];
		}
		if ($_GET['firstRow']){
			$this->FirstRow = $_GET['firstRow'];
		}else{
			$this->FirstRow = 0;
		}
	    return  $this->getPage();
	}
}
?>