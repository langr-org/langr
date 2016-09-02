-- $Id: collect_statistics.sql 118 2014-07-09 11:40:42Z huanghua $

-- �ɼ�����ÿ�ɼ�һ���ֺ������Ҫ���²ɼ�Ӧ����Ҫ���ø��ɼ�����(cot_xxx_goodslist_url.status)�����״̬�͸���ʱ�䣺status=0,updated=0
-- ���£���ÿ�ɼ�һ���ֺ������Ҫ�ٴθ�������Ӧ����Ҫ���ø��ɼ�����(cot_xxx_goods_url.status)�����״̬�͸���ʱ�䣺status=0,updated=0

-- ���Ҫ��ʼһ��ȫ�µĲɼ���
-- �ɼ�����
-- /usr/bin/php -f index.php Collect/category/mod/digikey
-- /usr/bin/php -f index.php Collect/category/mod/mouser
-- �ɼ������Ʒ�б�
-- /usr/bin/php -f index.php Collect/goodslist/mod/digikey
-- /usr/bin/php -f index.php Collect/goodslist/mod/mouser
update `cot_mouser_goodslist_url` gl set gl.status=0;
-- ���Ҫ��ʼһ���µĸ��£�
update `cot_mouser_goods_url` gu set gu.status=0;
update `cot_mouser_goods` g set g.status=0;

-- �ɼ���ɺ󻻵�����ʱ��Ҫִ��һ��
-- ���²ɼ�������goods.gid��goods_url.id��ͬ�������
select count(*) from `cot_mouser_goods` g,`cot_mouser_goods_url` gu where g.gds_url=gu.gds_url and g.gid!=gu.id;
update `cot_mouser_goods` g,`cot_mouser_goods_url` gu set g.gid=gu.id where g.gds_url=gu.gds_url and g.gid!=gu.id;
update `cot_digikey_goods` g,`cot_digikey_goods_url` gu set g.gid=gu.id where g.gds_url=gu.gds_url and g.gid!=gu.id;
update `cot_newark_goods` g,`cot_newark_goods_url` gu set g.gid=gu.id where g.gds_url=gu.gds_url and g.gid!=gu.id;
-- ������ݹ��󣬿��޸�where�ִ�ִ��
update `cot_mouser_goods` g,`cot_mouser_goods_url` gu set g.gid=gu.id where g.gds_url=gu.gds_url and g.gid!=gu.id and gu.id<1000000;
update `cot_digikey_goods` g,`cot_digikey_goods_url` gu set g.gid=gu.id where g.gds_url=gu.gds_url and g.gid!=gu.id and gu.id<1000000;
-- goods_url �� goodslist_url ������������id����
update `cot_mouser_goodslist_url` gl,`cot_mouser_goods_url` gu set gu.ctg_id=gl.ctg_id,gu.ctg_name=gl.ctg_name,gu.ctg_pid=gl.ctg_pid where gu.lid=gl.id;
update `cot_digikey_goodslist_url` gl,`cot_digikey_goods_url` gu set gu.ctg_id=gl.ctg_id,gu.ctg_name=gl.ctg_name,gu.ctg_pid=gl.ctg_pid where gu.lid=gl.id;
update `cot_newark_goodslist_url` gl,`cot_newark_goods_url` gu set gu.ctg_id=gl.ctg_id,gu.ctg_name=gl.ctg_name,gu.ctg_pid=gl.ctg_pid where gu.lid=gl.id;
-- ������ݹ��󣬿��޸�where�ִ�ִ��


-- ͳ�Ƹ�״̬���ж��˵Ĳɼ�����, updated С�� 1Сʱǰ��һ�����ɼ�1Сʱ��û���������������
SELECT COUNT(gu.id),gu.status FROM `cot_mouser_goodslist_url` gu where updated<UNIX_TIMESTAMP()-3600 GROUP BY gu.status;
-- ͬʱ�ָ��ж��˵Ĳɼ�����״̬
update `cot_mouser_goodslist_url` gu set gu.status=0,gu.updated=0 where gu.status=1 and updated<UNIX_TIMESTAMP()-3600;
-- ͳ�Ƹ�״̬���ж��˵ĸ��¹���, updated С�� 1Сʱǰ
SELECT COUNT(gu.id),gu.status FROM `cot_mouser_goods_url` gu where updated<UNIX_TIMESTAMP()-3600 GROUP BY gu.status;
-- ͬʱ�ָ��ж��˵Ĳɼ�����״̬
update `cot_mouser_goods_url` gu set gu.status=0,gu.updated=0 where gu.status=1 and updated<UNIX_TIMESTAMP()-3600;


-- ͳ�����ڹ����ĸ��ͻ���client_id(����)����������, created Ϊ���10��������
SELECT substring_index(client_id,'@',1) PC,username user,module 'ģ��',SUM(get) pages,ip,FROM_UNIXTIME(created) get_time FROM cot_client_log WHERE status=0 and created>UNIX_TIMESTAMP()-600 GROUP BY username ORDER BY pages DESC;
SELECT substring_index(client_id,'@',1) PC,username user,FLOOR(sum(get)/30) process,SUM(get) pages,ip,FROM_UNIXTIME(created) get_time FROM cot_client_log WHERE status=0 and created>UNIX_TIMESTAMP()-600 GROUP BY PC ORDER BY pages DESC;

-- ͳ�Ƹ�ģ��ĵ�ǰ�ɼ�����
select count(*) from cot_mouser_goods_url where status=0;
select count(*) from cot_digikey_goods_url where status=0;
-- ͳ�Ƹ�ģ����ܲɼ�����
SELECT count(*) task,module 'ģ��',do,SUM(get) pages,SUM(put) total,FROM_UNIXTIME(created) FROM cot_client_log WHERE do='update' and status=1 GROUP BY 'ģ��' ORDER BY pages DESC;
-- ͳ�Ƹ��˵��ܲɼ���
SELECT substring_index(client_id,'@',1) PC,username user,SUM(get) pages,SUM(put) total,ip,FROM_UNIXTIME(created),device FROM cot_client_log WHERE do='update' and status=1 GROUP BY user ORDER BY pages DESC;
-- ͳ�Ƹ��˵��ܸ�����
SELECT substring_index(client_id,'@',1) PC,username user,SUM(get) pages,SUM(put) total,ip,FROM_UNIXTIME(created),device FROM cot_client_log WHERE do='update' and status=1 GROUP BY user ORDER BY pages DESC;
-- ͳ�Ƹ����Ե�ĳ�µ��ܲɼ�/������
SELECT substring_index(client_id,'@',1) PC,username user,SUM(get) task,SUM(put) '�������',sum(put)/sum(get)*100 '�����',ip,FROM_UNIXTIME(created) stime FROM cot_client_log where from_unixtime(created)>'2014-05-31 23:59:59' GROUP BY PC ORDER BY task DESC;
SELECT substring_index(client_id,'@',1) PC,username user,SUM(get) '��������',SUM(put) '�������',sum(put)/sum(get)*100 '�����',ip,FROM_UNIXTIME(created) stime FROM cot_client_log where do='update' and created>UNIX_TIMESTAMP('2014-05-31 23:59:59') and created<UNIX_TIMESTAMP('2014-07-01 00:00:00') GROUP BY PC ORDER BY �������� DESC;
-- ͳ�Ƹ����Ե��ܲɼ�/������
SELECT substring_index(client_id,'@',1) PC,username user,module 'ģ��',SUM(get) task,SUM(put) '�������',sum(put)/(sum(get)*25)*100 '�����',ip,FROM_UNIXTIME(created),device FROM cot_client_log WHERE do='collect' and module='digikey' GROUP BY PC ORDER BY task DESC;
SELECT substring_index(client_id,'@',1) PC,username user,module 'ģ��',SUM(get) 'task',SUM(put) '�������',sum(put)/sum(get)*100 '�����',ip,FROM_UNIXTIME(created) stime FROM cot_client_log WHERE do='update' and module='digikey' GROUP BY PC ORDER BY task DESC;
SELECT substring_index(client_id,'@',1) PC,username user,SUM(get) task,SUM(put) '�������',sum(put)/sum(get)*100 '�����',ip,FROM_UNIXTIME(created) stime FROM cot_client_log WHERE do='update' GROUP BY PC,user ORDER BY task DESC;
SELECT substring_index(client_id,'@',1) PC,username user,SUM(get) task,SUM(put) 'done',sum(put)/sum(get)*100 '�����',ip,FROM_UNIXTIME(created) stime FROM cot_client_log WHERE do='update' GROUP BY PC,user ORDER BY task DESC;

-- ������
insert into cot_newark_goods_url(gds_name,gds_sn,ctg_name,gds_url,status) values ("M81044/12-12-9","50B4980","Cable - Multiconductor Miscellaneous","http://www.newark.com/manhattan-cdt/m81044-12-12-9/cable/dp/50B4980","0");
insert into cot_newark_goods(gds_id,gds_name,gds_sn,gds_url,status) values ("M81044/12-12-9","50B4980","http://www.newark.com/toroidal-power-transformers","0");
insert into cot_newark_goods_url(gds_name,gds_sn,ctg_name,gds_url,status) values ("83320","54M3612","Ceiling Roses & Lampholders","http://www.newark.com/pro-elec/83320/batten-lampholder-ho-skirt-t2/dp/54M3612","0");
insert into cot_newark_goods(gds_id,gds_name,gds_sn,gds_url,status) values ("83320","54M3612","http://www.newark.com/pro-elec/83320/batten-lampholder-ho-skirt-t2/dp/54M3612","0");
