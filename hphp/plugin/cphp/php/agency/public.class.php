<?php
class Index_Public extends Cortrol
{
	var	$isDealerAdmin	= false;
	/***
	 * ���캯����������ʵ��ʱ�Զ�ִ�����в���
	 */
	function Index_Public()
	{
		$this->Cortrol();					/* �������ʼ�� */
		$this->isLogin();					/* ִ��һ������ Action */
		$this->publicFun();
		$moduleAction = ACTION_PREFIX.ucfirst(ACTION_NAME); 
		$this->$moduleAction();

		return;
	}

	/***
	 * ��龭�����Ƿ��¼
	 */
	function isLogin()
	{
		if (MODULE_NAME == "index") return;
		if (!$_SESSION['ag_adminId']) {
			$this->ErrMsg	= "����: �Ƿ�����!";
			$this->UrlJump	= "./agency.php";
			$this->PromptMsg();
		}

		return;
	}

	/***
	 * ��ʾ twmj �����̱��
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
			$this->Tmpl['dealerAdmin'] = c("<div class=\"bb\"><a href=\"?module=twmj&action=deaNew\">�����½��N��</a></div>\r\n<div class=\"bb\"><a href=\"?module=twmj&action=importEdition\">����汾̖</a></div>\r\n<div class=\"bb\"><a href=\"?module=twmj&action=deaEdition\">����汾̖</a></div>");
			$this->isDealerAdmin	= True;
		}

		return;
	}
}
?>
