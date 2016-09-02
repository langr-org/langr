/** 
 * @file lagi.c
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
 * @author Langr <hua@langr.org> 2011/08/11 23:14
 * 
 * $Id: lagi.c 38 2012-01-17 09:23:08Z loghua@gmail.com $
 */

#include "php_lagi.h"

/* If you declare any globals in php_lagi.h uncomment this: */
ZEND_DECLARE_MODULE_GLOBALS(lagi)

/* True global resources - no need for thread safety here */
static int le_lagi_count, le_lagi_current, le_lagi_res, le_lagi_pres;

/* {{{ lagi_functions[]
 * Every user visible function must have an entry in lagi_functions[].
 */
zend_function_entry lagi_functions[] = {
	PHP_FE(lagi_connect,		NULL)
	PHP_FE(lagi_pconnect,		NULL)
	PHP_FE(lagi_action,		NULL)
	PHP_FE(lagi_command,		NULL)
	PHP_FE(lagi_login,		NULL)
	PHP_FE(lagi_close,		NULL)
	PHP_FE(lagi_originate,		NULL)
	PHP_FE(lagi_dbop,		NULL)
	PHP_FE(lagi_get_db,		NULL)
	PHP_FE(lagi_put_db,		NULL)
	PHP_FE(lagi_channel_analyse,	NULL)
	PHP_FE(lagi_hints,		NULL)
	PHP_FE(lagi_parked,		NULL)
	PHP_FE(lagi_outcall,		NULL)
	{NULL, NULL, NULL}		/* Must be the last line in lagi_functions[] */
};
/* }}} */

/* {{{ lagi_module_entry
 */
zend_module_entry lagi_module_entry = {
#if ZEND_MODULE_API_NO >= 20010901
	STANDARD_MODULE_HEADER,
#endif
	"lagi",
	lagi_functions,
	PHP_MINIT(lagi),
	PHP_MSHUTDOWN(lagi),
	PHP_RINIT(lagi),		/* Replace with NULL if there's nothing to do at request start */
	PHP_RSHUTDOWN(lagi),		/* Replace with NULL if there's nothing to do at request end */
	PHP_MINFO(lagi),
#if ZEND_MODULE_API_NO >= 20010901
	VERSION_STRING,
#endif
	STANDARD_MODULE_PROPERTIES
};
/* }}} */

#ifdef COMPILE_DL_LAGI
ZEND_GET_MODULE(lagi)
#endif

/* {{{ PHP_INI
 */
PHP_INI_BEGIN()
    STD_PHP_INI_ENTRY("lagi.max_links", "-1", PHP_INI_ALL, OnUpdateLong, max_links, zend_lagi_globals, lagi_globals)
    STD_PHP_INI_ENTRY("lagi.default_host", "localhost", PHP_INI_ALL, OnUpdateString, default_host, zend_lagi_globals, lagi_globals)
    STD_PHP_INI_ENTRY("lagi.default_port", "5038", PHP_INI_ALL, OnUpdateString, default_port, zend_lagi_globals, lagi_globals)
    STD_PHP_INI_ENTRY("lagi.default_user", NULL, PHP_INI_ALL, OnUpdateString, default_user, zend_lagi_globals, lagi_globals)
    STD_PHP_INI_ENTRY("lagi.default_pwd", NULL, PHP_INI_ALL, OnUpdateString, default_pwd, zend_lagi_globals, lagi_globals)
    STD_PHP_INI_ENTRY("lagi.default_timeout", "10", PHP_INI_ALL, OnUpdateLong, default_timeout, zend_lagi_globals, lagi_globals)
PHP_INI_END()
/* }}} */

static void php_lagi_init_globals(zend_lagi_globals *lagi_globals) /* {{{ */
{
	lagi_globals->default_link_id = -1;
	lagi_globals->default_host = NULL;
	/*lagi_globals->errno = 0;
	lagi_globals->error = NULL;*/
} /* }}} */

/* free resource */
static void php_lagi_res_dtor(zend_rsrc_list_entry * rsrc TSRMLS_DC) /* {{{ */
{
	lagi_res * r = (lagi_res *) rsrc->ptr;

	app_debug(DINFO"res_dtor::le_count:%d(le_current:%d):run_total:%d(run_count:%d):fd:%d,host:%d,res_id:%ld", le_lagi_count, le_lagi_current, LAGI_G(link_total), LAGI_G(link_count), r->fd, r->host, r->res_id);
	if ( r ) {
		/* if ( r->ref_count > 1 ) {...} */
		lagi_close(r->fd);
		efree(r);
		r = NULL;
		le_lagi_current--;
		LAGI_G(link_count)--;
		app_debug(DINFO"res_dtor::le_count:%d(le_current:%d):run_total:%d(run_count:%d)", le_lagi_count, le_lagi_current, LAGI_G(link_total), LAGI_G(link_count));
	}
} /* }}} */

static void php_lagi_res_dtor_p(zend_rsrc_list_entry * rsrc TSRMLS_DC) /* {{{ */
{
	lagi_res * r = (lagi_res *) rsrc->ptr;

	app_debug(DINFO"res_dtor_p::le_count:%d(le_current:%d):run_total:%d(run_count:%d):fd:%d,host:%d,res_id:%ld", le_lagi_count, le_lagi_current, LAGI_G(link_total), LAGI_G(link_count), r->fd, r->host, r->res_id);
	if ( r ) {
		lagi_close(r->fd);
		pefree(r, 1);
		r = NULL;
		le_lagi_current--;
		LAGI_G(link_count)--;
		app_debug(DINFO"res_dtor_p::le_count:%d(le_current:%d):run_total:%d(run_count:%d)", le_lagi_count, le_lagi_current, LAGI_G(link_total), LAGI_G(link_count));
	}
} /* }}} */

PHP_MINIT_FUNCTION(lagi) /* {{{ */
{
	/* 
	ZEND_INIT_MODULE_GLOBALS(lagi, php_lagi_init_globals, NULL);
	*/
	le_lagi_current = 0;
	le_lagi_count = 0;
	REGISTER_INI_ENTRIES();
	le_lagi_res = zend_register_list_destructors_ex(php_lagi_res_dtor, NULL, LAGI_RES_NAME, module_number);
	/*le_lagi_pres = zend_register_list_destructors_ex(NULL, php_lagi_res_dtor_p, LAGI_RES_NAME, module_number);*/

	return SUCCESS;
} /* }}} */

PHP_MSHUTDOWN_FUNCTION(lagi) /* {{{ */
{
	UNREGISTER_INI_ENTRIES();
	return SUCCESS;
} /* }}} */

PHP_RINIT_FUNCTION(lagi) /* {{{ */
{
	LAGI_G(default_link_id) = -1;
	LAGI_G(link_count) = 0;
	LAGI_G(link_total) = 0;
	LAGI_G(default_link) = NULL;
	/*LAGI_G(errno) = 0;
	LAGI_G(error) = NULL;*/

	return SUCCESS;
} /* }}} */

/* Remove if there's nothing to do at request end */
PHP_RSHUTDOWN_FUNCTION(lagi) /* {{{ */
{
	if ( LAGI_G(default_link_id) > 0 ) {
		app_debug(DINFO"no close default link:resid%ld,link_count:%d", LAGI_G(default_link_id), LAGI_G(link_count));
	}

	return SUCCESS;
} /* }}} */

PHP_MINFO_FUNCTION(lagi) /* {{{ */
{
	php_info_print_table_start();
	php_info_print_table_header(2, "lagi support", "enabled");
	php_info_print_table_row(2, "lagi version", VERSION_STRING" compile:"LAST_COMPILE_TIME);
	php_info_print_table_row(2, "report bugs", "hua@langr.org");
	php_info_print_table_end();

	DISPLAY_INI_ENTRIES();
} /* }}} */

PHP_FUNCTION(lagi_connect) /* {{{ */
{
	char * host = NULL, * s_port = NULL, * user = NULL, * pwd = NULL;
	char * rebuf = NULL;
	int host_len = 0, port_len = 0, user_len = 0, pwd_len = 0, n = 0;
	long timeo = 0;
	long port = 0;
	long fd = 0;
	lagi_res * link = NULL;

	if ( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "|ssssl", & host, & host_len, & s_port, & port_len, & user, & user_len, & pwd, & pwd_len, & timeo) == FAILURE ) {
		RETURN_FALSE;		
	}

	if ( host == NULL ) {
		host = LAGI_G(default_host);
	}
	if ( port_len <= 0 ) {
		s_port = LAGI_G(default_port);
	}
	if ( user_len <= 0 ) {
		user = LAGI_G(default_user);
	}
	if ( pwd_len <= 0 ) {
		pwd = LAGI_G(default_pwd);
	}
	if ( timeo <= 0 ) {
		timeo = LAGI_G(default_timeout);
	}

	/**
	 * 默认连接 和 最大连接限制 都不应再返回之前的资源, 因为之前的资源可能已经被释放了 
	 * 或者可以使用引用计数来处理资源是否需要真正释放, 需要吗? 
	 * @modify by Langr <hua@langr.org> 2011/12/02 12:16
	 * 不需要, 因为理解错误. 除非是持久资源, 其他非持久资源只在此次请求中有效.
	 * 如果使用LAGI_G()统计, 最大连接数理应是指此进程请求允许的最大连接, 而不是全部所有请求的连接总数, 可测试.
	 */
	if ( LAGI_G(max_links) > 0 && LAGI_G(link_count) >= LAGI_G(max_links) ) {
		app_debug(DINFO"lagi_connect:(max_links:%ld)link_count:%ld,too many", LAGI_G(max_links), LAGI_G(link_count));
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "lagi_connect:(max_links:%ld)link_count:%ld,too many.", LAGI_G(max_links), LAGI_G(link_count));
		RETURN_FALSE;
	}
	fd = (long) lagi_tcp_connect(host, s_port, timeo);
	if ( fd <= 0 ) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "resource fd (%ld) created error.", fd);
		RETURN_FALSE;		
	}

	rebuf = lagi_send(fd, "Action: login\r\nUsername: %s\r\nSecret: %s\r\nEvents: off\r\n\r\n", user, pwd);
	if ( rebuf == NULL ) {
		app_debug(DERROR"(fd:%d,user:%s,pwd:%s),ret:%d", fd, user, pwd, rebuf);
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "resource fd (%ld) created error.2", fd);
		RETURN_FALSE;
	}
	efree(rebuf);

	LAGI_G(link_count)++;
	LAGI_G(link_total)++;
	le_lagi_count++;
	le_lagi_current++;

	/* resource */
	link = emalloc(sizeof(lagi_res));
	if ( link == NULL ) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "emalloc memory error.");
		RETURN_FALSE;
	}
	link->fd = fd;
	inet_pton(AF_INET, host, & link->host);
	link->port = atoi(s_port);
	/* EG(regular_list) 中保存 */
	ZEND_REGISTER_RESOURCE(return_value, link, le_lagi_res);
	link->res_id = Z_RESVAL_P(return_value);
	LAGI_G(default_link) = link;
	LAGI_G(default_link_id) = Z_RESVAL_P(return_value);
	app_debug(DINFO"link(resource)fd:%d,host:%d,port:%d,s_host:%s,s_port:%s,resid:%d", link->fd, link->host, link->port, host, s_port, LAGI_G(default_link_id));

	return ;
} /* }}} */

PHP_FUNCTION(lagi_pconnect) /* {{{ */
{
	/* TODO: 持久资源 */
	char * host = NULL, * s_port = NULL, * user = NULL, * pwd = NULL;
	char * rebuf = NULL;
	int host_len = 0, port_len = 0, user_len = 0, pwd_len = 0, n = 0;
	long port = 0;
	long fd = 0;
	lagi_res * link = NULL;

	if ( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "|ssss", & host, & host_len, & s_port, & port_len, & user, & user_len, & pwd, & pwd_len) == FAILURE ) {
		RETURN_FALSE;
	}

	if ( host == NULL ) {
		host = LAGI_G(default_host);
	}
	if ( host_len <= 0 ) {
		/* 默认连接? 
		 * @modify by Langr <hua@langr.org> 2011/12/12 17:50
		 * !!! (p)connect() 不检测和返回默认连接 */
		if ( LAGI_G(default_link_id) > 0 ) {
			/*zend_list_addref((long)link);*/
			app_debug(DINFO"lagi_connect:(host_len:%d)default_link:%ld", host_len, LAGI_G(default_link_id));
			/* 查找资源并检查资源可用性, 返回资源 */
			/*ZEND_FETCH_RESOURCE(link, lagi_res *, & z_link, LAGI_G(default_link_id), LAGI_RES_NAME, le_lagi_res);
			ZEND_REGISTER_RESOURCE(return_value, LAGI_G(default_link), le_lagi_res);*/
			/* RETVAL_RESOURCE(LAGI_G(default_link_id)); $a=res1,$a=res2 已经被释放 */
			app_debug(DINFO"lagi_connect:(host_len:%d)default_link r,", host_len);
			return ;
		}
		app_debug(DINFO"host_len=0?(%d,s%d):%s,", host_len, strlen(host), host);
		host = LAGI_G(default_host);
	}
	if ( port_len <= 0 ) {
		s_port = LAGI_G(default_port);
	}
	if ( user_len <= 0 ) {
		user = LAGI_G(default_user);
	}
	if ( pwd_len <= 0 ) {
		pwd = LAGI_G(default_pwd);
	}
	app_debug(DINFO"host(%d):%s,s_port(%d):%s,user(%d):%s,pwd(%d):%s", host_len, host, port_len, s_port, user_len, user, pwd_len, pwd);
	
	/* TODO: 检测是否已经有注册的持久连接, 并检测连接是否有效, 有并正常, 则返回之前的持久连接 */
	/* ... */

	fd = (long) lagi_tcp_connect(host, s_port, LAGI_G(default_timeout));
	if ( fd <= 0 ) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "resource fd (%ld) created error.", fd);
		RETURN_FALSE;
	}

	rebuf = lagi_send(fd, "Action: login\r\nUsername: %s\r\nSecret: %s\r\nEvents: off\r\n\r\n", user, pwd);
	LAGI_G(link_count)++;
	LAGI_G(link_total)++;
	le_lagi_count++;
	le_lagi_current++;

	/* resource */
	link = pemalloc(sizeof(lagi_res), 1);
	if ( link == NULL ) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "pemalloc memory error.");
		RETURN_FALSE;
	}
	link->fd = fd;
	inet_pton(AF_INET, host, & link->host);
	link->port = atoi(s_port);
	/* EG(regular_list) 中保存 */
	ZEND_REGISTER_RESOURCE(return_value, link, le_lagi_pres);
	/* TODO: 同时在 EG(persistent_list) 中保存, ... */
	link->res_id = Z_RESVAL_P(return_value);
	LAGI_G(default_link) = link;
	LAGI_G(default_link_id) = Z_LVAL_P(return_value);
	app_debug(DINFO"php pconnect::link(resource)fd:%d,host:%d,port:%d,s_host:%s,s_port:%s", link->fd, link->host, link->port, host, s_port);
	/*
	zend_rsrc_list_entry le;
	char * hash_key = NULL;
	int hash_key_len = 0;

	le.type = le_lagi_pres;
	le.ptr = link;
	hash_key_len = spprintf(&hash_key, 0, "lagi_pconnect:%s:%s", host, s_port);
	zend_hash_update(&EG(persistent_list), hash_key, hash_key_len + 1, (void*)&le, sizeof(zend_rsrc_list_entry), NULL);
	if ( hash_key != NULL ) {
		efree(hash_key);
	}
	*/

	n = lagi_recv(fd, & rebuf);

	return ;
} /* }}} */

PHP_FUNCTION(lagi_action) /* {{{ */
{
	char * data = NULL;
	char * ret = NULL;
	int data_len = 0;
	zval * z_link = NULL;
	lagi_res * link = NULL;

	if ( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s|r", & data, & data_len, & z_link) == FAILURE ) {
		RETURN_FALSE;		
	}

	/* fetch resource */
	if ( z_link == NULL ) {
		link = LAGI_G(default_link);
		/* res_id = LAGI_G(default_link_id) */
	} else {
		ZEND_FETCH_RESOURCE(link, lagi_res *, & z_link, -1, LAGI_RES_NAME, le_lagi_res);
		/*ZEND_FETCH_RESOURCE2(link, lagi_res *, & z_link, -1, LAGI_RES_NAME, le_lagi_res, le_lagi_pres);*/
	}

	if ( link == NULL ) {
		RETURN_FALSE;
	}
	if ( link->fd <= 0 ) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "resource fd (%d) not created.", link->fd);
		RETURN_FALSE;
	}
	ret = lagi_action(link->fd, data);
	if ( ret == NULL ) {
		RETURN_FALSE;
	}

	lagi_return(return_value, ret TSRMLS_CC);
	return ;
	/*RETURN_STRINGL(ret, strlen(ret), 0);*/
} /* }}} */

PHP_FUNCTION(lagi_login) /* {{{ */
{
	char * user = NULL, * pwd = NULL;
	char * ret = NULL;
	int user_len = 0, pwd_len = 0, len = 0;
	zval * z_link = NULL;
	lagi_res * link = NULL;

	if ( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "ss|r", & user, & user_len, & pwd, & pwd_len, & z_link) == FAILURE ) {
		RETURN_FALSE;
	}

	/* fetch resource */
	if ( z_link == NULL ) {
		link = LAGI_G(default_link);
	} else {
		ZEND_FETCH_RESOURCE(link, lagi_res *, & z_link, -1, LAGI_RES_NAME, le_lagi_res);
	}

	if ( link == NULL ) {
		RETURN_FALSE;
	}
	if ( link->fd <= 0 ) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "resource fd (%d) not created.", link->fd);
		RETURN_FALSE;
	}
	ret = lagi_login(link->fd, user, pwd);
	if ( ret == NULL ) {
		RETURN_FALSE;
	}

	lagi_return(return_value, ret TSRMLS_CC);
	return ;
} /* }}} */

PHP_FUNCTION(lagi_command) /* {{{ */
{
	char * data = NULL;
	char * ret = NULL;
	int data_len = 0, len = 0;
	zval * z_link = NULL;
	lagi_res * link = NULL;

	if ( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s|r", & data, & data_len, & z_link) == FAILURE ) {
		RETURN_FALSE;		
	}

	/* fetch resource */
	if ( z_link == NULL ) {
		link = LAGI_G(default_link);
	} else {
		ZEND_FETCH_RESOURCE(link, lagi_res *, & z_link, -1, LAGI_RES_NAME, le_lagi_res);
	}

	if ( link == NULL ) {
		RETURN_FALSE;
	}
	if ( link->fd <= 0 ) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "resource fd (%d) not created.", link->fd);
		RETURN_FALSE;
	}
	ret = lagi_command(link->fd, data);
	if ( ret == NULL ) {
		RETURN_FALSE;
	}

	lagi_return(return_value, ret TSRMLS_CC);
	return ;
} /* }}} */

PHP_FUNCTION(lagi_originate) /* {{{ */
{
	char * channel = NULL, * callerid = NULL, * exten = NULL, * context = NULL, * priority = NULL;
	char * ret = NULL, def_context[] = "from-internal", def_priority[] = "1";
	int channel_len = 0, callerid_len = 0, exten_len = 0, context_len = 0, priority_len = 0, len = 0;
	zval * z_link = NULL;
	lagi_res * link = NULL;

	if ( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "sss|ssr", & channel, & channel_len, & exten, & exten_len, & callerid, & callerid_len, & context, & context_len, & priority, & priority_len, & z_link) == FAILURE ) {
		RETURN_FALSE;
	}
	if ( channel == NULL || exten == NULL ) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "lost variable: channel, exten");
		RETURN_FALSE;
	}
	if ( callerid == NULL ) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "lost variable: callerid");
		RETURN_FALSE;
	}
	if ( context_len <= 0 ) {
		context = def_context;
	}
	if ( priority_len <= 0 ) {
		priority = def_priority;
	}

	/* fetch resource */
	if ( z_link == NULL ) {
		link = LAGI_G(default_link);
	} else {
		ZEND_FETCH_RESOURCE(link, lagi_res *, & z_link, -1, LAGI_RES_NAME, le_lagi_res);
		/*ZEND_FETCH_RESOURCE2(link, lagi_res *, & z_link, -1, LAGI_RES_NAME, le_lagi_res, le_lagi_pres);*/
	}
	if ( link == NULL ) {
		/* TODO: ... */
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "resource not connect.1");
		RETURN_FALSE;
	} else if ( link->fd <= 0 ) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "resource (%d) not connect.2", link->fd);
		RETURN_FALSE;
	}

	ret = lagi_send(link->fd, "Action: originate\r\nChannel: %s\r\nWaitTime: 30\r\nCallerId: %s\r\nExten: %s\r\nContext: %s\r\nPriority: %s\r\n\r\n", channel, callerid, exten, context, priority);
	if ( ret == NULL ) {
		RETURN_FALSE;
	}

	lagi_return(return_value, ret TSRMLS_CC);
	return ;
} /* }}} */

PHP_FUNCTION(lagi_close) /* {{{ */
{
	long id = -1;
	zval * z_link = NULL;

	if ( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "|r", & z_link) == FAILURE ) {
		RETURN_FALSE;		
	}

	if ( z_link == NULL ) {
		id = LAGI_G(default_link_id);
	} else {
		id = Z_RESVAL_P(z_link);
	}
	if ( id <= 0 ) {
		app_debug(DWARN"close error, res_id:%ld", id);
		RETURN_FALSE;
	}

	zend_list_delete(id);

	RETURN_TRUE;
} /* }}} */

LAGI_API void lagi_return(zval * return_value, char * val TSRMLS_DC) /* {{{ */
{
	int i = 0;
	char * v_s = NULL;
	char * v_v = NULL;
	char * v_end = NULL;
	const int response_len = 10;		/* "Response: " */
	const int newline_len = 2;		/* "\r\n" */
	const char end_cmd[] = "--END ";	/* command 去掉此行, 与 phpagi 兼容 */

	/* get "Response: xxx\r\n" */
	v_s = strchr(val, '\r');
	if ( v_s == NULL || strlen(val) <= response_len ) {
		app_debug(DINFO"data error.");
		RETVAL_FALSE;
		return ;
	}

	array_init(return_value);
	add_assoc_stringl(return_value, "Response", val + response_len, v_s - val - response_len, 1);
	v_end = strstr(v_s, end_cmd);
	if ( v_end != NULL ) {
		/* Action: Command */
		/*add_assoc_stringl(return_value, "data", v_s + newline_len, strlen(v_s) - newline_len * 3, 1);*/
		add_assoc_stringl(return_value, "data", v_s + newline_len, v_end - v_s - newline_len, 1);
	} else {
		v_s += newline_len;
		add_assoc_string(return_value, "data", v_s, 1);
		while ( true ) {
			v_v = strchr(v_s, ':');
			if ( v_v == NULL ) {
				break;
			}
			v_end = strchr(v_s, '\r');
			* v_v = '\0';
			if ( v_end == NULL ) {
				add_assoc_string(return_value, v_s, v_v + 2, 1);
				break;
			}
			add_assoc_stringl(return_value, v_s, v_v + 2, v_end - v_v - 2, 1);
			v_s = v_end + newline_len;
		}
	}
	/*efree(val);*/

	return ;
} /* }}} */

LAGI_API void lagi_channel_analyse(zval * return_value, char * exten, char * data TSRMLS_DC) /* {{{ */
{
	int i = 0, j = 0, count = 0, tmpp_len = 0, array_count = 0;
	char * copy_data = NULL, * channel_1 = NULL;
	char * c_data = NULL, * tmpp = NULL, * tmpp2 = NULL;
	char * c_array[CHANNEL_NUMS] = {NULL};			/* 通道列表 */
	int8_t c_name_len[CHANNEL_NUMS] = {0};			/* 通道名列表, 存储通道名长度 */

	/* 分机或通道不在 data 里面, 快点返回... */
	if ( strstr(data, exten) == NULL ) {
		app_debug(DINFO"no exten");
		RETVAL_FALSE;
		return ;
	}

	copy_data = c_data = strdup(data);
	if ( c_data == NULL ) {
		RETVAL_FALSE;
		return ;
	}
	/* 分析 data 里面的通道 */
	i = 0;
	while ( true ) {
		/* data 第一行和最后两行不需要 */
		channel_1 = strchr(c_data, '\n');
		if ( channel_1 == NULL || i >= CHANNEL_NUMS ) {
			break;
		}
		* channel_1 = '\0';
		channel_1++;
		c_array[i] = channel_1;
		/* 取通道名 */
		tmpp2 = strchr(channel_1, ' ');
		if ( tmpp2 == NULL ) {
			c_name_len[i] = 0;
		} else {
			c_name_len[i] = tmpp2 - channel_1;
		}

		c_data = channel_1;
		i++;
	}
	/* 去掉最后两行无用数据和最后一次i++ */
	count = i - 3;

	array_init(return_value);
	array_count = 0;
	/* 查找 当前通道 */
	char __exten[32] = {0};
	for ( i = 0; i < count; i++ ) {
		app_debug(DINFO"for i:%2d,count:%d,", i, count);
		/* 确保是当前要查找的分机: 604 != 6604, 6040, SIP/604 != SIP/6040... */
		if ( strchr(exten, '/') == 0 ) {
			snprintf(__exten, sizeof(__exten), "/%s-", exten);
		} else {
			snprintf(__exten, sizeof(__exten), "%s-", exten);
		}
		if ( (tmpp = strstr(c_array[i], __exten)) == NULL || c_name_len[i] == 0 ) {
			continue;
		}

		/* 非当前通道, 与当前通道有关的对方通道 */
		if ( tmpp > c_array[i] + c_name_len[i] ) {
			/* 如果是转接通道, 则不取对方通道名, 直接交由下一次当前通道来处理 */
			if ( strstr(c_array[i], "Local") != NULL ) {
				continue;
			}
			/* 如果对方在振铃, 需不需要返回对方通道? "Ring/Ringing" */
			app_debug(DINFO"no Current i:%2d,no Local,", i);
			add_index_stringl(return_value, 1, c_array[i], c_name_len[i], 1);
			array_count++;
			continue;
		}

		/* 当前查找的通道: 非转接 */
		if ( strstr(c_array[i], "Local") == NULL ) {
			app_debug(DINFO"no Local i:%2d,c_array:%s,", i, c_array[i]);
			if ( (tmpp = strstr(c_array[i], "Bridged")) != NULL ) {
				tmpp = strchr(tmpp, '(');
				if ( tmpp == NULL ) {
					continue;
				}
				tmpp++;
				tmpp2 = strchr(tmpp, ')');
				if ( tmpp2 == NULL ) {
					php_error_docref(NULL TSRMLS_CC, E_WARNING, "expect data error.1");
					goto do_end;
					return ;
				}
				tmpp_len = tmpp2 - tmpp;

				add_index_stringl(return_value, 0, c_array[i], c_name_len[i], 1);
				array_count++;
				add_index_stringl(return_value, 1, tmpp, tmpp_len, 1);
				array_count++;
				break;
			}
			app_debug(DINFO"no Local i:%2d,no Bridged,", i);

			add_index_stringl(return_value, 0, c_array[i], c_name_len[i], 1);
			array_count++;
			continue;
		}

		app_debug(DINFO"Local i:%2d,c_array:%s,", i, c_array[i]);
		/* 转接, 'Local' */
		add_index_stringl(return_value, 0, c_array[i], c_name_len[i], 1);
		array_count++;
		/* Bridged, 依 ([Local/xxx]@xxx,a) 找 Transferred ([Local/xxx]@xxx,b) 通道名 */
		if ( (tmpp = strstr(c_array[i], "Bridged")) != NULL ) {
			tmpp = strchr(tmpp, '(');
			if ( tmpp == NULL ) {
				continue;
			}
			tmpp++;
			tmpp2 = strchr(tmpp, '@');
			if ( tmpp2 == NULL ) {
				php_error_docref(NULL TSRMLS_CC, E_WARNING, "expect data error.2");
				goto do_end;
				return ;
			}
			tmpp_len = tmpp2 - tmpp;
			* tmpp2 = '\0';
			for ( j = 0; j < count; j++ ) {
				if ( i == j ) {
					continue;
				}
				if ( strstr(c_array[j] + c_name_len[j], tmpp) != NULL && strstr(c_array[j] + c_name_len[j], "Transferred") != NULL ) {
					add_index_stringl(return_value, 1, c_array[j], c_name_len[j], 1);
					array_count++;
					break;
				}
			}
			break;
		/* Transferred, 反之 */
		} else {
			app_debug(DINFO"Local i:%2d,no Bridged,%s", i, c_array[i]);
			tmpp = strstr(c_array[i], "Transferred");
			tmpp = strchr(tmpp, '(');
			if ( tmpp == NULL ) {
				continue;
			}
			tmpp++;
			tmpp2 = strchr(tmpp, '@');
			if ( tmpp2 == NULL ) {
				php_error_docref(NULL TSRMLS_CC, E_WARNING, "expect data error.3");
				goto do_end;
				return ;
			}
			tmpp_len = tmpp2 - tmpp;
			* tmpp2 = '\0';
			for ( j = 0; j < count; j++ ) {
				if ( i == j ) {
					continue;
				}
				if ( strstr(c_array[j] + c_name_len[j], tmpp) != NULL && strstr(c_array[j] + c_name_len[j], "Bridged") != NULL ) {
					add_index_stringl(return_value, 1, c_array[j], c_name_len[j], 1);
					array_count++;
					break;
				}
			}
			break;
		}
	}

do_end :
	if ( array_count == 0 ) {
		RETVAL_FALSE;
	}
	if ( copy_data != NULL ) {
		free(copy_data);
	}
	return ;
} /* }}} */

LAGI_API void lagi_hints(zval * return_value, char * exten, char * data TSRMLS_DC) /* {{{ */
{
	int i = 0;
	char * copy_data = NULL, * line = NULL, * line_end = NULL;
	char * exten_key = NULL, * device_key = NULL, * state = NULL, * key_end = NULL;
	zval * array = NULL;

	app_debug(DINFO"exten:%s,data:%s", exten, data);
	RETVAL_FALSE;
	copy_data = strdup(data);
	if ( copy_data == NULL ) {
		return ;
	}
	if ( exten != NULL ) {
		char __exten[32] = {0};
		/* exten = "SIP/604" */
		if ( (exten_key = strchr(exten, '/')) != NULL ) {
			device_key = exten;
			exten_key++;
		/* exten = "604" */
		} else {
			exten_key = exten;
		}
		/* 确保是当前要查找的分机: 604 != 6604, 6040... */
		snprintf(__exten, sizeof(__exten), " %s@", exten_key);
		if ( (line = strstr(copy_data, __exten)) == NULL ) {
			app_debug(DINFO"no exten");
			RETVAL_FALSE;
			goto do_end;
			return ;
		}
		/* 取分机状态 */
		device_key = strstr(line, ": ");
		if ( device_key == NULL ) {
			RETVAL_FALSE;
			goto do_end;
			return ;
		}
		device_key += 2;
		exten_key = strchr(device_key, '/');
		if ( exten_key == NULL ) {
			RETVAL_FALSE;
			goto do_end;
			return ;
		}
		exten_key++;
		key_end = strchr(exten_key, ' ');
		if ( key_end == NULL ) {
			RETVAL_FALSE;
			goto do_end;
			return ;
		}
		* key_end = '\0';
		key_end++;
		state = strstr(key_end, "State:");
		key_end = strchr(state, ' ');
		if ( key_end == NULL ) {
			RETVAL_FALSE;
			goto do_end;
			return ;
		}
		* key_end = '\0';

		array_init(return_value);

		ALLOC_INIT_ZVAL(array);
		array_init(array);
		add_assoc_string(array, "dial", device_key, 1);
		add_assoc_stringl(array, "type", device_key, exten_key - device_key - 1, 1);
		add_assoc_string(array, "stat", state, 1);
		add_assoc_zval(return_value, exten_key, array);
		app_debug(DINFO"start exten:%s,device_key:%s,state:%s", exten, device_key, state);

		goto do_end;
		return ;
	}

	/* 分析 data 里面的分机数 */
	array_init(return_value);
	line_end = strchr(copy_data, '\n');
	while ( true ) {
		/**
		 * 分析 line 里面的分机状态
		 * data 前两行和最后两行不需要
		 */
		line = line_end;
		if ( line == NULL ) {
			app_debug(DINFO"end '\\n', i:%d", i);
			break;
		}
		line++;
		line_end = strchr(line, '\n');
		if ( line_end != NULL ) {
			* line_end = '\0';
		}

		/* 取分机状态 */
		device_key = strstr(line, ": ");
		if ( device_key == NULL ) {
			continue;
		}
		device_key += 2;
		exten_key = strchr(device_key, '/');
		if ( exten_key == NULL ) {
			continue;
		}
		exten_key++;
		key_end = strchr(exten_key, ' ');
		if ( key_end == NULL ) {
			continue;
		}
		* key_end = '\0';
		key_end++;
		state = strstr(key_end, "State:");
		key_end = strchr(state, ' ');
		if ( key_end == NULL ) {
			continue;
		}
		* key_end = '\0';

		/* ok */
		ALLOC_INIT_ZVAL(array);
		array_init(array);
		add_assoc_string(array, "dial", device_key, 1);
		add_assoc_stringl(array, "type", device_key, exten_key - device_key - 1, 1);
		add_assoc_string(array, "stat", state, 1);
		add_assoc_zval(return_value, exten_key, array);
		app_debug(DINFO"start i:%2d,device_key:%s,state:%s", i, device_key, state);

		i++;
	}

do_end :
	if ( copy_data != NULL ) {
		free(copy_data);
	}
	return ;
} /* }}} */

PHP_FUNCTION(lagi_dbop) /* {{{ */
{
	char * db = NULL, * ret = NULL;
	int db_len = 0, len = 0;
	zval * z_link = NULL;
	lagi_res * link = NULL;

	if ( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s|r", & db, & db_len, & z_link) == FAILURE ) {
		RETURN_FALSE;
	}
	if ( db == NULL ) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "lost variable: dbop key");
		RETURN_FALSE;
	}

	/* fetch resource */
	if ( z_link == NULL ) {
		link = LAGI_G(default_link);
	} else {
		ZEND_FETCH_RESOURCE(link, lagi_res *, & z_link, -1, LAGI_RES_NAME, le_lagi_res);
	}
	if ( link == NULL ) {
		/* TODO: ... */
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "resource not connect.1");
		RETURN_FALSE;
	} else if ( link->fd <= 0 ) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "resource (%d) not connect.2", link->fd);
		RETURN_FALSE;
	}
	ret = lagi_send(link->fd, "Action: Command\r\nCommand: database %s\r\n\r\n", db);

	if ( ret == NULL ) {
		RETURN_FALSE;
	}

	lagi_return(return_value, ret TSRMLS_CC);
	return ;
} /* }}} */

PHP_FUNCTION(lagi_get_db) /* {{{ */
{
	char * db = NULL, * ret = NULL;
	int db_len = 0, len = 0;
	zval * z_link = NULL;
	lagi_res * link = NULL;

	if ( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s|r", & db, & db_len, & z_link) == FAILURE ) {
		RETURN_FALSE;
	}
	if ( db == NULL ) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "lost variable: get db key");
		RETURN_FALSE;
	}

	/* fetch resource */
	if ( z_link == NULL ) {
		link = LAGI_G(default_link);
	} else {
		ZEND_FETCH_RESOURCE(link, lagi_res *, & z_link, -1, LAGI_RES_NAME, le_lagi_res);
	}
	if ( link == NULL ) {
		/* TODO: ... */
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "resource not connect.1");
		RETURN_FALSE;
	} else if ( link->fd <= 0 ) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "resource (%d) not connect.2", link->fd);
		RETURN_FALSE;
	}
	ret = lagi_send(link->fd, "Action: Command\r\nCommand: database show %s\r\n\r\n", db);

	if ( ret == NULL ) {
		RETURN_FALSE;
	}

	lagi_return(return_value, ret TSRMLS_CC);
	return ;
} /* }}} */

PHP_FUNCTION(lagi_put_db) /* {{{ */
{
	char * db = NULL, * ret = NULL;
	int db_len = 0, len = 0;
	zval * z_link = NULL;
	lagi_res * link = NULL;

	if ( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s|r", & db, & db_len, & z_link) == FAILURE ) {
		RETURN_FALSE;
	}
	if ( db == NULL ) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "lost variable: put db key");
		RETURN_FALSE;
	}

	/* fetch resource */
	if ( z_link == NULL ) {
		link = LAGI_G(default_link);
	} else {
		ZEND_FETCH_RESOURCE(link, lagi_res *, & z_link, -1, LAGI_RES_NAME, le_lagi_res);
	}
	if ( link == NULL ) {
		/* TODO: ... */
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "resource not connect.1");
		RETURN_FALSE;
	} else if ( link->fd <= 0 ) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "resource (%d) not connect.2", link->fd);
		RETURN_FALSE;
	}
	ret = lagi_send(link->fd, "Action: Command\r\nCommand: database put %s\r\n\r\n", db);

	if ( ret == NULL ) {
		RETURN_FALSE;
	}

	lagi_return(return_value, ret TSRMLS_CC);
	return ;
} /* }}} */

PHP_FUNCTION(lagi_channel_analyse) /* {{{ */
{
	char * exten = NULL, * data = NULL;
	int exten_len = 0, data_len = 0;

	if ( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "ss", & exten, & exten_len, & data, & data_len) == FAILURE ) {
		RETURN_FALSE;
	}
	if ( data == NULL || exten == NULL ) {
		RETURN_FALSE;
	}

	lagi_channel_analyse(return_value, exten, data TSRMLS_CC);
	return ;
} /* }}} */

PHP_FUNCTION(lagi_hints) /* {{{ */
{
	char * exten = NULL, * data = NULL;
	int exten_len = 0, data_len = 0;
	zval * z_link = NULL;
	lagi_res * link = NULL;

	if ( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "|ssr", & exten, & exten_len, & data, & data_len, & z_link) == FAILURE ) {
		RETURN_FALSE;
	}

	if ( data == NULL ) {
		if ( z_link == NULL ) {
			link = LAGI_G(default_link);
		} else {
			ZEND_FETCH_RESOURCE(link, lagi_res *, & z_link, -1, LAGI_RES_NAME, le_lagi_res);
		}
		if ( link == NULL ) {
			php_error_docref(NULL TSRMLS_CC, E_WARNING, "resource not connect.1");
			RETURN_FALSE;
		}

		data = lagi_command(link->fd, "core show hints");
	}
	if ( data == NULL ) {
		RETURN_FALSE;
	}
	lagi_hints(return_value, exten, data TSRMLS_CC);
	return ;
} /* }}} */

PHP_FUNCTION(lagi_parked) /* {{{ */
{
	char * channel = NULL, * data = NULL, * chan = NULL, * id_s = NULL, * id_e = NULL;
	int channel_len = 0, data_len = 0;
	int ret_name = false, free_flag = false;
	zval * z_link = NULL;
	lagi_res * link = NULL;

	if ( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s|sbr", & channel, & channel_len, & data, & data_len, & ret_name, & z_link) == FAILURE ) {
		RETURN_FALSE;
	}

	app_debug(DINFO"chanel:%s:%u,l:%d,data:%s:%u,l:%d,ret:%d,", channel, channel, channel_len, data, data, data_len, ret_name);
	if ( data_len <= 1 ) {
		if ( z_link == NULL ) {
			link = LAGI_G(default_link);
		} else {
			ZEND_FETCH_RESOURCE(link, lagi_res *, & z_link, -1, LAGI_RES_NAME, le_lagi_res);
		}
		if ( link == NULL ) {
			php_error_docref(NULL TSRMLS_CC, E_WARNING, "resource not connect.1");
			RETURN_FALSE;
		}

		data = lagi_command(link->fd, "show parkedcalls");
		free_flag = true;
	}
	if ( data == NULL || channel_len <= 1 ) {
		RETURN_FALSE;
	}

	app_debug(DINFO"2 chanel:%s,data:%s,ret:%d,", channel, data, ret_name);
	/* */
	if ( (chan = strstr(data, channel)) == NULL ) {
		RETURN_FALSE;
	}
	if ( ret_name ) {
		RETURN_STRING(channel, 1);
	}
	/* rstrchr(chan, data, '\n'); ^_^ */
	while ( chan > data ) {
		if ( *chan == '\n' ) {
			id_s = chan;
			break;
		}
		chan--;
	}
	id_s++;
	id_e = strchr(id_s, ' ');
	if ( id_e == NULL ) {
		RETURN_FALSE;
	}
	RETVAL_STRINGL(id_s, id_e - id_s, 1);
	if ( free_flag && data != NULL ) {
		efree(data);
	}

	return ;
} /* }}} */

PHP_FUNCTION(lagi_outcall) /* {{{ */
{
	char * data = NULL, * exten = NULL, * tel = NULL;
	char * ret = NULL;
	int data_len = 0, exten_len = 0, tel_len, len = 0;
	zval * z_link = NULL;
	lagi_res * link = NULL;

	if ( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "sss", & data, & data_len, & exten, & exten_len, & tel, & tel_len) == FAILURE ) {
		RETURN_FALSE;		
	}

	/* fetch resource */
	if ( z_link == NULL ) {
		link = LAGI_G(default_link);
	} else {
		ZEND_FETCH_RESOURCE(link, lagi_res *, & z_link, -1, LAGI_RES_NAME, le_lagi_res);
		/*ZEND_FETCH_RESOURCE2(link, lagi_res *, & z_link, -1, LAGI_RES_NAME, le_lagi_res, le_lagi_pres);*/
	}
	if ( link == NULL ) {
		/* TODO: ... */
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "resource not connect.1");
		RETURN_FALSE;
	} else if ( link->fd <= 0 ) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "resource (%d) not connect.2", link->fd);
		RETURN_FALSE;
	}
	
	ret = lagi_outcall(link->fd, data, exten, tel);
	if ( ret == NULL ) {
		RETURN_FALSE;
	}

	lagi_return(return_value, ret TSRMLS_CC);
	return ;
} /* }}} */

