<?php
/**
 *
 */

class daily_report_forms
{
	/**
	 * @fn
	 * @brief 统计通话量日负载
	 * 	需求: 按日期, 呼入呼出 条件检索统计当日24小时每小时的通话条数,
	 * 	及各小时每分钟的(开始)通话条数, 或每分钟正在通话(开始到结束)的总条数.
	 * @param 
	 * @return 
	 */
	function showDaily() /* {{{ */
	{
		$_db = 'asteriskcdrdb';
		$db_table = 'cdr';
		$where = 'where 1 ';
		$day = empty($_GET['day']) ? date('Y-m-d') : $_GET['day'];

		/* 月份表名 */
		$sub_table = substr($day, 2, 5);
		if ( $sub_table != date('y-m') ) {
			$db_table = $_db.'.`'.$db_table.'-'.$sub_table.'`';
		}

		$where .= "AND from_unixtime(calldate,'%Y-%m-%d %H:%i:%s') <= '$day 23:59:59' AND from_unixtime(calldate,'%Y-%m-%d %H:%i:%s') >= '$day 00:00:00' ";
		if ( !empty($_GET['called']) || !empty($_GET['caller']) ) {
			$call = '';
			if ( $_GET['called_type'] == 3 ) {
				$call .= "and dst LIKE '%".$_GET['called']."%' ";
			} else {
				$call .= "and dst='".$_GET['called']."' ";
			}
			if ( $_GET['caller_type'] == 3 ) {
				$call .= "and src LIKE '%".$_GET['called']."%' ";
			} else {
				$call .= "and src='".$_GET['called']."' ";
			}
			$where .= $call;
		}
		$select = "from_unixtime(calldate,'%Y-%m-%d %H:%i:%s') as calldate, billsec";

		$sql = "select $select from $db_table $where AES ";
		$data = $this->daily_data($sql);

		for () {
			;
		}
	} /* }}} */

	function daily_data($sql)
	{
		;
	}
}
/* end file */
