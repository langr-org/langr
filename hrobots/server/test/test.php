<?php
echo 1;
//if ( !defined('APP_DEBUG') ) { define('APP_DEBUG', true); }
if ( !defined('H2H_COOKIE_PATH') ) { define('H2H_COOKIE_PATH', './'); }
//if ( !defined('APP_LOG_PATH') ) { define('APP_LOG_PATH', '../Runtime/Logs/tmp'); }

$api_config['key'] = 'DHeL>rt%y<:JKO#:k+_p';
$user = isset($_GET['user']) ? $_GET['user'] : 'langr';
$key = md5($user.'@'.$api_config['key']);
echo $user.'@'.$key."<br/>";
echo md5($user.'@'.$key)."<br/>";
echo md5('HQPG94@D:\wamp\web\kitsmall\rebots\hrebots\client\lib'.'@'.$key)."<br/>";
/**
 * @fn
 * @brief 连接url，处理一个页面中的href连接，
 * 	同时需处理url中的path, ../, args...
 * @param $purl	父url, 
 * @param $surl	子url, 
 * @return 
 */
function _href_url($purl, $surl)
{
	if ( substr($purl, 0, 4) != 'http' ) { 
		$purl = 'http://'.$purl;
	}
	$p = parse_url($purl);
	$p['path'] = empty($p['path']) ? '/' : $p['path'];
	if ( substr($surl, 0, 4) != 'http' ) {
		$surl = ($surl[0] == '/' ? $p['scheme'].'://'.$p['host'].$surl
			: (substr($p['path'], -1) == '/' ? $p['scheme'].'://'.$p['host'].$p['path'].$surl 
				: $p['scheme'].'://'.$p['host'].$p['path'].'/../'.$surl));
		$surl = format_url($surl);
	}
	return $surl;
}

$mod_config['header'] = array(
	'User-Agent'=>'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36', 
	//'Accept-Language'=>'zh-CN,zh;q=0.8', 
	'Accept-Language'=>'en-US,en;q=0.6', 
	'Accept-Encoding'=>'',
	'Referer'=>'http://www.newark.com/'
);

require '../Common/common.php';
$url = "http://www.mouser.com/Electronic-Components/../Electromechanical/_/N-5g1x/";
$url1 = "http://www.mouser.com/Electronic-Components/../Electromechanical/_/N-5g1x/?";
$url2 = "http://www.mouser.com/Electronic-Components/../Electromechanical/_/N-5g1x";
$url3 = "http://www.mouser.com/Electronic-Components/../Electromechanical/_/N-5g1x?No=25&aa=/../a.html";
$url4 = "http://www.mouser.com/Semiconductors/Integrated-Circuits-ICs/Embedded-Processors-Controllers/Microcontrollers-MCU/_/N-a86ge/?No=22675";

//var_dump(parse_url($url), parse_url($url1, PHP_URL_QUERY), parse_url($url2), parse_url($url3));
echo href_url("www.a.com/e", 'a/b/c?c=/../d')."\r\n<br/>";
echo href_url($url2, '/../../a/b/?c=c/../d')."\r\n<br/>";
echo href_url($url2, 'a.b.com/../../a//b/c/../d')."\r\n<br/>";
echo href_url($url2, '../../a/b/c/..//d')."\r\n<br/>";

//$html = http2host($url4, null, $mod_config['header']);
//wlog('a2.html', $html);
//$html = file_get_contents('d (6).html');
$html = file_get_contents('n.html');
$rv['rule'] = '%<tr class="SearchResultsRow.*<a id="ctl00.*_lnkMouserPartNumber".* href="(.*)">(.*)</a>.*_MfrPartNumberLink" href=".*">(.*)</a>.*_lnkSupplier" href="(.*)">(.*)</a>.*_lnkAvailability">(.*)</span>%iUs';
//$rv['rule'] = '%<tr class="SearchResultsRow.*<a id="ctl00.*_lnkMouserPartNumber".* href="(.*)">(.*)</a>.*_MfrPartNumberLink" href=".*">(.*)</a>.*_lnkSupplier" href="(.*)">(.*)</a>.*_lnkAvailability">(.*)</span>%iUs';
$rv['rule'] = '%<a id="ctl.*MouserPartNumber".* href="(.*)">(.*)</a>.*PartNumberLink" href=".*">(.*)</a>.*Supplier" href="(.*)">(.*)</a>.*Availability">([0-9,]*)([^0-9,]*)</span>%iUs';
$rv['rule'] = '%<tr class="SearchResultsRow.*<a id="ctl00.*MouserPartNumber".* href="(.*)">(.*)</a>.*PartNumberLink" href=".*">(.*)</a>.*Supplier" href="(.*)">(.*)</a>.*Availability">([0-9,]*)([^0-9,]*)</span>%iUs';
$rv['rule'] = '%<tr class="SearchResultsRow.*<a id="ctl00.*MouserPartNumber".* href="(.*)">(.*)</a>.*PartNumberLink" href=".*">(.*)</a>.*Supplier" href="(.*)">(.*)</a>.*Availability">(.*)[a-zA-Z ]*</span>%iUs';
$rv['rule'] = '%<tr class="SearchResultsRow.*<a id="ctl00.*MouserPartNumber".* href="(.*)">(.*)</a>.*PartNumberLink" href=".*">(.*)</a>.*Supplier" href="(.*)">(.*)</a>.*Availability">([0-9,]*)([^0-9,]{1}.*)</span>%iUs';
$rv['rule'] = '%Availability">([0-9]*)<br/>Not Available%iUs';

$rv['rule'] = '%<tr itemscope itemtype=.*<td class="digikey-partnumber" nowrap>.*<a href="(.*)">(.*)</a>.*</td><td class="mfg-partnumber">.*<span itemprop="name">(.*)</span></a></td><td class="vendor".*<a itemprop="url" href="(.*)"><span itemprop="name">(.*)</span></a></span></td><td .*</td><td class="qtyAvailable" align=center>([\d,]+).*<br></td>%iUs';

$rv['rule'] = '%<tr itemscope itemtype=.*<td class="digikey-partnumber" nowrap>.*<a href="(.*)">(.*)</a>.*</td><td class="mfg-partnumber">.*<span itemprop="name">(.*)</span></a></td><td class="vendor".*<a itemprop="url" href="(.*)"><span itemprop="name">(.*)</span></a></span></td><td .*</td><td class="qtyAvailable" align=center>([0-9,]+)[^0-9,].*</td>%iUs';
 
$rv['rule'] = '%<tr itemscope itemtype=.*<td class="digikey-partnumber" nowrap>.*<a href="(.*)">(.*)</a>.*</td><td class="mfg-partnumber">.*<span itemprop="name">(.*)</span></a></td><td class="vendor".*<span itemprop="name">(.*)</span>.*</span></td><td .*</td><td class="qtyAvailable" align=center>([0-9,]+)[^0-9,].*</td>%iUs';
$rv['rule'] = '%<tr itemscope itemtype=.*<td class="digikey-partnumber" nowrap>.*<a href="(.*)">(.*)</a>.*</td><td class="mfg-partnumber">.*<span itemprop="name">(.*)</span></a></td><td class="vendor".*<span itemprop="name">(.*)</span>%iUs';
$rv['rule'] = '%						<li>.*<a href="(.*)">(.*)<span>\((.*)\)</span>.*</a>%iUs';
$rv['rule'] = '%<td class="qty">(.*)&nbsp;-.*<td class="threeColTd">.*\$(.+)</td>%iUs';
$rv['rule'] = '%<span class="product" id="descAttributeName.*">(.*)</span>.*<span id="descAttributeValue.*">(.*)</span>%iUs';
//$rv['rule'] = '%<title>.* - .* - (.*)\| Newark element14 US</title>%iUs';
preg_match_all($rv['rule'], $html, $res);
//echo $res[0][0];
var_dump($res);
$c = count($res[0]);
echo $c;
for ( $i=1; $i<=$c; $i++ ) {
	wlog('newark.txt', "\"{$res[3][$i]}\",\"{$res[2][$i]}\",\"{$res[1][$i]}\"");
}


//require '../../ThinkPHP/Extend/Vendor/MySelf/Snoopy.php';
//$snoopy = new Snoopy;
//$snoopy->agent = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36";
//$snoopy->rawheaders["Pragma"] = "no-cache";
//$snoopy->rawheaders = array('Accept-Language'=>'en-US,en;q=0.6','Cookie'=>'preferences=ps=www&pl=en-US&pc_www=USDu','Referer'=>'http://www.mouser.com/Electronic-Components');
//$snoopy->fetch($url);
//$html =	$snoopy->results;

//wlog('html.sy.html', $html);
//phpinfo();
?>
