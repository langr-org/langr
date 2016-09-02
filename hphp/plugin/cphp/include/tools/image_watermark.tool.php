<?php
/**
* ͼƬˮӡ��
*
* Copyright PhpUp Studio, http://www.phpup.com, iasky
*
* $Id: image_watermark.tool.php 6 2007-10-28 03:40:44Z langr $
*/
class PhpUpImageWater
{
var $groundImage;
var $groundImageWidth;
var $groundImageHeight;
var $groundImageHandle;
var $waterType;
var $waterPosType;
var $waterPosX;
var $waterPosY;
var $waterWidth;
var $waterHeight;
var $waterImage;
var $waterImageType;
var $waterImageHandle;
var $waterImageAlpha;
var $waterText;
var $waterTextColor;
var $waterTextSize;
var $waterTextFont;
var $errorMsg;

function PhpUpImageWater($groundImage = "demo.jpg", $waterPosType = 0)
{
if(false == file_exists($groundImage))
{
$this->throwError("groundImage404");
}
$this->checkGD();
$this->groundImage = &$groundImage;
$this->waterType = &$waterType;
$this->waterPosType = &$waterPosType;
$this->setGroundImageInfo();
}

function checkGD()
{
if(false == function_exists("gd_info"))
{
$this->throwError("NonGD");
}
}

function setGroundImageInfo()
{
$groundImageType = getimagesize($this->groundImage);
$this->groundImageWidth = $groundImageType[0];
$this->groundImageHeight = $groundImageType[1];
$this->groundImageType = $groundImageType[2];
if($this->groundImageWidth < 150 or $this->groundImageHeight < 150)
{
$this->throwError("TooSmall");
}
}

function setWaterTextInfo($waterText = "phpup.com",$waterTextColor = "#000000", $waterTextSize = "5", $waterTextFont = "./couri.ttf")
{
$this->waterType = 0;
if(strlen($waterTextColor) == 7)
{
$this->waterTextColor = &$waterTextColor;
}
else
{
$this->throwError("WrongColor");
}
$this->waterText = &$waterText;
$this->waterTextSize = &$waterTextSize;
$this->waterTextFont = &$waterTextFont;
$waterTextInfo = imagettfbbox(ceil($this->waterTextSize*1.2), 0, $this->waterTextFont, $this->waterText);
$this->waterWidth = $waterTextInfo[4] - $waterTextInfo[6];
$this->waterHeight = $waterTextInfo[1] - $waterTextInfo[7];
unset($waterTextInfo);
}

function setWaterImageInfo($waterImage = "logo.gif")
{
if(file_exists($waterImage))
{
$this->waterType = 1;
$this->waterImage = &$waterImage;
$waterImageInfo = getimagesize($this->waterImage);
$this->waterWidth = $waterImageInfo[0];
$this->waterHeight = $waterImageInfo[1];
$this->waterImageType = $waterImageInfo[2];
unset($waterImageInfo);
}
else
{
$this->throwError("waterImage404");
}
}

function setWaterPos()
{
switch($this->waterPosType)
{
case 0://���
$this->waterPosX = rand(0,($this->groundImageWidth - $this->waterWidth));
$this->waterPosY = rand(0,($this->groundImageHeight - $this->waterHeight));
break;
case 1://1Ϊ���˾���
$this->waterPosX = 0;
$this->waterPosY = 0;
break;
case 2://2Ϊ���˾���
$this->waterPosX = ($this->groundImageWidth - $this->waterWidth) / 2;
$this->waterPosY = 0;
break;
case 3://3Ϊ���˾���
$this->waterPosX = $this->groundImageWidth - $this->waterWidth;
$this->waterPosY = 0;
break;
case 4://4Ϊ�в�����
$this->waterPosX = 0;
$this->waterPosY = ($this->groundImageHeight - $this->waterHeight) / 2;
break;
case 5://5Ϊ�в�����
$this->waterPosX = ($this->groundImageWidth - $this->waterWidth) / 2;
$this->waterPosY = ($this->groundImageHeight - $this->waterHeight) / 2;
break;
case 6://6Ϊ�в�����
$this->waterPosX = $this->groundImageWidth - $this->waterWidth;
$this->waterPosY = ($this->groundImageHeight - $this->waterHeight) / 2;
break;
case 7://7Ϊ�׶˾���
$this->waterPosX = 0;
$this->waterPosY = $this->groundImageHeight - $this->waterHeight * rand(115,125) / 100;
break;
case 8://8Ϊ�׶˾���
$this->waterPosX = ($this->groundImageWidth - $this->waterWidth) / 2;
$this->waterPosY = $this->groundImageHeight - $this->waterHeight * rand(115,125) / 100;
break;
case 9://9Ϊ�׶˾���
$this->waterPosX = $this->groundImageWidth - $this->waterWidth;
$this->waterPosY = $this->groundImageHeight - $this->waterHeight * rand(115,120) / 100;
break;
default://���
$this->waterPosX = rand(0,($this->groundImageWidth - $this->waterWidth));
$this->waterPosY = rand(0,($this->groundImageHeight - $this->waterHeight));
}
}

function setGroundImageHandle()
{
switch($this->groundImageType)
{
case 1:
$this->groundImageHandle = imagecreatefromgif($this->groundImage);
break;
case 2:
$this->groundImageHandle = imagecreatefromjpeg($this->groundImage);
break;
case 3:
$this->groundImageHandle = imagecreatefrompng($this->groundImage);
break;
default:
$this->throwError("NonType");
}
}

function setWaterImageHandle()
{
switch($this->waterImageType)
{
case 1:
$this->waterImageHandle = imagecreatefromgif($this->waterImage);
break;
case 2:
$this->waterImageHandle = imagecreatefromjpeg($this->waterImage);
break;
case 3:
$this->waterImageHandle = imagecreatefrompng($this->waterImage);
break;
default:
$this->throwError("NonType");
}
}

function putWateredImage($extFileName)
{
switch($this->groundImageType)
{
case 1:
imagegif($this->groundImageHandle, $extFileName.$this->groundImage);
break;
case 2:
imagejpeg($this->groundImageHandle, $extFileName.$this->groundImage);
break;
case 3:
imagepng($this->groundImageHandle, $extFileName.$this->groundImage);
break;
default:
$this->throwError("NonType");
}
}

function destroyHandle()
{
imagedestroy($this->groundImageHandle);
if(isset($this->waterImageHandle))
{
imagedestroy($this->waterImageHandle);
}
}

function makeWater($extFileName = "")
{
$this->setGroundImageHandle();
$this->setWaterPos();
imagealphablending($this->groundImageHandle, true);
if($this->waterType == 0)
{
imagettftext($this->groundImageHandle, $this->waterTextSize, 0, $this->waterPosX, $this->waterHeight + $this->waterPosY, imagecolorallocate($this->groundImageHandle, hexdec(substr($this->waterTextColor,1,2)), hexdec(substr($this->waterTextColor,3,2)), hexdec(substr($this->waterTextColor,5,2))), $this->waterTextFont, $this->waterText);
}
else
{
$this->setWaterImageHandle();
imagecopy($this->groundImageHandle,$this->waterImageHandle , $this->waterPosX, $this->waterPosY, 0, 0, $this->waterWidth,$this->waterHeight);
}
//@unlink($this->groundImage);
$this->putWateredImage($extFileName);
$this->destroyHandle();
}

function throwError($errType)
{
switch($errType)
{
case "TooSmall":
$this->errorMsg = "Ҫ��ˮӡͼƬ̫С";
break;
case "groundImage404":
$this->errorMsg = "Ҫ��ˮӡͼƬ������";
break;
case "waterImage404":
$this->errorMsg = "ˮӡͼƬ������";
break;
case "NonGD":
$this->errorMsg = "û�а�װGD��";
break;
case "NonType":
$this->errorMsg = "��֧�ֵ��ļ���ʽ";
break;
case "WrongColor":
$this->errorMsg = "�������ɫ��ʽ";
break;
default:
$this->errorMsg = "δ֪����";
}

die($this->errorMsg);
exit();
}
}
?>

����

<?php
$h = new PhpUpImageWater("109901809576343.jpg",1);
$h->setWaterImageInfo("small_php.gif",10);
//$h->setWaterTextInfo("phpup.com","#000000","10");
$h->makeWater("watered_");
?>
