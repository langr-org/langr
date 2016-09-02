<?php
class Index_Public extends Cortrol
{
	var	$isDealerAdmin	= false;
	/***
	 * 构造函数，定义类实体时自动执行所有操作
	 */
	function Index_Public()
	{
		$this->Cortrol();					/* 控制类初始化 */
		$this->isLogin();					/* 执行一个公共 Action */
		$this->publicFun();
		$moduleAction = ACTION_PREFIX.ucfirst(ACTION_NAME); 
		$this->$moduleAction();

		return;
	}

	/***
	 * 检查经销商是否登录
	 */
	function isLogin()
	{
		if (MODULE_NAME == "index") return;
		if (!$_SESSION['ag_adminId']) {
			$this->ErrMsg	= "错误: 非法访问!";
			$this->UrlJump	= "./agency.php";
			$this->PromptMsg();
		}

		return;
	}

	/***
	 * 显示 twmj 经销商编号
	 */
	function publicFun()
	{
		$this->Tmpl['admin'] = $_SESSION['ag_admin'];

		$q	= $this->loadDB();
		$sql	= "select Name from dealerinfo where Id='".$_SESSION['ag_adminId']."'";
		$q->query($sql);
		$q->nextRecord();
		$this->Tmpl['adminName'] = $q->record['Name'];
		$q->free();

		if ($this->Tmpl['admin'] == 'A00001') {
			$this->Tmpl['dealerAdmin'] = c("<div class=\"bb\"><a href=\"?module=twmj&action=deaNew\">審核新經銷商</a></div>\r\n<div class=\"bb\"><a href=\"?module=twmj&action=importEdition\">導入版本號</a></div>\r\n<div class=\"bb\"><a href=\"?module=twmj&action=deaEdition\">分配版本號</a></div>");
			$this->isDealerAdmin	= True;
		}

		return;
	}
}
?>
