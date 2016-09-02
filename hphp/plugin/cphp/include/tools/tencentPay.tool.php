<?php
/***************************************************************
* 名称：财付通支付网关
*
***************************************************************/
class Tool_tencentPay{

	var $key = "b~36d6@5509E706d668W;e9d787&02c9";//密钥
	var $returnUrl = "127.0.0.1/include/php/tencentReturn.php";//返回URL
	var $attach = "www.8591.com.cn财付通支付平台";//商户数据包，自定义
	var $tencentId = "1200028201";//商家的商户号,有腾讯公司唯一分配
	var $tencentPayId = 0;//本地产生的订单编号
	var $transActionId="";//交易单号

	var $SQL;		// 外部类的对象实体
	var $ErrMsg;	// 错误提示信息

	function Tool_tencentPay(& $sqlClass){
	 	if(!CFG_MIN_STORE_VALUE){
			define('CFG_MIN_STORE_VALUE',"10");
			define('CFG_DISCOUNT',"1");//设置为整数，计算的时候除以100
		}
	 	$this->SQL = & $sqlClass;
	}

	//====================================================================================
	// 产生要提交的表单
	//====================================================================================
	function createForm(){
		if(!isset($_SESSION['userId'])){
			$this->PromptMsg = "请您先登录！";
			$this->UrlJump = "index.php?module=user&action=login";
			return false;
		}
	 	$this->createTransactionId();
		$str = "<form name=\"tencentPayForm\" method=\"POST\" action=\"http://portal.tenpay.com/cfbiportal/cgi-bin/cfbiin.cgi\">";
		$str .= "<input type=\"hidden\" name=\"cmdno\" id=\"cmdno\" value=\"1\">";//业务代码, 财付通支付支付接口填  1
		$str .= "<input type=\"hidden\" name=\"date\" id=\"date\" value=\"".date("Ymd")."\">";//商户日期：如20051212
		$str .= "<input type=\"hidden\" name=\"bank_type\" id=\"bank_type\" value=\"".$_POST['bankId']."\">";//银行类型:支持纯网关和财付通
		$str .= "<input type=\"hidden\" name=\"desc\" id=\"desc\" value=\"".$this->getWareName()."\">";//交易的商品名称
		$str .= "<input type=\"hidden\" name=\"purchaser_id\" id=\"purchaser_id\" value=\"".$this->getUserTencentPayId()."\">";//用户(买方)的财付通帐户,可以为空
		$str .= "<input type=\"hidden\" name=\"bargainor_id\" id=\"bargainor_id\" value=\"".$this->tencentId."\">";//商家的商户号,有腾讯公司唯一分配
		$str .= "<input type=\"hidden\" name=\"transaction_id\" id=\"transaction_id\" value=\"".$this->transActionId."\">";//交易号(订单号)
		$str .= "<input type=\"hidden\" name=\"sp_billno\" id=\"sp_billno\" value=\"".$this->tencentPayId."\">";//商户系统内部的定单号，此参数仅在对账时提供。
		$str .= "<input type=\"hidden\" name=\"total_fee\" id=\"total_fee\" value=\"".$_POST['totalPayMoney']."\">";//总金额，以分为单位
		$str .= "<input type=\"hidden\" name=\"fee_type\" id=\"fee_type\" value=\"1\">";//现金支付币种
		$str .= "<input type=\"hidden\" name=\"return_url\" id=\"return_url\" value=\"".$this->returnUrl."\">";//接收财付通返回结果的URL(推荐使用ip)
		$str .= "<input type=\"hidden\" name=\"attach\" id=\"attach\" value=\"".urlencode($this->attach)."\">";//商家数据包，原样返回
		$str .= "<input type=\"hidden\" name=\"sign\" id=\"sign\" value=\"".$this->md5Sign()."\">";//MD5签名
		$str .= "</form>";

		return $str;
	}

	/***************************************************************
	* MD5签名
	*	MD5(
	*		任务代码：    cmdno+
	*		商户日期：    date+
	*		卖家商户号：  bargainor_id+
	*		财付通交易号：transaction_id+
	*		商户订单号：  sp_billno +
	*		订单金额：    total_fee +
	*		币种：        fee_type +
	*		返回商户的URL：return_url+
	*		商户数据包：  attach+
	*		商户密钥：    key
	*	)
	*	注：从上向下拼成一个无间隔的字符串(char 型，顺序不要改变)。其中参数key为商户在
	*	财付通帐户管理里设置的16 位密钥值。
	*	使用标准MD5算法对该字符串进行加密，加密结果全部转换成大写
	**************************************************************/

	function md5Sign(){
		$str = "cmdno=1".
					 "&date=".date("Ymd").
					 "&bargainor_id=".$this->tencentId.
					 "&transaction_id=".$this->transActionId.
					 "&sp_billno=".$this->tencentPayId.
					 "&total_fee=".$_POST['totalPayMoney'].
					 "&fee_type=1".
					 "&return_url=".$this->returnUrl.
					 "&attach=".urlencode($this->attach).
					 "&key=".$this->key;
		return strtoupper(md5($str));
	}

	//====================================================================================
	// 入库
	//====================================================================================
	function orderInsert(){
		$sql = "insert into tencentPay(userId,wareId,storeMoney,discount,totalPayMoney,bankId,actionTime)
		        values('".$_SESSION['userId']."','".$_POST['wareId']."','".(number_format($_POST['storeMoney'], 2, '.', '')*100)."','".CFG_DISCOUNT."','".(number_format($_POST['totalPayMoney'], 2, '.', '')*100)."','".$_POST['bankId']."','".time()."')";
		$q = $this->SQL->loadDB();
		$q->query($sql);
		$this->tencentPayId = $q->insertId();
	}

	//====================================================================================
	// 产生交易单号
	// 规则：28位长的数值，其中前10位为C2C网站编号(SPID即商户号)，由财付通统一分配；之后8位为订单
	//       产生的日期，如20050415；最后10位C2C需要保证一天内不同的事务（用户订购或使用
	//			 一次服务），其ID不相同。
	//====================================================================================
	function createTransactionId(){
		$this->transActionId = $this->tencentId.date("Ymd").sprintf("%010s",$this->tencentPayId);
	}

	//====================================================================================
	// 取得用户的财付通账户
	//====================================================================================
	function getUserTencentPayId(){
		return "";
	}

	//====================================================================================
	// 取得交易商品名称
	//====================================================================================
	function getWareName(){
		$sql = "select title from ware_item where id='".$_POST['wareId']."'";
		$q = $this->SQL->loadDB();
		$arr = $q->selectOne($sql);
		return $arr['title'];
	}

	//====================================================================================
	// 根据腾讯协议产生的支付成功md5签名
	//====================================================================================
	function successMd5Sign(){
		$str = "cmdno=1".
					 "&pay_result=0".
					 "&date=".date("Ymd").
					 "&transaction_id=".$_GET['transaction_id'].
					 "&sp_billno=".$_GET['sp_billno'].
					 "&total_fee=".$_GET['total_fee'].
					 "&fee_type=1".
					 "&attach=".urlencode($this->attach).
					 "&key=".$this->key;
		return strtoupper(md5($str));
	}

	//====================================================================================
	// 对腾讯返回的数据进行分析
	//====================================================================================
	function getReturnFromTencent(){
		$q = $this->SQL;//这里直接传入数据库查询类
		$sql = "select id from ware_item where id='".$_GET['sp_billno']."'";
		if(!$q->selectOne($sql)){
			return false;
		}

		if($_GET['pay_result'] == 0){
			if($_GET['sign'] == $this->successMd5Sign()){
				$sql = "update tencentPay set status = 1 where id='".$_GET["sp_billno"]."'";
				$q->query($sql);
				include "../../include/public/money.public.php";
				$m = new Public_Money;
				$m->addMoneyFromTencent($_GET['sp_billno']);
				return true;
			}else
				return false;
		}else{
			return false;
		}
	}
}
?>