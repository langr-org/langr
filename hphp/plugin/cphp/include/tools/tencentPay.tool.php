<?php
/***************************************************************
* ���ƣ��Ƹ�֧ͨ������
*
***************************************************************/
class Tool_tencentPay{

	var $key = "b~36d6@5509E706d668W;e9d787&02c9";//��Կ
	var $returnUrl = "127.0.0.1/include/php/tencentReturn.php";//����URL
	var $attach = "www.8591.com.cn�Ƹ�֧ͨ��ƽ̨";//�̻����ݰ����Զ���
	var $tencentId = "1200028201";//�̼ҵ��̻���,����Ѷ��˾Ψһ����
	var $tencentPayId = 0;//���ز����Ķ������
	var $transActionId="";//���׵���

	var $SQL;		// �ⲿ��Ķ���ʵ��
	var $ErrMsg;	// ������ʾ��Ϣ

	function Tool_tencentPay(& $sqlClass){
	 	if(!CFG_MIN_STORE_VALUE){
			define('CFG_MIN_STORE_VALUE',"10");
			define('CFG_DISCOUNT',"1");//����Ϊ�����������ʱ�����100
		}
	 	$this->SQL = & $sqlClass;
	}

	//====================================================================================
	// ����Ҫ�ύ�ı�
	//====================================================================================
	function createForm(){
		if(!isset($_SESSION['userId'])){
			$this->PromptMsg = "�����ȵ�¼��";
			$this->UrlJump = "index.php?module=user&action=login";
			return false;
		}
	 	$this->createTransactionId();
		$str = "<form name=\"tencentPayForm\" method=\"POST\" action=\"http://portal.tenpay.com/cfbiportal/cgi-bin/cfbiin.cgi\">";
		$str .= "<input type=\"hidden\" name=\"cmdno\" id=\"cmdno\" value=\"1\">";//ҵ�����, �Ƹ�֧ͨ��֧���ӿ���  1
		$str .= "<input type=\"hidden\" name=\"date\" id=\"date\" value=\"".date("Ymd")."\">";//�̻����ڣ���20051212
		$str .= "<input type=\"hidden\" name=\"bank_type\" id=\"bank_type\" value=\"".$_POST['bankId']."\">";//��������:֧�ִ����غͲƸ�ͨ
		$str .= "<input type=\"hidden\" name=\"desc\" id=\"desc\" value=\"".$this->getWareName()."\">";//���׵���Ʒ����
		$str .= "<input type=\"hidden\" name=\"purchaser_id\" id=\"purchaser_id\" value=\"".$this->getUserTencentPayId()."\">";//�û�(��)�ĲƸ�ͨ�ʻ�,����Ϊ��
		$str .= "<input type=\"hidden\" name=\"bargainor_id\" id=\"bargainor_id\" value=\"".$this->tencentId."\">";//�̼ҵ��̻���,����Ѷ��˾Ψһ����
		$str .= "<input type=\"hidden\" name=\"transaction_id\" id=\"transaction_id\" value=\"".$this->transActionId."\">";//���׺�(������)
		$str .= "<input type=\"hidden\" name=\"sp_billno\" id=\"sp_billno\" value=\"".$this->tencentPayId."\">";//�̻�ϵͳ�ڲ��Ķ����ţ��˲������ڶ���ʱ�ṩ��
		$str .= "<input type=\"hidden\" name=\"total_fee\" id=\"total_fee\" value=\"".$_POST['totalPayMoney']."\">";//�ܽ��Է�Ϊ��λ
		$str .= "<input type=\"hidden\" name=\"fee_type\" id=\"fee_type\" value=\"1\">";//�ֽ�֧������
		$str .= "<input type=\"hidden\" name=\"return_url\" id=\"return_url\" value=\"".$this->returnUrl."\">";//���ղƸ�ͨ���ؽ����URL(�Ƽ�ʹ��ip)
		$str .= "<input type=\"hidden\" name=\"attach\" id=\"attach\" value=\"".urlencode($this->attach)."\">";//�̼����ݰ���ԭ������
		$str .= "<input type=\"hidden\" name=\"sign\" id=\"sign\" value=\"".$this->md5Sign()."\">";//MD5ǩ��
		$str .= "</form>";

		return $str;
	}

	/***************************************************************
	* MD5ǩ��
	*	MD5(
	*		������룺    cmdno+
	*		�̻����ڣ�    date+
	*		�����̻��ţ�  bargainor_id+
	*		�Ƹ�ͨ���׺ţ�transaction_id+
	*		�̻������ţ�  sp_billno +
	*		������    total_fee +
	*		���֣�        fee_type +
	*		�����̻���URL��return_url+
	*		�̻����ݰ���  attach+
	*		�̻���Կ��    key
	*	)
	*	ע����������ƴ��һ���޼�����ַ���(char �ͣ�˳��Ҫ�ı�)�����в���keyΪ�̻���
	*	�Ƹ�ͨ�ʻ����������õ�16 λ��Կֵ��
	*	ʹ�ñ�׼MD5�㷨�Ը��ַ������м��ܣ����ܽ��ȫ��ת���ɴ�д
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
	// ���
	//====================================================================================
	function orderInsert(){
		$sql = "insert into tencentPay(userId,wareId,storeMoney,discount,totalPayMoney,bankId,actionTime)
		        values('".$_SESSION['userId']."','".$_POST['wareId']."','".(number_format($_POST['storeMoney'], 2, '.', '')*100)."','".CFG_DISCOUNT."','".(number_format($_POST['totalPayMoney'], 2, '.', '')*100)."','".$_POST['bankId']."','".time()."')";
		$q = $this->SQL->loadDB();
		$q->query($sql);
		$this->tencentPayId = $q->insertId();
	}

	//====================================================================================
	// �������׵���
	// ����28λ������ֵ������ǰ10λΪC2C��վ���(SPID���̻���)���ɲƸ�ͨͳһ���䣻֮��8λΪ����
	//       ���������ڣ���20050415�����10λC2C��Ҫ��֤һ���ڲ�ͬ�������û�������ʹ��
	//			 һ�η��񣩣���ID����ͬ��
	//====================================================================================
	function createTransactionId(){
		$this->transActionId = $this->tencentId.date("Ymd").sprintf("%010s",$this->tencentPayId);
	}

	//====================================================================================
	// ȡ���û��ĲƸ�ͨ�˻�
	//====================================================================================
	function getUserTencentPayId(){
		return "";
	}

	//====================================================================================
	// ȡ�ý�����Ʒ����
	//====================================================================================
	function getWareName(){
		$sql = "select title from ware_item where id='".$_POST['wareId']."'";
		$q = $this->SQL->loadDB();
		$arr = $q->selectOne($sql);
		return $arr['title'];
	}

	//====================================================================================
	// ������ѶЭ�������֧���ɹ�md5ǩ��
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
	// ����Ѷ���ص����ݽ��з���
	//====================================================================================
	function getReturnFromTencent(){
		$q = $this->SQL;//����ֱ�Ӵ������ݿ��ѯ��
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