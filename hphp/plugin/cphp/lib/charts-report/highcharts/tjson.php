<?php
$t_array = array(array('name'=>'nums','data'=>"3,4,5"), "name:'aaa',data:'2,5,3'",array('name'=>'nums','data'=>array(4,3,2)));
$t_array = array(array('name'=>'nums','data'=>"[3,4,5]"), "{name:'aaa',data:[2,5,3]}",array('name'=>'nums','data'=>array(4,3,2)));
$t_array4 = array(array('name'=>'nums','data'=>"[3,4,5]"), "{name:'aaa',data:[2,5,3]}",array('name'=>'nums','data'=>"[4,3,2.1]"));
$t_array = array(array('name'=>'nums','data'=>array(array(3,4),array(4,5),array(5,6))), '1'=>"{name:'aaa',data:[8,5,3]}", array('name'=>'nnnn', 'data'=>"[5,'q',20]"), array('name'=>'nums','data'=>array(2,5,'3')),array('name'=>'nums','data'=>array('4',3.2,'a')));

$res = json_encode($t_array);

echo $res.'<br/>';

echo _ready_data($t_array);

echo '<br/>'.do_json($t_array).'<br/>';

	function _ready_data($data = array(), $json_encode = false) /* {{{ */
	{
		if ( is_string($data) ) {
			return $data;
		}
		if ( $json_encode ) {
			return json_encode($data);
		}

		$serise = '';
		$serise_count = count($data);
		foreach ( $data as $k => $v ) {
			if ( !is_array($v) ) {
				if ( $v[0] == '{' || $v[0] == '[' ) {
					$serise .= "$v,";
				} else {
					$serise .= "\"$v\",";
				}
				continue;
			}
			$serise .= '{';
			foreach ( $v as $k2 => $v2 ) {
				/* data: */
				if ( !is_numeric($k2) ) {
					$serise .= "\"$k2\":";
				}
				if ( !is_array($v2) ) {
					if ( $v2[0] == '{' || $v2[0] == '[' ) {
						$serise .= "$v2,";
					} else {
						$serise .= "\"$v2\",";
					}
					continue;
				}
				$serise .= '[';
				foreach ( $v2 as $k3 => $v3 ) {
					if ( is_numeric($v3) ) {
						$serise .= "$v3,";
					} else if ( !is_array($v3) ) {
						if ( $v3[0] == '{' || $v3[0] == '[' ) {
							$serise .= "$v3,";
						} else {
							$serise .= "\"$v3\",";
						}
					} else {
						$serise .= "\"Array\",";
					}
					continue;
				}
				if ( $serise[strlen($serise) - 1] == ',' ) {
					$serise = substr($serise, 0, -1);
				}
				$serise .= '],';
			}
			if ( $serise[strlen($serise) - 1] == ',' ) {
				$serise = substr($serise, 0, -1);
			}
			$serise .= '},';
		}
		if ( $serise[strlen($serise) - 1] == ',' ) {
			$serise = substr($serise, 0, -1);
		}

		return '['.$serise.']';
	} /* }}} */

	function _do_json($data = array()) {
		if ( is_string($data) ) {
			if ( $data[0] == '{' || $data[0] == '[' ) {
				return $data;
			} else {
				return "\"$data\"";
			}
		}
		
		return _do_json($data);
	}

	function do_json($data = array())
	{
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
				if ( $v[0] == '{' || $v[0] == '[' ) {
					$serise .= "$v,";
				} else {
					$serise .= "\"$v\",";
				}
			} else {
				//$serise .= "\"Array\",";
				$serise .= do_json($v).',';
			}
			continue;
		}
		if ( $serise[strlen($serise) - 1] == ',' ) {
			$serise = substr($serise, 0, -1);
		}

		return $serise_s.$serise.$serise_e;
	}
