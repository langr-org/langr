<?php
session_start();
/* foreach 遍历对象私有属性... */
    class sample implements Iterator
    {
        private $_items = array(1,2,3,4,5,6,7);
     
        public function __construct() {
              ;//void
        }
        public function rewind() { reset($this->_items); }
        public function current() { return current($this->_items); }
        public function key() { return key($this->_items); }
        public function next() { return next($this->_items); }
        public function valid() { return ( $this->current() !== false ); }
    }
     
    $sa = new sample();
    foreach($sa as $key => $val){
        print $key . "=>" .$val.'<br/>';
    }

class a
{
	private $priv = array(2,3,4,5,6,7);
	//public $priv = array(2,3,4,5,6,7);
	public $pub = array(2,3,4,5,6,7);
	protected $_age = 28;
	public function __construct() {
		;//void
	}
}
$t1 = new a();
foreach ($t1 as $key => $val) {
	print 'key:'.$key.'=>'.print_r($val, true).'<br/>';
}

var_dump($t1);
$tmp = (array)$t1;
var_dump($tmp);
foreach ($tmp["\0a\0priv"] as $key => $val) {
	print 'key 2:'.$key.'=>'.print_r($val, true).'<br/>';
}

class Foo {
    private $_name = "laruence";
    protected $_age = 28;
}
$foo = new Foo();
var_dump($arr);
$arr = (array) $foo;
var_dump($arr);
var_dump($arr["\0Foo\0_name"]);
var_dump($arr["\0*\0_age"]);

?>
<html>
<head>
<title>lang</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<?php
?>
</body>
</html>
