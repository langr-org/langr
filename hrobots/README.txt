$Id: README.txt 70 2014-05-24 08:08:23Z huanghua $

目录：
client/
	采集客户端源代码，
client/php5.3.13/
	采集客户端代码php执行库(Windows平台需要)
client-crypt/
	客户端采集程序，发布版，通过hcrypt/tools/hcrypt 加密。
docs/
	文档
dynamic_ip/
	动态ip心跳汇报程序服务器端，部署在第三方公共固定ip服务器上。
hcrypt/
	php代码加密解密扩展，解密扩展在对应(VC9/gcc)平台编译后放在php扩展目录，并设置好，
server/
	采集程序服务器端
server/key.php
	采集程序密匙生成程序
server/Runtime/Data
	采集程序客户端自动升级代码包存放处
ThinkPHP/
	服务器端使用的MVC框架


hqrobots 采集说明：

服务器端CLI执行：
	第一轮分类采集，会插入产品列表数据到(cot_xxx_ctg_url)
	第一轮产品列表采集，会插入产品列表数据到(cot_xxx_goodslist_url)

客户端分配执行：
	第一轮采集，会插入产品数据到(cot_xxx_goods_url,cot_xx_goods)
	可能需要执行同步：
	update `cot_mouser_goods` g,`cot_mouser_goods_url` gl set g.gid=gl.id where g.gds_url=gl.gds_url and g.gid!=gl.id;
	update `cot_mouser_goods` g,`cot_mouser_goods_url` gl set g.gid=gl.id where g.gds_url=gl.gds_url and g.gid!=gl.id and gl.id<1800000;
	update `cot_mouser_goods` g,`cot_mouser_goods_url` gl set g.gid=gl.id where g.gds_url=gl.gds_url and g.gid!=gl.id and gl.id>=1800000;

采集：在每采集一大轮后，如果需要重新采集应该需要重置各采集数据(cot_xxx_goodslist_url.status)对象的状态和updated：status=0,updated=0
更新：在每更新一大轮后，如果需要再次更新数据应该需要重置各采集数据(cot_xxx_goods_url.status)对象的状态和updated：status=0,updated=0


服务器在动态ip环境时，定时执行服务器心跳函数：
*/10 * * * * curl http://server/api/seat > /dev/null 2>&1
如：
*/10 * * * * curl http://192.168.200.202:10000/server/api/beat > /dev/null 2>&1

