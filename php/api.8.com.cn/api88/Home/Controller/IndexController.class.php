<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
	public function index() 
	{
		$this->show(APP_NAME,'utf-8');
	}

	/**
	 * 一篮子鸡蛋，2个2个拿剩1个...
	 */
	public function test()
	{
		for ( $x = 1; ; $x++ ) {
			if ( $x%2 == 1 && 
				$x%3 == 0 && 
				$x%4 == 1 && 
				$x%5 == 1 && 
				$x%6 == 3 && 
				$x%7 == 0 &&
				$x%8 == 1 &&
				$x%9 == 0
			) {
				break;
			}
		}
		echo "x=".$x;
	}
}
/* end file */
