<?php
return array(
	/* 数据库设置 */
	'DB_DSN' => 'mysql://root:zkc123456@192.168.0.13:3306/community_base_db#utf8',
	//数据库配置1
	'db_default'	= array(
		'db_type'  => 'mysql',
		'db_host'  => '192.168.0.13',
		'db_user'  => 'root',
		'db_pwd'   => 'zkc123456',
		'db_name'  => 'community_base_db'
		'db_port'  => '3306',
		'db_charset'  => 'utf8',
	),
	//数据库配置2
	'db_wealth'	=> 'mysql://root:zkc123456@192.168.0.13:3306/community_wealth_db#utf8',
	//数据库配置n
	'db_account'	=> 'mysql://root:zkc123456@192.168.0.13:3306/community_account_db#utf8',
	'db_admin'	=> 'mysql://root:zkc123456@192.168.0.13:3306/community_admin_db#utf8',
);

/* end file */
