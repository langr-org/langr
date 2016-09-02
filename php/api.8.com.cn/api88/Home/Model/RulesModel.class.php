<?php
namespace Home\Model;
use Think\Model;

/**
 * 规则模型(活动规则)
 * 
 * @uses Model
 * @package 
 * @version $id$
 * @copyright 1997-2005 The PHP Group
 * @author wenming.pan <wenming.pan@outlook.com> 
 * @license PHP Version 3.0 {@link http://www.php.net/license/3_0.txt}
 */
class RulesModel extends Model
{
    protected $trueTableName = 'data_userules';
    protected $dbName = 'log_data';

	/**
	 * 获取所有可以使用88币的产品 
	 * 
	 * Array ( [0] => 489 [1] => 722  ) 
	 * @access public
	 * @return array
	 */
	public function canUserCoinsProductsIds()
	{
		$where['isDel'] = 1;
		$result = $this->field('products')->where($where)->select();
		return $result;
	}
}
