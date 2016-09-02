/**
 * @file popwindow.conf.js
 * @brief 弹屏插件js 配置
 * 
 * @package popwindow
 * @author Langr <hua@langr.org> 2012/03/22 15:37
 * 
 * $Id: popwindow.conf.js 538 2012-04-20 01:48:16Z huangh $
 */

/**
 * 来/去电弹屏地址, 后面会自动加上来/去电信息, 如: 'srcno=808&dstno=15912345678&type=from'
 * srcno 本机号
 * dstno 对方电话号
 * type 来/去电类型: 'from' 来电; 'to' 去电; 'inv' 内部分机互打
 *
 * 请在地址后加上 '?' 或者 '&'
 * pop_url = pop_url + ['&']/['?'] + 'srcno=808&dstno=15912345678&type=in'
 */
if ( typeof pop_url == "undefined" ) {
	var pop_url = "/callcenter/index.php?module=crm&action=popWindow&";
}
/* _self, frame_name, _blank */
if ( typeof pop_target == "undefined" ) {
	var pop_target = "_blank";
}
/* pop_target(_self, frame_name) show: replace, div, iframe */
if ( typeof pop_show == "undefined" ) {
	var pop_show = 'iframe';
}
/**
 * 当 pop_show == 'div' 时, 
 * pop_show_scroll, pop_width, pop_height 设置才有效.
 * pop_show_scroll 当弹出页面高度不够时, 是否显示滚动条: 'no' 不显示; 'yes' 一直显示; 'auto' 自动.
 * pop_width 弹屏宽度: '90%', '900px' ...
 * pop_height 弹屏高度: '100%', '800px' ...
 */
if ( typeof pop_show_scroll == "undefined" ) {
	var pop_show_scroll = 'no';
}
if ( typeof pop_width == "undefined" ) {
	var pop_width = '99.8%';
}
if ( typeof pop_height == "undefined" ) {
	var pop_height = '100%';
}
/**
 * 弹屏页面标题和顶部div提示框显示
 * pop_flash_title 页面标题闪烁提示.
 * pop_title {'from':false,'to':'去电'} 值为 false 或 null, 则不显示div来去电, 但显示关闭和新开窗口按钮.
 * pop_show_title 顶陪div提示是否显示.
 */
if ( typeof pop_flash_title == "undefined" ) {
	var pop_flash_title = true;
}
if ( typeof pop_title == "undefined" ) {
	var pop_title = {'from':'来电:','to':'去电:','inv':'内线:'};
}
if ( typeof pop_show_title == "undefined" ) {
	var pop_show_title = true;
}
/* long connect setting: 1=realtime, n=(n-1)*4s */
if ( typeof pop_connect == "undefined" ) {
	var pop_connect = 5;
}

if ( typeof api_host == "undefined" ) {
	var api_host = window.location.hostname;
}
if ( typeof api_key == "undefined" ) {
	var api_key = "";
}

if ( typeof callbar_extension == "undefined" ) {
	/* */
}

