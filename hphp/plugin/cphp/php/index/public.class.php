<?php
class Index_Public extends Cortrol
{
	/* private static $dealer_edition = null; */
	var $is_login = false;
	/***
	 * ���캯����������ʵ��ʱ�Զ�ִ�����в���
	 */
	function Index_Public()
	{
	//	prevent_cc(3, 5);			/* ��ˢ�»��� 5s �ڵ� 3 �� */
		args();					/* */
		$this->Cortrol();			/* �������ʼ�� */
		$this->publicFun();			/* ִ��һ������ Action */
		$moduleAction = ACTION_PREFIX.ucfirst(ACTION_NAME); 
		$this->$moduleAction();
		//$this->dealer_edition = array();
		return;
	}

	/***
	 * ��Ա���Ĺ������������ twmj ��Ա�Ƿ��¼
	 */
	function publicFun()
	{
		if (isset($_SESSION['mj_user']) && !empty($_SESSION['mj_user'])) {
			$this->Tmpl['login'] = $_SESSION['mj_user']." (<a href='".url('?module=member&action=logout')."'>".c('�ǳ�')."</a>)";
			$this->is_login = true;
		} else {
			$this->Tmpl['login'] = "<a href='".url('?module=member&action=login').c("'>����</a>");
			$this->is_login = false;
		}
		return;
	}

	/***
	 * ��ȡ�������ʺ��� (DealerNo)
	 */
	function getDealer()
	{
		$uri	= "http://".$_SERVER["HTTP_HOST"];
		$url = parse_url($uri);

		$host	= explode('.', $url['host']);
		$dealer	= $host[0];

		if ($dealer == "www")
			return $dealer;

		$f	= $this->loadDB();
		$sql	= "select DealerNo from dealerinfo where Edition='$dealer'";
		$f->query($sql);
		$f->nextRecord();
		$dealer	= $f->record['DealerNo'];

		if (empty($dealer))
			$dealer = "A00001";
		
		return $dealer;
	}
}
?>
