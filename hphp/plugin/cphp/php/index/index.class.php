<?php
/***
 * 前台 
 *
 * by: langr <hua@langr.org> May 2007
 * $Id: index.class.php 8 2009-10-20 10:05:34Z langr $
 */
class Index extends Index_Public 
{

	function showIndex()
	{
		$this->display();
	}

	/***
	 * 获取经销商帐号名 (DealerNo)
	 */
	function showGetDealer()
	{
		$uri	= "http://".$_SERVER["HTTP_HOST"];
		$url = parse_url($uri);

		$host	= explode('.', $url['host']);
		$dealer	= $host[0];

		if ($dealer == "www")
			return $dealer;

		$f	= $this->loadDB();
		$sql	= "select DealerNo from dealerinfo where Edition='$dealer'";
		$f->query($sql);
		$f->nextRecord();
		$dealer	= $f->record['DealerNo'];

		if (empty($dealer))
			$dealer = "A00001";
		
		return $dealer;
	}

	/***
	 * 使用 GD 库函数 创建验证码图片
	 */
	function showVerifyCode()
	{
		$type = ($_GET['t'])?($_GET['t']):'png';
		$width = ($_GET['w'])?($_GET['w']):54;
		$height = ($_GET['h'])?($_GET['h']):22;

		Header("Content-type: image/".$type);

		srand((double)microtime()*1000000);
		$randval = sprintf("%04d", rand(1,9999));
		$_SESSION['verifyCode'] = $randval;

		$im = @imagecreate($width,$height);
		$r = Array(225,255,255,223);
		$g = Array(225,236,237,255);
		$b = Array(225,236,166,125);

		$key = rand(0,3);

		$backColor = ImageColorAllocate($im, $r[$key],$g[$key],$b[$key]); 
		$borderColor = ImageColorAllocate($im, 0, 0, 0);				  
		$pointColor = ImageColorAllocate($im, 0, 255, 255);				  

		imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);
		@imagerectangle($im, 0, 0, $width-1, $height-1, $borderColor);
		$stringColor = ImageColorAllocate($im, 255,51,153);
		for($i=0;$i<=10;$i++){
			$pointX = rand(2,$width-2);
			$pointY = rand(2,$height-2);
			@imagesetpixel($im, $pointX, $pointY, $pointColor);
		}

		imagestring($im, 5, 8, 3, $randval, $stringColor);
		$ImageFun='Image'.$type;
		$ImageFun($im);
		@ImageDestroy($im);	
		
		return;
	}
}
