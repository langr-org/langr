<?php 
$url="http://a.b.com/v/k/a/b/c/../d/e/../../../e.html";
$url="https://a.b.com/../v/k/a/../b/c/../d/e/../../../e.html";
//$url="a.b.com/../v/k/a/../b/c/../d/e/../../../e.html";
var_dump(parse_url($url));
echo rurl($url);
function rurl($url)
{
	if ( substr($url, 0, 4) != 'http' ) { 
		$url = 'http://'.$url;
	}
	$http_arr = parse_url($url);
	$arr = explode("/",$http_arr['path']);
	for ( $i=0; $i < count($arr); $i++ ) {
		if ( $arr[$i] == '..' ) {
			if ( $i == 0 ) {
				array_shift($arr);
			} else {
				array_splice($arr, $i-1, 2);				
			}
			$i = -1;
			continue;
		}
	}
	$new_url = join("/",$arr);
	$new_url = $http_arr['scheme']."//".$http_arr['host'].'/'.$new_url;
	return $new_url;
}
?>
