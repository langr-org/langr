/** 
 * @file agi.c
 * @brief lagi agi(asterisk gateway interface)
 * 
 * Copyright (C) 2011 LangR.Org
 * 
 * This is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 * 
 * @package tests
 * @author Langr <hua@langr.org> 2011/11/08 18:35
 * 
 * $Id: agi.c 4 2011-11-10 00:57:06Z loghua@gmail.com $
 */

#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <stdarg.h>
#include <sys/types.h>
#include "php_lagi.h"

LAGI_API int lagi_agi_command(const char * host, const char * port) /* {{{ */
{
	int	fd = 0, n = 0;
	struct addrinfo	hints, * res = NULL, * _res = NULL;
	char buffer[BUFFER_SIZE >> 1] = {0};

	memset(& hints, 0, sizeof(struct addrinfo));
	hints.ai_family = AF_INET;		/* 仅使用 IPv4 */
	hints.ai_socktype = SOCK_STREAM;

	n = getaddrinfo(host, port, & hints, & res);
	app_assert(n != 0, EMSG, "getaddrinfo return error:%s:%s\n%s", host, port, gai_strerror(n));

	_res = res;
	do {
		fd = socket(res->ai_family, res->ai_socktype, 0);
		if ( fd < 0 ) {
			app_debug(DINFO"open socket error:%d,res->ai_addr:%s", fd, inet_ntop(res->ai_family, & ((struct sockaddr_in *)(res->ai_addr))->sin_addr, buffer, INET_ADDRSTRLEN));
			continue;
		}
		app_debug(DINFO"open socket:%d,res->ai_addr:%s", fd, inet_ntop(res->ai_family, & ((struct sockaddr_in *)(res->ai_addr))->sin_addr, buffer, INET_ADDRSTRLEN));
		n = connect(fd, res->ai_addr, res->ai_addrlen);
		app_assert(n < 0, ENOOP, "ENOOP: connect return error:%d:%s:%d", n, host, port);
		if ( n == 0 ) {
			break;
		}

		close(fd);
	} while ( (res = res->ai_next) != NULL );
	freeaddrinfo(_res);

	app_debug(DINFO"connect read:%d...", fd);
	n = read(fd, buffer, sizeof(buffer));
	app_debug(DINFO"connect read:%d:%s", n, buffer);

	return fd;
} /* }}} */

LAGI_API int lagi_close(int fd) /* {{{ */
{
	if ( fd >= 0 ) {
		lagi_logoff(fd);
		shutdown(fd, SHUT_RDWR);
		/*close(fd);*/
		app_debug(DINFO"lagi_close:fd:%d:ok", fd);
		return 0;
	}

	return -1;
} /* }}} */

LAGI_API int lagi_send(int fd, char * action, ...) /* {{{ */
{
	int n = 0, r = 0;
	char buffer[BUFFER_SIZE >> 1] = {0};
	va_list ap;

	app_assert(fd <= 0, ESOCKET, "lagi_send socket fd error:%d", fd);
	va_start(ap, action);
	/*r = vsprintf(buffer, action, ap);*/
	r = vsnprintf(buffer, BUFFER_SIZE >> 1, action, ap);
	va_end(ap);

	n = write(fd, buffer, strlen(buffer));
	app_assert(n <= 0, ESOCKET, "write socket error:%d", n);
	app_debug(DINFO"write:%d:buf:\n%s", n, buffer);

	return n;
} /* }}} */

LAGI_API int lagi_recv(int fd, char ** buf) /* {{{ */
{
	int n = 0, c = 0, buf_tlen = 0, buf_len = 0;
	char * buf_ptr, * tmp_p = NULL;

	/* 在高负荷服务器上需要多次读取，直到读到协议终止符 */
	buf_tlen = BUFFER_SIZE;			/* 缓冲区总大小 */
	buf_len = buf_tlen;			/* 未读缓冲区大小 */
	* buf = emalloc(buf_tlen);
	memset(* buf, 0, buf_tlen);
	buf_ptr = * buf;			/* 缓冲区当前指针 */
	app_assert(fd <= 0, ESOCKET, "lagi_recv socket fd error:%d", fd);
	do {
		n = read(fd, buf_ptr, buf_len);
		app_assert(n < 0, ECONN, "read socket error:%d", n);
		app_debug(DINFO"read(fd:%d):c%d,n%d,buf_len:%d:", fd, c, n, buf_len);
		buf_len -= n;
		c += n;
		if ( buf_ptr[n - 4] == '\r' && buf_ptr[n - 3] == '\n' 
				&& buf_ptr[n - 2] == '\r' && buf_ptr[n - 1] == '\n' ) {
			break;
		}
		if ( buf_len > 0 && n == 0 ) {
			break;
		}
		buf_ptr += n;
		/* NOTE: ENOOP */
		app_assert(buf_len <= 0, ENOOP, "ENOOP:read socket buffer small(fd:%d):c:%d,n:%d,buf_t:%d,buf_len:%d:", fd, c, n, buf_tlen, buf_len);
		if ( buf_len == 0 ) {
			/* 自动分配内存处理缓冲区过小问题 */
			app_debug(DINFO"read socket buffer small(fd:%d):c:%d,n:%d,buf_t:%d,buf_len:%d:", fd, c, n, buf_tlen, buf_len);
			buf_len = buf_tlen;
			buf_tlen = buf_tlen << 1;	/* 扩大一倍内存 */
			tmp_p = emalloc(buf_tlen);
			memset(tmp_p, 0, buf_tlen);
			memcpy(tmp_p, * buf, buf_len);
			efree(* buf);
			buf_ptr = tmp_p + buf_len;
			* buf = tmp_p;
			app_debug(DINFO"read socket buffer small(fd:%d):c:%d,n:%d,buf_t:%d,buf_len:%d:", fd, c, n, buf_tlen, buf_len);
		}
	} while ( true );

	_applog(DEBUG_FILE, * buf);
	return c;
} /* }}} */

LAGI_API char * lagi_command(int fd, char * command) /* {{{ */
{
	int r = 0;
	char * rebuf = NULL;

	r = lagi_send(fd, "Action: command\r\nCommand: %s\r\n\r\n", command);
	if ( r <= 0 ) {
		app_debug(DWARN"lagi_send(fd:%d)ret:%d", fd, r);
		return NULL;
	}

	r = lagi_recv(fd, & rebuf);
	if ( r <= 0 ) {
		app_debug(DWARN"lagi_recv(fd:%d)ret:%d", fd, r);
		efree(rebuf);
		return NULL;
	}

	return rebuf;
} /* }}} */


LAGI_API void lagi_return(zval * return_value, char * val TSRMLS_DC) /* {{{ */
{
	int i = 0;
	char * v_1 = NULL;

	/* TODO: return array */
	/* get "Response: xxx\r\n" */
	/*v_2 = memchr(val, ':', 10);*/
	v_1 = strchr(val, '\r');

	array_init(return_value);
	add_assoc_stringl(return_value, "Response", val + 10, v_1 - val - 10, 1);
	add_assoc_string(return_value, "Data", v_1 + 2, 1);

	return ;
} /* }}} */

