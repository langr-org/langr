<?php
class Index_Public extends Cortrol
{
	/***
	 * ���캯����������ʵ��ʱ�Զ�ִ�����в���
	 */
	function Index_Public()
	{
		$this->Cortrol();					/* �������ʼ�� */
		$this->isLogin();					/* ִ��һ������ Action */
		$this->publicFun();
		$this->pubCookieAuth(86400000);				/* 1000 �� */
		$moduleAction = ACTION_PREFIX.ucfirst(ACTION_NAME); 
		$this->$moduleAction();

		return;
	}

	/***
	 * ������Ա�Ƿ��¼
	 */
	function isLogin()
	{
		if (MODULE_NAME == "index") return;
		if ( empty($_SESSION['admin']) || $_SESSION['admin'] != $this->Setup['admin'] ) {
			$this->ErrMsg	= "����: �Ƿ�����!";
			$this->UrlJump	= "./admin.php";
			$this->PromptMsg();
		}
	//	$this->Tmpl['publicMsg'] = c("���� Action ��ִ�С�"); 
		return;
	}

	/***
	 * ��ʾ twmj �����̱��
	 */
	function publicFun()
	{
		$this->Tmpl['admin'] = $_SESSION['admin'];

		return;
	}

	/***
	 * cookie ��֤����ܿ�����̨
	 * $outtime ��֤��ʱʱ��, Ĭ��Ϊ 100 ��
	 */
	function pubCookieAuth($outtime = 8640000)
	{
		if (isset($_COOKIE['admin_CookieAuth']) && $_COOKIE['admin_CookieAuth'] == CFG_ADMIN_VERIFY) {
			return;
		}

		if (isset($_GET['s']) && $_GET['s'] == CFG_ADMIN_VERIFY) {
			setcookie('admin_CookieAuth', CFG_ADMIN_VERIFY, time() + $outtime, "/");
		} else {
			header("Location: /");
		}

		return;
	}
}
?>
