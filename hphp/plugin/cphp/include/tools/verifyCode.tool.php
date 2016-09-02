<?
	session_start(); 
	$type = ($_GET['t'])?($_GET['t']):'png';
	$width = ($_GET['w'])?($_GET['w']):198;
	$height = ($_GET['h'])?($_GET['h']):34;
	$letterArr = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");

	Header("Content-type: image/".$type);

	$verifyCodeRandArray = randArray($letterArr,11);
	array_push($verifyCodeRandArray,"X");
	unset($_SESSION['verifyCode']);
	$i=0;
	while (list($k,$v)=each($verifyCodeRandArray)) {
		$_SESSION['verifyCode'][$i] = $v;
		$i++;
	}
	$letter = implode(" ",$_SESSION['verifyCode']);
	$_SESSION['verifyCode'] = array_flip($_SESSION['verifyCode']);

	$im = @imagecreate($width,$height);
	$r = Array(225,255,255,223);
	$g = Array(225,236,237,255);
	$b = Array(225,236,166,125);

	$key = rand(0,3);

	$backColor = ImageColorAllocate($im, $r[$key],$g[$key],$b[$key]); //背景色（隨機）
	$borderColor = ImageColorAllocate($im, 0, 0, 0);				  //邊框色

	imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);
	@imagerectangle($im, 0, 0, $width-1, $height-1, $borderColor);
	$stringColor1 = ImageColorAllocate($im, 255,rand(0,100), rand(0,100));
	$stringColor2 = ImageColorAllocate($im, rand(0,100), rand(0,100), 255);
	for($i=0;$i<=50;$i++){
		$pointX = rand(4,$width-4);
		$pointY = rand(16,$height-4);
		@imagesetpixel($im, $pointX, $pointY, $stringColor2);
	}

	imagestring($im, 5, 5, 1, "0 1 2 3 4 5 6 7 8 9 X", $stringColor1);
	imagestring($im, 5, 5, 15, $letter, $stringColor2);
	$ImageFun='Image'.$type;
	$ImageFun($im);
	@ImageDestroy($im);	

	#============================================================================
	# 隨機從數組中取N個組成新的數組
	# dealArray ：數組
	# num ：數量
	#----------------------------------------------------------------------------
	function randArray($dealArray,$num){
		if ( !is_array($dealArray) ) Return "";
		if ( $num > count($dealArray) ) Return $dealArray;
		if ( $num <= 0 ) Return "";
		srand((float) microtime() * 10000000);
		$rand_keys = array_rand($dealArray, $num);
		if ( $num == 1 ) {
			$resultArray[$rand_keys] = $dealArray[$rand_keys];
		} else {
			for($j=0;$j<$num;$j++){
				$resultArray[$rand_keys[$j]] = $dealArray[$rand_keys[$j]];
			}
		}
		return $resultArray;
	}
?>