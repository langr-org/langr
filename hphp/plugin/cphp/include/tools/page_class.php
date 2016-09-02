<script language="JavaScript" type="text/JavaScript">
<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
//-->
</script>

<?PHP

Class page {
	
	function makePageList() {
		$pageTotal = ceil($this->totalRows/$this->listRows);
		for ($i=1;$i<=$pageTotal;$i++) {
			$nextRow = ($i-1)*$this->listRows;
			$url = "?firstRow=".$nextRow."&totalRows=".$this->totalRows;
			if ( ($i-1) == floor($this->firstRow/$this->listRows) ) {
				$pageList .= "<option value=".$url." selected>".$i."</option>";
			}else{
				$pageList .= "<option value=".$url.">".$i."</option>";
			}
		}
		$this->list = "<form><select onChange=\"MM_jumpMenu('parent',this,0)\">".$pageList."</select></form>";
	}

}

?>