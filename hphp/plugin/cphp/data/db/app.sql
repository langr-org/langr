/***
 * cphp 网站架构基本数据库
 * 数据资料的说明讲参见 APP.sql
 * 
 * $Id: app.sql 8 2009-10-20 10:05:34Z langr $
 */
set names UTF8;
drop table if exists `{$db_prefix}c_siteset`;
create table `{$db_prefix}c_siteset` (
	`id` int(10) unsigned not null auto_increment,
	`name` varchar(255),
	`content` text,
	`createtime` datetime,
	`updatetime` datetime,
	`state` tinyint,
	`note` varchar(255),
	index (id),
	primary key (id)
) engine=MyISAM default charset=utf8;

drop table if exists `{$db_prefix}c_user`;
create table `{$db_prefix}c_user` (
	`id` int(10) unsigned not null auto_increment,
	`account` varchar(32),
	`pwd` varchar(32),
	`pwd2` varchar(32),
	`gid` int(10) unsigned not null default '0',
	`attr` int(10),
	`money` int(10),
	`icon` varchar(255),
	`role` int(10),
	`power_site` int(10) not null default '0',
	`power_article` int(10) not null default '0',
	`power_other` int(10) not null default '0',
	`last_logintime` datetime,
	`last_loginip` varchar(20),
	`login_count` int(10),
	`createtime` datetime,
	`updatetime` datetime,
	`state` tinyint,
	`note` varchar(255),
	index (id),
	primary key (id)
) engine=MyISAM default charset=utf8;

drop table if exists `{$db_prefix}c_userinfo`;
create table `{$db_prefix}c_userinfo` (
	`id` int(10) unsigned not null auto_increment,
	`uid` int(10) unsigned not null default '0',
	`nickname` varchar(32),
	`name` varchar(32),
	`sex` varchar(2),
	`birthday` date,
	`email` varchar(255),
	`url` varchar(255),
	`tel` varchar(255),
	`post` varchar(10),
	`addr` varchar(255),
	`createtime` datetime,
	`updatetime` datetime,
	`state` tinyint,
	`note` varchar(255),
	index (id),
	primary key (id)
) engine=MyISAM default charset=utf8;

drop table if exists `{$db_prefix}c_modules`;
create table `{$db_prefix}c_modules` (
	`id` int(10) unsigned not null auto_increment,
	`idname` varchar(32),
	`name` varchar(32),
	`pid` int(10) unsigned not null default '0',
	`class` int(10) unsigned not null default '0',
	`area` varchar(10),
	`attr` int(10),
	`order` int(10), 
	`link` varchar(255),
	`target` varchar(10),
	`title` varchar(255),
	`content` text,
	`createtime` datetime,
	`updatetime` datetime,
	`state` tinyint,
	`note` varchar(255),
	index (id),
	primary key (id)
) engine=MyISAM default charset=utf8;

drop table if exists `{$db_prefix}c_class`;
create table `{$db_prefix}c_class` (
	`id` int(10) unsigned not null auto_increment,
	`idname` varchar(32),
	`name` varchar(32),
	`pid` int(10) unsigned not null default '0',
	`attr` int(10),
	`order` int(10),
	`description` varchar(255),
	`createtime` datetime,
	`updatetime` datetime,
	`state` tinyint,
	`note` varchar(255),
	index (id),
	primary key (id)
) engine=MyISAM default charset=utf8;

drop table if exists `{$db_prefix}c_article`;
create table `{$db_prefix}c_article` (
	`id` int(10) unsigned not null auto_increment,
	`title` varchar(255),
	`class` int(10),
	`attr` int(10),
	`view` int(10),
	`comment` int(10),
	`author` varchar(50),
	`body` text,
	`ubb` int(10) not null default '1',
	`createtime` datetime,
	`updatetime` datetime,
	`state` tinyint,
	`note` varchar(255),
	index (id),
	primary key (id)
) engine=MyISAM default charset=utf8;

drop table if exists `{$db_prefix}c_comment`;
create table `{$db_prefix}c_comment` (
	`id` int(10) unsigned not null auto_increment,
	`title` varchar(255),
	`aid` int(10),
	`attr` int(10),
	`comment` text,
	`author` varchar(50),
	`email` varchar(255),
	`url` varchar(255),
	`ip` varchar(17),
	`reply` text,
	`createtime` datetime,
	`updatetime` datetime,
	`state` tinyint,
	`note` varchar(255),
	index (id),
	primary key (id)
) engine=MyISAM default charset=utf8;

drop table if exists `{$db_prefix}c_guestbook`;
create table `{$db_prefix}c_guestbook` (
	`id` int(10) unsigned not null auto_increment,
	`title` varchar(255),
	`uid` int(10),
	`attr` int(10),
	`comment` text,
	`author` varchar(50),
	`email` varchar(255),
	`url` varchar(255),
	`ip` varchar(17),
	`reply` text,
	`by` varchar(50),
	`createtime` datetime,
	`updatetime` datetime,
	`state` tinyint,
	`note` varchar(255),
	index (id),
	primary key (id)
) engine=MyISAM default charset=utf8;

drop table if exists `{$db_prefix}c_view`;
create table `{$db_prefix}c_view` (
	`id` int(10) unsigned not null auto_increment,
	`url` text,
	`ip` varchar(20),
	`ipaddr` varchar(255),
	`createtime` datetime,
	`updatetime` datetime,
	`state` tinyint,
	`note` varchar(255),
	index (id),
	primary key (id)
) engine=MyISAM default charset=utf8;


drop table if exists `{$db_prefix}p_waveclass`;
create table `{$db_prefix}p_waveclass` (
	`id` int(10) unsigned not null auto_increment,
	`name` varchar(255),
	`pid` int(10) unsigned not null default '0',
	`attr` int(10),
	`order` int(10),
	`description` varchar(255),
	`createtime` datetime,
	`updatetime` datetime,
	`state` tinyint,
	`note` varchar(255),
	index (id),
	primary key (id)
) engine=MyISAM default charset=utf8;

drop table if exists `{$db_prefix}p_wave`;
create table `{$db_prefix}p_wave` (
	`id` int(10) unsigned not null auto_increment,
	`name` varchar(255),
	`waveclass` int(10),
	`measure` varchar(50),
	`cost` double(16,2) unsigned NOT NULL default '0.00',
	`money` double(16,2) unsigned NOT NULL default '0.00',
	`storage` int(10) unsigned NOT NULL default '0',
	`sell` int(10) unsigned NOT NULL default '0',
	`attr` int(10),
	`view` int(10),
	`comment` int(10),
	`by` varchar(50),
	`img` varchar(255),
	`body` text,
	`createtime` datetime,
	`updatetime` datetime,
	`state` tinyint,
	`note` varchar(255),
	index (id),
	primary key (id)
) engine=MyISAM default charset=utf8;
