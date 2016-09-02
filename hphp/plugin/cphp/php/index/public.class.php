<?php
class Index_Public extends Cortrol
{
	/* private static $dealer_edition = null; */
	var $is_login = false;
	/***
	 * 构造函数，定义类实体时自动执行所有操作
	 */
	function Index_Public()
	{
	//	prevent_cc(3, 5);			/* 防刷新机制 5s 内点 3 次 */
		args();					/* */
		$this->Cortrol();			/* 控制类初始化 */
		$this->publicFun();			/* 执行一个公共 Action */
		$moduleAction = ACTION_PREFIX.ucfirst(ACTION_NAME); 
		$this->$moduleAction();
		//$this->dealer_edition = array();
		return;
	}

	/***
	 * 会员中心公共方法：检查 twmj 会员是否登录
	 */
	function publicFun()
	{
		if (isset($_SESSION['mj_user']) && !empty($_SESSION['mj_user'])) {
			$this->Tmpl['login'] = $_SESSION['mj_user']." (<a href='".url('?module=member&action=logout')."'>".c('登出')."</a>)";
			$this->is_login = true;
		} else {
			$this->Tmpl['login'] = "<a href='".url('?module=member&action=login').c("'>登入</a>");
			$this->is_login = false;
		}
		return;
	}

	/***
	 * 获取经销商帐号名 (DealerNo)
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
