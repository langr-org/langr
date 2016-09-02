/** 
 * @file php_lagi.h
 * @brief lagi php extension
 * 
 * Copyright (C) 2011 LangR.Org
 * 
 * This is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 * 
 * @package lagi
 * @author Huang Hua <hua@langr.org> 2011/08/11 23:13
 *
 * $Id: php_lagi.h 34 2012-01-06 11:25:38Z loghua@gmail.com $
 */

#ifndef PHP_LAGI_H
#define PHP_LAGI_H

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"

#define	PROGRAM_NAME		"lagi"
#define	VERSION_STRING		"0.6.2 (82)"
#define	LAST_COMPILE_TIME	"2012-01-05 17:57:48"
#include "debug.h"
#include "ami.h"
#include "amid/iotimeout.h"

extern zend_module_entry lagi_module_entry;
#define phpext_lagi_ptr &lagi_module_entry

#ifdef PHP_WIN32
#	define PHP_LAGI_API __declspec(dllexport)
#elif defined(__GNUC__) && __GNUC__ >= 4
#	define PHP_LAGI_API __attribute__ ((visibility("default")))
#else
#	define PHP_LAGI_API
#endif

#ifdef ZTS
#include "TSRM.h"
#endif

#ifndef	LAGI_API
 #define	LAGI_API	
#endif

#define	LAGI_RES_NAME		"lagi_link resource"
#define	LAGI_PRES_NAME		"lagi_plink resource"

#ifndef	BUFFER_SIZE
 #define	BUFFER_SIZE		2048
#endif
#ifndef	BIG_BUFFER_SIZE
 #define	BIG_BUFFER_SIZE	4096
#endif
#ifndef	MAX_BUFFER_SIZE
 #define	MAX_BUFFER_SIZE	8192
#endif

#define	CHANNEL_NUMS	1024

typedef	struct lagi_link { /* {{{ */
	int		fd;
	int		host;		/* ... string */
	short		port;
	long		res_id;
} lagi_res; /* }}} */

PHP_MINIT_FUNCTION(lagi);
PHP_MSHUTDOWN_FUNCTION(lagi);
PHP_RINIT_FUNCTION(lagi);
PHP_RSHUTDOWN_FUNCTION(lagi);
PHP_MINFO_FUNCTION(lagi);

PHP_FUNCTION(lagi_connect);				/*  */
PHP_FUNCTION(lagi_pconnect);				/*  */
PHP_FUNCTION(lagi_action);				/*  */
PHP_FUNCTION(lagi_login);				/*  */
PHP_FUNCTION(lagi_command);				/*  */
PHP_FUNCTION(lagi_close);				/*  */
/* ... */
PHP_FUNCTION(lagi_originate);				/*  */
PHP_FUNCTION(lagi_dbop);				/*  */
PHP_FUNCTION(lagi_get_db);				/*  */
PHP_FUNCTION(lagi_put_db);				/*  */
PHP_FUNCTION(lagi_channel_analyse);			/*  */
PHP_FUNCTION(lagi_hints);				/*  */
PHP_FUNCTION(lagi_parked);				/*  */
PHP_FUNCTION(lagi_outcall);				/*  */

/**
 * @fn
 * @brief return array
 * @param val Response data
 * @return void
 */
LAGI_API void lagi_return(zval * return_value, char * val TSRMLS_DC);

/**
 * @fn
 * @brief return array
 * data 数据模板见 (core) show channels
 * @NOTE asterisk 版本不同, (core show)/(show) channels 返回数据(长度)略有不同.
 * @param exten 需要分析的分机或通道
 * @param data Response data
 * @return void
 * @value-result return_value 当前分机相关的通道(数组)
 */
LAGI_API void lagi_channel_analyse(zval * return_value, char * exten, char * data TSRMLS_DC);

/**
 * @fn
 * @brief 返回分机状态
 * data 数据模板见 (core) show hints.
 * @param exten 需要分析的分机, NULL 则返回所有分机状态
 * @param data Response data
 * @return void
 * @value-result return_value 当前分机或全部分机的状态(数组)
 */
LAGI_API void lagi_hints(zval * return_value, char * exten, char * data TSRMLS_DC);

/* 
  	Declare any global variables you may need between the BEGIN
	and END macros here:     
*/
ZEND_BEGIN_MODULE_GLOBALS(lagi)
	long default_link_id;
	long link_count;
	long link_total;
	long max_links;
	lagi_res * default_link;
	char * default_host;
	char * default_port;
	char * default_user;
	char * default_pwd;
	long default_timeout;
	/*long errno;
	char * error;*/
ZEND_END_MODULE_GLOBALS(lagi)

/* In every utility function you add that needs to use variables 
   in php_lagi_globals, call TSRMLS_FETCH(); after declaring other 
   variables used by that function, or better yet, pass in TSRMLS_CC
   after the last function argument and declare your utility function
   with TSRMLS_DC after the last declared argument.  Always refer to
   the globals in your function as LAGI_G(variable).  You are 
   encouraged to rename these macros something shorter, see
   examples in any other php module directory.
*/

#ifdef ZTS
#define LAGI_G(v) TSRMG(lagi_globals_id, zend_lagi_globals *, v)
#else
#define LAGI_G(v) (lagi_globals.v)
#endif

/*
#define	errno	LAGI_G(_errno);
#define	error	LAGI_G(_error);
*/

#endif	/* PHP_LAGI_H */
