<?php
/**
 * 分頁工具類
 *
 * @author Arnold 2007/04/30
 * @package Tool
 */
class Tool_Page
{
	/**
	 * 获取架构对象实体
	 *
	 * @var object
	 */
	var $M	= '';

	/**
	 * 分页操作属性
	 * $FirstRow	:	// 起始行
	 * $ListRows	:	// 列表行数
	 * $Parameter	:	// 页数跳转时要带的参数
	 * $TotalPages	:	// 总页数
	 * $TotalRows	:	// 总行数
	 * $NowPage		:	// 当前页数
	 * $CoolPages	:	// 分页的栏的总页数
	 * $RollPage	:	// 分页栏每页显示的页数
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
	 * 获取分页资料的Sql语句
	 *
	 * @var string
	 */
	var $Sql	= '';

	/**
	 * 构造函数，定义类实体时将架构对象传递给 $this->M,方便调用架构的对象和方法
	 *
	 * @param unknown_type $m
	 * @return Tool_Page
	 */
	function Tool_Page(& $m){
		$this->M = & $m;
		return;
	}
	/**
	 * 獲取分頁資料
	 *
	 * @return string 分頁資料
	 */
	function getPage(){
		$this->Parameter .= "&module=".MODULE_NAME."&action=".ACTION_NAME;
		if(0 == $this->TotalRows) {
			return;
		}
 	    $this->TotalPages = ceil($this->TotalRows/$this->ListRows); 	//总页数
		$this->CoolPages  = ceil($this->TotalPages/$this->RollPage);
	    $this->NowPage	  = floor($this->FirstRow/$this->ListRows+1);    //当前页号
		$nowCoolPage	  = ceil($this->NowPage/$this->RollPage);

		//上下翻页字串
		$upRow   = $this->FirstRow-$this->ListRows;
		$downRow = $this->FirstRow+$this->ListRows;
		if ($upRow>=0){
			$upPage="[<a href='".PHP_SCRIPT."?firstRow=$upRow&totalRows=".$this->TotalRows."&".$this->Parameter."'>上一页</a>]";
		}else{
			$upPage="";
		}
		if ($downRow<$this->TotalRows){
			$downPage="[<a href='".PHP_SCRIPT."?firstRow=$downRow&totalRows=".$this->TotalRows."&".$this->Parameter."'>下一页</a>]";
		}else{
			$downPage="";
		}
		// << < > >>
		if($nowCoolPage == 1){
			$theFirst = "";
			$prePage = "";
		}else{
			$preRow =  ($this->RollPage*($nowCoolPage-1)-1)*$this->ListRows;
			$prePage = "[<a href='".PHP_SCRIPT."?firstRow=$preRow&totalRows=".$this->TotalRows."&".$this->Parameter."' title='上".$this->RollPage."页'>上".$this->RollPage."页</a>]";
			$theFirst = "[<a href='".PHP_SCRIPT."?firstRow=0&totalRows=".$this->TotalRows."&".$this->Parameter."' title='第一页'>第一页</a>]";
		}
		if($nowCoolPage == $this->CoolPages){
			$nextPage = "";
			$theEnd="";
		}else{
			$nextRow = ($nowCoolPage*$this->RollPage)*$this->ListRows;
			$theEndRow = ($this->TotalPages-1)*$this->ListRows;
			$nextPage = "[<a href='".PHP_SCRIPT."?firstRow=$nextRow&totalRows=".$this->TotalRows."&".$this->Parameter."' title='下".$this->RollPage."页'>下".$this->RollPage."页</a>]";
			$theEnd = "[<a href='".PHP_SCRIPT."?firstRow=$theEndRow&totalRows=".$this->TotalRows."&".$this->Parameter."' title='最后一页'>最后一页</a>]";
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
		$pageStr = $upPage." ".$downPage." 共".$this->TotalPages."页 ".$theFirst." ".$prePage." ".$linkPage." ".$nextPage." ".$theEnd; 
		$pageStr = c($pageStr);
		return $pageStr;
	}
	/**
	 * 根据SQL语句，獲取分頁資料，请根据需要提前指定 Sql、ListRows、Parameter属性的值
	 *
	 * @return string 分頁資料
	 */
	function fromSqlGetPage(){
		if (empty($this->Sql)) return;
		$q   = $this->M->loadDB();		 // 载入MySQL对象
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