-- kitsmall datacenter
-- by Langr <hua@langr.org> 2014/04/21 11:22

-- hrobots 采集中心 数据结构
-- $Id: robots.sql 118 2014-07-09 11:40:42Z huanghua $

-- collect_client_log `cot_log` 表的结构 - 客户端采集信息日志 
CREATE TABLE IF NOT EXISTS `cot_client_log` (
`id` int(11) not null AUTO_INCREMENT,
`client_id` varchar(255) default null comment '客户端名:@pcname[@user|path]',
`username` varchar(32) default '' comment '客户名, 由客户指定, 或下载时自动生成(KEY)',
`version` varchar(32) default '' comment '客户端版本号',
`ip` varchar(18) default '0.0.0.0' comment '客户端ip',
`module` varchar(10) default '' comment '采集的模块: cot_xxx_goods_url.src',
`do` varchar(10) default 'collect' comment '采集动作: collect 采集, update 更新',
`count` mediumint(8) not null default '1' comment '客户端请求任务包数量',
`get` mediumint(8) not null default '0' comment '客户端请求任务数量',
`put` mediumint(8) not null default '0' comment '客户端实际完成任务数量',
`device` varchar(1023) default null comment 'device info',
`note` varchar(255) default null comment '备注',
`status` tinyint(1) not null default '0' comment '状态: 0 已经(请求)发送任务包正在采集; 1 采集已返回; 2 被服务器拒绝',
`created` int(11) not null default '0' comment '请求时间',
`updated` int(11) not null default '0' comment '数据返回时间',
PRIMARY KEY (`id`),
KEY (`client_id`),
KEY (`username`),
KEY (`status`)
) ENGINE=MyISAM default charset=utf8 comment='采集客户端信息';

-- collect_goods_url `cot_goods_url` 表的结构 - (某模块)产品url, 更新用
CREATE TABLE IF NOT EXISTS `cot_goods_url` (
`id` mediumint(8) not null AUTO_INCREMENT,
`gds_id` int(11) not null default '0' comment '商品唯一id, 与大数据中心对应',
`gds_name` varchar(200) default null comment '商品名,制造商零件编号',
`gds_sn` varchar(200) default null comment '供应商零件编号',
`src` varchar(10) default '' comment '提供商[模块]: digikey,mouser,future,element14,avnet,arrow,rs,tti',
`lid` mediumint(8) not null default '0' comment 'goodslist_url.id',
`ctg_name` varchar(100) default null comment '分类名',
`ctg_id` mediumint(8) not null default '0' comment '采集分类id',
`ctg_pid` mediumint(8) not null default '0' comment '采集分类父id',
`referer` varchar(255) not null default '' comment '父级url',
`gds_url` varchar(255) not null default '' comment '产品url',
`cot_count` mediumint(4) not null default '0' comment '抓取总次数,抓取次数越多代表产品关注度越高',
`power` mediumint(4) not null default '0' comment '权重, 9999 强制优先更新',
`hot` int(11) not null default '0' comment '热度: 算法如 访问量x2，购买量(或金额)x5，搜索x1等计算，优先更新',
`client` varchar(80) default null comment '最后一次分配客户端',
`status` tinyint(1) not null default '0' comment '状态: 0 未更新; 1 已分配任务,正在更新; 2 本次已经更新完成,但没推送; 3 更新失败; 4 更新并推送完成',
`created` int(11) not null default '0' comment '创建时间',
`updated` int(11) not null default '0' comment '更新时间, 最后一次操作时间, 如: 分配时间, 采集时间',
PRIMARY KEY (`id`),
UNIQUE KEY (`gds_url`),
KEY (`gds_id`),
KEY (`gds_name`),
KEY (`power`),
KEY (`status`)
) ENGINE=InnoDB default charset=utf8 comment='产品url, 更新用';

-- collect_goods `cot_goods` 表的结构 - (总表)采集/更新到的产品
CREATE TABLE IF NOT EXISTS `cot_goods` (
`id` int(11) not null AUTO_INCREMENT,
`gid` int(11) not null  comment 'cot_goods_url.id, 与cot_goods_url对应',
`gds_id` int(11) not null default '0' comment '商品唯一id, 与大数据中心对应',
`gds_name` varchar(200) default null comment '商品名,制造商零件编号',
`gds_sn` varchar(200) default null comment '供应商零件编号',
`src` varchar(10) default '' comment '提供商[模块]: digikey,mouser,future,element14,avnet,arrow,rs,tti',
`provider` varchar(200) DEFAULT NULL COMMENT '制造商',
`provider_url` varchar(255) DEFAULT NULL COMMENT '制造商url',
`brand_name` varchar(30) default null comment '商品品牌名',
-- `ctg_name` varchar(100) default null comment '我们的分类名',
-- `ctg_id` mediumint(8) not null default '0' comment '我们的标准分类id',
-- `ctg_pid` mediumint(8) not null default '0' comment '我们的标准分类父id',
`gds_doc` varchar(255) not null default '' comment '采集到的商品手册(远程)',
`gds_thumb` varchar(255) not null default '' comment '缩图',
`gds_img` varchar(255) not null default '' comment '产品图',
`inventory` varchar(30) not null default '0' comment '库存,只作展示',
`warehouse` varchar(255) DEFAULT '-' comment '仓库位置: 大陆，香港，海外...',
`spq` varchar(8) NOT NULL DEFAULT '0' comment 'SPQ 标准包装量',
`moq` varchar(8) NOT NULL DEFAULT '0' comment 'MOQ 最小订购量',
`currency` varchar(10) default 'USD' comment '货币单位: USD,CNY,HKD,NTD,EUR',
`market_price` decimal(10,2) default '0' comment '市场单价',
`shop_price` decimal(12,4) DEFAULT '0.0000' comment '我们的单价',
`cost_price` decimal(12,4) DEFAULT '0.0000' comment '我们的成本价',
`prices` varchar(1023) DEFAULT '' comment '阶梯价格 json:[{nums:1,price:0.5,rmb:3.0},{nums:10,price:0.49,rmb:3.0}{nums:100,price:0.47,rmb:2.9}]',
`ctg_path` varchar(255) default null comment '商品分类路径',
`gds_attrs` text default null comment '产品属性 json:{k1:v1,k2:v2,k3:v3}',
`gds_description` text default null comment '商品描述',
`gds_url` varchar(255) not null default '' comment '产品url',
`encap` varchar(255) DEFAULT '-' comment '封装',
`package` varchar(255) DEFAULT '-' comment '包装',
`is_rohs` tinyint(1) not null default '0' comment 'rohs状态: 0 没有rohs信息; 1 通过; 2 未通过',
`rohsstate` varchar(200) DEFAULT NULL COMMENT 'RoHS 状态',
-- ...
`note` varchar(200) DEFAULT NULL COMMENT '备用',
`status` tinyint(1) not null default '0' comment '状态: 0 未更新; 2 本次已经更新完成, 但没推送; 4 更新并推送完成',
`created` int(11) not null default '0' comment '创建时间',
`updated` int(11) not null default '0' comment '更新时间, 最后一次操作时间, 如: 更新时间, 推送时间',
PRIMARY KEY (`id`),
-- FOREIGN KEY(`id`) REFERENCES cot_goods_url(`id`) on delete cascade,
KEY (`gid`),
KEY (`gds_id`),
KEY (`gds_name`),
UNIQUE KEY (`gds_url`),
KEY (`status`)
) ENGINE=InnoDB default charset=utf8 comment='采集/更新到的产品';

-- 弃用!
-- collect_goods_info `cot_goods_info` 表的结构 - (总表)采集/更新到的产品详细信息
/*
CREATE TABLE IF NOT EXISTS `cot_goods_info` (
`id` int(11) not null  comment 'cot_goods.id, 与cot_goods对应',
`gds_id` int(11) not null default '0' comment '商品唯一id, 与大数据中心对应',
`gds_name` varchar(200) default null comment '商品名',
`ctg_path` varchar(255) default null comment '商品分类路径',
`gds_attrs` text default null comment '产品属性 json:{k1:v1,k2:v2,k3:v3}',
`gds_description` text default null comment '商品描述',
`gds_url` varchar(255) not null default '' comment '产品url',
`encap` varchar(255) DEFAULT '-' comment '封装',
`package` varchar(255) DEFAULT '-' comment '包装',
`is_rohs` tinyint(1) not null default '0' comment 'rohs状态: 0 没有rohs信息; 1 通过; 2 未通过',
`rohsstate` varchar(200) DEFAULT NULL COMMENT 'RoHS 状态',
PRIMARY KEY (`id`),
KEY (`gds_id`),
KEY (`gds_name`)
) ENGINE=InnoDB default charset=utf8 comment='采集/更新到的产品详细信息';
*/

-- 优先顺序 mouser,digikey,element14,
-- mouser
-- 考虑中: `cot_ctg_url` 可与 `cot_xxx_ctg_url` 表合并
-- collect_xxx_category_url `cot_xxx_ctg_url` 表的结构 - (某模块)分类/子分类url, 用来获取分类产品列表
CREATE TABLE IF NOT EXISTS `cot_mouser_ctg_url` (
`id` mediumint(8) not null AUTO_INCREMENT,
`pid` mediumint(8) not null default '0' comment '父id',
`ctg_name` varchar(200) default null comment '分类名',
`ctg_id` mediumint(8) not null default '0' comment '标准分类id',
`ctg_pid` mediumint(8) not null default '0' comment '标准分类父id',
`ctg_ppid` mediumint(8) not null default '0' comment '缓存，标准分类父父id',
`src` varchar(10) default 'mouser' comment '提供商[模块]: digikey,mouser,future,element14,avnet,arrow,rs,tti',
`referer` varchar(255) not null default '' comment '父级url',
`ctg_url` varchar(255) not null default '' comment '分类url',
`ctg_level` mediumint(8) not null default '0' comment '分类层级',
`ctg_attrs` text default null comment '分类属性 json:{k1:v1,k2:v2,k3:v3}',
`ctg_description` text default null comment '分类描述',
`gds_sum` mediumint(8) not null default '0' comment '此分类的商品总数',
`gds_pages` mediumint(8) not null default '0' comment '此分类的商品分页总数',
`power` mediumint(8) not null default '0' comment '权重',
`status` tinyint(1) not null default '1' comment '状态: 0 最后级分类, url已经是产品列表; 1 至少还有1级子分类,2,3...',
`created` int(11) not null default '0' comment '数据创建时间',
`updated` int(11) not null default '0' comment '数据更新时间',
PRIMARY KEY (`id`),
UNIQUE KEY (`ctg_url`),
KEY (`ctg_id`),
KEY (`status`)
) ENGINE=MyISAM default charset=utf8 comment='分类/子分类url, 用来获取分类产品列表';

-- 考虑中: `cot_goodslist_url` 可与 `cot_xxx_goodslist_url` 表合并
-- collect_xxx_goodslist_url `cot_xxx_goodslist_url` 表的结构 - (某模块)分类产品列表url, 采集用
CREATE TABLE IF NOT EXISTS `cot_mouser_goodslist_url` (
`id` mediumint(8) not null AUTO_INCREMENT,
`cid` mediumint(8) not null default '0' comment '采集库的分类id: cot_xxx_ctg_url.id',
`ctg_name` varchar(200) default null comment '分类名',
`ctg_id` mediumint(8) not null default '0' comment '采集分类id',
`ctg_pid` mediumint(8) not null default '0' comment '采集分类父id',
`src` varchar(10) default 'mouser' comment '提供商[模块]: digikey,mouser,future,element14,avnet,arrow,rs,tti',
`referer` varchar(255) not null default '' comment '父级url',
`gdslist_url` varchar(255) not null default '' comment '分类产品列表url',
`gdslist_args` varchar(255) not null default '' comment '产品列表分页参数,POST用,GET参数直接在url后面',
`gds_count` mediumint(8) not null default '0' comment '此页商品总数',
`power` mediumint(4) not null default '0' comment '权重',
`client` varchar(80) default null comment '最后一次分配客户端: taskid@cli_name',
`status` tinyint(1) not null default '0' comment '状态: 0 未采集; 1 已分配任务,正在采集; 2 已采集完成; 3 采集失败',
`created` int(11) not null default '0' comment '创建时间',
`updated` int(11) not null default '0' comment '更新时间',
PRIMARY KEY (`id`),
UNIQUE KEY (`gdslist_url`),
KEY (`ctg_id`),
KEY (`status`)
) ENGINE=MyISAM default charset=utf8 comment='分类产品列表url, 采集用';

-- 考虑中: `cot_goods_url` 可与 `cot_xxx_goods_url` 表合并
-- collect_xxx_goods_url `cot_xxx_goods_url` 表的结构 - (某模块)产品url, 更新用
CREATE TABLE IF NOT EXISTS `cot_mouser_goods_url` (
`id` mediumint(8) not null AUTO_INCREMENT,
`gds_id` int(11) not null default '0' comment '商品唯一id, 与大数据中心对应',
`gds_name` varchar(200) default null comment '商品名,制造商零件编号',
`gds_sn` varchar(200) default null comment '供应商零件编号',
`src` varchar(10) default 'mouser' comment '提供商[模块]: digikey,mouser,future,element14,avnet,arrow,rs,tti',
`lid` mediumint(8) not null default '0' comment 'goodslist_url.id',
`ctg_name` varchar(100) default null comment '分类名',
`ctg_id` mediumint(8) not null default '0' comment '采集分类id',
`ctg_pid` mediumint(8) not null default '0' comment '采集分类父id',
`referer` varchar(255) not null default '' comment '父级url',
`gds_url` varchar(255) not null default '' comment '产品url',
`cot_count` mediumint(4) not null default '0' comment '抓取总次数,抓取次数越多代表产品关注度越高',
`power` mediumint(4) not null default '0' comment '权重, 9999 强制优先更新',
`hot` int(11) not null default '0' comment '热度: 算法如 访问量x2，购买量(或金额)x5，搜索x1等计算，优先更新',
`client` varchar(80) default null comment '最后一次分配客户端',
`status` tinyint(1) not null default '0' comment '状态: 0 未更新; 1 已分配任务,正在更新; 2 本次已经更新完成,但没推送; 3 更新失败; 4 更新并推送完成',
`created` int(11) not null default '0' comment '创建时间',
`updated` int(11) not null default '0' comment '更新时间, 最后一次操作时间, 如: 分配时间, 采集时间',
PRIMARY KEY (`id`),
UNIQUE KEY (`gds_url`),
KEY (`gds_id`),
KEY (`gds_name`),
KEY (`power`),
KEY (`status`)
) ENGINE=InnoDB default charset=utf8 comment='产品url, 更新用';

-- `cot_goods` 可与 `cot_xxx_goods` 表合并
-- collect_xxx_goods `cot_xxx_goods` 表的结构 - (某模块)采集/更新到的产品
CREATE TABLE IF NOT EXISTS `cot_mouser_goods` (
`id` int(11) not null AUTO_INCREMENT,
`gid` int(11) not null  comment 'cot_xxx_goods_url.id, 与cot_xxx_goods_url对应',
`gds_id` int(11) not null default '0' comment '商品唯一id, 与大数据中心对应',
`gds_name` varchar(200) default null comment '商品名,制造商零件编号',
`gds_sn` varchar(200) default null comment '供应商零件编号',
`src` varchar(10) default 'mouser' comment '提供商[模块]: digikey,mouser,future,element14,avnet,arrow,rs,tti',
`provider` varchar(200) DEFAULT NULL COMMENT '制造商',
`provider_url` varchar(255) DEFAULT NULL COMMENT '制造商url',
`brand_name` varchar(30) default null comment '商品品牌名',
-- `ctg_name` varchar(100) default null comment '我们的标准分类名',
-- `ctg_id` mediumint(8) not null default '0' comment '我们的标准分类id',
-- `ctg_pid` mediumint(8) not null default '0' comment '我们的分类父id',
`gds_doc` varchar(255) not null default '' comment '采集到的商品手册(远程)',
`gds_thumb` varchar(255) not null default '' comment '缩图',
`gds_img` varchar(255) not null default '' comment '产品图',
`inventory` varchar(30) not null default '0' comment '库存,只作展示',
`warehouse` varchar(255) DEFAULT '-' comment '仓库位置: 大陆，香港，海外...',
`spq` varchar(8) NOT NULL DEFAULT '0' comment 'SPQ 标准包装量',
`moq` varchar(8) NOT NULL DEFAULT '0' comment 'MOQ 最小订购量',
`currency` varchar(10) default 'USD' comment '货币单位: USD,CNY,HKD,NTD,EUR',
`market_price` decimal(10,2) default '0' comment '市场单价',
`shop_price` decimal(12,4) DEFAULT '0.0000' comment '我们的单价',
`cost_price` decimal(12,4) DEFAULT '0.0000' comment '我们的成本价',
`prices` varchar(1023) DEFAULT '' comment '阶梯价格 json:[{nums:1,price:0.5,rmb:3.0},{nums:10,price:0.49,rmb:3.0}{nums:100,price:0.47,rmb:2.9}]',
`ctg_path` varchar(255) default null comment '商品分类路径',
`gds_attrs` text default null comment '产品属性 json:{k1:v1,k2:v2,k3:v3}',
`gds_description` text default null comment '商品描述',
`gds_url` varchar(255) not null default '' comment '产品url',
`encap` varchar(255) DEFAULT '-' comment '封装',
`package` varchar(255) DEFAULT '-' comment '包装',
`is_rohs` tinyint(1) not null default '0' comment 'rohs状态: 0 没有rohs信息; 1 通过; 2 未通过',
`rohsstate` varchar(200) DEFAULT NULL COMMENT 'RoHS 状态',
-- ...
`note` varchar(200) DEFAULT NULL COMMENT '备用',
`status` tinyint(1) not null default '0' comment '状态: 0 未更新; 2 本次已经更新完成, 但没推送; 4 更新并推送完成',
`created` int(11) not null default '0' comment '创建时间',
`updated` int(11) not null default '0' comment '更新时间, 最后一次操作时间, 如: 更新时间, 推送时间',
PRIMARY KEY (`id`),
-- FOREIGN KEY(`id`) REFERENCES cot_mouser_goods_url(`id`) on delete cascade,
KEY (`gid`),
KEY (`gds_id`),
KEY (`gds_name`),
UNIQUE KEY (`gds_url`),
KEY (`status`)
) ENGINE=InnoDB default charset=utf8 comment='采集/更新到的产品';

-- 弃用!
-- `cot_goods_info` 可与 `cot_xxx_goods_info` 表合并
-- collect_xxx_goods_info `cot_xxx_goods_info` 表的结构 - (某模块)采集/更新到的产品详细信息
/*
CREATE TABLE IF NOT EXISTS `cot_mouser_goods_info` (
`id` int(11) not null comment 'cot_xxx_goods.id, 与cot_xxx_goods对应',
`gid` int(11) not null  comment 'cot_xxx_goods_url.id, 与cot_xxx_goods_url对应',
`gds_id` int(11) not null default '0' comment '商品唯一id, 与大数据中心对应',
`gds_name` varchar(200) default null comment '商品名',
`ctg_path` varchar(255) default null comment '商品分类路径',
`gds_attrs` text default null comment '产品属性 json:{k1:v1,k2:v2,k3:v3}',
`gds_description` text default null comment '商品描述',
`gds_url` varchar(255) not null default '' comment '产品url',
`encap` varchar(255) DEFAULT '-' comment '封装',
`package` varchar(255) DEFAULT '-' comment '包装',
`is_rohs` tinyint(1) not null default '0' comment 'rohs状态: 0 没有rohs信息; 1 通过; 2 未通过',
`rohsstate` varchar(200) DEFAULT NULL COMMENT 'RoHS 状态',
PRIMARY KEY (`id`),
--FOREIGN KEY(`id`) REFERENCES cot_mouser_goods_url(`id`) on delete cascade,
KEY (`gds_id`),
KEY (`gds_name`)
) ENGINE=InnoDB default charset=utf8 comment='采集/更新到的产品详细信息';
*/

-- digikey
-- 考虑中: `cot_ctg_url` 可与 `cot_xxx_ctg_url` 表合并
-- collect_xxx_category_url `cot_xxx_ctg_url` 表的结构 - (某模块)分类/子分类url, 用来获取分类产品列表
CREATE TABLE IF NOT EXISTS `cot_digikey_ctg_url` (
`id` mediumint(8) not null AUTO_INCREMENT,
`pid` mediumint(8) not null default '0' comment '父id',
`ctg_name` varchar(200) default null comment '分类名',
`ctg_id` mediumint(8) not null default '0' comment '标准分类id',
`ctg_pid` mediumint(8) not null default '0' comment '标准分类父id',
`ctg_ppid` mediumint(8) not null default '0' comment '缓存，标准分类父父id',
`src` varchar(10) default 'digikey' comment '提供商[模块]: digikey,mouser,future,element14,avnet,arrow,rs,tti',
`referer` varchar(255) not null default '' comment '父级url',
`ctg_url` varchar(255) not null default '' comment '分类url',
`ctg_level` mediumint(8) not null default '0' comment '分类层级',
`ctg_attrs` text default null comment '分类属性 json:{k1:v1,k2:v2,k3:v3}',
`ctg_description` text default null comment '分类描述',
`gds_sum` mediumint(8) not null default '0' comment '此分类的商品总数',
`gds_pages` mediumint(8) not null default '0' comment '此分类的商品分页总数',
`power` mediumint(8) not null default '0' comment '权重',
`status` tinyint(1) not null default '1' comment '状态: 0 最后级分类, url已经是产品列表; 1 至少还有1级子分类,2,3...',
`created` int(11) not null default '0' comment '数据创建时间',
`updated` int(11) not null default '0' comment '数据更新时间',
PRIMARY KEY (`id`),
UNIQUE KEY (`ctg_url`),
KEY (`ctg_id`),
KEY (`status`)
) ENGINE=MyISAM default charset=utf8 comment='分类/子分类url, 用来获取分类产品列表';

-- 考虑中: `cot_goodslist_url` 可与 `cot_xxx_goodslist_url` 表合并
-- collect_xxx_goodslist_url `cot_xxx_goodslist_url` 表的结构 - (某模块)分类产品列表url, 采集用
CREATE TABLE IF NOT EXISTS `cot_digikey_goodslist_url` (
`id` mediumint(8) not null AUTO_INCREMENT,
`cid` mediumint(8) not null default '0' comment '采集库的分类id: cot_xxx_ctg_url.id',
`ctg_name` varchar(200) default null comment '分类名',
`ctg_id` mediumint(8) not null default '0' comment '采集分类id',
`ctg_pid` mediumint(8) not null default '0' comment '采集分类父id',
`src` varchar(10) default 'digikey' comment '提供商[模块]: digikey,mouser,future,element14,avnet,arrow,rs,tti',
`referer` varchar(255) not null default '' comment '父级url',
`gdslist_url` varchar(255) not null default '' comment '分类产品列表url',
`gdslist_args` varchar(255) not null default '' comment '产品列表分页参数,POST用,GET参数直接在url后面',
`gds_count` mediumint(8) not null default '0' comment '此页商品总数',
`power` mediumint(4) not null default '0' comment '权重',
`client` varchar(80) default null comment '最后一次分配客户端: taskid@cli_name',
`status` tinyint(1) not null default '0' comment '状态: 0 未采集; 1 已分配任务,正在采集; 2 已采集完成; 3 采集失败',
`created` int(11) not null default '0' comment '创建时间',
`updated` int(11) not null default '0' comment '更新时间',
PRIMARY KEY (`id`),
UNIQUE KEY (`gdslist_url`),
KEY (`ctg_id`),
KEY (`status`)
) ENGINE=MyISAM default charset=utf8 comment='分类产品列表url, 采集用';

-- 考虑中: `cot_goods_url` 可与 `cot_xxx_goods_url` 表合并
-- collect_xxx_goods_url `cot_xxx_goods_url` 表的结构 - (某模块)产品url, 更新用
CREATE TABLE IF NOT EXISTS `cot_digikey_goods_url` (
`id` mediumint(8) not null AUTO_INCREMENT,
`gds_id` int(11) not null default '0' comment '商品唯一id, 与大数据中心对应',
`gds_name` varchar(200) default null comment '商品名,制造商零件编号',
`gds_sn` varchar(200) default null comment '供应商零件编号',
`src` varchar(10) default 'digikey' comment '提供商[模块]: digikey,mouser,future,element14,avnet,arrow,rs,tti',
`lid` mediumint(8) not null default '0' comment 'goodslist_url.id',
`ctg_name` varchar(100) default null comment '分类名',
`ctg_id` mediumint(8) not null default '0' comment '采集分类id',
`ctg_pid` mediumint(8) not null default '0' comment '采集分类父id',
`referer` varchar(255) not null default '' comment '父级url',
`gds_url` varchar(255) not null default '' comment '产品url',
`cot_count` mediumint(4) not null default '0' comment '抓取总次数,抓取次数越多代表产品关注度越高',
`power` mediumint(4) not null default '0' comment '权重, 9999 强制优先更新',
`hot` int(11) not null default '0' comment '热度: 算法如 访问量x2，购买量(或金额)x5，搜索x1等计算，优先更新',
`client` varchar(80) default null comment '最后一次分配客户端',
`status` tinyint(1) not null default '0' comment '状态: 0 未更新; 1 已分配任务,正在更新; 2 本次已经更新完成,但没推送; 3 更新失败; 4 更新并推送完成',
`created` int(11) not null default '0' comment '创建时间',
`updated` int(11) not null default '0' comment '更新时间, 最后一次操作时间, 如: 分配时间, 采集时间',
PRIMARY KEY (`id`),
UNIQUE KEY (`gds_url`),
KEY (`gds_id`),
KEY (`gds_name`),
KEY (`power`),
KEY (`status`)
) ENGINE=InnoDB default charset=utf8 comment='产品url, 更新用';

-- `cot_goods` 可与 `cot_xxx_goods` 表合并
-- collect_xxx_goods `cot_xxx_goods` 表的结构 - (某模块)采集/更新到的产品
CREATE TABLE IF NOT EXISTS `cot_digikey_goods` (
`id` int(11) not null AUTO_INCREMENT,
`gid` int(11) not null  comment 'cot_xxx_goods_url.id, 与cot_xxx_goods_url对应',
`gds_id` int(11) not null default '0' comment '商品唯一id, 与大数据中心对应',
`gds_name` varchar(200) default null comment '商品名,制造商零件编号',
`gds_sn` varchar(200) default null comment '供应商零件编号',
`src` varchar(10) default 'digikey' comment '提供商[模块]: digikey,mouser,future,element14,avnet,arrow,rs,tti',
`provider` varchar(200) DEFAULT NULL COMMENT '制造商',
`provider_url` varchar(255) DEFAULT NULL COMMENT '制造商url',
`brand_name` varchar(30) default null comment '商品品牌名',
-- `ctg_name` varchar(100) default null comment '我们的标准分类名',
-- `ctg_id` mediumint(8) not null default '0' comment '我们的标准分类id',
-- `ctg_pid` mediumint(8) not null default '0' comment '我们的分类父id',
`gds_doc` varchar(255) not null default '' comment '采集到的商品手册(远程)',
`gds_thumb` varchar(255) not null default '' comment '缩图',
`gds_img` varchar(255) not null default '' comment '产品图',
`inventory` varchar(30) not null default '0' comment '库存,只作展示',
`warehouse` varchar(255) DEFAULT '-' comment '仓库位置: 大陆，香港，海外...',
`spq` varchar(8) NOT NULL DEFAULT '0' comment 'SPQ 标准包装量',
`moq` varchar(8) NOT NULL DEFAULT '0' comment 'MOQ 最小订购量',
`currency` varchar(10) default 'USD' comment '货币单位: USD,CNY,HKD,NTD,EUR',
`market_price` decimal(10,2) default '0' comment '市场单价',
`shop_price` decimal(12,4) DEFAULT '0.0000' comment '我们的单价',
`cost_price` decimal(12,4) DEFAULT '0.0000' comment '我们的成本价',
`prices` varchar(1023) DEFAULT '' comment '阶梯价格 json:[{nums:1,price:0.5,rmb:3.0},{nums:10,price:0.49,rmb:3.0}{nums:100,price:0.47,rmb:2.9}]',
`ctg_path` varchar(255) default null comment '商品分类路径',
`gds_attrs` text default null comment '产品属性 json:{k1:v1,k2:v2,k3:v3}',
`gds_description` text default null comment '商品描述',
`gds_url` varchar(255) not null default '' comment '产品url',
`encap` varchar(255) DEFAULT '-' comment '封装',
`package` varchar(255) DEFAULT '-' comment '包装',
`is_rohs` tinyint(1) not null default '0' comment 'rohs状态: 0 没有rohs信息; 1 通过; 2 未通过',
`rohsstate` varchar(200) DEFAULT NULL COMMENT 'RoHS 状态',
-- ...
`note` varchar(200) DEFAULT NULL COMMENT '备用',
`status` tinyint(1) not null default '0' comment '状态: 0 未更新; 2 本次已经更新完成, 但没推送; 4 更新并推送完成',
`created` int(11) not null default '0' comment '创建时间',
`updated` int(11) not null default '0' comment '更新时间, 最后一次操作时间, 如: 更新时间, 推送时间',
PRIMARY KEY (`id`),
-- FOREIGN KEY(`id`) REFERENCES cot_digikey_goods_url(`id`) on delete cascade,
KEY (`gid`),
KEY (`gds_id`),
KEY (`gds_name`),
UNIQUE KEY (`gds_url`),
KEY (`status`)
) ENGINE=InnoDB default charset=utf8 comment='采集/更新到的产品';

-- newark(element14 en)
-- 考虑中: `cot_ctg_url` 可与 `cot_xxx_ctg_url` 表合并
-- collect_xxx_category_url `cot_xxx_ctg_url` 表的结构 - (某模块)分类/子分类url, 用来获取分类产品列表
CREATE TABLE IF NOT EXISTS `cot_newark_ctg_url` (
`id` mediumint(8) not null AUTO_INCREMENT,
`pid` mediumint(8) not null default '0' comment '父id',
`ctg_name` varchar(200) default null comment '分类名',
`ctg_id` mediumint(8) not null default '0' comment '标准分类id',
`ctg_pid` mediumint(8) not null default '0' comment '标准分类父id',
`ctg_ppid` mediumint(8) not null default '0' comment '缓存，标准分类父父id',
`src` varchar(10) default 'newark' comment '提供商[模块]: digikey,mouser,future,newark,element14,avnet,arrow,rs,tti',
`referer` varchar(255) not null default '' comment '父级url',
`ctg_url` varchar(255) not null default '' comment '分类url',
`ctg_level` mediumint(8) not null default '0' comment '分类层级',
`ctg_attrs` text default null comment '分类属性 json:{k1:v1,k2:v2,k3:v3}',
`ctg_description` text default null comment '分类描述',
`gds_sum` mediumint(8) not null default '0' comment '此分类的商品总数',
`gds_pages` mediumint(8) not null default '0' comment '此分类的商品分页总数',
`power` mediumint(8) not null default '0' comment '权重',
`status` tinyint(1) not null default '1' comment '状态: 0 最后级分类, url已经是产品列表; 1 至少还有1级子分类,2,3...',
`created` int(11) not null default '0' comment '数据创建时间',
`updated` int(11) not null default '0' comment '数据更新时间',
PRIMARY KEY (`id`),
UNIQUE KEY (`ctg_url`),
KEY (`ctg_id`),
KEY (`status`)
) ENGINE=MyISAM default charset=utf8 comment='分类/子分类url, 用来获取分类产品列表';

-- 考虑中: `cot_goodslist_url` 可与 `cot_xxx_goodslist_url` 表合并
-- collect_xxx_goodslist_url `cot_xxx_goodslist_url` 表的结构 - (某模块)分类产品列表url, 采集用
CREATE TABLE IF NOT EXISTS `cot_newark_goodslist_url` (
`id` mediumint(8) not null AUTO_INCREMENT,
`cid` mediumint(8) not null default '0' comment '采集库的分类id: cot_xxx_ctg_url.id',
`ctg_name` varchar(200) default null comment '分类名',
`ctg_id` mediumint(8) not null default '0' comment '采集分类id',
`ctg_pid` mediumint(8) not null default '0' comment '采集分类父id',
`src` varchar(10) default 'newark' comment '提供商[模块]: digikey,mouser,future,newark,element14,avnet,arrow,rs,tti',
`referer` varchar(255) not null default '' comment '父级url',
`gdslist_url` varchar(255) not null default '' comment '分类产品列表url',
`gdslist_args` varchar(255) not null default '' comment '产品列表分页参数,POST用,GET参数直接在url后面',
`gds_count` mediumint(8) not null default '0' comment '此页商品总数',
`power` mediumint(4) not null default '0' comment '权重',
`client` varchar(80) default null comment '最后一次分配客户端: taskid',
`status` tinyint(1) not null default '0' comment '状态: 0 未采集; 1 已分配任务,正在采集; 2 已采集完成; 3 采集失败',
`created` int(11) not null default '0' comment '创建时间',
`updated` int(11) not null default '0' comment '更新时间',
PRIMARY KEY (`id`),
UNIQUE KEY (`gdslist_url`),
KEY (`ctg_id`),
KEY (`status`)
) ENGINE=MyISAM default charset=utf8 comment='分类产品列表url, 采集用';

-- 考虑中: `cot_goods_url` 可与 `cot_xxx_goods_url` 表合并
-- collect_xxx_goods_url `cot_xxx_goods_url` 表的结构 - (某模块)产品url, 更新用
CREATE TABLE IF NOT EXISTS `cot_newark_goods_url` (
`id` mediumint(8) not null AUTO_INCREMENT,
`gds_id` int(11) not null default '0' comment '商品唯一id, 与大数据中心对应',
`gds_name` varchar(200) default null comment '商品名,制造商零件编号',
`gds_sn` varchar(200) default null comment '供应商零件编号',
`src` varchar(10) default 'newark' comment '提供商[模块]: digikey,mouser,future,newark,element14,avnet,arrow,rs,tti',
`lid` mediumint(8) not null default '0' comment 'goodslist_url.id',
`ctg_name` varchar(100) default null comment '分类名',
`ctg_id` mediumint(8) not null default '0' comment '采集分类id',
`ctg_pid` mediumint(8) not null default '0' comment '采集分类父id',
`referer` varchar(255) not null default '' comment '父级url',
`gds_url` varchar(255) not null default '' comment '产品url',
`cot_count` mediumint(4) not null default '0' comment '抓取总次数,抓取次数越多代表产品关注度越高',
`power` mediumint(4) not null default '0' comment '权重, 9999 强制优先更新',
`hot` int(11) not null default '0' comment '热度: 算法如 访问量x2，购买量(或金额)x5，搜索x1等计算，优先更新',
`client` varchar(80) default null comment '最后一次分配客户端',
`status` tinyint(1) not null default '0' comment '状态: 0 未更新; 1 已分配任务,正在更新; 2 本次已经更新完成,但没推送; 3 更新失败; 4 更新并推送完成',
`created` int(11) not null default '0' comment '创建时间',
`updated` int(11) not null default '0' comment '更新时间, 最后一次操作时间, 如: 分配时间, 采集时间',
PRIMARY KEY (`id`),
UNIQUE KEY (`gds_url`),
KEY (`gds_id`),
KEY (`gds_name`),
KEY (`power`),
KEY (`status`)
) ENGINE=MyISAM default charset=utf8 comment='产品url, 更新用';

-- `cot_goods` 可与 `cot_xxx_goods` 表合并
-- collect_xxx_goods `cot_xxx_goods` 表的结构 - (某模块)采集/更新到的产品
CREATE TABLE IF NOT EXISTS `cot_newark_goods` (
`id` int(11) not null AUTO_INCREMENT,
`gid` int(11) not null  comment 'cot_xxx_goods_url.id, 与cot_xxx_goods_url对应',
`gds_id` int(11) not null default '0' comment '商品唯一id, 与大数据中心对应',
`gds_name` varchar(200) default null comment '商品名,制造商零件编号',
`gds_sn` varchar(200) default null comment '供应商零件编号',
`src` varchar(10) default 'newark' comment '提供商[模块]: digikey,mouser,future,newark,element14,avnet,arrow,rs,tti',
`provider` varchar(200) DEFAULT NULL COMMENT '制造商',
`provider_url` varchar(255) DEFAULT NULL COMMENT '制造商url',
`brand_name` varchar(30) default null comment '商品品牌名',
-- `ctg_name` varchar(100) default null comment '我们的标准分类名',
-- `ctg_id` mediumint(8) not null default '0' comment '我们的标准分类id',
-- `ctg_pid` mediumint(8) not null default '0' comment '我们的分类父id',
`gds_doc` varchar(255) not null default '' comment '采集到的商品手册(远程)',
`gds_thumb` varchar(255) not null default '' comment '缩图',
`gds_img` varchar(255) not null default '' comment '产品图',
`inventory` varchar(30) not null default '0' comment '库存,只作展示',
`warehouse` varchar(255) DEFAULT '-' comment '仓库位置: 大陆，香港，海外...',
`spq` varchar(8) NOT NULL DEFAULT '0' comment 'SPQ 标准包装量',
`moq` varchar(8) NOT NULL DEFAULT '0' comment 'MOQ 最小订购量',
`currency` varchar(10) default 'USD' comment '货币单位: USD,CNY,HKD,NTD,EUR',
`market_price` decimal(10,2) default '0' comment '市场单价',
`shop_price` decimal(12,4) DEFAULT '0.0000' comment '我们的单价',
`cost_price` decimal(12,4) DEFAULT '0.0000' comment '我们的成本价',
`prices` varchar(1023) DEFAULT '' comment '阶梯价格 json:[{nums:1,price:0.5,rmb:3.0},{nums:10,price:0.49,rmb:3.0}{nums:100,price:0.47,rmb:2.9}]',
`ctg_path` varchar(255) default null comment '商品分类路径',
`gds_attrs` text default null comment '产品属性 json:{k1:v1,k2:v2,k3:v3}',
`gds_description` text default null comment '商品描述',
`gds_url` varchar(255) not null default '' comment '产品url',
`encap` varchar(255) DEFAULT '-' comment '封装',
`package` varchar(255) DEFAULT '-' comment '包装',
`is_rohs` tinyint(1) not null default '0' comment 'rohs状态: 0 没有rohs信息; 1 通过; 2 未通过',
`rohsstate` varchar(200) DEFAULT NULL COMMENT 'RoHS 状态',
-- ...
`note` varchar(200) DEFAULT NULL COMMENT '备用',
`status` tinyint(1) not null default '0' comment '状态: 0 未更新; 2 本次已经更新完成, 但没推送; 4 更新并推送完成',
`created` int(11) not null default '0' comment '创建时间',
`updated` int(11) not null default '0' comment '更新时间, 最后一次操作时间, 如: 更新时间, 推送时间',
PRIMARY KEY (`id`),
-- FOREIGN KEY(`id`) REFERENCES cot_digikey_goods_url(`id`) on delete cascade,
KEY (`gid`),
KEY (`gds_id`),
KEY (`gds_name`),
UNIQUE KEY (`gds_url`),
KEY (`status`)
) ENGINE=MyISAM default charset=utf8 comment='采集/更新到的产品';

-- bigdata_goods `big_goods`

-- ecs_goods `ecs_goods`

