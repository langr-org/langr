-- $Id: collect_statistics.sql 118 2014-07-09 11:40:42Z huanghua $

-- 采集：在每采集一大轮后，如果需要重新采集应该需要重置各采集数据(cot_xxx_goodslist_url.status)对象的状态和更新时间：status=0,updated=0
-- 更新：在每采集一大轮后，如果需要再次更新数据应该需要重置各采集数据(cot_xxx_goods_url.status)对象的状态和更新时间：status=0,updated=0

-- 如果要开始一轮全新的采集：
-- 采集分类
-- /usr/bin/php -f index.php Collect/category/mod/digikey
-- /usr/bin/php -f index.php Collect/category/mod/mouser
-- 采集分类产品列表
-- /usr/bin/php -f index.php Collect/goodslist/mod/digikey
-- /usr/bin/php -f index.php Collect/goodslist/mod/mouser
update `cot_mouser_goodslist_url` gl set gl.status=0;
-- 如果要开始一轮新的更新：
update `cot_mouser_goods_url` gu set gu.status=0;
update `cot_mouser_goods` g set g.status=0;

-- 采集完成后换到更新时需要执行一下
-- 更新采集后少数goods.gid与goods_url.id不同步的情况
select count(*) from `cot_mouser_goods` g,`cot_mouser_goods_url` gu where g.gds_url=gu.gds_url and g.gid!=gu.id;
update `cot_mouser_goods` g,`cot_mouser_goods_url` gu set g.gid=gu.id where g.gds_url=gu.gds_url and g.gid!=gu.id;
update `cot_digikey_goods` g,`cot_digikey_goods_url` gu set g.gid=gu.id where g.gds_url=gu.gds_url and g.gid!=gu.id;
update `cot_newark_goods` g,`cot_newark_goods_url` gu set g.gid=gu.id where g.gds_url=gu.gds_url and g.gid!=gu.id;
-- 如果数据过大，可修改where分次执行
update `cot_mouser_goods` g,`cot_mouser_goods_url` gu set g.gid=gu.id where g.gds_url=gu.gds_url and g.gid!=gu.id and gu.id<1000000;
update `cot_digikey_goods` g,`cot_digikey_goods_url` gu set g.gid=gu.id where g.gds_url=gu.gds_url and g.gid!=gu.id and gu.id<1000000;
-- goods_url 与 goodslist_url 分类名，分类id关联
update `cot_mouser_goodslist_url` gl,`cot_mouser_goods_url` gu set gu.ctg_id=gl.ctg_id,gu.ctg_name=gl.ctg_name,gu.ctg_pid=gl.ctg_pid where gu.lid=gl.id;
update `cot_digikey_goodslist_url` gl,`cot_digikey_goods_url` gu set gu.ctg_id=gl.ctg_id,gu.ctg_name=gl.ctg_name,gu.ctg_pid=gl.ctg_pid where gu.lid=gl.id;
update `cot_newark_goodslist_url` gl,`cot_newark_goods_url` gu set gu.ctg_id=gl.ctg_id,gu.ctg_name=gl.ctg_name,gu.ctg_pid=gl.ctg_pid where gu.lid=gl.id;
-- 如果数据过大，可修改where分次执行


-- 统计各状态和中断了的采集工作, updated 小于 1小时前，一个包采集1小时后还没返回则可以抛弃。
SELECT COUNT(gu.id),gu.status FROM `cot_mouser_goodslist_url` gu where updated<UNIX_TIMESTAMP()-3600 GROUP BY gu.status;
-- 同时恢复中断了的采集数据状态
update `cot_mouser_goodslist_url` gu set gu.status=0,gu.updated=0 where gu.status=1 and updated<UNIX_TIMESTAMP()-3600;
-- 统计各状态和中断了的更新工作, updated 小于 1小时前
SELECT COUNT(gu.id),gu.status FROM `cot_mouser_goods_url` gu where updated<UNIX_TIMESTAMP()-3600 GROUP BY gu.status;
-- 同时恢复中断了的采集数据状态
update `cot_mouser_goods_url` gu set gu.status=0,gu.updated=0 where gu.status=1 and updated<UNIX_TIMESTAMP()-3600;


-- 统计正在工作的各客户或client_id(电脑)开启进程数, created 为最近10分钟以内
SELECT substring_index(client_id,'@',1) PC,username user,module '模块',SUM(get) pages,ip,FROM_UNIXTIME(created) get_time FROM cot_client_log WHERE status=0 and created>UNIX_TIMESTAMP()-600 GROUP BY username ORDER BY pages DESC;
SELECT substring_index(client_id,'@',1) PC,username user,FLOOR(sum(get)/30) process,SUM(get) pages,ip,FROM_UNIXTIME(created) get_time FROM cot_client_log WHERE status=0 and created>UNIX_TIMESTAMP()-600 GROUP BY PC ORDER BY pages DESC;

-- 统计各模块的当前采集进度
select count(*) from cot_mouser_goods_url where status=0;
select count(*) from cot_digikey_goods_url where status=0;
-- 统计各模块的总采集进度
SELECT count(*) task,module '模块',do,SUM(get) pages,SUM(put) total,FROM_UNIXTIME(created) FROM cot_client_log WHERE do='update' and status=1 GROUP BY '模块' ORDER BY pages DESC;
-- 统计各人的总采集数
SELECT substring_index(client_id,'@',1) PC,username user,SUM(get) pages,SUM(put) total,ip,FROM_UNIXTIME(created),device FROM cot_client_log WHERE do='update' and status=1 GROUP BY user ORDER BY pages DESC;
-- 统计各人的总更新数
SELECT substring_index(client_id,'@',1) PC,username user,SUM(get) pages,SUM(put) total,ip,FROM_UNIXTIME(created),device FROM cot_client_log WHERE do='update' and status=1 GROUP BY user ORDER BY pages DESC;
-- 统计各电脑的某月的总采集/更新数
SELECT substring_index(client_id,'@',1) PC,username user,SUM(get) task,SUM(put) '完成任务',sum(put)/sum(get)*100 '完成率',ip,FROM_UNIXTIME(created) stime FROM cot_client_log where from_unixtime(created)>'2014-05-31 23:59:59' GROUP BY PC ORDER BY task DESC;
SELECT substring_index(client_id,'@',1) PC,username user,SUM(get) '分配任务',SUM(put) '完成任务',sum(put)/sum(get)*100 '完成率',ip,FROM_UNIXTIME(created) stime FROM cot_client_log where do='update' and created>UNIX_TIMESTAMP('2014-05-31 23:59:59') and created<UNIX_TIMESTAMP('2014-07-01 00:00:00') GROUP BY PC ORDER BY 分配任务 DESC;
-- 统计各电脑的总采集/更新数
SELECT substring_index(client_id,'@',1) PC,username user,module '模块',SUM(get) task,SUM(put) '完成任务',sum(put)/(sum(get)*25)*100 '完成率',ip,FROM_UNIXTIME(created),device FROM cot_client_log WHERE do='collect' and module='digikey' GROUP BY PC ORDER BY task DESC;
SELECT substring_index(client_id,'@',1) PC,username user,module '模块',SUM(get) 'task',SUM(put) '完成任务',sum(put)/sum(get)*100 '完成率',ip,FROM_UNIXTIME(created) stime FROM cot_client_log WHERE do='update' and module='digikey' GROUP BY PC ORDER BY task DESC;
SELECT substring_index(client_id,'@',1) PC,username user,SUM(get) task,SUM(put) '完成任务',sum(put)/sum(get)*100 '完成率',ip,FROM_UNIXTIME(created) stime FROM cot_client_log WHERE do='update' GROUP BY PC,user ORDER BY task DESC;
SELECT substring_index(client_id,'@',1) PC,username user,SUM(get) task,SUM(put) 'done',sum(put)/sum(get)*100 '完成率',ip,FROM_UNIXTIME(created) stime FROM cot_client_log WHERE do='update' GROUP BY PC,user ORDER BY task DESC;

-- 补物料
insert into cot_newark_goods_url(gds_name,gds_sn,ctg_name,gds_url,status) values ("M81044/12-12-9","50B4980","Cable - Multiconductor Miscellaneous","http://www.newark.com/manhattan-cdt/m81044-12-12-9/cable/dp/50B4980","0");
insert into cot_newark_goods(gds_id,gds_name,gds_sn,gds_url,status) values ("M81044/12-12-9","50B4980","http://www.newark.com/toroidal-power-transformers","0");
insert into cot_newark_goods_url(gds_name,gds_sn,ctg_name,gds_url,status) values ("83320","54M3612","Ceiling Roses & Lampholders","http://www.newark.com/pro-elec/83320/batten-lampholder-ho-skirt-t2/dp/54M3612","0");
insert into cot_newark_goods(gds_id,gds_name,gds_sn,gds_url,status) values ("83320","54M3612","http://www.newark.com/pro-elec/83320/batten-lampholder-ho-skirt-t2/dp/54M3612","0");
