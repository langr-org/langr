<?php
/**
 * $Id: report_graph.class.php 624 2012-05-11 04:00:59Z huangh $
 */

class report_graph extends Index_Public
{
	function showMonths_t() /* {{{ */ 
	{
		global $arr_local_priv;
		$this->isAuth( 'monthsstat_sel', $arr_local_priv, iconv('utf-8','gbk','您没有查看月流量统计的权限！') );
		$this->Tmpl['id_'.ACTION_NAME] = "id='current'";
		$default_m = 6;
		$months = array();
		$cur_month = date('Y-m').'-01';
		for ( $i = 0; $i < $default_m; $i++ ) {
			$months[] = substr($cur_month, 0, 7);
			$cur_month = date('Y-m-d', strtotime('last month', strtotime($cur_month)));
		}
		$this->Tmpl['months_list'] = $months;
		$this->display();
	} /* }}} */

	function showDays_t() /* {{{ */ 
	{
		global $arr_local_priv;
		$this->isAuth( 'daysstat_sel', $arr_local_priv, iconv('utf-8','gbk','您没有查看日负载统计的权限！') );
		$this->Tmpl['id_'.ACTION_NAME] = "id='current'";
		$this->display();
	} /* }}} */

	function showHours_t() /* {{{ */ 
	{
		global $arr_local_priv;
		$this->isAuth( 'hourstat_sel', $arr_local_priv, iconv('utf-8','gbk','您没有查看每小时负载统计的权限！') );
		$this->Tmpl['id_'.ACTION_NAME] = "id='current'";
		$this->display();
	} /* }}} */

	function showDaysDiff_t() /* {{{ */ 
	{
		global $arr_local_priv;
		$this->isAuth( 'daysdiff_sel', $arr_local_priv, iconv('utf-8','gbk','您没有查看通话比较统计的权限！') );
		$this->Tmpl['id_'.ACTION_NAME] = "id='current'";
		$this->display();
	} /* }}} */

	function showExtension_t() /* {{{ */ 
	{
		global $arr_local_priv;
		$this->isAuth( 'extstat_sel', $arr_local_priv, iconv('utf-8','gbk','您没有查看分机统计的权限！') );
		$this->Tmpl['id_'.ACTION_NAME] = "id='current'";
		$this->display();
	} /* }}} */

	function showQueues_t() /* {{{ */ 
	{
		global $arr_local_priv;
		$this->isAuth( 'queuestat_sel', $arr_local_priv, iconv('utf-8','gbk','您没有查看队列统计的权限！') );
		$this->Tmpl['id_'.ACTION_NAME] = "id='current'";
		$this->display();
	} /* }}} */

	function showFeedback_t() /* {{{ */ 
	{
		global $arr_local_priv;
		$this->isAuth( 'feedbakstat_sel', $arr_local_priv, iconv('utf-8','gbk','您没有查看质检统计的权限！') );
		$this->Tmpl['id_'.ACTION_NAME] = "id='current'";
		$this->display();
	} /* }}} */

	/**
	 * @fn
	 * @brief 统计通话量日负载
	 * 	需求: 按日期, 呼入呼出 条件检索统计当日24小时每小时的通话条数,
	 * 	及各小时每分钟的(开始)通话条数, 或每分钟正在通话(开始到结束)的总条数.
	 * @param 
	 * @return 
	 */
	function showDays() /* {{{ */
	{
		$_db = ASTERISKCDRDB_DB_NAME;
		$db_table = 'cdr';
		$where = 'where 1 ';
		$day = empty($_GET['day']) ? date('Y-m-d') : $_GET['day'];
		$__title = '';

		/* 月份表名 */
		$sub_table = substr($day, 2, 5);
		if ( $sub_table != date('y-m') ) {
			$db_table = $_db.'.`'.$db_table.'-'.$sub_table.'`';
		} else {
			$db_table = $_db.'.'.$db_table;
		}

		$where .= "AND from_unixtime(calldate,'%Y-%m-%d %H:%i:%s') <= '$day 23:59:59' AND from_unixtime(calldate,'%Y-%m-%d %H:%i:%s') >= '$day 00:00:00' ";
		if ( !empty($_GET['extension']) ) {
			$where .= "and (src='".$_GET['extension']."' or dst='{$_GET['extension']}') ";
		}
		if ( !empty($_GET['called']) ) {
			if ( $_GET['called_type'] == 3 ) {
				$where .= "and dst LIKE '".$_GET['called']."%' ";
			} else {
				$where .= "and dst='".$_GET['called']."' ";
			}
		}
		if ( !empty($_GET['caller']) ) {
			if ( $_GET['caller_type'] == 3 ) {
				$where .= "and src LIKE '".$_GET['caller']."%' ";
			} else {
				$where .= "and src='".$_GET['caller']."' ";
			}
		}
		$select = "count(*) as count_t,id,from_unixtime(calldate,'%H') as call_t, sum(billsec) as sum_billsec";
		$where .= "group by from_unixtime(calldate, '%Y-%m-%d %H') order by call_t ";
		$sql = "select $select from $db_table $where ";
		//SELECT count(*) as count_t,id,from_unixtime(calldate,'%H') as call_t, sum(billsec) as sum_billsec FROM `cdr` WHERE from_unixtime(calldate,'%Y-%m-%d %H:%i:%s') <= '2012-02-10 23:59:59' AND from_unixtime(calldate,'%Y-%m-%d %H:%i:%s') >= '2012-02-10 00:00:00' group by from_unixtime(calldate, '%Y-%m-%d %H') order by call_t;
		$data = $this->get_data($sql);

		if ( $_GET['title'] ) {
			$__title = $_GET['title'];
		}
		$table_data = array();
		/* graph */
		$graph_setting = array();
		$graph_setting['renderTo'] = "daily_hours_div";
		//$graph_setting['title'] = "$day 日负载图";
		$graph_setting['title'] = ($__title ? $__title : "$day 日负载图");
		$graph_setting['yAxis'] = "[{title:{text:'次',align:'high',rotation:0}},{title:{text:'秒',style:{color:'#aa4643'},align:'high',rotation:0},min:0,opposite:true}]";
		$graph_setting['xcategories'] = "['00','01','02','03','04','05','06','07','08','09']";
		$graph_setting['tooltip'] = "function(){return this.x+'<br/>'+this.y+'<br/>';}";

		$graph_data = array();
		$graph_data[0]['name'] = "每小时通话量";
		$graph_data[0]['data'] = array();
		$graph_data[1]['type'] = 'line';
		$graph_data[1]['name'] = "通话时间";
		$graph_data[1]['yAxis'] = '1';
		$graph_setting['tooltip'] = "function(){unit={'{$graph_data[0]['name']}':'次','{$graph_data[1]['name']}':'秒'}[this.series.name];return '<b>'+this.x+'时</b><br/>通话: '+this.y+unit+'<br/>';}";
		$graph_setting['plotOptions'] = "{line:{dataLabels:{enabled:true,formatter:function(){unit={'{$graph_data[0]['name']}':'次','{$graph_data[1]['name']}':'秒'}[this.series.name];return this.y>0?(this.y+unit):'';}} },column:{dataLabels:{enabled:true,formatter:function(){unit={'{$graph_data[0]['name']}':'次','{$graph_data[1]['name']}':'秒'}[this.series.name];return this.y>0?(this.y+unit):'';}} },pie:{cursor:'pointer',dataLabels:{enabled:true,formatter:function(){return this.point.name+' 通话:'+this.y+'次';}},showInLegend:true}}";
		$sum_time = 0;
		$_count = count($data);
		$_v = 24;		/* 24时 */
		$i = 0;
		$j = 0;
		for ( $i = 0; $i < $_v; $i++ ) {
			if ( $i === (int) $data[$j]['call_t'] ) {
				$graph_data[0]['data'][] = $data[$j]['count_t'];
				$graph_data[1]['data'][] = $data[$j]['sum_billsec'];
				$j++;
			} else {
				$graph_data[0]['data'][] = 0;
				$graph_data[1]['data'][] = 0;
			}
		}
		/* pie 加参数显示 次数或时间, 默认显示时间 */
		//if ( $seriesType == 'pie' ) {
			if ( $_GET['show_time'] != '1' ) {
				unset($graph_data[1]);
			}
			if ( $_GET['show_count'] != '1' ) {
				$graph_data[0] = $graph_data[1];
				unset($graph_data[1]);
			}
		//}

		$lib_file = FILE_PATH.'/lib/report/report_forms.php';
		if ( !file_exists($lib_file) ) {
			return false;
		}
		include($lib_file);
		$graph_report = new report_forms();
		$this->Tmpl['daily_hours'] = $graph_report->column_basic($graph_data, $graph_setting);
		$this->display();
	} /* }}} */

	/**
	 * @fn
	 * @brief 统计通话量各小时负载
	 * 	需求: 按日期的各小时, 呼入呼出 条件检索统计当日各指定小时内,
	 * 	各小时每分钟的(开始)通话条数, 或每分钟正在通话(开始到结束)的总条数.
	 * @param 
	 * @return 
	 */
	function showHours() /* {{{ */
	{
		$_db = ASTERISKCDRDB_DB_NAME;
		$db_table = 'cdr';
		$where = 'where 1 ';
		$day = empty($_GET['day']) ? date('Y-m-d') : $_GET['day'];
		$hours = empty($_GET['hours']) ? '00' : $_GET['hours'];
		$seriesType = empty($_GET['type']) ? '' : $_GET['type'];
		$__title = '';

		/* 月份表名 */
		$sub_table = substr($day, 2, 5);
		if ( $sub_table != date('y-m') ) {
			$db_table = $_db.'.`'.$db_table.'-'.$sub_table.'`';
		} else {
			$db_table = $_db.'.'.$db_table;
		}

		$where .= "AND from_unixtime(calldate,'%Y-%m-%d %H:%i:%s') <= '$day $hours:59:59' AND from_unixtime(calldate,'%Y-%m-%d %H:%i:%s') >= '$day $hours:00:00' ";
		if ( !empty($_GET['extension']) ) {
			$where .= "and (src='".$_GET['extension']."' or dst='{$_GET['extension']}') ";
		}
		if ( !empty($_GET['called']) ) {
			if ( $_GET['called_type'] == 3 ) {
				$where .= "and dst LIKE '".$_GET['called']."%' ";
			} else {
				$where .= "and dst='".$_GET['called']."' ";
			}
		}
		if ( !empty($_GET['caller']) ) {
			if ( $_GET['caller_type'] == 3 ) {
				$where .= "and src LIKE '".$_GET['caller']."%' ";
			} else {
				$where .= "and src='".$_GET['caller']."' ";
			}
		}
		$select = "count(*) as count_t,from_unixtime(calldate,'%i') as call_t, sum(billsec) as sum_billsec";
		$where .= "group by from_unixtime(calldate, '%Y-%m-%d %H:%i') order by call_t ";
		$sql = "select $select from $db_table $where ";
		//SELECT count(*) as count_t,id,from_unixtime(calldate,'%H') as call_t, sum(billsec) as sum_billsec FROM `cdr` WHERE from_unixtime(calldate,'%Y-%m-%d %H:%i:%s') <= '2012-02-10 23:59:59' AND from_unixtime(calldate,'%Y-%m-%d %H:%i:%s') >= '2012-02-10 00:00:00' group by from_unixtime(calldate, '%Y-%m-%d %H') order by call_t;
		$data = $this->get_data($sql);
		if ( $_GET['title'] ) {
			$__title = $_GET['title'];
		}

		$table_data = array();
		/* graph */
		$graph_setting = array();
		$graph_setting['renderTo'] = ACTION_NAME."daily_hours_div".date('His');
		$graph_setting['seriesType'] = $seriesType;
		//$graph_setting['title'] = "$day $hours:00~$hours:59 时负载图";
		$graph_setting['title'] = ($__title ? $__title : "$day $hours:00~$hours:59 时负载图");
		$graph_setting['yAxis'] = "{title:{text:''},min:0}";
		$graph_setting['xcategories'] = "['00','01','02','03','04','05','06','07','08','09']";
		$graph_setting['xAxis'] = "{title:{text:''},categories:['00','01','02','03','04','05','06','07','08','09'],labels:{rotation:-90}}";
		$graph_setting['tooltip'] = "function(){return '<b>'+this.x+'分</b><br/>通话量: '+this.y+'次<br/>';}";
		$graph_setting['plotOptions'] = "{column:{dataLabels:{enabled:true,formatter:function(){return this.y>0?(this.y+'次'):'';}} }}";

		$graph_data = array();
		$graph_data[0]['name'] = "每分钟通话量";
		$graph_data[0]['data'] = array();
		$sum_time = 0;
		$_count = count($data);
		$_v = 60;		/* 60分 */
		$i = 0;
		$j = 0;
		for ( $i = 0; $i < $_v; $i++ ) {
			if ( $i === (int) $data[$j]['call_t'] ) {
				$graph_data[0]['data'][] = $data[$j]['count_t'];
				$j++;
			} else {
				$graph_data[0]['data'][] = 0;
			}
		}

		$lib_file = FILE_PATH.'/lib/report/report_forms.php';
		if ( !file_exists($lib_file) ) {
			return false;
		}
		include($lib_file);
		$graph_report = new report_forms();
		$this->Tmpl['render_id'] = $graph_setting['renderTo'];
		$this->Tmpl['render_js'] = $graph_report->column_basic($graph_data, $graph_setting);
		$this->display();
	} /* }}} */

	/**
	 * @fn
	 * @brief 统计通话量月负载及比较.
	 * 	需求: 按月及指定月前2-6个月, 呼入呼出 条件检索统计当月总通话时间和通话条数与指定前几个月的比较图,
	 * 	本来支持 柱状图 和 圆饼图 指定参数type=pie调用显示, 但调试复杂, 目前默认只支持 柱状图.
	 * @param 
	 * @return 
	 */
	function showMonths() /* {{{ */
	{
		$_db = ASTERISKCDRDB_DB_NAME;
		$db_table = 'cdr';
		$where = 'where 1 ';
		$month = empty($_GET['month']) ? date('Y-m') : $_GET['month'];
		$seriesType = empty($_GET['type']) ? 'column' : $_GET['type'];
		$last_months = empty($_GET['last_months']) ? 2 : $_GET['last_months'];

		/* 要比较的前几个月份的表名 */
		$db_tables = array();
		$cur_month = $month.'-01';
		for ( $i = 0; $i <= $last_months; $i++ ) {
			$sub_table = substr($cur_month, 2, 5);
			$months_a[] = substr($cur_month, 0, 7).'月';
			if ( $sub_table != date('y-m') ) {
				$db_tables[] = $_db.'.`'.$db_table.'-'.$sub_table.'`';
			} else {
				$db_tables[] = $_db.'.'.$db_table;
			}
			$cur_month = date('Y-m-d', strtotime('last month', strtotime($cur_month)));
		}
		asort($db_tables);
		asort($months_a);
		/* 比较指定月份 */
		if ( $_GET['months_select_t'] == '2' ) {
			$db_tables = array();
			$months_a = array();
			/** 指定月份不比较当前月
			if ( !in_array($month, (array)$_GET['months_list']) ) {
				$_GET['months_list'][] = $month;
			}
			 */
			if ( !is_array($_GET['months_list']) ) {
				$_GET['months_list'] = array();
			}
			asort($_GET['months_list']);
			foreach ( $_GET['months_list'] as $m ) {
				$months_a[] = substr($m, 0, 7).'月';
				$m = substr($m, 2, 5);
				if ( $m != date('y-m') ) {
					$db_tables[] = $_db.'.`'.$db_table.'-'.$m.'`';
				} else {
					$db_tables[] = $_db.'.'.$db_table;
				}
			}
			$__title = "指定月份通话比较图";
		}

		$where .= ' ';
		if ( !empty($_GET['extension']) ) {
			$where .= "and (src='".$_GET['extension']."' or dst='{$_GET['extension']}') ";
			$sub_title = "分机".$_GET['extension'].' ';
		}
		if ( !empty($_GET['called']) ) {
			if ( $_GET['called_type'] == 3 ) {
				$where .= "and dst LIKE '".$_GET['called']."%' ";
			} else {
				$where .= "and dst='".$_GET['called']."' ";
			}
			$sub_title = "被叫".$_GET['called'].' ';
		}
		if ( !empty($_GET['caller']) ) {
			if ( $_GET['caller_type'] == 3 ) {
				$where .= "and src LIKE '".$_GET['caller']."%' ";
			} else {
				$where .= "and src='".$_GET['caller']."' ";
			}
			$sub_title = "主叫".$_GET['caller'].' ';
		}
		$select = "count(*) as count_t,from_unixtime(calldate,'%Y-%m') as call_t, sum(billsec) as sum_billsec";
		$where .= "group by from_unixtime(calldate, '%Y-%m') order by call_t ";
		$data = array();
		foreach ( $db_tables as $db_table ) {
			$sql = "select $select from $db_table $where ";
			$__tmp = $this->get_data($sql);
			if ( is_array($__tmp) ) {
				$data = array_merge($data, $__tmp);
			}
		}

		if ( $_GET['title'] ) {
			$__title = $_GET['title'];
		}

		$table_data = array();
		/* graph */
		$graph_setting = array();
		$graph_setting['renderTo'] = ACTION_NAME."month_div".date('His');
		$graph_setting['seriesType'] = $seriesType;
		$graph_setting['title'] = $sub_title.' '.($__title ? $__title : "$month 与前 $last_months 个月通话比较图");
		//$graph_setting['yAxis'] = "[{title:{text:'次'}},{title:{text:'时间'},min:0}]";
		$graph_setting['yAxis'] = "[{title:{text:'次',align:'high',rotation:0}},{title:{text:'分<br/>钟',style:{color:'#aa4643'},align:'high',rotation:0},min:0,opposite:true}]";
		$graph_setting['xcategories'] = $months_a;//"['00','01','02','03','04','05']";
		$graph_setting['legend'] = "{align:'right',x:-30,y:50,verticalAlign:'top',floating:true,layout:'vertical',dclicked:false}";
		$graph_setting['tooltip'] = $seriesType == 'pie' ? "function(){return '<b>'+((this.point.name) ? this.point.name+' '+Math.floor(this.percentage*100)/100+'%</b><br/>' : this.x+' ')+'通话量: '+this.y+'次<br/>';}" : "function(){return '<b>'+this.x+' '+'通话量: '+this.y+'次<br/>';}";

		$graph_data = array();
		$graph_data[0]['name'] = "通话量";
		$graph_data[0]['wtSelect'] = "false";
		$graph_data[0]['pointWidth'] = 50;
		$graph_data[0]['data'] = array();
		$graph_data[1]['wtSelect'] = "true";
		$graph_data[1]['pointWidth'] = 50;
		//$graph_data[1]['type'] = 'line';
		$graph_data[1]['name'] = "通话时间";
		$graph_data[1]['yAxis'] = '1';
		$graph_setting['tooltip'] = "function(){unit={'{$graph_data[0]['name']}':'次','{$graph_data[1]['name']}':'分钟'}[this.series.name];return '<b>'+((this.point.name) ? this.point.name+' '+Math.floor(this.percentage*100)/100+'%</b><br/>' : this.x+' ') + '通话量: '+this.y+unit+'<br/>';}";
		$graph_setting['plotOptions'] = "{line:{dataLabels:{enabled:true,formatter:function(){unit={'{$graph_data[0]['name']}':'次','{$graph_data[1]['name']}':'分钟'}[this.series.name];return this.y+unit;}} },column:{dataLabels:{enabled:true,formatter:function(){unit={'{$graph_data[0]['name']}':'次','{$graph_data[1]['name']}':'分钟'}[this.series.name];return this.y+unit;}} },pie:{cursor:'pointer',dataLabels:{enabled:true,formatter:function(){return this.point.name+' 通话:'+this.y+'次';}},showInLegend:true}}";
		$sum_time = 0;
		$_count = count($data);
		$_v = $last_months;		/* 比较的月数 */
		$i = 0;
		$j = 0;
		//for ( $i = 0; $i < $_count; $i++ ) {}
		foreach ( $months_a as $month_diff ) {
			//if ( $i == $_count - 1 ) {}
			/* 无数据月份填0 */
			if ( $data[$j]['call_t'] != substr($month_diff, 0, 7) ) {
				$graph_data[0]['data'][] = $graph_setting['seriesType'] != 'pie' ? 0 : array($month_diff, 0);
				$graph_data[1]['data'][] = $graph_setting['seriesType'] != 'pie' ? 0 : array($month_diff, 0);
				continue;
			}
			/* 要比较的月份 */
			if ( $month == $data[$j]['call_t'] ) {
				$graph_data[0]['data'][] = "{name:'".($graph_setting['seriesType'] != 'pie'? '' : $data[$j]['call_t'])."',y:{$data[$j]['count_t']},sliced:true,selected:true}";
				$graph_data[1]['data'][] = "{name:'".($graph_setting['seriesType'] != 'pie'? '' : $data[$j]['call_t'])."',y:".ceil($data[$j]['sum_billsec'] / 60).",sliced:true,selected:true}";
			} else {
				$graph_data[0]['data'][] = $graph_setting['seriesType'] != 'pie' ? $data[$j]['count_t'] : array($data[$j]['call_t'].'月', $data[$j]['count_t']);
				$graph_data[1]['data'][] = $graph_setting['seriesType'] != 'pie' ? ceil($data[$j]['sum_billsec'] / 60) : array($data[$j]['call_t'].'月', ceil($data[$j]['sum_billsec'] / 60));
			}
			//$graph_data[0]['data'][] = array($data[$j]['call_t'].'月', $data[$j]['count_t']);
			$j++;
		}
		/* pie 加参数显示 次数或时间, 默认显示时间 */
		//if ( $seriesType == 'pie' ) {
			if ( $_GET['show_time'] != '1' ) {
				unset($graph_data[1]);
			}
			if ( $_GET['show_count'] != '1' ) {
				$graph_data[0] = $graph_data[1];
				unset($graph_data[1]);
			}
		//}

		$lib_file = FILE_PATH.'/lib/report/report_forms.php';
		if ( !file_exists($lib_file) ) {
			return false;
		}
		include($lib_file);
		$graph_report = new report_forms();
		$this->Tmpl['render_id'] = $graph_setting['renderTo'];
		$this->Tmpl['render_js'] = $graph_report->column_basic($graph_data, $graph_setting);
		$this->display();
	} /* }}} */

	function showMonthsPie() /* {{{ */
	{
		$_db = ASTERISKCDRDB_DB_NAME;
		$db_table = 'cdr';
		$where = 'where 1 ';
		$month = empty($_GET['month']) ? date('Y-m') : $_GET['month'];
		$seriesType = empty($_GET['type']) ? 'pie' : $_GET['type'];
		$last_months = empty($_GET['last_months']) ? 2 : $_GET['last_months'];

		/* 要比较的前几个月份的表名 */
		$cur_month = $month.'-01';
		for ( $i = 0; $i <= $last_months; $i++ ) {
			$sub_table = substr($cur_month, 2, 5);
			$months_a[] = substr($cur_month, 0, 7).'月';
			if ( $sub_table != date('y-m') ) {
				$db_tables[] = $_db.'.`'.$db_table.'-'.$sub_table.'`';
			} else {
				$db_tables[] = $_db.'.'.$db_table;
			}
			$cur_month = date('Y-m-d', strtotime('last month', strtotime($cur_month)));
		}

		$where .= ' ';
		if ( !empty($_GET['extension']) ) {
			$where .= "and (src='".$_GET['extension']."' or dst='{$_GET['extension']}') ";
		}
		if ( !empty($_GET['called']) ) {
			if ( $_GET['called_type'] == 3 ) {
				$where .= "and dst LIKE '".$_GET['called']."%' ";
			} else {
				$where .= "and dst='".$_GET['called']."' ";
			}
		}
		if ( !empty($_GET['caller']) ) {
			if ( $_GET['caller_type'] == 3 ) {
				$where .= "and src LIKE '".$_GET['caller']."%' ";
			} else {
				$where .= "and src='".$_GET['caller']."' ";
			}
		}
		$select = "count(*) as count_t,from_unixtime(calldate,'%Y-%m') as call_t, sum(billsec) as sum_billsec";
		$where .= "group by from_unixtime(calldate, '%Y-%m') order by call_t ";
		$data = array();
		foreach ( $db_tables as $db_table ) {
			$sql = "select $select from $db_table $where ";
			$data = array_merge($data, $this->get_data($sql));
		}

		$table_data = array();
		/* graph */
		$graph_setting = array();
		$graph_setting['renderTo'] = ACTION_NAME."month_div".date('His');
		$graph_setting['seriesType'] = $seriesType;
		$graph_setting['title'] = "$month 与前 $last_months 个月通话量和时间比较图";
		$graph_setting['tooltip'] = "function(){return '<b>'+((this.point.name) ? this.point.name+' '+Math.floor(this.percentage*100)/100+'%</b><br/>' : this.x+' ') + '通话量: '+this.y+'次<br/>';}";

		$graph_data = array();
		$graph_data[0]['name'] = "通话量";
		$graph_data[0]['size'] = "90%";
		$graph_data[0]['innerSize'] = "71%";
		$graph_data[0]['data'] = array();
		$graph_data[1]['type'] = $seriesType == 'pie' ? 'pie':'line';
		$graph_data[1]['name'] = "通话时间";
		$graph_data[1]['size'] = "70%";
		$graph_data[1]['yAxis'] = '1';
		$graph_setting['tooltip'] = "function(){unit={'{$graph_data[0]['name']}':'次','{$graph_data[1]['name']}':'分'}[this.series.name];return '<b>'+((this.point.name) ? this.point.name+' '+Math.floor(this.percentage*100)/100+'%</b><br/>' : this.x+' ') + '通话: '+(unit=='次'?this.y:Math.floor(this.y/60))+unit+'<br/>';}";
		$graph_setting['plotOptions'] = "{pie:{cursor:'pointer',dataLabels:{enabled:true,formatter:function(){unit={'{$graph_data[0]['name']}':'次','{$graph_data[1]['name']}':'分'}[this.series.name];return this.point.name+' 通话:'+(unit=='次'?(this.y+unit+Math.floor(this.percentage*100)/100+'%'):(Math.floor(this.y/60)+unit));}},showInLegend:true}}";
		//$graph_setting['plotOptions'] = "{pie:{cursor:'pointer',dataLabels:{enabled:true,formatter:function(){return this.point.name+' 通话:'+this.y+'次 '+Math.floor(this.percentage*100)/100+'%';}},showInLegend:true}}";
		$sum_time = 0;
		$_count = count($data);
		$_v = $last_months;		/* 比较的月数 */
		$i = 0;
		$j = 0;
		for ( $i = 0; $i < $_count; $i++ ) {
		//foreach ( $months_a as $month_diff ) {}
			/* 要比较的月份 */
			if ( $i == 0 ) {
				$graph_data[0]['data'][] = "{name:'".($graph_setting['seriesType'] != 'pie'? '' : $data[$j]['call_t'])."',y:{$data[$j]['count_t']},sliced:true,selected:true}";
				$graph_data[1]['data'][] = "{name:'".($graph_setting['seriesType'] != 'pie'? '' : $data[$j]['call_t'])."',y:{$data[$j]['sum_billsec']},sliced:true,selected:true}";
			} else {
				$graph_data[0]['data'][] = $graph_setting['seriesType'] != 'pie' ? $data[$j]['count_t'] : array($data[$j]['call_t'].'月', $data[$j]['count_t']);
				$graph_data[1]['data'][] = $graph_setting['seriesType'] != 'pie' ? $data[$j]['sum_billsec'] : array($data[$j]['call_t'].'月', $data[$j]['sum_billsec']);
			}
			//$graph_data[0]['data'][] = array($data[$j]['call_t'].'月', $data[$j]['count_t']);
			$j++;
		}
		/* pie 加参数显示 次数或时间, 默认显示时间 */
		//if ( $seriesType == 'pie' ) {
			if ( $_GET['show_time'] != '1' ) {
				$graph_data[0]['innerSize'] = "0";
				$graph_data[0]['size'] = "80%";
				unset($graph_data[1]);
			}
			if ( $_GET['show_count'] != '1' ) {
				$graph_data[0] = $graph_data[1];
				unset($graph_data[1]);
			}
		//}

		$lib_file = FILE_PATH.'/lib/report/report_forms.php';
		if ( !file_exists($lib_file) ) {
			return false;
		}
		include($lib_file);
		$graph_report = new report_forms();
		$this->Tmpl['render_id'] = $graph_setting['renderTo'];
		$this->Tmpl['render_js'] = $graph_report->column_basic($graph_data, $graph_setting);
		$this->display();
	} /* }}} */

	/**
	 * @fn
	 * @brief 统计通话量日负载及比较.
	 * 	需求: 按日期及指定日期前2-6天, 呼入呼出 条件检索统计当天总通话时间和通话条数与指定前几天的比较图,
	 * 	NOTE: 不需要跨表, 但可以跨表.
	 * @brief 统计通话量负载及比较.
	 * 	需求: 按指定日期区间或者多段指定日期区间, 呼入呼出 (按指定统计间隔为x轴)条件检索统计各区间总通话时间和通话条数与指定各段日期区间的比较图,
	 * 	NOTE: 需要跨表.
	 * @param 
	 * @return 
	 */
	function showDaysDiff() /* {{{ */
	{
		$_db = ASTERISKCDRDB_DB_NAME;
		$db_table = 'cdr';
		$where = 'where 1 ';
		$day = empty($_GET['day']) ? date('Y-m-d') : $_GET['day'];
		$seriesType = empty($_GET['type']) ? 'line' : $_GET['type'];
		$last_days = empty($_GET['last_days']) ? 2 : $_GET['last_days'];
		/* 显示通话时间, 通话次数? */
		$show = empty($_GET['chked']) ? 'show_time' : $_GET['chked'];
		/* 允许查询的月份最大限制, 默认6月 */
		$limit_m = empty($_GET['limit']) ? 6 : $_GET['limit'];
		$__title = '';
		$s_days = $_GET['s_day'];
		$e_days = $_GET['e_day'];
		$diff_list = count($s_days);
		if ( $diff_list != count($e_days) ) {
			/* error */;
		}

		if ( $_GET['title'] ) {
			$__title = $_GET['title'];
		}
		$table_data = array();
		$graph_data = array();
		$__vv = 0;
		$count_days = count($s_days);

		/* for list ... */
	for ( $_i = 0; $_i < $count_days; $_i++ ) {
		$day_s = $s_days[$_i];
		$day_e = $e_days[$_i];
		$months_a = array();
		$db_tables = array();
		/* 月份表名 */
		$cur_day = $day_e;
		$cur_month = date('Y-m', strtotime($day_e));
		$last_month = date('Y-m', strtotime($day_s));
		for ( $i = 0; $i < $limit_m; $i++ ) {
			$sub_table = substr($cur_month, 2, 5);
			$months_a[] = $cur_month;
			if ( $sub_table != date('y-m') ) {
				$db_tables[] = $_db.'.`'.$db_table.'-'.$sub_table.'` as c';
			} else {
				$db_tables[] = $_db.'.'.$db_table.' as c';
			}
			if ( $cur_month <= $last_month ) {
				break;
			}
			$cur_month = date('Y-m', strtotime('last month', strtotime($cur_month.'-01')));
		}
		/* 超过限定时间界, 则清到时间界 */
		if ( $i == $limit_m ) {
			$day_s = date('Y-m', strtotime('+1 month', strtotime($cur_month.'-01'))).'-01';
		}

		$where .= ' ';
		if ( !empty($_GET['extension']) ) {
			$where .= "and (src='".$_GET['extension']."' or dst='{$_GET['extension']}') ";
		}
		if ( !empty($_GET['called']) ) {
			if ( $_GET['called_type'] == 3 ) {
				$where .= "and dst LIKE '".$_GET['called']."%' ";
			} else {
				$where .= "and dst='".$_GET['called']."' ";
			}
		}
		if ( !empty($_GET['caller']) ) {
			if ( $_GET['caller_type'] == 3 ) {
				$where .= "and src LIKE '".$_GET['caller']."%' ";
			} else {
				$where .= "and src='".$_GET['caller']."' ";
			}
		}

		$__s = 'count(*)';
		if ( $show == 'show_time' ) {
			$__s = "sum(billsec)";
			$s_title = "通话时间";;
			$s_unit = "分钟";
			$y_title = "分<br/>钟";
		} else if ( $show == 'show_count' ) {
			$__s = "count(*)";
			$s_title = "通话次数";;
			$y_title = $s_unit = "次";
		}
		$time_area = $_GET['time_area'];
		//$select = "count(*) as count_t,from_unixtime(calldate,'%H') as call_t, sum(billsec) as sum_billsec";
		$select = "$__s as sum,count(*) as count_t,floor(calldate/$time_area) as call_t, sum(billsec) as sum_billsec";
		//$group = "group by from_unixtime(calldate, '%Y-%m-%d %H') order by call_t ";
		$group = "group by call_t order by call_t ";
		/* 取数据 */
		$data = array();
		$i = 0;
		foreach ( $db_tables as $_table ) {
			//$sql = "select $select from $_table $where and from_unixtime(calldate,'%Y-%m-%d')='{$days_a[$i]}' $group";
			$sql = "select $select from $_table $where and from_unixtime(calldate,'%Y-%m-%d %H:%i:%s')>='$day_s' and from_unixtime(calldate,'%Y-%m-%d %H:%i:%s')<='$day_e' $group";
			$__tmp = $this->get_data($sql);
			if ( is_array($__tmp) ) {
				/* NOTE: 注意 $data array 顺序 */
				$data = $__tmp + $data;
			}
			$i++;
		}

		//$_i = 0;			/* 线条计数 */
		//$graph_data[0]['name'] = $s_title;
		//$graph_data[0]['data'] = array();
		//$graph_data[1]['type'] = 'line';
		//$graph_data[1]['name'] = "通话时间";
		//$graph_data[1]['yAxis'] = '1';
		$sum_time = 0;
		$_v = count($data);		/* x轴点数 */
		//$_v = $data[$_v-1]['call_t'] - $data[0]['call_t'];
		$_v = ceil((strtotime($day_e)-strtotime($day_s))/$time_area);
		/* 找x轴最大长度 */
		if ( $__vv < $_v ) {
			$__vv = $_v;
		}
		$i = 0;
		$j = 0;
		$graph_data[$_i]['name'] = $day_s;
		if ( $_v < 5 ) {
			$graph_data[$_i]['pointWidth'] = 50;
		}
		/* 填充x轴开始为空的段... */
		$graph_data[$_i]['data'][0] = 0;
		$v_ = $data[0]['call_t'] - ceil(strtotime($day_s)/$time_area);
		for ( $v_; $v_ > 0; $v_-- ) {
			$_v--;
			$graph_data[$_i]['data'][] = 0;
		}
		for ( $i = 0; $i < $_v; $i++ ) {
			if ( $i === ($data[$j]['call_t'] - $data[0]['call_t']) ) {
				if ( $show == 'show_time' ) {
					$graph_data[$_i]['data'][] = ceil($data[$j]['sum'] / 60);
				} else {
					$graph_data[$_i]['data'][] = $data[$j]['sum'] ? $data[$j]['sum'] : 0;
				}
				$j++;
			/* 填充x轴中间和后面无值的段 */
			} else {
				$graph_data[$_i]['data'][] = 0;
			}
			//$graph_data[0]['data'][] = array($data[$j]['call_t'].'月', $data[$j]['count_t']);
		}
	}
		/* end for ... */

		/* 取x轴单位 */
		$x = $time_area;
		$x_unit = array('秒','分','时','天','月','年');
		$x_u = array(60,60,24,31,12);
		for ( $x_i = 0; ; ) {
			if ( $x >= $x_u[$x_i] ) {
				$x = floor($x / $x_u[$x_i]);
				$x_i++;
			} else {
				break;
			}
		}
		$x = $x < 1 ? 1 : $x;
		$x_title = $x_unit[$x_i];
		/* graph */
		$graph_setting = array();
		$graph_setting['renderTo'] = ACTION_NAME."day_div".date('His');
		$graph_setting['seriesType'] = $seriesType;
		$graph_setting['title'] = ($__title ? $__title : "{$s_title}比较图");
		$graph_setting['yAxis'] = "{title:{text:'$y_title',align:'high',rotation:0},min:0}";
		$graph_setting['xAxis'] = "{title:{text:'$x$x_title',align:'high'},min:0,allowDecimals:false,tickWidth:0}";
		//$graph_setting['xcategories'] = "[0,1,2]";
		$graph_setting['tooltip'] = "function(){return '<b>'+this.series.name+' 通话量: '+this.y+'$s_unit<br/>';}";
		$graph_setting['plotOptions'] = "{line:{dataLabels:{enabled:true,formatter:function(){return this.y>0?(this.y+'$s_unit'):'';}} },column:{dataLabels:{enabled:true,formatter:function(){return this.y>0?(this.y+'$s_unit'):'';}} }}";

		/* pie 加参数显示 次数或时间, 默认显示时间 */
		/*if ( $seriesType == 'pie' ) {
			if ( $_GET['show_time'] != '1' ) {
				unset($graph_data[1]);
			}
			if ( $_GET['show_count'] != '1' ) {
				$graph_data[0] = $graph_data[1];
				unset($graph_data[1]);
			}
		}*/

		$lib_file = FILE_PATH.'/lib/report/report_forms.php';
		if ( !file_exists($lib_file) ) {
			return false;
		}
		include($lib_file);
		$graph_report = new report_forms();
		$this->Tmpl['render_id'] = $graph_setting['renderTo'];
		$this->Tmpl['render_js'] = $graph_report->column_basic($graph_data, $graph_setting);
		$this->display();
	} /* }}} */

	/**
	 * @fn
	 * @brief 统计通话量日负载及比较, 表图.
	 * 	需求: 按日期及指定日期前2-6天, 呼入呼出 条件检索统计当天总通话时间和通话条数与指定前几天的比较图,
	 * 	NOTE: 不需要跨表, 但可以跨表.
	 * @param 
	 * @return 
	 */
	function showDaysDiffTab() /* {{{ */
	{
		$_db = ASTERISKCDRDB_DB_NAME;
		$db_table = 'cdr';
		$where = 'where 1 ';
		$day = empty($_GET['day']) ? date('Y-m-d') : $_GET['day'];
		$seriesType = empty($_GET['type']) ? 'line' : $_GET['type'];
		$last_days = empty($_GET['last_days']) ? 2 : $_GET['last_days'];
		/* 自定义显示栏位 */
		$__show = array('show_time','show_tu','show_count','show_time_avg');
		if ( is_array($_GET['chked']) ) {
			foreach ( $__show as $v ) {
				$this->Tmpl[$v] = 'none';
			}
			foreach ( $_GET['chked'] as $v ) {
				$this->Tmpl[$v] = '';
			}
		}
		if ( $_GET['title'] ) {
			$this->Tmpl['title'] = $_GET['title'];
		} else {
			$this->Tmpl['title'] = "通话比较统计";
		}

		/* 月份表名 */
		/*$sub_table = substr($day, 2, 5);
		if ( $sub_table != date('y-m') ) {
			$db_table = $_db.'.`'.$db_table.'-'.$sub_table.'`';
		} else {
			$db_table = $_db.'.'.$db_table;
		}*/
		/* 要比较的前几个月份的表名 */
		$cur_day = $day;
		for ( $i = 0; $i <= $last_days; $i++ ) {
			$sub_table = substr($cur_day, 2, 5);
			$days_a[] = $cur_day;
			if ( $sub_table != date('y-m') ) {
				$db_tables[] = $_db.'.`'.$db_table.'-'.$sub_table.'`';
			} else {
				$db_tables[] = $_db.'.'.$db_table;
			}
			$cur_day = date('Y-m-d', strtotime('last day', strtotime($cur_day)));
		}

		$where .= ' ';
		if ( !empty($_GET['extension']) ) {
			$where .= "and (src='".$_GET['extension']."' or dst='{$_GET['extension']}') ";
		}
		if ( !empty($_GET['called']) ) {
			if ( $_GET['called_type'] == 3 ) {
				$where .= "and dst LIKE '".$_GET['called']."%' ";
			} else {
				$where .= "and dst='".$_GET['called']."' ";
			}
		}
		if ( !empty($_GET['caller']) ) {
			if ( $_GET['caller_type'] == 3 ) {
				$where .= "and src LIKE '".$_GET['caller']."%' ";
			} else {
				$where .= "and src='".$_GET['caller']."' ";
			}
		}
		$select = "count(*) as count_t,from_unixtime(calldate,'%Y-%m-%d') as call_t, sum(billsec) as sum_billsec";
		$group = "group by from_unixtime(calldate, '%Y-%m-%d') order by call_t ";
		$data = array();
		$i = 0;
		foreach ( $db_tables as $db_table ) {
			$sql = "select $select from $db_table $where and from_unixtime(calldate,'%Y-%m-%d')='{$days_a[$i]}' $group";
			$d = $this->get_data($sql);
			if ( !count($d) ) {
				$d[]['call_t'] = $days_a[$i];
			}
			$data[] = $d[0];
			$i++;
		}

		$table_data = array();
		/* table */
		$table_data = array();
		$t_call_time = 0;
		$t_call_sum = 0;
		$t_call_time_avg = 0;
		
		/* graph */

		$this->Tmpl['max_call_time'] = 1;
		$sum_time = 0;
		$_count = count($data);
		$_v = 24;		/* */
		/* 每日两条统计线图, 时间,次数 */
		for ( $_i = 0; $_i < $_count; $_i++ ) {
			$i = 0;
			$table_data[$_i]['calldate'] = $data[$_i]['call_t'];
			if ( $data[$_i]['count_t'] ) {
				$table_data[$_i]['call_sum'] = $data[$_i]['count_t'];
				$table_data[$_i]['call_time'] = $data[$_i]['sum_billsec'];
				$table_data[$_i]['call_time_avg'] = round($data[$_i]['sum_billsec'] / $data[$_i]['count_t']);
			} else {
				$table_data[$_i]['call_sum'] = 0;
				$table_data[$_i]['call_time'] = 0;
				$table_data[$_i]['call_time_avg'] = 0;
			}
			if ( $this->Tmpl['max_call_time'] < $table_data[$_i]['call_time'] ) {
				$this->Tmpl['max_call_time'] = $table_data[$_i]['call_time'];
			}
			$t_call_time += $table_data[$_i]['call_time'];
			$t_call_sum += $table_data[$_i]['call_sum'];
		}

		$this->Tmpl['list'] = $table_data;
		$this->Tmpl['t_call_time'] = $t_call_time;
		$this->Tmpl['t_call_sum'] = $t_call_sum;
		$this->Tmpl['t_call_time_avg'] = $t_call_sum == 0 ? 0 : round($t_call_time/$t_call_sum);
		$this->display();
	} /* }}} */

	/**
	 * @fn
	 * @brief 队列统计.
	 * 	需求: 按日期及指定日期区间, 
	 * 	统计各队列的 呼入量,呼通数量,呼损率,接听损率,平均通话时长,总通话时常 等,
	 * 	NOTE: 可跨表
	 * @param 
	 * @return 
	 */
	function showQueues() /* {{{ */
	{
		$_db = ASTERISKCDRDB_DB_NAME;
		$db_table = 'cdr';
		$where = "where queue!='' ";
		$day_s = empty($_GET['day_s']) ? date('Y-m-d 00:00:00') : $_GET['day_s'];
		$day_e = empty($_GET['day_e']) ? date('Y-m-d 23:59:59') : $_GET['day_e'];
		/* 允许查询的月份最大限制, 默认1年 */
		$limit_m = empty($_GET['limit']) ? 12 : $_GET['limit'];
		$seriesType = empty($_GET['type']) ? 'column' : $_GET['type'];

		/* 要比较的前几个月份的表名 */
		$cur_day = $day_e;
		$cur_month = date('Y-m', strtotime($day_e));
		$last_month = date('Y-m', strtotime($day_s));
		for ( $i = 0; $i < $limit_m; $i++ ) {
			$sub_table = substr($cur_month, 2, 5);
			$months_a[] = $cur_month;
			if ( $sub_table != date('y-m') ) {
				$db_tables[] = $_db.'.`'.$db_table.'-'.$sub_table.'`';
			} else {
				$db_tables[] = $_db.'.'.$db_table;
			}
			if ( $cur_month <= $last_month ) {
				break;
			}
			$cur_month = date('Y-m', strtotime('last month', strtotime($cur_month.'-01')));
		}
		if ( $i == $limit_m ) {
			$day_s = date('Y-m', strtotime('+1 month', strtotime($cur_month.'-01'))).'-01';
		}

		/* */
		if ( !empty($_GET['queue']) ) {
			$where .= "and queue='".$_GET['queue']."' ";
		}
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
		$select = "count(*) as count_t,queue,from_unixtime(calldate,'%Y-%m') as call_t, sum(billsec) as sum_billsec";
		$group = "group by queue order by call_t ";
		/* 取数据 */
		$data = array();
		$_data = array();
		$_dbs = count($db_tables);
		$i = 0;
		for ( $i = 0; $i < $_dbs; $i++ ) {
			$w_day = '';
			if ( $i == 0 && $_dbs == 1 ) {
				$w_day = "and from_unixtime(calldate,'%Y-%m-%d %H:%i:%s')<='$day_e' and from_unixtime(calldate,'%Y-%m-%d %H:%i:%s')>='$day_s' ";
			} else if ( $i == 0 ) {
				$w_day = "and from_unixtime(calldate,'%Y-%m-%d %H:%i:%s')<='$day_e' ";
			} else if ( $i == $_dbs - 1 ) {
				$w_day = "and from_unixtime(calldate,'%Y-%m-%d %H:%i:%s')>='$day_s' ";
			}
			/* 取所有呼入 */
			$sql = "select $select from {$db_tables[$i]} $where $w_day $group";
			$__tmp = $this->get_data($sql);
			if ( is_array($__tmp) ) {
				$data = array_merge($data, $__tmp);
				//$data[] = $__tmp;
			}
			/* 取有接通呼入 */
			$sql = "select $select from {$db_tables[$i]} $where and billsec>0 $w_day $group";
			$__tmp = $this->get_data($sql);
			if ( is_array($__tmp) ) {
				$_data = array_merge($_data, $__tmp);
			}
		}

		/* 合并队列数据 */
		$tmp = array();
		$_tmp = array();
		$_dbs = count($data);
		for ( $i = 0; $i < $_dbs; $i++ ) {
			if ( $tmp[$data[$i]['queue']]['queue'] == $data[$i]['queue'] ) {
				$tmp[$data[$i]['queue']]['count_t'] += $data[$i]['count_t'];
				$tmp[$data[$i]['queue']]['sum_billsec'] += $data[$i]['sum_billsec'];
			} else {
				$tmp[$data[$i]['queue']] = $data[$i];
			}
			if ( !empty($_data[$i]['queue']) && $_tmp[$_data[$i]['queue']]['queue'] == $_data[$i]['queue'] ) {
				$_tmp[$_data[$i]['queue']]['count_t'] += $_data[$i]['count_t'];
				$_tmp[$_data[$i]['queue']]['sum_billsec'] += $_data[$i]['sum_billsec'];
			} else if ( !empty($_data[$i]['queue']) ) {
				$_tmp[$_data[$i]['queue']] = $_data[$i];
			}
		}
		$data = $tmp;
		$_data = $_tmp;

		$table_data = array();
		$table_data[] = array(''=>'队列号码', ''=>'呼入数量', ''=>'呼通数量', ''=>'呼损率', ''=>'接听损失数', ''=>'平均通话时长', ''=>'总通话时长');
		/* graph */
		$graph_setting = array();
		$graph_setting['renderTo'] = ACTION_NAME."day_div".date('His');
		$graph_setting['seriesType'] = $seriesType;
		$graph_setting['title'] = "$day_s 至 {$day_e} {$_GET['queue']}队列通话统计图";
		//$graph_setting['yAxis'] = "{title:{text:'$s_unit'},min:0}";
		$graph_setting['xcategories'] = "['呼入量(次)','呼通数(次)','接听损失数(次)','呼损率(%)','平均时长(分钟)','总时长(分钟)']";
		$graph_setting['tooltip'] = "function(){return '<b>'+this.series.name+'队列 '+this.x+'</b><br/> '+this.y+'$s_unit<br/>';}";
		$graph_setting['plotOptions'] = "{line:{dataLabels:{enabled:true,formatter:function(){return this.y>0?(this.y):'';}} },column:{dataLabels:{enabled:true,formatter:function(){return this.y>0?(this.y):'';}} }}";

		$graph_data = array();
		//$graph_data[0]['name'] = "通话量";
		//$graph_data[0]['data'] = array();
		//$graph_data[1]['type'] = 'line';
		//$graph_data[1]['name'] = "通话时间";
		//$graph_data[1]['yAxis'] = '1';
		$sum_time = 0;
		$_count = count($data);
		$_v = 24;		/* */
		/* 每队列一个柱统计线图 */
		$_i = 0;
		foreach ( $data as $_k => $_v ) {
			$call_ok = isset($_data[$_k]['count_t']) ? $_data[$_k]['count_t'] : 0;
			$call_fail = $data[$_k]['count_t'] - (isset($_data[$_k]['count_t']) ? $_data[$_k]['count_t'] : 0);
			$call_fail_b = round($call_fail / $data[$_k]['count_t'] * 100, 2);
			$call_avg = $call_ok > 0 ? round($data[$_k]['sum_billsec'] / $call_ok / 60, 2) : 0;
			$call_sum = ceil($data[$_k]['sum_billsec'] / 60);
			$graph_data[$_i]['name'] = $data[$_k]['queue'];
			$graph_data[$_i]['data'] = "[{$data[$_k]['count_t']},$call_ok,$call_fail,$call_fail_b,$call_avg,$call_sum]";
			$_i++;
		}

		$lib_file = FILE_PATH.'/lib/report/report_forms.php';
		if ( !file_exists($lib_file) ) {
			return false;
		}
		include($lib_file);
		$graph_report = new report_forms();
		$this->Tmpl['render_id'] = $graph_setting['renderTo'];
		$this->Tmpl['render_js'] = $graph_report->column_basic($graph_data, $graph_setting);
		$this->display();
	} /* }}} */

	/**
	 * @fn
	 * @brief 队列统计, 表图.
	 * 	需求: 按日期及指定日期区间, 
	 * 	统计各队列的 呼入量,呼通数量,呼损率,接听损率,平均通话时长,总通话时常 等,
	 * 	NOTE: 可跨表
	 * @param 
	 * @return 
	 */
	function showQueuesTab() /* {{{ */
	{
		$_db = ASTERISKCDRDB_DB_NAME;
		$db_table = 'cdr';
		$where = "where queue!='' ";
		$day_s = empty($_GET['day_s']) ? date('Y-m-d 00:00:00') : $_GET['day_s'];
		$day_e = empty($_GET['day_e']) ? date('Y-m-d 23:59:59') : $_GET['day_e'];
		/* 允许查询的月份最大限制, 默认1年 */
		$limit_m = empty($_GET['limit']) ? 12 : $_GET['limit'];
		$seriesType = empty($_GET['type']) ? 'column' : $_GET['type'];

		/* 要比较的前几个月份的表名 */
		$cur_day = $day_e;
		$cur_month = date('Y-m', strtotime($day_e));
		$last_month = date('Y-m', strtotime($day_s));
		for ( $i = 0; $i < $limit_m; $i++ ) {
			$sub_table = substr($cur_month, 2, 5);
			$months_a[] = $cur_month;
			if ( $sub_table != date('y-m') ) {
				$db_tables[] = $_db.'.`'.$db_table.'-'.$sub_table.'`';
			} else {
				$db_tables[] = $_db.'.'.$db_table;
			}
			if ( $cur_month <= $last_month ) {
				break;
			}
			$cur_month = date('Y-m', strtotime('last month', strtotime($cur_month.'-01')));
		}
		if ( $i == $limit_m ) {
			$day_s = date('Y-m', strtotime('+1 month', strtotime($cur_month.'-01'))).'-01';
		}

		/* */
		if ( !empty($_GET['queue']) ) {
			$where .= "and queue='".$_GET['queue']."' ";
		}
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
		$select = "count(*) as count_t,queue,from_unixtime(calldate,'%Y-%m') as call_t, sum(billsec) as sum_billsec";
		$group = "group by queue order by call_t ";
		/* 取数据 */
		$data = array();
		$_data = array();
		$_dbs = count($db_tables);
		$i = 0;
		for ( $i = 0; $i < $_dbs; $i++ ) {
			$w_day = '';
			if ( $i == 0 && $_dbs == 1 ) {
				$w_day = "and from_unixtime(calldate,'%Y-%m-%d %H:%i:%s')<='$day_e' and from_unixtime(calldate,'%Y-%m-%d %H:%i:%s')>='$day_s' ";
			} else if ( $i == 0 ) {
				$w_day = "and from_unixtime(calldate,'%Y-%m-%d %H:%i:%s')<='$day_e' ";
			} else if ( $i == $_dbs - 1 ) {
				$w_day = "and from_unixtime(calldate,'%Y-%m-%d %H:%i:%s')>='$day_s' ";
			}
			/* 取所有呼入 */
			$sql = "select $select from {$db_tables[$i]} $where $w_day $group";
			$__tmp = $this->get_data($sql);
			if ( is_array($__tmp) ) {
				$data = array_merge($data, $__tmp);
				//$data[] = $__tmp;
			}
			/* 取有接通呼入 */
			$sql = "select $select from {$db_tables[$i]} $where and billsec>0 $w_day $group";
			$__tmp = $this->get_data($sql);
			if ( is_array($__tmp) ) {
				$_data = array_merge($_data, $__tmp);
			}
		}

		/* 合并队列数据 */
		$tmp = array();
		$_tmp = array();
		$_dbs = count($data);
		for ( $i = 0; $i < $_dbs; $i++ ) {
			if ( $tmp[$data[$i]['queue']]['queue'] == $data[$i]['queue'] ) {
				$tmp[$data[$i]['queue']]['count_t'] += $data[$i]['count_t'];
				$tmp[$data[$i]['queue']]['sum_billsec'] += $data[$i]['sum_billsec'];
			} else {
				$tmp[$data[$i]['queue']] = $data[$i];
			}
			if ( !empty($_data[$i]['queue']) && $_tmp[$_data[$i]['queue']]['queue'] == $_data[$i]['queue'] ) {
				$_tmp[$_data[$i]['queue']]['count_t'] += $_data[$i]['count_t'];
				$_tmp[$_data[$i]['queue']]['sum_billsec'] += $_data[$i]['sum_billsec'];
			} else if ( !empty($_data[$i]['queue']) ) {
				$_tmp[$_data[$i]['queue']] = $_data[$i];
			}
		}
		$data = $tmp;
		$_data = $_tmp;

		$table_data = array();
		//$table_data[] = array(''=>'队列号码', ''=>'呼入数量', ''=>'呼通数量', ''=>'呼损率', ''=>'接听损失数', ''=>'平均通话时长', ''=>'总通话时长');
		/* graph */
		$graph_data = array();

		$t_call_t = 0;
		$t_call_ok = 0;
		$t_call_fail = 0;
		$t_call_fail_b = 0;
		$t_call_avg = 0;
		$t_call_sum = 0;
		$sum_time = 0;
		$_count = count($data);
		/* 每队列一个柱统计线图 */
		$_i = 0;
		foreach ( $data as $_k => $_v ) {
			$t_call_ok += $call_ok = isset($_data[$_k]['count_t']) ? $_data[$_k]['count_t'] : 0;
			$t_call_fail += $call_fail = $data[$_k]['count_t'] - (isset($_data[$_k]['count_t']) ? $_data[$_k]['count_t'] : 0);
			$call_fail_b = round($call_fail / $data[$_k]['count_t'] * 100, 2);
			$call_avg = $call_ok > 0 ? round($data[$_k]['sum_billsec'] / $call_ok / 60) : 0;
			$t_call_sum += $call_sum = ceil($data[$_k]['sum_billsec'] / 60);
			$graph_data[$_i]['name'] = $data[$_k]['queue'];
			$graph_data[$_i]['data'] = "[{$data[$_k]['count_t']},$call_ok,$call_fail,$call_fail_b,$call_avg,$call_sum]";

			$table_data[$_i]['name'] = $data[$_k]['queue'];
			$t_call_t += $table_data[$_i]['call_t'] = $data[$_k]['count_t'];
			$table_data[$_i]['call_ok'] = $call_ok;
			$table_data[$_i]['call_fail'] = $call_fail;
			$table_data[$_i]['call_fail_b'] = $call_fail_b;
			$table_data[$_i]['call_avg'] = $call_avg;
			$table_data[$_i]['call_sum'] = $call_sum;
			$_i++;
		}

		$this->Tmpl['list'] = $table_data;
		$this->Tmpl['t_call_t'] = $t_call_t;
		$this->Tmpl['t_call_ok'] = $t_call_ok;
		$this->Tmpl['t_call_fail'] = $t_call_fail;
		$this->Tmpl['t_call_avg'] = $t_call_ok ? round($t_call_sum / $t_call_ok) : 0;
		$this->Tmpl['t_call_sum'] = $t_call_sum;
		$this->display();
	} /* }}} */

	/**
	 * @fn
	 * @brief 分机统计, 生成表格表, 不生成图表.
	 * 	需求: 按日期及指定日期区间, 指定分机, 被叫主叫,
	 * 	统计各分机的 呼入量,呼通数量,呼损率,接听损率,平均通话时长,总通话时常 等,
	 * 	NOTE: 本应不需要跨表, 但实现已经可跨表
	 * @param 
	 * @return 
	 */
	function showExtension() /* {{{ */
	{
		$_db = ASTERISKCDRDB_DB_NAME;
		$_ast_db = CFG_DB_NAME_PBX;
		$_db_users = ','.$_ast_db.'.users as e';
		$db_table = 'cdr';
		$where = "where 1 ";
		$day_s = empty($_GET['day_s']) ? date('Y-m-d 00:00:00') : $_GET['day_s'];
		$day_e = empty($_GET['day_e']) ? date('Y-m-d 23:59:59') : $_GET['day_e'];
		/* 允许查询的月份最大限制, 默认6月 */
		$limit_m = empty($_GET['limit']) ? 6 : $_GET['limit'];
		$seriesType = empty($_GET['type']) ? 'column' : $_GET['type'];

		/* 要比较的前几个月份的表名 */
		$cur_day = $day_e;
		$cur_month = date('Y-m', strtotime($day_e));
		$last_month = date('Y-m', strtotime($day_s));
		for ( $i = 0; $i < $limit_m; $i++ ) {
			$sub_table = substr($cur_month, 2, 5);
			$months_a[] = $cur_month;
			if ( $sub_table != date('y-m') ) {
				$db_tables[] = $_db.'.`'.$db_table.'-'.$sub_table.'` as c';
			} else {
				$db_tables[] = $_db.'.'.$db_table.' as c';
			}
			if ( $cur_month <= $last_month ) {
				break;
			}
			$cur_month = date('Y-m', strtotime('last month', strtotime($cur_month.'-01')));
		}
		if ( $i == $limit_m ) {
			$day_s = date('Y-m', strtotime('+1 month', strtotime($cur_month.'-01'))).'-01';
		}

		//取所有满足条件的分机
		$where_e = '';
		/* */
		$only_exten = '';
		if ( !empty($_GET['extension']) ) {
			$where .= "and (c.src='".$_GET['extension']."' or c.dst='{$_GET['extension']}') ";
			$where_e .= "or (e.extension='".$_GET['extension']."') ";
			$only_exten = $_GET['extension'];
		}
		if ( !empty($_GET['called']) ) {
			if ( $_GET['called_type'] == 3 ) {
				$where .= "and c.dst LIKE '".$_GET['called']."%' ";
				$where_e .= "or (e.extension like '".$_GET['called']."%') ";
			} else {
				$where .= "and c.dst='".$_GET['called']."' ";
				$where_e .= "or (e.extension='".$_GET['called']."') ";
			}
			$only_exten = $_GET['called'];
		}
		if ( !empty($_GET['caller']) ) {
			if ( $_GET['caller_type'] == 3 ) {
				$where .= "and c.src LIKE '".$_GET['caller']."%' ";
				$where_e .= "or (e.extension like '".$_GET['caller']."%') ";
			} else {
				$where .= "and c.src='".$_GET['caller']."' ";
				$where_e .= "or (e.extension='".$_GET['caller']."') ";
			}
			$only_exten = $_GET['caller'];
		}
		if ( !empty($where_e) ) {
			$where_e = "where (0 $where_e)";
		}
		$select_exten = "select extension from $_ast_db.users as e $where_e ";
		$__tmp = $this->get_data($select_exten);
		$_exten_array = array();
		$_exten_str = '';
		foreach ( $__tmp as $k => $v ) {
			$_exten_array[$v['extension']] = 1;
			$_exten_str .= "'{$v['extension']}',";
		}
		$_exten_str = strlen($_exten_str) ? substr($_exten_str, 0, -1) : "''";
		ksort($_exten_array);

		$select = "count(*) as count_t, c.dst as exten, sum(c.billsec) as sum_billsec, e.extension, e.name ";
		$select_2 = "count(*) as count_t, c.src as exten, sum(c.billsec) as sum_billsec, e.extension, e.name ";
		$group = "group by exten order by exten ";
		/* 取数据 */
		$data = array();	/* 接听 */
		$to_data = array();	/* 呼出 */
		$_dbs = count($db_tables);
		$i = 0;
		for ( $i = 0; $i < $_dbs; $i++ ) {
			$w_day = '';
			if ( $i == 0 && $_dbs == 1 ) {
				$w_day = "and from_unixtime(calldate,'%Y-%m-%d %H:%i:%s')<='$day_e' and from_unixtime(calldate,'%Y-%m-%d %H:%i:%s')>='$day_s' ";
			} else if ( $i == 0 ) {
				$w_day = "and from_unixtime(calldate,'%Y-%m-%d %H:%i:%s')<='$day_e' ";
			} else if ( $i == $_dbs - 1 ) {
				$w_day = "and from_unixtime(calldate,'%Y-%m-%d %H:%i:%s')>='$day_s' ";
			}
			/* 取所有呼入 */
			$sql = "select $select from {$db_tables[$i]} $_db_users $where and c.dst=e.extension $w_day $group";
			$__tmp = $this->get_data($sql);
			if ( is_array($__tmp) ) {
				$data = array_merge($data, $__tmp);
				//$data[] = $__tmp;
			}
			/* 取有呼出 */
			$sql = "select $select_2 from {$db_tables[$i]} $_db_users $where and c.src=e.extension $w_day $group";
			$__tmp = $this->get_data($sql);
			if ( is_array($__tmp) ) {
				$to_data = array_merge($to_data, $__tmp);
			}
		}

		/* 合并数据 */
		$tmp = array();
		$_tmp = array();
		$_tmp_exten = array();
		$_dbs = count($data);
		$_dbs_ = count($to_data);
		$_dbs = $_dbs > $_dbs_ ? $_dbs : $_dbs_;
		for ( $i = 0; $i < $_dbs; $i++ ) {
		//foreach ( $_exten_array as $_k => $_v ) {}
			if ( $only_exten && ($only_exten != $data[$i]['extension'] && $only_exten != $to_data[$i]['extension']) ) {
				continue;
			}
			if ( !empty($data[$i]['extension']) && $tmp[$data[$i]['extension']]['extension'] == $data[$i]['extension'] ) {
				$tmp[$data[$i]['extension']]['count_t'] += $data[$i]['count_t'];
				$tmp[$data[$i]['extension']]['sum_billsec'] += $data[$i]['sum_billsec'];
			} else if ( !empty($data[$i]['extension']) ) {
				$tmp[$data[$i]['extension']] = $data[$i];
				$tmp[$data[$i]['extension']]['exten'] = $data[$i]['name'].'('.$data[$i]['extension'].')';
				$_tmp_exten[$data[$i]['extension']] = 1;
			}
			if ( !empty($to_data[$i]['extension']) && $_tmp[$to_data[$i]['extension']]['extension'] == $to_data[$i]['extension'] ) {
				$_tmp[$to_data[$i]['extension']]['count_t'] += $to_data[$i]['count_t'];
				$_tmp[$to_data[$i]['extension']]['sum_billsec'] += $to_data[$i]['sum_billsec'];
			} else if ( !empty($to_data[$i]['extension']) ) {
				$_tmp[$to_data[$i]['extension']] = $to_data[$i];
				$_tmp[$to_data[$i]['extension']]['exten'] = $to_data[$i]['name'].'('.$to_data[$i]['extension'].')';
				$_tmp_exten[$to_data[$i]['extension']] = 1;
			}
		}
		$data = $tmp;
		$to_data = $_tmp;
		ksort($_tmp_exten);
		//var_dump($data,$to_data);

		$table_data = array();
		$table_data[] = array('分机号', '接听数量', '接听时长', '接听平均时长', '呼出数量', '呼出时长', '呼出平均时长', '总通话量', '总通话时长');
		$__show = array('分机号'=>'no_num_show','姓名'=>'name_show','接听数量'=>'called_show','接听时长'=>'called_time_show','接听平均时长'=>'called_avg_show','呼出数量'=>'caller_show','呼出时长'=>'caller_time_show','呼出平均时长'=>'caller_avg_show','总通话量'=>'call_sum_show','总通话时长'=>'call_sum_time_show');
		/* 自定义显示栏位 */
		/*if ( is_array($_GET['chked']) ) {
			foreach ( $__show as $v ) {
				$this->Tmpl[$v] = 'none';
			}
			foreach ( $_GET['chked'] as $v ) {
				$this->Tmpl[$v] = '';
			}
		}*/
		/* graph */
		$graph_setting = array();
		$graph_setting['renderTo'] = ACTION_NAME."day_div".date('His');
		$graph_setting['seriesType'] = $seriesType;
		$graph_setting['title'] = " $day_s 至 {$day_e} {$_GET['extension']}分机通话统计图";
		//$graph_setting['yAxis'] = "{title:{text:'$s_unit'},min:0}";
		$graph_setting['yAxis'] = "{title:{text:''},min:0}";
		$graph_setting['xcategories'] = "['接听量(次)','接听时长(分钟)','接听平均时长(秒)','呼出量(次)','呼出时长(分钟)','呼出平均时长(秒)','总通话量','总时长(分钟)']";
		$graph_setting['tooltip'] = "function(){return '<b>'+this.series.name+'分机 '+this.x+'</b><br/> '+this.y+'$s_unit<br/>';}";

		$graph_data = array();
		//$graph_data[0]['name'] = "通话量";
		//$graph_data[0]['data'] = array();
		//$graph_data[1]['type'] = 'line';
		//$graph_data[1]['name'] = "通话时间";
		//$graph_data[1]['yAxis'] = '1';
		$sum_time = 0;
		$_count = $_dbs;
		$_v = 24;		/* */
		/* 每分机一个柱统计线图, 因为图片尺寸关系, 只显示前10个 */
		$_i = 0;
		//for ( $_i = 0; $_i < $_count; $_i++ ) {}
		/* x 轴: 各统计 */
		/*foreach ( $_tmp_exten as $_k => $_v ) {
		//foreach ( $_exten_array as $_k => $_v ) {}
			$called = isset($data[$_k]['count_t']) ? $data[$_k]['count_t'] : 0;
			$caller = isset($to_data[$_k]['count_t']) ? $to_data[$_k]['count_t'] : 0;
			$call_sum = $called + $caller;
			$called_time = isset($data[$_k]['sum_billsec']) ? $data[$_k]['sum_billsec'] : 0;
			$caller_time = isset($to_data[$_k]['sum_billsec']) ? $to_data[$_k]['sum_billsec'] : 0;
			$call_sum_time = round(($called_time + $caller_time) / 60, 2);
			$called_avg = $called > 0 ? round($called_time / $called / 60, 2) : 0;
			$caller_avg = $caller > 0 ? round($caller_time / $caller / 60, 2) : 0;
			$called_time = round($called_time / 60, 2);
			$caller_time = round($caller_time / 60, 2);
			$graph_data[$_i]['name'] = empty($data[$_k]['exten']) ? $to_data[$_k]['exten'] : $data[$_k]['exten'];
			$graph_data[$_i]['data'] = "[$called,$called_time,$called_avg,$caller,$caller_time,$caller_avg,$call_sum,$call_sum_time]";
			$_i++;
			if ( $_i > 9 ) { break; }
		}*/
		/* x 轴: 分机 */
		$_count = count($table_data[0]) - 1;
		$graph_setting['xcategories'] = "";
		for ( $_i = 0; $_i < $_count; $_i++ ) {
			$graph_data[$_i]['name'] = $table_data[0][$_i + 1];
			$graph_data[$_i]['wtSelect'] = "true";
			$j = 0;
			foreach ( $_tmp_exten as $_k => $_v ) {
			//foreach ( $_exten_array as $_k => $_v ) {}
				if ( $only_exten && ($only_exten != $data[$_k]['extension'] && $only_exten != $to_data[$_k]['extension']) ) {
					continue;
				}
				$called = isset($data[$_k]['count_t']) ? $data[$_k]['count_t'] : 0;
				$caller = isset($to_data[$_k]['count_t']) ? $to_data[$_k]['count_t'] : 0;
				$call_sum = $called + $caller;
				$called_time = isset($data[$_k]['sum_billsec']) ? $data[$_k]['sum_billsec'] : 0;
				$caller_time = isset($to_data[$_k]['sum_billsec']) ? $to_data[$_k]['sum_billsec'] : 0;
				$call_sum_time = round(($called_time + $caller_time) / 60, 2);
				$called_avg = $called > 0 ? round($called_time / $called / 60, 2) : 0;
				$caller_avg = $caller > 0 ? round($caller_time / $caller / 60, 2) : 0;
				$called_time = round($called_time / 60, 2);
				$caller_time = round($caller_time / 60, 2);
				switch ($_i) {
				case 0: $graph_data[$_i]['data'][] = $called;
					break;
				case 1: 
					$graph_data[$_i]['data'][] = $called_time;
					//$graph_data[$_i]['yAxis'] = '1';
					break;
				case 2: $graph_data[$_i]['data'][] = $called_avg;
					//$graph_data[$_i]['yAxis'] = '1';
					break;
				case 3: $graph_data[$_i]['data'][] = $caller;
					break;
				case 4: $graph_data[$_i]['data'][] = $caller_time;
					//$graph_data[$_i]['yAxis'] = '1';
					break;
				case 5: $graph_data[$_i]['data'][] = $caller_avg;
					//$graph_data[$_i]['yAxis'] = '1';
					break;
				case 6: $graph_data[$_i]['data'][] = $call_sum;
					break;
				case 7: $graph_data[$_i]['data'][] = $call_sum_time;
					//$graph_data[$_i]['yAxis'] = '1';
					break;
				}
				if ( $_i == 0 && (!empty($data[$_k]['exten']) || !empty($to_data[$_k]['exten'])) ) {
					$graph_setting['xcategories'][] = empty($data[$_k]['exten']) ? $to_data[$_k]['exten'] : $data[$_k]['exten'];
				}
				$j++;
				if ( $j > 9 ) { break; }
			}
			if ( is_array($_GET['chked']) && !in_array($__show[$graph_data[$_i]['name']], $_GET['chked']) ) {
				unset($graph_data[$_i]);
				continue;
			}
		}
		//
		$graph_setting['xAxis']['categories'] = $graph_setting['xcategories'];
		$graph_setting['xAxis']['labels'] = "{rotation:-15,align:'right',style:{font:'normal 18px Verdana'}}";

		$lib_file = FILE_PATH.'/lib/report/report_forms.php';
		if ( !file_exists($lib_file) ) {
			return false;
		}
		include($lib_file);
		$graph_report = new report_forms();
		$this->Tmpl['render_id'] = $graph_setting['renderTo'];
		$this->Tmpl['render_js'] = $graph_report->column_basic($graph_data, $graph_setting);
		$this->display();
	} /* }}} */

	/**
	 * @fn
	 * @brief 分机统计, 生成表格表, 不生成图表.
	 * 	需求: 按日期及指定日期区间, 指定分机, 被叫主叫,
	 * 	统计各分机的 呼入量,呼通数量,呼损率,接听损率,平均通话时长,总通话时常 等,
	 * 	NOTE: 本应不需要跨表, 但实现已经可跨表
	 * @param 
	 * @param _GET['export'] == 1 ? 导出结果为文件
	 * @return 
	 */
	function showExtensionTab() /* {{{ */
	{
		$_db = ASTERISKCDRDB_DB_NAME;
		$_ast_db = CFG_DB_NAME_PBX;
		$_db_users = ','.$_ast_db.'.users as e';
		$db_table = 'cdr';
		$where = "where 1 ";
		$day_s = empty($_GET['day_s']) ? date('Y-m-d 00:00:00') : $_GET['day_s'];
		$day_e = empty($_GET['day_e']) ? date('Y-m-d 23:59:59') : $_GET['day_e'];
		/* 允许查询的月份最大限制, 默认6月 */
		$limit_m = empty($_GET['limit']) ? 6 : $_GET['limit'];
		$seriesType = empty($_GET['type']) ? 'column' : $_GET['type'];
		$this->Tmpl['day_s'] = $day_s;
		$this->Tmpl['day_e'] = $day_e;
		$this->Tmpl['d_url'] = $_SERVER['REQUEST_URI'];

		$__show = array('no_num_show','name_show','called_show','called_time_show','called_avg_show','caller_show','caller_time_show','caller_avg_show','call_sum_show','call_sum_time_show');
		/* 自定义显示栏位 */
		if ( is_array($_GET['chked']) ) {
			foreach ( $__show as $v ) {
				$this->Tmpl[$v] = 'none';
			}
			foreach ( $_GET['chked'] as $v ) {
				$this->Tmpl[$v] = '';
			}
		}

		/* 要比较的前几个月份的表名 */
		$cur_day = $day_e;
		$cur_month = date('Y-m', strtotime($day_e));
		$last_month = date('Y-m', strtotime($day_s));
		for ( $i = 0; $i < $limit_m; $i++ ) {
			$sub_table = substr($cur_month, 2, 5);
			$months_a[] = $cur_month;
			if ( $sub_table != date('y-m') ) {
				$db_tables[] = $_db.'.`'.$db_table.'-'.$sub_table.'` as c';
			} else {
				$db_tables[] = $_db.'.'.$db_table.' as c';
			}
			if ( $cur_month <= $last_month ) {
				break;
			}
			$cur_month = date('Y-m', strtotime('last month', strtotime($cur_month.'-01')));
		}
		if ( $i == $limit_m ) {
			$day_s = date('Y-m', strtotime('+1 month', strtotime($cur_month.'-01'))).'-01';
		}

		//取所有满足条件的分机
		$where_e = '';
		/* */
		if ( !empty($_GET['extension']) ) {
			$where .= "and (c.src='".$_GET['extension']."' or c.dst='{$_GET['extension']}') ";
			$where_e .= "or (e.extension='".$_GET['extension']."') ";
			$this->Tmpl['s_extension'] = $_GET['extension'];
		}
		if ( !empty($_GET['called']) ) {
			if ( $_GET['called_type'] == 3 ) {
				$where .= "and c.dst LIKE '".$_GET['called']."%' ";
				$where_e .= "or (e.extension like '".$_GET['called']."%') ";
			} else {
				$where .= "and c.dst='".$_GET['called']."' ";
				$where_e .= "or (e.extension='".$_GET['called']."') ";
			}
			$this->Tmpl['s_called'] = $_GET['called'];
		}
		if ( !empty($_GET['caller']) ) {
			if ( $_GET['caller_type'] == 3 ) {
				$where .= "and c.src LIKE '".$_GET['caller']."%' ";
				$where_e .= "or (e.extension like '".$_GET['caller']."%') ";
			} else {
				$where .= "and c.src='".$_GET['caller']."' ";
				$where_e .= "or (e.extension='".$_GET['caller']."') ";
			}
			$this->Tmpl['s_caller'] = $_GET['caller'];
		}
		if ( !empty($where_e) ) {
			$where_e = "where (0 $where_e)";
		}
		$select_exten = "select extension from $_ast_db.users as e $where_e ";
		$__tmp = $this->get_data($select_exten);
		$_exten_array = array();
		$_exten_str = '';
		foreach ( $__tmp as $k => $v ) {
			$_exten_array[$v['extension']] = 1;
			$_exten_str .= "'{$v['extension']}',";
		}
		$_exten_str = strlen($_exten_str) ? substr($_exten_str, 0, -1) : "''";
		ksort($_exten_array);

		$select = "count(*) as count_t, c.dst as exten, sum(c.billsec) as sum_billsec, e.extension, e.name ";
		$select_2 = "count(*) as count_t, c.src as exten, sum(c.billsec) as sum_billsec, e.extension, e.name ";
		$group = "group by exten order by exten ";
		/* 取数据 */
		$data = array();	/* 接听 */
		$to_data = array();	/* 呼出 */
		$_dbs = count($db_tables);
		$i = 0;
		for ( $i = 0; $i < $_dbs; $i++ ) {
			$w_day = '';
			if ( $i == 0 && $_dbs == 1 ) {
				$w_day = "and from_unixtime(calldate,'%Y-%m-%d %H:%i:%s')<='$day_e' and from_unixtime(calldate,'%Y-%m-%d %H:%i:%s')>='$day_s' ";
			} else if ( $i == 0 ) {
				$w_day = "and from_unixtime(calldate,'%Y-%m-%d %H:%i:%s')<='$day_e' ";
			} else if ( $i == $_dbs - 1 ) {
				$w_day = "and from_unixtime(calldate,'%Y-%m-%d %H:%i:%s')>='$day_s' ";
			}
			/* 取所有呼入 */
			$sql = "select $select from {$db_tables[$i]} $_db_users $where and c.dst=e.extension $w_day $group";
			$__tmp = $this->get_data($sql);
			if ( is_array($__tmp) ) {
				$data = array_merge($data, $__tmp);
				//$data[] = $__tmp;
			}
			/* 取有呼出 */
			$sql = "select $select_2 from {$db_tables[$i]} $_db_users $where and c.src=e.extension $w_day $group";
			$__tmp = $this->get_data($sql);
			if ( is_array($__tmp) ) {
				$to_data = array_merge($to_data, $__tmp);
			}
		}

		/* 合并数据 */
		$tmp = array();
		$_tmp = array();
		$_tmp_exten = array();
		$_dbs = count($data);
		$_dbs_ = count($to_data);
		$_dbs = $_dbs > $_dbs_ ? $_dbs : $_dbs_;
		for ( $i = 0; $i < $_dbs; $i++ ) {
		//foreach ( $_exten_array as $_k => $_v ) {}
			if ( !empty($data[$i]['extension']) && $tmp[$data[$i]['extension']]['extension'] == $data[$i]['extension'] ) {
				$tmp[$data[$i]['extension']]['count_t'] += $data[$i]['count_t'];
				$tmp[$data[$i]['extension']]['sum_billsec'] += $data[$i]['sum_billsec'];
			} else if ( !empty($data[$i]['extension']) ) {
				$tmp[$data[$i]['extension']] = $data[$i];
				$tmp[$data[$i]['extension']]['exten'] = $data[$i]['extension'];
				$_tmp_exten[$data[$i]['extension']] = 1;
			}
			if ( !empty($to_data[$i]['extension']) && $_tmp[$to_data[$i]['extension']]['extension'] == $to_data[$i]['extension'] ) {
				$_tmp[$to_data[$i]['extension']]['count_t'] += $to_data[$i]['count_t'];
				$_tmp[$to_data[$i]['extension']]['sum_billsec'] += $to_data[$i]['sum_billsec'];
			} else if ( !empty($to_data[$i]['extension']) ) {
				$_tmp[$to_data[$i]['extension']] = $to_data[$i];
				$_tmp[$to_data[$i]['extension']]['exten'] = $to_data[$i]['extension'];
				$_tmp_exten[$to_data[$i]['extension']] = 1;
			}
		}
		$data = $tmp;
		$to_data = $_tmp;
		ksort($_tmp_exten);

		$table_data = array();
		//$csv_data = "'座席号码', '接听数量', '接听时长', '接听平均时长', '呼出数量', '呼出时长', '呼出平均时长', '总通话量', '总通话时长'\r\n";
		$csv_data = "工号, 姓名, 接听数量, 接听时长, 接听平均时长, 呼出数量, 呼出时长, 呼出平均时长, 总通话量, 总通话时长\r\n";
		$t_called = 0;
		$t_called_time = 0;
		$t_called_avg = 0;
		$t_caller = 0;
		$t_caller_time = 0;
		$t_caller_avg = 0;
		$t_call_sum = 0;
		$t_call_sum_avg = 0;
		/* graph */
		$graph_setting = array();
		$graph_data = array();
		$sum_time = 0;
		$_count = $_dbs;
		/* */
		$line = (!empty($_GET['line']) && is_numeric($_GET['line'])) ? $_GET['line'] : 10;
		$p = (!empty($_GET['p']) && is_numeric($_GET['p'])) ? $_GET['p'] : 1;
        	$pg = loadClass('tool','page',$this);
		$record_nums = count($_tmp_exten);
		$pg->setNumPerPage($line);
		$pg->setVar($_GET);
		$pg->set($record_nums, $p);
		$this->Tmpl['show_pages'] = $pg->output(true);

		/* 每分机一个柱统计线图, 因为图片尺寸关系, 只显示前10个 */
		$limit = ($p - 1) * $line;
		$_i = 0;
		//for ( $_i = 0; $_i < $_count; $_i++ ) {}
		/* x 轴: 各统计 */
		foreach ( $_tmp_exten as $_k => $_v ) {
		//foreach ( $_exten_array as $_k => $_v ) {}
			if ( $_GET['export'] != 1 ) {
				if ( $_i < $limit ) { $_i++; continue; }
				if ( $_i >= $limit + $line ) { break; }
			}
			$t_called += $called = isset($data[$_k]['count_t']) ? $data[$_k]['count_t'] : 0;
			$t_caller += $caller = isset($to_data[$_k]['count_t']) ? $to_data[$_k]['count_t'] : 0;
			$t_call_sum += $call_sum = $called + $caller;
			$t_called_time += $called_time = isset($data[$_k]['sum_billsec']) ? $data[$_k]['sum_billsec'] : 0;
			$t_caller_time += $caller_time = isset($to_data[$_k]['sum_billsec']) ? $to_data[$_k]['sum_billsec'] : 0;
			$t_call_sum_time += $call_sum_time = ($called_time + $caller_time);
			$t_called_avg += $called_avg = $called > 0 ? ($called_time / $called) : 0;
			$t_caller_avg += $caller_avg = $caller > 0 ? ($caller_time / $caller) : 0;
			$graph_data[$_i]['exten'] = empty($data[$_k]['exten']) ? $to_data[$_k]['exten'] : $data[$_k]['exten'];
			$graph_data[$_i]['name'] = empty($data[$_k]['name']) ? $to_data[$_k]['name'] : $data[$_k]['name'];
			$graph_data[$_i]['data'] = "[$called,$called_time,$called_avg,$caller,$caller_time,$caller_avg,$call_sum,$call_sum_time]";
			$table_data[$_i]['exten'] = $graph_data[$_i]['exten'];
			$table_data[$_i]['name'] = $graph_data[$_i]['name'];
			$table_data[$_i]['called'] = $called;
			$table_data[$_i]['called_time'] = $called_time;
			$table_data[$_i]['called_avg'] = round($called_avg);
			$table_data[$_i]['caller'] = $caller;
			$table_data[$_i]['caller_time'] = $caller_time;
			$table_data[$_i]['caller_avg'] = round($caller_avg);
			$table_data[$_i]['call_sum'] = $call_sum;
			$table_data[$_i]['call_sum_time'] = $call_sum_time;
			if ( $_GET['export'] == 1 ) {
			//$csv_data .= "'座席号码', '接听数量', '接听时长', '接听平均时长', '呼出数量', '呼出时长', '呼出平均时长', '总通话量', '总通话时长'\r\n";
			$csv_data .= "{$graph_data[$_i]['exten']}, {$graph_data[$_i]['name']}, {$table_data[$_i]['called']}, {$table_data[$_i]['called_time']}, {$table_data[$_i]['called_avg']}, {$table_data[$_i]['caller']}, {$table_data[$_i]['caller_time']}, {$table_data[$_i]['caller_avg']}, {$table_data[$_i]['call_sum']}, {$table_data[$_i]['call_sum_time']}\r\n";
			}
			$_i++;
		}

		$this->Tmpl['list'] = $table_data;
		$this->Tmpl['t_called'] = $t_called;
		$this->Tmpl['t_called_time'] = $t_called_time;
		$this->Tmpl['t_called_avg'] = round($t_called_avg);
		$this->Tmpl['t_caller'] = $t_caller;
		$this->Tmpl['t_caller_time'] = $t_caller_time;
		$this->Tmpl['t_caller_avg'] = round($t_caller_avg);
		$this->Tmpl['t_call_sum'] = $t_call_sum;
		$this->Tmpl['t_call_sum_time'] = $t_call_sum_time;
		if ( $_GET['export'] == 1 ) {
			$csv_data .= "总计:, ,{$t_called}, {$t_called_time}, {$this->Tmpl['t_called_avg']}, {$t_caller}, {$t_caller_time}, {$this->Tmpl['t_caller_avg']}, {$t_call_sum}, {$t_call_sum_time}\r\n";
			$csv_data = iconv('utf-8', 'gb2312', $csv_data);
			$this->export_file($csv_data, null, 'csv');
			exit;
		}
		$this->display();
	} /* }}} */

	/**
	 * @fn
	 * @brief 质检统计, 统计饼图.
	 * 	需求: 按日期及指定日期区间, 
	 * 	统计各座席质检数量,质检有效数量,有效平均得分 等,
	 * @param 
	 * @return 
	 */
	function showFeedback() /* {{{ */
	{
		$_db = ASTERISKCDRDB_DB_NAME;
		$db_table = 'feedback';
		$where = "where 1 ";
		$day_s = empty($_GET['day_s']) ? date('Y-m-d 00:00:00') : $_GET['day_s'];
		$day_e = empty($_GET['day_e']) ? date('Y-m-d 23:59:59') : $_GET['day_e'];
		$day_st = $day_s;
		$day_et = $day_e;
		$seriesType = empty($_GET['type']) ? 'line' : $_GET['type'];

		$db_table = "$_db.$db_table";
		/* 条件 */
		if ( !empty($_GET['callerid']) ) {
			$where .= "and callerid='".$_GET['callerid']."' ";
		}
		if ( !empty($_GET['agent']) ) {
			$where .= "and agent='".$_GET['agent']."' ";
		}
		if ( !empty($_GET['score']) ) {
			/* > */
			if ( $_GET['score_type'] == 3 ) {
				$where .= "and score > '".$_GET['score']."' ";
			/* < */
			} else if ( $_GET['score_type'] == 2 ) {
				$where .= "and score < '".$_GET['score']."' ";
			/* = */
			} else {
				$where .= "and score = '".$_GET['score']."' ";
			}
		}
		$select = "count(*) as count_t,uniqueid,agent,score,callerid,sum(score) as sum_score";
		$group = "group by agent order by agent ";
		/* 取数据 */
		$data = array();
		$i = 0;
		$where .= "and calltime<='$day_et' and calltime>='$day_st' ";

		/* 取所有质检 */
		$sql = "select count(*) as count_t from $db_table $where $group";
		$_data = $this->get_data($sql);
		/* 取有效质检 */
		$sql = "select $select from $db_table $where and (score!='' or score is not null) $group";
		$data = $this->get_data($sql);

		$table_data = array();
		$table_data[] = array(''=>'座席号码', ''=>'质检数量', ''=>'有效质检数量', ''=>'平均质检得分');
		/* graph */
		$graph_setting = array();
		$graph_setting['renderTo'] = ACTION_NAME."day_div".date('His');
		$graph_setting['seriesType'] = $seriesType;
		$graph_setting['title'] = "$day_s 至 {$day_e} {$_GET['agent']}质检统计图";
		//$graph_setting['yAxis'] = "{title:{text:'$s_unit'},min:0}";
		$graph_setting['yAxis'] = "[]";
		$graph_setting['xcategories'] = "['质检次数(次)','质检有效数(次)','总得分','平均得分']";
		$graph_setting['tooltip'] = "function(){return '<b>'+this.series.name+'分机 '+this.x+'</b><br/> '+this.y+'$s_unit<br/>';}";
		$graph_setting['plotOptions'] = "{line:{dataLabels:{enabled:true,formatter:function(){return this.y>0?(this.y):'';}} },column:{dataLabels:{enabled:true,formatter:function(){return this.y>0?(this.y):'';}} }}";

		$graph_data = array();
		//$graph_data[0]['name'] = "通话量";
		//$graph_data[0]['data'] = array();
		//$graph_data[1]['type'] = 'line';
		//$graph_data[1]['name'] = "通话时间";
		//$graph_data[1]['yAxis'] = '1';
		$sum_time = 0;
		$_count = count($data);
		$_v = 24;		/* */
		/* TODO:每类数据一个质检饼型统计线图 */
		$_i = 0;
		foreach ( $data as $_k => $_v ) {
			$fb_all = $_data[$_k]['count_t'];
			$fb_ok = $data[$_k]['count_t'];
			$fb_sum = $data[$_k]['sum_score'];
			$fb_avg = $fb_all > 0 ? round($data[$_k]['sum_score'] / $fb_ok, 2) : 0;
			$graph_data[$_i]['name'] = $data[$_k]['agent'];
			$graph_data[$_i]['data'] = "[$fb_all,$fb_ok,$fb_sum,$fb_avg]";
			$_i++;
		}

		$lib_file = FILE_PATH.'/lib/report/report_forms.php';
		if ( !file_exists($lib_file) ) {
			return false;
		}
		include($lib_file);
		$graph_report = new report_forms();
		$this->Tmpl['render_id'] = $graph_setting['renderTo'];
		$this->Tmpl['render_js'] = $graph_report->column_basic($graph_data, $graph_setting);
		$this->display();
	} /* }}} */

	function showFeedbackPie() /* {{{ */
	{
		$_db = ASTERISKCDRDB_DB_NAME;
		$db_table = 'feedback';
		$where = "where 1 ";
		$day_s = empty($_GET['day_s']) ? date('Y-m-d 00:00:00') : $_GET['day_s'];
		$day_e = empty($_GET['day_e']) ? date('Y-m-d 23:59:59') : $_GET['day_e'];
		$day_st = $day_s;
		$day_et = $day_e;
		$seriesType = empty($_GET['type']) ? 'pie' : $_GET['type'];

		$db_table = "$_db.$db_table";
		/* 条件 */
		if ( !empty($_GET['callerid']) ) {
			$where .= "and callerid='".$_GET['callerid']."' ";
		}
		if ( !empty($_GET['agent']) ) {
			$where .= "and agent='".$_GET['agent']."' ";
		}
		if ( !empty($_GET['score']) ) {
			/* > */
			if ( $_GET['score_type'] == 3 ) {
				$where .= "and score > '".$_GET['score']."' ";
			/* < */
			} else if ( $_GET['score_type'] == 2 ) {
				$where .= "and score < '".$_GET['score']."' ";
			/* = */
			} else {
				$where .= "and score = '".$_GET['score']."' ";
			}
		}
		$select = "count(*) as count_t,uniqueid,agent,score,callerid,sum(score) as sum_score";
		$group = "group by agent order by agent ";
		/* 取数据 */
		$data = array();
		$i = 0;
		$where .= "and calltime<='$day_et' and calltime>='$day_st' ";

		/* 取所有质检 */
		$sql = "select count(*) as count_t from $db_table $where $group";
		$_data = $this->get_data($sql);
		/* 取有效质检 */
		$sql = "select $select from $db_table $where and (score!='' or score is not null) $group";
		$data = $this->get_data($sql);

		$table_data = array();
		$table_data[] = array(''=>'座席号码', ''=>'质检数量', ''=>'有效质检数量', ''=>'平均质检得分');
		/* graph */
		$graph_setting = array();
		$graph_setting['renderTo'] = ACTION_NAME."day_div".date('His');
		$graph_setting['seriesType'] = $seriesType;
		$graph_setting['title'] = "$day_s 至 {$day_e} {$_GET['agent']}质检统计图";
		//$graph_setting['yAxis'] = "{title:{text:'$s_unit'},min:0}";
		$graph_setting['yAxis'] = "[{title:{text:'次'}}]";
		$graph_setting['xcategories'] = "['质检次数(次)','质检有效数(次)','平均得分']";
		$graph_setting['tooltip'] = "function(){return '<b>'+this.series.name+'分机</b><br/> '+this.y+'$s_unit<br/>';}";
		$graph_setting['plotOptions'] = "{pie:{cursor:'pointer',dataLabels:{enabled:true,formatter:function(){return this.series.name+' 平均得分:'+this.y+'分 '+Math.floor(this.percentage*100)/100+'%';}},showInLegend:true}}";

		$graph_data = array();
		//$graph_data[0]['name'] = "通话量";
		//$graph_data[0]['data'] = array();
		//$graph_data[1]['type'] = 'line';
		//$graph_data[1]['name'] = "通话时间";
		//$graph_data[1]['yAxis'] = '1';
		$sum_time = 0;
		$_count = count($data);
		$_v = 24;		/* */
		/* 每分机一个质检柱统计线图 */
		$_i = 0;
		foreach ( $data as $_k => $_v ) {
			$fb_all = $_data[$_k]['count_t'];
			$fb_ok = $data[$_k]['count_t'];
			$fb_sum = $data[$_k]['sum_score'];
			$fb_avg = $fb_all > 0 ? round($data[$_k]['sum_score'] / $fb_ok, 2) : 0;
			$graph_data[$_i]['name'] = $data[$_k]['agent'];
			$graph_data[$_i]['data'][] = $fb_avg;
			$_i++;
		}

		$lib_file = FILE_PATH.'/lib/report/report_forms.php';
		if ( !file_exists($lib_file) ) {
			return false;
		}
		include($lib_file);
		$graph_report = new report_forms();
		$this->Tmpl['render_id'] = $graph_setting['renderTo'];
		$this->Tmpl['render_js'] = $graph_report->column_basic($graph_data, $graph_setting);
		$this->display();
	} /* }}} */

	/**
	 * @fn
	 * @brief 质检统计, 统计表图.
	 * 	需求: 按日期及指定日期区间, 
	 * 	统计各座席质检数量,质检有效数量,有效平均得分 等,
	 * @param 
	 * @return 
	 */
	function showFeedbackTab() /* {{{ */
	{
		$_db = ASTERISKCDRDB_DB_NAME;
		$db_table = 'feedback';
		$where = "where 1 ";
		$day_s = empty($_GET['day_s']) ? date('Y-m-d 00:00:00') : $_GET['day_s'];
		$day_e = empty($_GET['day_e']) ? date('Y-m-d 23:59:59') : $_GET['day_e'];
		$day_st = $day_s;
		$day_et = $day_e;
		$seriesType = empty($_GET['type']) ? 'line' : $_GET['type'];

		$db_table = "$_db.$db_table";
		/* 条件 */
		if ( !empty($_GET['callerid']) ) {
			$where .= "and callerid='".$_GET['callerid']."' ";
		}
		if ( !empty($_GET['agent']) ) {
			$where .= "and agent='".$_GET['agent']."' ";
		}
		if ( !empty($_GET['score1']) ) {
			/* > */
			if ( $_GET['score_type1'] == 3 ) {
				$where .= "and score > '".$_GET['score1']."' ";
			/* >= */
			} else if ( $_GET['score_type1'] == 4 ) {
				$where .= "and score >= '".$_GET['score1']."' ";
			/* < */
			} else if ( $_GET['score_type1'] == 1 ) {
				$where .= "and score < '".$_GET['score1']."' ";
			/* <= */
			} else if ( $_GET['score_type1'] == 2 ) {
				$where .= "and score <= '".$_GET['score1']."' ";
			/* = */
			} else {
				$where .= "and score = '".$_GET['score1']."' ";
			}
		}
		if ( !empty($_GET['score2']) ) {
			/* > */
			if ( $_GET['score_type2'] == 3 ) {
				$where .= "and score > '".$_GET['score2']."' ";
			/* >= */
			} else if ( $_GET['score_type2'] == 4 ) {
				$where .= "and score >= '".$_GET['score2']."' ";
			/* < */
			} else if ( $_GET['score_type2'] == 1 ) {
				$where .= "and score < '".$_GET['score2']."' ";
			/* <= */
			} else if ( $_GET['score_type2'] == 2 ) {
				$where .= "and score <= '".$_GET['score2']."' ";
			/* = */
			} else {
				$where .= "and score = '".$_GET['score2']."' ";
			}
		}
		$select = "count(*) as count_t,uniqueid,agent,score,callerid,sum(score) as sum_score";
		$group = "group by agent order by agent ";
		/* 取数据 */
		$data = array();
		$i = 0;
		$where .= "and calltime<='$day_et' and calltime>='$day_st' ";

		/* 取所有质检 */
		$sql = "select count(*) as count_t from $db_table $where $group";
		$_data = $this->get_data($sql);
		/* 取有效质检 */
		$sql = "select $select from $db_table $where and (score!='' or score is not null) $group";
		$data = $this->get_data($sql);

		/* table */
		$table_data = array();
		//$table_data[] = array(''=>'座席号码', ''=>'质检数量', ''=>'有效质检数量', ''=>'平均质检得分');
		$t_fb_all = 0;
		$t_fb_ok = 0;
		$t_fb_sum = 0;
		$t_fb_avg = 0;

		/* graph */
		$graph_data = array();

		$sum_time = 0;
		$_count = count($data);
		$_i = 0;
		foreach ( $data as $_k => $_v ) {
			$t_fb_all += $fb_all = $_data[$_k]['count_t'];
			$t_fb_ok += $fb_ok = $data[$_k]['count_t'];
			$t_fb_sum += $fb_sum = $data[$_k]['sum_score'];
			$fb_avg = $fb_all > 0 ? round($data[$_k]['sum_score'] / $fb_ok, 2) : 0;
			$graph_data[$_i]['name'] = $data[$_k]['agent'];
			$graph_data[$_i]['data'] = "[$fb_all,$fb_ok,$fb_sum,$fb_avg]";

			$table_data[$_i]['name'] = $data[$_k]['agent'];
			$table_data[$_i]['fb_all'] = $fb_all;
			$table_data[$_i]['fb_ok'] = $fb_ok;
			$table_data[$_i]['fb_sum'] = $fb_sum;
			$table_data[$_i]['fb_avg'] = $fb_avg;
			$_i++;
		}

		$this->Tmpl['list'] = $table_data;
		$this->Tmpl['t_fb_all'] = $t_fb_all;
		$this->Tmpl['t_fb_ok'] = $t_fb_ok;
		$this->Tmpl['t_fb_sum'] = $t_fb_sum;
		$this->Tmpl['t_fb_avg'] = $t_fb_ok ? round($t_fb_sum / $t_fb_ok, 2) : 0;
		$this->display();
	} /* }}} */

	/**
	 * @fn
	 * @brief 质检日志.
	 * 	需求: 按日期及指定日期区间, 
	 * 	显示统计质检时间,内外线,得分,录音 等,
	 * @param 
	 * @return 
	 */
	function showFeedbackLog() /* {{{ */
	{
		global $arr_local_priv;
		$this->isAuth( 'feedbacklog_sel', $arr_local_priv, iconv('utf-8','gbk','您没有查看质检日志的权限！') );
		$this->Tmpl['id_'.ACTION_NAME] = "id='current'";
		$_db = ASTERISKCDRDB_DB_NAME;
		$db_table = 'feedback';
		$where = "where 1 ";
		//部门主管，部门上级主管，分主管可见
		if ( POWER_EXTENLIST )
		{
			$where .= "and agent in(".POWER_EXTENLIST.") ";
		}
		$day_s = empty($_GET['day_s']) ? date('Y-m-d') : $_GET['day_s'];
		$day_e = empty($_GET['day_e']) ? date('Y-m-d') : $_GET['day_e'];
		$day_st = $day_s.' 00:00:00';
		$day_et = $day_e.' 23:59:59';
		$line = (!empty($_GET['line']) && is_numeric($_GET['line'])) ? $_GET['line'] : 10;
		$p = (!empty($_GET['p']) && is_numeric($_GET['p'])) ? $_GET['p'] : 1;
		$this->Tmpl['day_s'] = $day_s;
		$this->Tmpl['day_e'] = $day_e;

		$db_table = "$_db.$db_table";
		/* 条件 */
		if ( !empty($_GET['callerid']) ) {
			$where .= "and callerid='".$_GET['callerid']."' ";
			$this->Tmpl['callerid'] = $_GET['callerid'];
		}
		if ( !empty($_GET['agent']) ) {
			$where .= "and agent='".$_GET['agent']."' ";
			$this->Tmpl['agent'] = $_GET['agent'];
		}
		if ( !empty($_GET['score1']) ) {
			/* > */
			if ( $_GET['score_type1'] == 3 ) {
				$where .= "and score > '".$_GET['score1']."' ";
			/* >= */
			} else if ( $_GET['score_type1'] == 4 ) {
				$where .= "and score >= '".$_GET['score1']."' ";
			/* < */
			} else if ( $_GET['score_type1'] == 1 ) {
				$where .= "and score < '".$_GET['score1']."' ";
			/* <= */
			} else if ( $_GET['score_type1'] == 2 ) {
				$where .= "and score <= '".$_GET['score1']."' ";
			/* = */
			} else {
				$where .= "and score = '".$_GET['score1']."' ";
			}
			$this->Tmpl['score1'] = $_GET['score1'];
		}
		if ( !empty($_GET['score2']) ) {
			/* > */
			if ( $_GET['score_type2'] == 3 ) {
				$where .= "and score > '".$_GET['score2']."' ";
			/* >= */
			} else if ( $_GET['score_type2'] == 4 ) {
				$where .= "and score >= '".$_GET['score2']."' ";
			/* < */
			} else if ( $_GET['score_type2'] == 1 ) {
				$where .= "and score < '".$_GET['score2']."' ";
			/* <= */
			} else if ( $_GET['score_type2'] == 2 ) {
				$where .= "and score <= '".$_GET['score2']."' ";
			/* = */
			} else {
				$where .= "and score = '".$_GET['score2']."' ";
			}
			$this->Tmpl['score2'] = $_GET['score2'];
		}
		$_score_list = array('1'=>'小于','2'=>'小于等于','0'=>'等于','3'=>'大于','4'=>'大于等于');
		$this->Tmpl['score_list1'] = select_list('score_type1', $_score_list, empty($_GET['score_type1']) ? '3' : $_GET['score_type1']);
		$this->Tmpl['score_list2'] = select_list('score_type2', $_score_list, $_GET['score_type2']);

		$select = "uniqueid,calltime,agent,score,callerid,score ";
		$order = "order by agent ";
		$limit = "limit ".(($p - 1) * $line).",$line";
		/* 取数据 */
		$data = array();
		$i = 0;
		$where .= "and calltime<='$day_et' and calltime>='$day_st' ";

		$sql = "select count(*) as counts from $db_table $where ";
        	$pg = loadClass('tool','page',$this);
		$record_nums = $this->get_data($sql, 'row');
		$pg->setNumPerPage($line);
		$pg->setVar($_GET);
		$pg->set($record_nums['counts'], $p);
		$this->Tmpl['show_pages'] = $pg->output(true);
		/* 取所有质检 */
		$sql = "select $select from $db_table $where $order $limit";
		$data = $this->get_data($sql);

		/* table */
		$table_data = array();
		$table_data[] = array(''=>'座席号码', ''=>'质检数量', ''=>'有效质检数量', ''=>'平均质检得分');
		/* foreach table in tmpl */

		/* graph */
			
		$this->Tmpl['list'] = $data;
		$this->display();
	} /* }}} */

	/**
	 * @fn
	 * @brief 通话日志.
	 * 	需求: 按日期及指定日期区间, 分机, 外线, 等搜索
	 * 	显示查找分机通话时间,内外线 等,
	 * 	NOTE: 因日志过大, 此处不跨表.
	 * @param 
	 * @return 
	 */
	function showCallLog() /* {{{ */
	{
		$this->Tmpl['id_'.ACTION_NAME] = "id='current'";
		$_db = ASTERISKCDRDB_DB_NAME;
		$db_table = 'cdr';
		$where = "where 1 ";
		$day_s = empty($_GET['day_s']) ? date('Y-m-d') : $_GET['day_s'];
		$day_e = empty($_GET['day_e']) ? date('Y-m-d') : $_GET['day_e'];
		$day_st = $day_s.' 00:00:00';
		$day_et = $day_e.' 23:59:59';
		$line = (!empty($_GET['line']) && is_numeric($_GET['line'])) ? $_GET['line'] : 10;
		$p = (!empty($_GET['p']) && is_numeric($_GET['p'])) ? $_GET['p'] : 1;
		$this->Tmpl['day_s'] = $day_s;
		$this->Tmpl['day_e'] = $day_e;

		/* 月份表名 */
		$sub_table = substr($day_s, 2, 5);
		if ( $sub_table != date('y-m') ) {
			$db_table = $_db.'.`'.$db_table.'-'.$sub_table.'`';
		} else {
			$db_table = $_db.'.'.$db_table;
		}
		/* 条件 */
		if ( !empty($_GET['extension']) ) {
			$where .= "and (src='".$_GET['extension']."' or dst='{$_GET['extension']}') ";
			$this->Tmpl['s_extension'] = $_GET['extension'];
		}
		if ( !empty($_GET['src']) ) {
			$where .= "and src='".$_GET['src']."' ";
			$this->Tmpl['s_src'] = $_GET['src'];
		}
		if ( !empty($_GET['dst']) ) {
			$where .= "and dst='".$_GET['dst']."' ";
			$this->Tmpl['s_dst'] = $_GET['dst'];
		}
		if ( !empty($_GET['flag_type']) ) {
			$where .= "and amaflags = '".$_GET['flag_type']."' ";
		}
		$_flag_list = array('0'=>'全部','1'=>'呼入','2'=>'呼出','3'=>'互打');
		$this->Tmpl['flag_list'] = select_list('flag_type', $_flag_list, $_GET['flag_type']);

		$select = "id,calldate,src,clid,dst,dcontext,channel,dstchannel,lastapp,lastdata,duration,billsec,disposition,amaflags,waittime,queuetime,hanguper ";
		$order = "order by id ";
		$limit = "limit ".(($p - 1) * $line).",$line";
		/* 取数据 */
		$data = array();
		$i = 0;
		$where .= "AND from_unixtime(calldate,'%Y-%m-%d %H:%i:%s') <= '$day_e 23:59:59' AND from_unixtime(calldate,'%Y-%m-%d %H:%i:%s') >= '$day_s 00:00:00' ";

		$sql = "select count(*) as counts from $db_table $where ";
        	$pg = loadClass('tool','page',$this);
		$record_nums = $this->get_data($sql, 'row');
		$pg->setNumPerPage($line);
		$pg->setVar($_GET);
		$pg->set($record_nums['counts'], $p);
		$this->Tmpl['show_pages'] = $pg->output(true);
		/* 取所有质检 */
		$sql = "select $select from $db_table $where $order $limit";
		$data = $this->get_data($sql);

		$hanguper_list = array('caller'=>'主叫方','callee'=>'被叫方','cancel'=>'取消');
		$disposition_list = array('NO ANSWER'=>'未接','FAILED'=>'呼叫失败','BUSY'=>'被叫忙','ANSWERED'=>'已接听');
		/* table */
		$table_data = array();
		$table_data[] = array(''=>'座席号码', ''=>'质检数量', ''=>'有效质检数量', ''=>'平均质检得分');
		/* foreach table in tmpl */

		/* graph */

		$this->Tmpl['list'] = $data;
		$this->Tmpl['hanguper_arr'] = $hanguper_list;
		$this->Tmpl['disposition_arr'] = $disposition_list;
		$this->display();
	} /* }}} */

	/**
	 * @fn
	 * @brief 通话转接日志.
	 * 	需求: 按日期及指定日期区间, 分机, 外线, 等搜索
	 * 	显示查找通话转接时间,内外线 等,
	 * @param 
	 * @return 
	 */
	function showTransCallLog() /* {{{ */
	{
		global $arr_local_priv;
		$this->isAuth( 'transcalllog_sel', $arr_local_priv, iconv('utf-8','gbk','您没有查看通话转接日志的权限！') );
		$this->Tmpl['id_'.ACTION_NAME] = "id='current'";
		$_db = ASTERISKCDRDB_DB_NAME;
		$db_table = 'cdr';
		$where = "where 1 and transfer != '' ";
		//部门主管，部门上级主管，分主管可见
		if ( POWER_EXTENLIST )
		{
			$extensionlist = POWER_EXTENLIST;
			$where .= "and (src in ($extensionlist) or dst in ($extensionlist) or shift='".$_SESSION['userinfo']['extension']."' or transfer='".$_SESSION['userinfo']['extension']."') ";
		}
		$day_s = empty($_GET['day_s']) ? date('Y-m-d') : $_GET['day_s'];
		$day_e = empty($_GET['day_e']) ? date('Y-m-d') : $_GET['day_e'];
		$day_st = $day_s.' 00:00:00';
		$day_et = $day_e.' 23:59:59';
		$line = (!empty($_GET['line']) && is_numeric($_GET['line'])) ? $_GET['line'] : 10;
		$p = (!empty($_GET['p']) && is_numeric($_GET['p'])) ? $_GET['p'] : 1;
		$this->Tmpl['day_s'] = $day_s;
		$this->Tmpl['day_e'] = $day_e;

		/* 月份表名 */
		$sub_table = substr($day_s, 2, 5);
		if ( $sub_table != date('y-m') ) {
			$db_table = $_db.'.`'.$db_table.'-'.$sub_table.'`';
		} else {
			$db_table = $_db.'.'.$db_table;
		}
		/* 条件 */
		if ( !empty($_GET['extension']) ) {
			$where .= "and (src='".$_GET['extension']."' or dst='{$_GET['extension']}') ";
			$this->Tmpl['s_extension'] = $_GET['extension'];
		}
		if ( !empty($_GET['src']) ) {
			$where .= "and src='".$_GET['src']."' ";
			$this->Tmpl['s_src'] = $_GET['src'];
		}
		if ( !empty($_GET['dst']) ) {
			$where .= "and dst='".$_GET['dst']."' ";
			$this->Tmpl['s_dst'] = $_GET['s_dst'];
		}
		if ( !empty($_GET['flag_type']) ) {
			$where .= "and amaflags = '".$_GET['flag_type']."' ";
		}
		$_flag_list = array('0'=>'全部','1'=>'呼入','2'=>'呼出','3'=>'互打');
		$this->Tmpl['flag_list'] = select_list('flag_type', $_flag_list, $_GET['flag_type']);

		$select = "id, calldate, channel, src, dst, transfer, shift, disposition, billsec, hanguper ";
		$order = "order by id ";
		$limit = "limit ".(($p - 1) * $line).",$line";
		/* 取数据 */
		$data = array();
		$i = 0;
		$where .= "AND from_unixtime(calldate,'%Y-%m-%d %H:%i:%s') <= '$day_e 23:59:59' AND from_unixtime(calldate,'%Y-%m-%d %H:%i:%s') >= '$day_s 00:00:00' ";

		$sql = "select count(*) as counts from $db_table $where ";
        	$pg = loadClass('tool','page',$this);
		$record_nums = $this->get_data($sql, 'row');
		$pg->setNumPerPage($line);
		$pg->setVar($_GET);
		$pg->set($record_nums['counts'], $p);
		$this->Tmpl['show_pages'] = $pg->output(true);
		/* 取所有质检 */
		$sql = "select $select from $db_table $where $order $limit";
		$data = $this->get_data($sql);

		$hanguper_list = array('caller'=>'主叫方','callee'=>'被叫方','cancel'=>'取消');
		$disposition_list = array('NO ANSWER'=>'未接','FAILED'=>'呼叫失败','BUSY'=>'被叫忙','ANSWERED'=>'已接听');
		/* table */
		$table_data = array();
		$table_data[] = array(''=>'座席号码', ''=>'质检数量', ''=>'有效质检数量', ''=>'平均质检得分');
		/* foreach table in tmpl */

		/* graph */

		$this->Tmpl['list'] = $data;
		$this->Tmpl['hanguper_arr'] = $hanguper_list;
		$this->Tmpl['disposition_arr'] = $disposition_list;
		$this->display();
	} /* }}} */

	/**
	 * @fn
	 * @brief IVR日志.
	 * 	需求: 按日期及指定日期区间, 分机, 外线, 等搜索
	 * 	显示查找分机通话时间,内外线 等,
	 * @param 
	 * @return 
	 */
	function showIvrLog() /* {{{ */
	{
		global $arr_local_priv;
		$this->isAuth( 'ivrlog_sel', $arr_local_priv, iconv('utf-8','gbk','您没有查看IVR日志的权限！') );
		$this->Tmpl['id_'.ACTION_NAME] = "id='current'";
		$_db = ASTERISKCDRDB_DB_NAME;
		$db_table = 'newivr';
		$where = "where 1 ";
		$day_s = empty($_GET['day_s']) ? date('Y-m-d') : $_GET['day_s'];
		$day_e = empty($_GET['day_e']) ? date('Y-m-d') : $_GET['day_e'];
		$day_st = $day_s.' 00:00:00';
		$day_et = $day_e.' 23:59:59';
		$line = (!empty($_GET['line']) && is_numeric($_GET['line'])) ? $_GET['line'] : 10;
		$p = (!empty($_GET['p']) && is_numeric($_GET['p'])) ? $_GET['p'] : 1;
		$this->Tmpl['day_s'] = $day_s;
		$this->Tmpl['day_e'] = $day_e;

		/* 月份表名 */
		$db_table = $_db.'.'.$db_table;
		/* 条件 */
		if ( !empty($_GET['CallerID']) ) {
			$where .= "and CallerID='".$_GET['CallerID']."' ";
			$this->Tmpl['s_CallerID'] = $_GET['CallerID'];
		}
		if ( !empty($_GET['CurrentIvr']) ) {
			$where .= "and CurrentIvr='".$_GET['CurrentIvr']."' ";
			$this->Tmpl['s_CurrentIvr'] = $_GET['CurrentIvr'];
		}
		if ( !empty($_GET['Key']) ) {
			$where .= "and `Key`='".$_GET['Key']."' ";
			$this->Tmpl['s_Key'] = $_GET['Key'];
		}
		if ( !empty($_GET['divr']) ) {
			$where .= "and divr='".$_GET['divr']."' ";
			$this->Tmpl['s_ivr'] = $_GET['divr'];
		}
		if ( !empty($_GET['iid']) ) {
			$where .= "and id = '".$_GET['iid']."' ";
			$this->Tmpl['s_iid'] = $_GET['iid'];
		}
		$_flag_list = array('0'=>'全部','1'=>'呼入','2'=>'呼出','3'=>'互打');
		$this->Tmpl['flag_list'] = select_list('flag_type', $_flag_list, $_GET['flag_type']);

		$select = "`id`,`CallerID`, `SrcTime`, `SrcChannel`, `CurrentIvr`, `Key`, `Ivr`, `Time` ";
		$order = "order by id ";
		$limit = "limit ".(($p - 1) * $line).",$line";
		/* 取数据 */
		$data = array();
		$i = 0;
		$where .= "AND SrcTime <= '$day_e 23:59:59' AND SrcTime >= '$day_s 00:00:00' ";

		$sql = "select count(*) as counts from $db_table $where ";
        	$pg = loadClass('tool','page',$this);
		$record_nums = $this->get_data($sql, 'row');
		$pg->setNumPerPage($line);
		$pg->setVar($_GET);
		$pg->set($record_nums['counts'], $p);
		$this->Tmpl['show_pages'] = $pg->output(true);
		/* 取所有质检 */
		$sql = "select $select from $db_table $where $order $limit";
		$data = $this->get_data($sql);

		$hanguper_list = array('caller'=>'主叫方','callee'=>'被叫方','cancel'=>'取消');
		$disposition_list = array('NO ANSWER'=>'未接','FAILED'=>'呼叫失败','BUSY'=>'被叫忙','ANSWERED'=>'已接听');
		/* table */
		$table_data = array();
		$table_data[] = array(''=>'座席号码', ''=>'质检数量', ''=>'有效质检数量', ''=>'平均质检得分');
		/* foreach table in tmpl */

		/* graph */

		$this->Tmpl['list'] = $data;
		$this->Tmpl['hanguper_arr'] = $hanguper_list;
		$this->Tmpl['disposition_arr'] = $disposition_list;
		$this->display();
	} /* }}} */

	/**
	 * @fn
	 * @brief 导出数组或字符串为指定类型文件.
	 * @param $data 
	 * 	array: CSV, xls
	 * 	string: txt, CSV, xls, pdf...
	 * @return 
	 */
	function export_file(& $data = '', $fn = '', $sub = '') /* {{{ */
	{
		$fn = empty($fn) ? date('YmdHis') : $fn;
		$fn = empty($sub) ? $fn : $fn.'.'.$sub;
		if ( is_array($data) && $sub != '' ) {
			$__f = 'export_'.$sub;
			$data = $this->{$__f}($data);
		}
		header("Content-type: application/octet-stream");
		header("Accept-Ranges: bytes");
		header("Accept-Length: ".strlen($data));
		header("Content-Disposition: attachment; filename=".basename($fn));
		echo $data;
		exit;
	} /* }}} */

	function export_xls(& $data = array()) /* {{{ */
	{
		if ( !is_array($data) ) {
			return $data;
		}

		$i = 1;
		$j = 0;
		$res = '';
		$res .= $this->xlsBOF();
		foreach ( $data as $v ) {
			if ( !is_array($v) ) {
				$v = iconv('UTF-8', 'GB2312', $v);
				$res .= $this->xlsWriteLabel($i, 0, $v);  
				continue;
			}
			$j = 0;
			foreach ( $v as $v2 ) {
				$v2 = iconv('UTF-8', 'GB2312', $v2);
				$res .= $this->xlsWriteLabel($i, $j, $v2);
				$j++;
			}
			$i++;
		}
		$res .= $this->xlsEOF();

		return $res;
	} /* }}} */

	function export_csv(& $data = array(), $tab = ",") /* {{{ */
	{
		if ( !is_array($data) ) {
			return $data;
		}

		$res = '';
		foreach ( $data as $v ) {
			if ( !is_array($v) ) {
				$v = iconv('UTF-8', 'GB2312', $v);
				$res .= $v."\r\n";
				continue;
			}
			foreach ( $v as $v2 ) {
				$v2 = iconv('UTF-8', 'GB2312', $v2);
				$res .= "$v2".$tab;
			}
			$res .= "\r\n";
		}

		return $res;
	} /* }}} */

	/**
	xlsBOF();  
	xlsWriteLabel(1,0,"My excel line one");
	xlsWriteLabel(2,0,"My excel line two : ");
	xlsWriteLabel(2,1,"Hello everybody");
	xlsEOF();
	*/
	function xlsBOF() /* {{{ */
	{
		return pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
		//return ;
	} /* }}} */

	function xlsEOF() /* {{{ */
	{
		return pack("ss", 0x0A, 0x00);
		//return ;
	} /* }}} */
	
	function xlsWriteNumber($Row, $Col, $Value) /* {{{ */
	{
		$t = '';
		$t = pack("sssss", 0x203, 14, $Row, $Col, 0x0);
		$t .= pack("d", $Value);
		return $t;
	} /* }}} */

	function xlsWriteLabel($Row, $Col, $Value) /* {{{ */
	{
		$t = '';
		$L = strlen($Value);
		$t = pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
		$t .= $Value;
		return $t;
	} /* }}} */

	function get_data($sql, $one = false) /* {{{ */
	{
		static $db = NULL;
		if ( !$db ) {
			$db = $this->loadDB();
		}
		if ( $one == 'one' ) {
			$res = $db->GetOne($sql);
		} else if ( $one == 'row' ) {
			$res = $db->GetRow($sql);
		} else {
			$res = $db->GetAll($sql);
		}
		return $res;
	} /* }}} */

	function get_one_data($sql) /* {{{ */
	{
		static $db = NULL;
		if ( !$db ) {
			$db = $this->loadDB();
		}
		$res = $db->GetOne($sql);
		return $res;
	} /* }}} */
}

function select_list($fSelectName, & $fSelectArray, $fNowVal = "", $fFirstOption = "", $fJavaScript = "", $fBgColorArr = array()) /* {{{ */
{
	$fSelectStr = "<SELECT ID=\"".$fSelectName."\" NAME=\"".$fSelectName."\" ".$fJavaScript.">";
	if (!empty($fFirstOption)) {
		$fSelectStr .= "<option value=\"\">".$fFirstOption."</option>";
	}
	foreach ( $fSelectArray as $key => $val ) {
		$gbColor  = "";
		$selected = "";
		if (!empty($fBgColorArr[$key])) $gbColor = "style=\"COLOR: #".$fBgColorArr[$key]."\" ";
		if (( "$fNowVal" == "$key" ) && ( "$fNowVal" !== "" )) $selected = "SELECTED";
		$fSelectStr .= "<option value=\"".$key."\" ".$gbColor.$selected.">".$val."</option>\n";
	}
	$fSelectStr .= "</SELECT>";
	return $fSelectStr;
} /* }}} */

/* end file */
