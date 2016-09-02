<?php
/**
 * @file base_hc.php
 * @brief 报表统计图, 基类.
 * 
 * Copyright (C) 2012 WiseTalk.cn
 * All rights reserved.
 * 
 * @package base_highcharts
 * @author Langr <hua@langr.org> 2012/02/06 13:57
 * 
 * $Id: base_highcharts.php 602 2012-04-25 09:46:58Z huangh $
 */

class base_highcharts
{
	public $renderID = '';

	function __construct() /* {{{ */
	{
	} /* }}} */

	function __destruct() /* {{{ */
	{
	} /* }}} */

	/**
	 * @fn
	 * @brief 
	 * @param 
	 * @return 
	 */
	function include_js($path = '', $export = '') /* {{{ */
	{
		$js = "<script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js\" ></script>";
		$js .= "<script type=\"text/javascript\" src=\"".$path."js/highcharts.js\" ></script>";
		if ( $export == 'export' ) {
			$js .= "<script type=\"text/javascript\" src=\"".$path."js/modules/exporting.js\"></script>";
		}
		return $js;
	} /* }}} */

	/**
	 * @fn
	 * @brief 
	 * @param $data array()
	 * @param $setting array or json string:
	 * 	array(
	 * 		renderTo => '',		//div id
	 * 		seriseType => '',	//line, spline, area, areaspline, column, bar, pie, scatter...
	 * 		inverted => '',		//false, true
	 * 		width => '',
	 * 		height => '',
	 * 		title => '',
	 * 		subtitle => '',
	 * 		xtitle => '',
	 * 		ytitle => '',
	 * 		xcategories => '',
	 * 		ycategories => '',
	 * 		xAxis => '',
	 * 		yAxis => '',
	 * 		xAxisMax => '',
	 * 		yAxisMax => '',
	 * 		tooltip => '',
	 * 		series => '[{,[]},{,[]}]',
	 * 	)
	 * 	string:
	 * 	"{chart:{renderTo:'renderDiv',defaultSeriesType:'line',marginRight:130,marginBottom:25},
	 * 	title:{text:'xx statistical chart',x:-20},subtitle:{text:'xx.xx',x:-20},
	 * 	xAxis:{},
	 * 	yAxis:{},
	 * 	tooltip:{},
	 * 	legend:{},
	 * 	series:[{name:'',data:[]},{name:'',data:[]},{name:'',data:[]}]
	 * 	}"
	 * @return 
	 */
	function base_setting(&$data = array(), &$base_setting = array()) /* {{{ */
	{
		$this->renderID = $base_setting['renderTo'];
		$base_data = empty($base_setting['series']) ? $this->_ready_data($data) : $base_setting['series'];
		$base_plotOptions = empty($base_setting['plotOptions']) ? '{}' : $this->_ready_data($base_setting['plotOptions']);
		$base_xAxis = empty($base_setting['xAxis']) ? 
			"{title:{text:'{$base_setting['xtitle']}'},categories:{$this->_ready_data($base_setting['xcategories'])}}"
			: $this->_ready_data($base_setting['xAxis']);
		$base_yAxis = empty($base_setting['yAxis']) ? 
			"{title:{text:'{$base_setting['ytitle']}'},categories:{$this->_ready_data($base_setting['ycategories'])}}" 
			: $this->_ready_data($base_setting['yAxis']);
		$legend = empty($base_setting['legend']) ? '' : 'legend:'.$this->_ready_data($base_setting['legend']).',';
		$set_data = $this->renderID." = {
			chart:{renderTo:'{$base_setting['renderTo']}',defaultSeriesType:'{$base_setting['seriesType']}',inverted:{$base_setting['inverted']}},
			title:{text:'{$base_setting['title']}'},
			subtitle:{text:'{$base_setting['subtitle']}'},
			xAxis:$base_xAxis,
			yAxis:$base_yAxis,
			$legend
			tooltip:{formatter:{$base_setting['tooltip']}},
			plotOptions:$base_plotOptions,
			/*credits:{enabled:false},*/
			series:$base_data
			};";
		return $set_data;
	} /* }}} */

	/**
	 * @fn
	 * @brief 
	 * @param $renderID renderDiv ID 
	 * @param $setting highcharts object:
	 * 	$setting = "subtitle = {text:'subtitle.com'}"; 
	 * 	$setting = "subtitle.text = 'subtitle.com'"; 
	 * 	$setting = "chart.renderTo = 'otherDiv'";
	 * @return 
	 */
	function setting($renderID, $setting) /* {{{ */
	{
		return $renderID.'.'.$setting.';';
	} /* }}} */

	/**
	 * @fn
	 * @brief 
	 * @param $type defaultSeriseType. 
	 * @param $data array()
	 * 		series:[{data:[x,y]},{data:[x2,y2]},{data:[x3,y3]}]
	 * 		series[0] = {data:[[],[],[]]}
	 * 	$data = array(
	 * 	)
	 * @return 
	 */
	function _ready_data($data = array(), $json_encode = false) /* {{{ */
	{
		if ( $json_encode && function_exists('json_encode') ) {
			return json_encode($data);
		}

		return $this->do_json_encode($data);
	} /* }}} */

	/**
	 * @fn
	 * @brief 完整的 json_encode(), 比json插件编码要完善,
	 * 	支持数组, 字符串, 及数组与字符串的混合.
	 * @author Langr <hua@langr.org> 2012/02/09 14:59
	 * @param $data array()
	 * 	$arr = array(
	 * 	array('name'=>'nums','data'=>array(array(3,4),array(4,5),array(5,6))), 
	 * 	array('name'=>'nums','data'=>array(2,5,'3')),
	 * 	"{name:'aaa',data:[8,5,3]}",
	 * 	array('name'=>'nnnn', 'data'=>"[5,'q',20]"),
	 * 	array('name'=>'nums','data'=>array('4',3.2,'a')),
	 * 	)
	 * @return json: 
	 * 	[{"name":"nums","data":[[3,4],[4,5],[5,6]]},{name:'aaa',data:[8,5,3]},{"name":"nnnn","data":[5,'q',20]},{"name":"nums","data":[2,5,3]},{"name":"nums","data":[4,3.2,"a"]}]
	 */
	function do_json_encode(&$data = array()) /* {{{ */
	{
		if ( empty($data) ) {
			return "''";
		}
		if ( is_string($data) ) {
			if ( $data[0] == '{' || $data[0] == '[' ) {
				return $data;
			} else {
				return "\"$data\"";
			}
		}
		$serise = '';
		$serise_s = '[';
		$serise_e = ']';
		foreach ( $data as $k => $v ) {
			if ( !is_numeric($k) ) {
				$serise_s = '{';
				$serise_e = '}';
				break;
			}
		}
		foreach ( $data as $k => $v ) {
			if ( $serise_s == '{' ) {
				$serise .= "\"$k\":";
			}
			if ( is_numeric($v) ) {
				$serise .= "$v,";
			} else if ( !is_array($v) ) {
				if ( $v[0] == '{' || $v[0] == '[' ||
						$v == 'true' || $v == 'false' ) {
					$serise .= "$v,";
				} else {
					$serise .= "\"$v\",";
				}
			} else {
				$serise .= $this->do_json_encode($v).',';
			}
			continue;
		}
		if ( $serise[strlen($serise) - 1] == ',' ) {
			$serise = substr($serise, 0, -1);
		}

		return $serise_s.$serise.$serise_e;
	} /* }}} */

	function render($renderID) /* {{{ */
	{
		return "$(document).ready(function() { chart = new Highcharts.Chart($renderID); });";
	} /* }}} */

	function base_line(&$data = array(), &$base_setting = array()) /* {{{ */
	{
		if ( is_string($base_setting) ) {
			return $base_setting;
		}
		$ret = $this->base_setting($data, $base_setting);
		$ret .= $this->render($this->renderID);
		return $ret;
	} /* }}} */

	/**
	 * @fn
	 * @brief 
	 * @param 
	 * @return 
	 */
	function base_area(&$data = array(), &$base_setting = array()) /* {{{ */
	{
		if ( is_string($base_setting) ) {
			return $base_setting;
		}
		$ret = $this->base_setting($data, $base_setting);
		$ret .= $this->render($this->renderID);
		return $ret;
	} /* }}} */

	/**
	 * @fn
	 * @brief 
	 * @param 
	 * @return 
	 */
	function base_column(&$data = array(), &$base_setting = array()) /* {{{ */
	{
		if ( is_string($base_setting) ) {
			return $base_setting;
		}
		$ret = $this->base_setting($data, $base_setting);
		$ret .= $this->render($this->renderID);
		return $ret;
	} /* }}} */

	/**
	 * @fn
	 * @brief 
	 * @param 
	 * @return 
	 */
	function base_pie(&$data = array(), &$base_setting = array()) /* {{{ */
	{
		if ( is_string($base_setting) ) {
			return $base_setting;
		}
		$ret = $this->base_setting($data, $base_setting);
		$ret .= $this->render($this->renderID);
		return $ret;
	} /* }}} */

	/**
	 * @fn
	 * @brief 
	 * @param 
	 * @return 
	 */
	function base_3d(&$data = array(), &$base_setting = array()) /* {{{ */
	{
		;
		return ;
	} /* }}} */
}
/* end file */
