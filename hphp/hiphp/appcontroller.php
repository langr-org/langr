<?php

$__inc_files[] = __FILE__;

class appController extends controller
{
	public $model = null;
	public $view = null;
	protected $models = array();
	protected $views = array();
	protected $data = array();
	private static $count = 0;

	function __construct($view)
	{
		echo __CLASS__."__construct()<br/>";

		$this->view = $view;
		$this->count += 1;
		parent::__construct();
	}

	function test($id, $page)
	{
		echo "action: ".__METHOD__.":test($id, $page)<br/>";
		echo $this->view.'<br/>';
		$this->test2($id, $page);
		$this->_test3($id, $page);
	}

	function test2($id, $page)
	{
		echo "action: ".__METHOD__.":test2($id, $page)<br/>";
		echo $this->name.'<br/>';
	}

	function _test3($id, $page)
	{
		echo "action: ".__METHOD__.":_test3($id, $page)<br/>";
		parent::_test3($id, $page, $this->name);
	}

	static function getThis()
	{
		echo __METHOD__.' self::count: '.'<br/>';
	}

	/**
	 * Îö¹¹º¯Êý
	 */
	function __destruct()
	{
		if ( $this->model ) {
			/* $this->model->free(); */
		}
		
		echo __CLASS__."__destruct()<br/>";
	}
}

/* end file */
