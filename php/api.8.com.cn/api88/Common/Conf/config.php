<?php
return array(
	'MODULE_ALLOW_LIST'	=> array('Home','Member','Admin'),
	'DEFAULT_MODEL'		=> 'Home',// 默认模块
	'DEFAULT_ACTION'	=> 'index', // 默认操作名称

	/* 数据库设置 */
	'DB_TYPE'		=> 'mysql',	// 数据库类型
	'DB_HOST'		=> '192.168.0.13',
	'DB_NAME'		=> 'system_data',
	'DB_USER'		=> 'root',
	'DB_PWD'		=> 'zkc123456',

	'DB_PORT'		=> '',
	'DB_PREFIX'		=> 'data_',	// DB表前缀
	'DB_FIELDTYPE_CHECK'	=> false,	// 是否进行字段类型检查
	'DB_FIELDS_CACHE'	=> true,	// 启用字段缓存
	'DB_CHARSET'		=> 'utf8',	// 数据库编码默认采用utf8
	'DB_DEPLOY_TYPE'	=> 0,		// 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
	'DB_RW_SEPARATE'	=> false,	// 数据库读写是否分离 主从式有效
	'DB_MASTER_NUM'		=> 1,		// 读写分离后 主服务器数量
	'DB_SQL_BUILD_CACHE'	=> false,	// 数据库查询的SQL创建缓存
	'DB_SQL_BUILD_QUEUE'	=> 'file',	// SQL缓存队列的缓存方式 支持 file xcache和apc
	'DB_SQL_BUILD_LENGTH'	=> 20,		// SQL缓存的队列长度
	//'DB_PARAMS'		=> array(\PDO::ATTR_CASE => \PDO::CASE_NATURAL),	//表列表保持原样
	//'DB_PARAMS'		=> array(\PDO::ATTR_CASE => \PDO::CASE_LOWER),		// 强制表列名小写

	'LOAD_EXT_FILE' => 'api',
	/* Cookie设置 */
	'COOKIE_EXPIRE'		=> 604800,	// Coodie有效期
	'COOKIE_DOMAIN'		=> '',		// Cookie有效域名
	'COOKIE_PATH'		=> '/',		// Cookie路径
	'COOKIE_PREFIX'		=> 'ZKC_',	// Cookie前缀 避免冲突
	'DEFAULT_TIMEZONE'	=> 'Asia/Shanghai',
	
	/* SESSION设置 */
	'SESSION_AUTO_START'	=> true,	// 是否自动开启Session
	'SESSION_OPTIONS'	=> array(),	// session 配置数组 支持type name id path expire domian 等参数
	'SESSION_TYPE'		=> '',		// session hander类型 默认无需设置 除非扩展了session hander驱动
	'SESSION_PREFIX'	=> 'ZKCS_',	// session 前缀
	'VAR_SESSION_ID'	=> 'session_id',//sessionID的提交变量
	'SESSION_EXPIRE'	=> 604800,
	
	/* 数据缓存设置 */
	'DATA_CACHE_TYPE'	=> 'File',	// 数据缓存类型,支持:File|Db|Apc|Memcache|Shmop|Sqlite|Xcache|Apachenote|Eaccelerator
	'DATA_CACHE_TIME'	=> 3600,	// 数据缓存有效期 0表示永久缓存
	'DATA_CACHE_PREFIX'	=> 'tmp_',
	'DATA_CACHE_COMPRESS'	=> false,	// 数据缓存是否压缩缓存
	'DATA_CACHE_CHECK'	=> false,	// 数据缓存是否校验缓存
	'DATA_CACHE_PATH'	=> TEMP_PATH,	// 缓存路径设置 (仅对File方式缓存有效)
	'DATA_CACHE_SUBDIR'	=> true,	// 使用子目录缓存 (自动根据缓存标识的哈希创建子目录)
	'DATA_PATH_LEVEL'	=> 2,		// 子目录缓存级别
	
	/* 缓存时间设置 */
	'CACHE_TYPE'		=> 'redis',	// 存储介质
	/* cache redis server(只缓存并没有数据持久化) */
	'REDIS_HOST'		=> '192.168.0.13',
	'REDIS_PORT'		=> '6380',
	'REDIS_AUTH'		=> '',
	/* 持久cache,可作队列服务 */
	'REDIS_SERVER' => array(
		/* 注册队列 */
		'register_queue' => array(
			'host'=>'192.168.0.13',
			'port'=>'6379',
			'auth'=>'haha',
		),
		/* 订单队列 */
		'orders_queue' => array(
			'host'=>'192.168.0.13',
			'port'=>'6379',
			'auth'=>'haha',
		),
	),

	/* URL设置 */
	'URL_CASE_INSENSITIVE'	=> true,	//url不区分大小写
	//'URL_PATHINFO_DEPR'	=> '/',		//PATHINFO模式下，各参数之间的分割符号
	'URL_HTML_SUFFIX'	=> '.html',	//URL伪静态后缀设置
	'URL_MODEL'		=> 2,
	/* URL路由 */
	'URL_ROUTER_ON' => true,		//开启路由
	/* 静态路由 */
	'URL_MAP_RULES' => array(
		'image-verify-code'=>'verifycode/image',
		'sms-verify-code'=>'verifycode/sms',
	),
	/* 动态路由 */
	'URL_ROUTE_RULES' => array(
		'users/:id\d'=>'home/users/info/:1',
		'orders/:id\d'=>'home/orders/detail?orderid=:1',
		'licaiplan/:id\d'=>'home/licaiplan/detail?typeid=:1',
		'news/:id\d'=>'news/detail/:1',
		'products/:id\d'=>'home/products/detail?id=:1',
		'products/history/:id\d/:page\d'=>'home/products/history?id=:1&page=:2',
	),
		
	'TOKEN_ON'=>false,		//是否开启令牌验证
	/*
	'TOKEN_ON'=>true,		//是否开启令牌验证
	'TOKEN_NAME'=>'__hash__',	//令牌验证的表单隐藏字段名称
	'TOKEN_TYPE'=>'md5',		//令牌哈希验证规则 默认为MD5
	'TOKEN_RESET'=>true,		//令牌验证出错后是否重置令牌 默认为true
	 */

	//静态缓存		
	/* 'HTML_CACHE_ON' => true,		//开启静态缓存
	'HTML_CACHE_TIME' => 60,		//缓存时间
	'HTML_CACHE_RULES' => array (		//静态缓存规则
		'index:index'=>array('index','3600'),
	), */

	/*模板引擎*/
	//'TMPL_ENGINE_TYPE' =>'PHP',
	//模版替换
	'TMPL_PARSE_STRING' => array(
		'__UPFILE__' => '/pub/uploads',		//读取上传文件位置用
		//'__STYLE__' => 'http://imgs.com/pub',	//模版中会自动把__STYLE__替换为http://imgs.com/public
		'__STYLE__' => '/pub',			//css,js,imgage样式路径
	),

	/*自定义配置文件开始*/	
	'WEB_HOST' => 'http://api88.88.com.cn',		//网站域名，发邮件什么的都会用到 php读取: C('WEB_HOST')

	'APP_FILE_CASE' => true,			// 是否检查文件的大小写 对Windows平台有效
	/* 上线时删除下列配置 */
	//'SHOW_ERROR_MSG' => true,			// 显示错误信息		
	/* 运行时间设置 */
	//'SHOW_PAGE_TRACE' => true,			// 显示TRACE	
	//'SHOW_RUN_TIME' => true,			// 运行时间显示
	//'SHOW_ADV_TIME' => true,			// 显示详细的运行时间
	//'SHOW_DB_TIMES' => true,			// 显示数据库查询和写入次数
	//'SHOW_CACHE_TIMES' => true,			// 显示缓存操作次数
	//'SHOW_USE_MEM' => true,				// 显示内存开销
);
/* end file */
