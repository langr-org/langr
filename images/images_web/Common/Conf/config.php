<?php
return array(
	'MODULE_ALLOW_LIST'	=> array('Home'),
	'DEFAULT_MODEL'		=> 'Home',// 默认模块
	'DEFAULT_ACTION'	=> 'index', // 默认操作名称

	'LOAD_EXT_FILE' => 'config_db',
	'DEFAULT_TIMEZONE'	=> 'Asia/Shanghai',

	/* Cookie设置 */
	'COOKIE_EXPIRE'		=> 604800,	// Coodie有效期
	'COOKIE_DOMAIN'		=> '',		// Cookie有效域名
	'COOKIE_PATH'		=> '/',		// Cookie路径
	'COOKIE_PREFIX'		=> 'IMG_',	// Cookie前缀 避免冲突
	/* SESSION设置 */
	'SESSION_AUTO_START'	=> true,	// 是否自动开启Session
	'SESSION_OPTIONS'	=> array(),	// session 配置数组 支持type name id path expire domian 等参数
	'SESSION_TYPE'		=> '',		// session hander类型 默认无需设置 除非扩展了session hander驱动
	'SESSION_PREFIX'	=> 'IMG_',	// session 前缀
	'VAR_SESSION_ID'	=> 'session_id',//sessionID的提交变量
	'SESSION_EXPIRE'	=> 604800,
	
	/* 数据缓存设置 */
	'DATA_CACHE_TYPE'	=> 'File',	// 数据缓存类型,支持:File|Db|Apc|Memcache|Shmop|Sqlite|Xcache|Apachenote|Eaccelerator
	'DATA_CACHE_TIME'	=> 0,		// 数据缓存有效期 0表示永久缓存
	'DATA_CACHE_COMPRESS'	=> false,	// 数据缓存是否压缩缓存
	'DATA_CACHE_CHECK'	=> false,	// 数据缓存是否校验缓存
	'DATA_CACHE_PATH'	=> TEMP_PATH,	// 缓存路径设置 (仅对File方式缓存有效)
	'DATA_CACHE_SUBDIR'	=> true,	// 使用子目录缓存 (自动根据缓存标识的哈希创建子目录)
	'DATA_PATH_LEVEL'	=> 2,		// 子目录缓存级别
	
	/* 缓存设置 */
	'CACHE_TYPE'		=> 'redis',	// 存储介质
	'REDIS_HOST'		=> '192.168.0.13',
	'REDIS_PORT'		=> '6379',

	/* URL设置 */
	'URL_CASE_INSENSITIVE'	=> true,	//url不区分大小写
	'URL_MODEL'		=> 2,
	//'URL_PATHINFO_DEPR'	=> '/',		//PATHINFO模式下，各参数之间的分割符号
	'URL_HTML_SUFFIX'	=> '.html',	//URL伪静态后缀设置
	/* URL路由 */
	'URL_ROUTER_ON' => true,		//开启路由
	'URL_ROUTE_RULES' => array(
		'/^images\/(.*)$/'=>'api/rfile?src=:1',
	),

	/*模板引擎*/
	//'TMPL_ENGINE_TYPE' =>'PHP',
	//模版替换
	'TMPL_PARSE_STRING' => array(
		'__UPFILE__' => '/images',			//读取上传文件位置用
		//'__STYLE__' => 'http://imgs.com/public',	//模版中会自动把__STYLE__替换为http://imgs.com/public
		'__STYLE__' => '/public',			// css,js,imgage样式路径
	),

	/*自定义配置文件开始*/	
	'WEB_HOST' => 'http://img.h8.com/',		//网站域名，读取: C('WEB_HOST')
	'IMG_PATH' => 'http://img.h8.hua/images/',	//IMG url路径，不带分配的目录和文件名
	'UPLOAD_PATH' => './images/',			/* 图片上传的服务器路径 */

	/*
	// 上线时删除下列配置
	'APP_FILE_CASE' => true,			// 是否检查文件的大小写 对Windows平台有效
	'SHOW_ERROR_MSG' => true,			// 显示错误信息
	// 运行时间设置
	'SHOW_PAGE_TRACE' => true,			// 显示TRACE	
	'SHOW_RUN_TIME' => true,			// 运行时间显示
	'SHOW_ADV_TIME' => true,			// 显示详细的运行时间
	'SHOW_DB_TIMES' => true,			// 显示数据库查询和写入次数
	'SHOW_CACHE_TIMES' => true,			// 显示缓存操作次数
	'SHOW_USE_MEM' => true,				// 显示内存开销
	 */
);

/* end file */
