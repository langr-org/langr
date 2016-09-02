<?php

// 调试输出
function p($arr)
{
	echo '<pre>';
	header("Content-Type:text/html;charset=utf-8");
	print_r($arr);
}

/**
 * 二维数组转一维数组
 *
 * 
 * @param array $ids 二维数组
 * @access protected
 * @return array
 */
function array2a1($ids)
{
	if(empty($ids)) {
		return $ids;
	}

	foreach ($ids As $value) {
		foreach($value as $v){
			$array[] =$v;
		}
	}  
	return $array;
}

/**
 * 518预热活动 检测是否可以加息
 * 
 * @param type $jx_array
 * @param type $jx_date
 * @return boolean
 */
function check_product_jx($jx_array, $jx_date)
{
	$date = date('Y-m-d');
	$time = time();
	if (in_array($date, $jx_date)) {//日期符合条件
		for ($i = 0; $i < count($jx_array['jxtime']); $i++) {
			$time_array = explode('~', $jx_array['jxtime'][$i]);
			$stime = strtotime($date . " " . $time_array[0]);
			$etime = strtotime($date . " " . $time_array[1]);
			if ($time >= $stime && $time <= $etime) {
				return true;
			}
		}
	}

	return false;
}

/**
 * 截取字符串
 * 
 * @param string $string 要截取的字符串
 * @param int $start 开始
 * @param int $length 长度
 * @return string
 */
function cut_string($string, $start = 0, $length = 1)
{
	if (strlen($string) > $length) {
		$str = '';
		$len = $start + $length;
		$i = $start;
		while ($i < $len) {
			if (ord(substr($string, $i, 1)) >= 128) {
				$str.=substr($string, $i, 3);
				$i = $i + 3;
			} else {
				$str.=substr($string, $i, 1);
				$i ++;
			}
		}
		return $str;
	}
	
	return $string;
}
