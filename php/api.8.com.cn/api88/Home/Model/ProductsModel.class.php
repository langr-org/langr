<?php
namespace Home\Model;
use Think\Model;

/**
 * 我的投资 订单模型
 * 
 * @uses Model
 * @package 
 * @version $id$
 * @copyright 1997-2005 The PHP Group
 * @author wenming.pan <wenming.pan@outlook.com> 
 * @license PHP Version 3.0 {@link http://www.php.net/license/3_0.txt}
 */
class ProductsModel extends Model
{
    protected $trueTableName = 'data_products';
    protected $dbName = 'products_data';
    
    /**
     * 根据认购金额获取年利率  
     * 
     * @param int $id 产品id
     * @param int $m  认购金额
     * @param string $investType month按月算，day按日算
     * @param int $investTime 月份或者天数
     * @access public
     * @return float 
     */
    public function get_yly($id, $m, $investType, $investTime){/*{{{*/
        $m1 = $m / 10000;
        $where = "data_productsyield.pdid={$id} and 
            (data_productsyield.startMoney<={$m1} and data_productsyield.endMoney>{$m1}) and 
            data_productsyield.investType='{$investType}' and 
            data_productsyield.investTime={$investTime}";
        $result = $this->table('products_data.data_productsyield')
            ->field('data_productsyield.yield')
            ->where($where)
            ->order('data_productsyield.yield asc')
            ->find();
        return $result['yield'] / 100;
    }/*}}}*/
	
	/**
	 * 获取产品列表 
	 * 
	 * @param mixed $where 
	 * @param int $page 
	 * @param int $pagesize 
	 * @access public
	 * @return void
	 */
	public function getProducts($where, $page=1, $pagesize=20)/*{{{*/
	{
		$wsql = 'data_products.exp_pType = 0 and data_products.isshow=1 and (data_products.status=2 or data_products.status=3) ';
		$wsql .= $where;
		$offset = ($page - 1) * $pageSize;
		$field = "data_products.isdjj,data_products.pType,data_products.productName,data_products.inDate,data_products.rgtime,data_products.jxtime,data_products.status,data_products.productType,data_products.presellEndtime,data_products.rgEndtime,data_products.id,data_productsitem.returnType,data_products.istransfer,data_productsitem.hopeRate1,data_productsitem.hopeRate2,data_productsitem.totalAmount,data_productsitem.totalMoney,data_productsitem.beginMoney,data_productsitem.oneaddMoney,data_productsitem.investTime,data_productsitem.investTime1,data_productsitem.investTime2,data_productsitem.day,data_productsitem.day1,data_productsitem.day2,data_products.ishot,data_products.hd_msg,data_products.hd_url,data_products.transfer_difftime ";
		$result = $this->join('LEFT JOIN products_data.data_productsitem on data_productsitem.pdid=data_products.id')
			->field($field)
			->where($wsql)
			->order('data_products.status asc,data_productsitem.hopeRate1 desc')
			->limit($offset, $pagesize)
			->select();
		return $result;
	}/*}}}*/

	/**
	 * 获取产品总数 
	 * 
	 * @param string $where 查询条件 
	 * @access public
	 * @return int
	 */
	public function getProductsCount($where)/*{{{*/
	{
		$wsql = 'data_products.exp_pType = 0 and data_products.isshow=1 and (data_products.status=2 or data_products.status=3) ';
		$wsql .= $where;
		$result = $this->join('LEFT JOIN products_data.data_productsitem on data_productsitem.pdid=data_products.id')
			->where($wsql)
			->count();
		return $result;
	}/*}}}*/

	/**
	 * 获取薪资宝产品信息
	 * 
	 * @param int $id 产品id 
	 * @param string $productName 产品名称
	 * @access public
	 * @return array
	 */
	public function getProductXzb($id, $productName)/*{{{*/
	{
		$where = 'data_products.exp_pType = 1 and data_products.isshow=1 and (data_products.status=2 or data_products.status=3) and (data_products.id='.$id.' or data_products.productName like "%'.$productName.'%")';
		$field = "data_products.isdjj,data_products.pType,data_products.productName,data_products.inDate,data_products.rgtime,data_products.jxtime,data_products.status,data_products.productType,data_products.presellEndtime,data_products.rgEndtime,data_products.id,data_productsitem.returnType,data_products.istransfer,data_productsitem.hopeRate1,data_productsitem.hopeRate2,data_productsitem.totalAmount,data_productsitem.totalMoney,data_productsitem.beginMoney,data_productsitem.oneaddMoney,data_productsitem.investTime,data_productsitem.investTime1,data_productsitem.investTime2,data_productsitem.day,data_productsitem.day1,data_productsitem.day2,data_products.ishot,data_products.hd_msg,data_products.hd_url,data_products.transfer_difftime ";
		$result = $this->join('LEFT JOIN products_data.data_productsitem on data_productsitem.pdid=data_products.id')
			->field($field)
			->where($where)
			->order('data_products.id desc,data_productsitem.hopeRate1 desc')
			->find();
		return $result;
	}/*}}}*/

	/**
	 * 获取复利宝类型信息 
	 * 
	 * @param int $id  
	 * @access public
	 * @return array
	 */
	public function info_flbtype($id)/*{{{*/
	{
		$where['id'] = $id;
		$result = $this->table($this->dbName.'.data_flbtype')
			->where($where)
			->find();
		return $result;
	}/*}}}*/

	/**
	 * 获取产品详细信息
	 * 
	 * @param int $id 产品id 
	 * @access public
	 * @return array
	 */
	public function product_detail($id)/*{{{*/
	{
		$where['id'] = $id;
		$field = "data_products.id, data_products.pnumber, data_products.productName, data_products.productType, data_products.presellEndtime, data_products.rgEndtime, data_products.inDate, data_products.status, data_products.maxcount, data_products.jxtime, data_products.rgtime, data_products.istransfer, data_productsitem.totalAmount, data_productsitem.totalMoney, data_productsitem.beginMoney, data_productsitem.oneaddMoney, data_productsitem.maxmoney, data_productsitem.investTime, data_productsitem.investTime1, data_productsitem.investTime2, data_productsitem.day, data_productsitem.day1, data_productsitem.day2, data_productsitem.returnType, data_productsitem.hopeRate1, data_productsitem.hopeRate2, data_productsitem.advantage, data_productsitem.description, data_productsitem.fxkz, data_productsitem.mzfcontent, data_productsitem.cpys_edit, data_productsitem.jjjg_edit, data_productsitem.fkcs_edit, data_productsitem.cpyous_edit, data_productsitem.dbf_edit, data_productsitem.str_edit";
		$result = $this->join('LEFT JOIN products_data.data_productsitem on data_productsitem.pdid=data_products.id')
			->field($field)
			->where($where)
			->find();
		return $result;
	}/*}}}*/

	/**
	 * 获取产品的年利率 
	 * 
	 * @param int $id 产品id 
	 * @access public
	 * @return array
	 */
	public function get_productsyield($id)/*{{{*/
	{
		$where['pdid'] = $id;
		$field = "data_productsyield.startMoney, data_productsyield.endMoney, data_productsyield.yield, data_productsyield.transfer, data_productsyield.investType, data_productsyield.investTime";
		$result = $this->table($this->dbName.'.data_productsyield')
			->field($field)
			->where($where)
			->find();
		return $result;

	}/*}}}*/

	/**
	 * 获取产品的风控措施 
	 * 
	 * @param int $id 产品id
	 * @access public
	 * @return array
	 */
	public function get_productsfk($id)/*{{{*/
	{
		$where['pdid'] = $id;
		$field = "data_productsfk.sxbid, data_productsfk.fileName, data_productsfk.fileUrl, data_productsfk.filedesc, data_productsfk.isbuy, data_productsfk.isvip";

		$result = $this->table($this->dbName.'.data_productsfk')
			->field($field)
			->where($where)
			->find();
		return $result;
	}/*}}}*/

	/**
	 * 获取产品的内容 
	 * 
	 * @param int $id 产品id 
	 * @access public
	 * @return array
	 */
	public function sel_product_all_content($id)/*{{{*/
	{
		$where['pid'] = $id;
		$result = $this->table($this->dbName.'.data_products_content')
			->where($where)
			->find();
		return $result;
	}/*}}}*/

	/**
	 * 获取产品投资记录总数
	 * 
	 * @param string $where 查询条件
	 * @access public
	 * @return int
	 */
	public function get_countofproduct_history($where)/*{{{*/
	{
		$result = $this->table("order_data.data_orders")
			->join("LEFT JOIN members_data.data_users on data_orders.userid = data_users.userid")
			->where($where)
			->count();
		return $result;
	}/*}}}*/

	/**
	 * 获取产品的投资记录 
	 * 
	 * @param string $where 查询条件 
	 * @param int $page 当前页
	 * @param int $pagesize 每页多少条数据
	 * @access public
	 * @return array
	 */
	public function get_productofhistory($where, $page, $pagesize=10)/*{{{*/
	{
		$offset = ($page - 1) * $pagesize;
		$field = "data_orders.accountMoney, data_orders.payTime, data_users.realName";

		$result = $this->table("order_data.data_orders")
			->join("LEFT JOIN members_data.data_users on data_orders.userid = data_users.userid")
			->field($field)
			->where($where)
			->order('data_orders.payTime desc')
			->limit($offset, $pagesize)
			->select();
		return $result;
	}/*}}}*/

}
