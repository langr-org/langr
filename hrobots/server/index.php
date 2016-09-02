<?php
	if ( PHP_SAPI == "cli" ) {
		define('MODE_NAME', 'cli');
	}
	//define('BUILD_DIR_SECURE', true);//每个目录下生存index.html，防止目录预览
	define('APP_DEBUG',true);//开启调试模式
	//define('APP_NAME', 'server');//应用名
	//define('APP_PATH', './');//应用目录
	//define( 'DS' , DIRECTORY_SEPARATOR);//系统目录分隔符
	//define( 'ROOT' , dirname( __FILE__ ) . DS  );//根目录路径	
	require '../ThinkPHP/ThinkPHP.php';
?>
