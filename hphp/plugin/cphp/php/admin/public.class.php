<?php
class Index_Public extends Cortrol
{
	/***
	 * 构造函数，定义类实体时自动执行所有操作
	 */
	function Index_Public()
	{
		$this->Cortrol();					/* 控制类初始化 */
		$this->isLogin();					/* 执行一个公共 Action */
		$this->publicFun();
		$this->pubCookieAuth(86400000);				/* 1000 天 */
		$moduleAction = ACTION_PREFIX.ucfirst(ACTION_NAME); 
		$this->$moduleAction();

		return;
	}

	/***
	 * 检查管理员是否登录
	 */
	function isLogin()
	{
		if (MODULE_NAME == "index") return;
		if ( empty($_SESSION['admin']) || $_SESSION['admin'] != $this->Setup['admin'] ) {
			$this->ErrMsg	= "错误: 非法访问!";
			$this->UrlJump	= "./admin.php";
			$this->PromptMsg();
		}
	//	$this->Tmpl['publicMsg'] = c("公共 Action 已执行。"); 
		return;
	}

	/***
	 * 显示 twmj 经销商编号
	 */
	function publicFun()
	{
		$this->Tmpl['admin'] = $_SESSION['admin'];

		return;
	}

	/***
	 * cookie 认证后才能看到后台
	 * $outtime 论证超时时间, 默认为 100 天
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
