<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<meta name="Keywords" content="麻將大悶鍋,台灣麻將,16張麻將,麻將,麻將遊戲,遊戲,線上麻將,打麻將,玩麻將,小遊戲,好玩遊戲,好玩遊戲區,免費遊戲,免費線上遊戲,免費麻將遊戲下載,免費麻將,網路遊戲,online game,益智遊戲,遊戲下載,打發時間" /> 
<meta name="Description" content="提供線上免費麻將遊戲，打麻將不怕3缺1，免等，永遠不寂寞！畫面清晰，遊戲流暢，港台明星模仿秀，真人錄音超  ！" />
<meta name="author" content="艾德網科技股份有限公司" /> 
<title>{$webSiteTitle}</title>
<style type="text/css">
<!--
body {
	margin-top: 0px;
	background-image: url();
	background-repeat: repeat;
	background-color: #FF9900;
}
.style1 {color: #FFFFFF}
.style2 {
	color: #FF0000;
	font-weight: bold;
}
.style3 {
	color: #666666;
	font-weight: bold;
}
.style4 {color: #000000}
.style5 {color: #999999}
-->
</style>
<script type="text/JavaScript">
<!--
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->
</script>
</head>

<body onload="MM_preloadImages('images/index/menu_01-over.gif','images/index/menu_02-over.gif','images/index/menu_03-over.gif','images/index/menu_04-over.gif','images/index/menu_05-over.gif','images/index/menu_06-over.gif','images/index/menu_07-over.gif','images/index/menu_08-over.gif','images/index/menu_09-over.gif','images/index/menu_10-over.gif','images/index/menu_11-over.gif','images/index/menu_12-over.gif')">
<table width="10%" height="53%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
	  <td align="center" valign="top" scope="row">
<!-- 公共头部 -->
<!-- ?
$this->loadTmplate(TEMPLATE_PATH."public/twmj/left.tpl.php");
? -->
<!-- 公共头部 -->
{$header}
<!-- end head -->
	  </td>
  </tr>
  <tr>
    <td height="43" align="center" scope="row"><table width="950" height="41" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <th width="963" background="images/index/top.jpg" scope="row"><table width="790" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="175" scope="row"> <div align="left" class="style1">首頁&gt;</div></td>
            <td width="175">&nbsp;</td>
            <td width="175">&nbsp;</td>
            <td width="175">&nbsp;</td>
            <td width="68">{$login}</td>
          </tr>
        </table></th>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="434" align="center" valign="top" scope="row"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr align="left">
        <th width="176" valign="top" scope="row"><table width="165" height="376" border="1" cellpadding="0" cellspacing="0" bordercolor="e1e1e1">
          <tr>
		  <td align="center">
<!-- 公共导航条 -->
<!-- ?
$this->loadTmplate(TEMPLATE_PATH."public/twmj/left.tpl.php");
? -->
<!-- 公共头部 -->
{$header}
<!-- end head -->
		  </td>
          </tr>
        </table>
          <br />
          <p>&nbsp;</p></th>
        <td valign="top">
		<!-- body -->
		{$body}
		<!-- end body -->
        </td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <th valign="top" scope="row"><table width="950" height="30" border="0" cellpadding="0" cellspacing="0" bgcolor="#33CC66">
      <tr>
 	      <td height="30" scope="row">
<!-- 公共尾部 -->
<?
$this->loadTmplate(TEMPLATE_PATH."public/twmj/foot.tpl.php");
?>
	      </td>
      </tr>
    </table></th>
  </tr>
</table>
</body>
</html>
