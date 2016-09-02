<?php
/**
 * @file report_forms.php
 * @brief 
 * 
 * Copyright (C) 2012 WiseTalk.cn
 * All rights reserved.
 * 
 * @package highcharts
 * @author Langr <hua@langr.org> 2012/02/07 15:40
 * 
 * $Id: report_forms.php 539 2012-04-20 01:55:33Z huangh $
 */

class report_forms
{
	/**
	 * @var $core: 'highcharts', 'swf'
	 * @default 'highcharts'
	 */
	public $core = 'highcharts';
	public $lib = null;

	function __construct($lib_name = '') /* {{{ */
	{
		$this->set_lib($lib_name);
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
	function set_lib($lib_name) /* {{{ */
	{
		if ( !empty($lib_name) ) {
			$this->core = $lib_name;
		}
		return $this->load_lib();
	} /* }}} */

	function load_lib() /* {{{ */
	{
		if ( empty($this->core) ) {
			return false;
		}
		$lib_file = dirname(__FILE__).'/base_'.$this->core.'.php';
		if ( !file_exists($lib_file) ) {
			return false;
		}
		include($lib_file);
		$class = 'base_'.$this->core;
		$this->lib = new $class();

		return $this->lib;
	} /* }}} */

	/**
	 * @fn
	 * @brief 
	 * @param 
	 * @return 
	 */
	function line(&$data = array(), &$setting = array()) /* {{{ */
	{
		return $this->lib->base_line($data, $setting);
	} /* }}} */

	function line_basic(&$data = array(), &$setting = array()) /* {{{ */
	{
		if ( empty($setting['seriesType']) ) {
			$setting['seriesType'] = 'line';
		}
		if ( !isset($setting['inverted']) ) {
			$setting['inverted'] = 'false';
		}
		if ( empty($setting['tooltip']) ) {
			$setting['tooltip'] = "function(){return '<b>'+this.x+'</b><br/>'+this.series.name+': '+this.y+'<br/>';}";
		}
		return $this->lib->base_line($data, $setting);
	} /* }}} */

	function line_spline(&$data = array(), &$setting = array()) /* {{{ */
	{
		if ( empty($setting['seriesType']) ) {
			$setting['seriesType'] = 'spline';
		}
		if ( !isset($setting['inverted']) ) {
			$setting['inverted'] = 'false';
		}
		if ( empty($setting['tooltip']) ) {
			$setting['tooltip'] = "function(){return '<b>'+this.x+'</b><br/>'+this.series.name+': '+this.y+'<br/>';}";
		}
		return $this->lib->base_line($data, $setting);
	} /* }}} */

	function line_inverted(&$data = array(), &$setting = array()) /* {{{ */
	{
		if ( empty($setting['seriesType']) ) {
			$setting['seriesType'] = 'line';
		}
		if ( !isset($setting['inverted']) ) {
			$setting['inverted'] = 'true';
		}
		if ( empty($setting['tooltip']) ) {
			$setting['tooltip'] = "function(){return '<b>'+this.x+'</b><br/>'+this.series.name+': '+this.y+'<br/>';}";
		}
		return $this->lib->base_line($data, $setting);
	} /* }}} */

	/**
	 * @fn
	 * @brief 
	 * @param 
	 * @return 
	 */
	function area_basic(&$data = array(), &$setting = array()) /* {{{ */
	{
		;
	} /* }}} */

	/**
	 * @fn
	 * @brief 
	 * @param 
	 * @return 
	 */
	function column_basic(&$data = array(), &$setting = array()) /* {{{ */
	{
		if ( empty($setting['seriesType']) ) {
			$setting['seriesType'] = 'column';
		}
		if ( !isset($setting['inverted']) ) {
			$setting['inverted'] = 'false';
		}
		if ( empty($setting['tooltip']) ) {
			$setting['tooltip'] = "function(){return '<b>'+this.x+'</b><br/>'+this.series.name+': '+this.y+'<br/>';}";
		}
		return $this->lib->base_line($data, $setting);
	} /* }}} */

	function column_bar(&$data = array(), &$setting = array()) /* {{{ */
	{
		if ( empty($setting['seriesType']) ) {
			$setting['seriesType'] = 'bar';
		}
		if ( !isset($setting['inverted']) ) {
			$setting['inverted'] = 'false';
		}
		if ( empty($setting['tooltip']) ) {
			$setting['tooltip'] = "function(){return '<b>'+this.x+'</b><br/>'+this.series.name+': '+this.y+'<br/>';}";
		}
		return $this->lib->base_line($data, $setting);
	} /* }}} */

	/**
	 * @fn
	 * @brief 
	 * @param 
	 * @return 
	 */
	function pie_basic(&$data = array(), &$setting = array()) /* {{{ */
	{
		;
	} /* }}} */

	/**
	 * @fn
	 * @brief 
	 * @param 
	 * @return 
	 */
	function pie_3d(&$data = array(), &$setting = array()) /* {{{ */
	{
		;
	} /* }}} */
}
/* end file */
