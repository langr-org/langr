/** 
 * @file ami.c
 * @brief lagi ami(asterisk manager interface)
 * 
 * Copyright (C) 2011 LangR.Org
 * 
 * This is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 * 
 * @package lagi
 * @author Langr <hua@langr.org> 2011/08/11 23:19
 * 
 * $Id: ami.c 30 2011-12-30 02:57:03Z loghua@gmail.com $
 */

#include "ami.h"

LAGI_API int lagi_connect(const char * host, const short port) /* {{{ */
{
	int	fd = 0, r = 0;
	struct sockaddr_in	servaddr;
	char buffer[BUFFER_SIZE >> 1] = {0};

	fd = socket(AF_INET, SOCK_STREAM, 0);
	app_assert(fd < 0, ESOCKET, "open socket error:%d.", fd);

	memset(& servaddr, 0, sizeof(servaddr));
	servaddr.sin_family = AF_INET;
	servaddr.sin_port = htons(port);
	r = inet_pton(AF_INET, host, & servaddr.sin_addr);
	app_assert(r <= 0, EMSG, "inet_pton return error:%d:%s", r, host);

	r = connect(fd, (struct sockaddr *) & servaddr, sizeof(servaddr));
	app_assert(r < 0, ECONN, "connect return error:%d:%s:%d", r, host, port);
	r = read(fd, buffer, sizeof(buffer));
	app_debug(DINFO"connect read:%d:%s", r, buffer);

	return fd;
} /* }}} */

LAGI_API int lagi_tcp_connect(const char * host, const char * port, const int timeo) /* {{{ */
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
		n = sockfd_timeo(fd, timeo, SO_SNDTIMEO);
		app_assert(n != 0, ENOOP, "ENOOP: sockfd_timeo return error:%d,fd:%d,timeout:%d", n, fd, timeo);
		n = connect(fd, res->ai_addr, res->ai_addrlen);
		app_assert(n < 0, ENOOP, "ENOOP: connect return error:%d,fd:%d,host:%s,port:%s", n, fd, host, port);
		if ( n == 0 ) {
			break;
		}

		close(fd);
	} while ( (res = res->ai_next) != NULL );
	freeaddrinfo(_res);

	/* connect error or timeout */
	if ( n < 0 ) {
		return n;
	}
	sockfd_timeo(fd, timeo, SO_RCVTIMEO);
	/* read Asterisk Call Manager/1.0 */
	n = read(fd, buffer, sizeof(buffer));
	app_debug(DINFO"connect read: fd:%d,len:%d :%s", fd, n, buffer);

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

LAGI_API char * lagi_send(int fd, char * action, ...) /* {{{ */
{
	int n = 0, r = 0;
	char buffer[BUFFER_SIZE >> 1] = {0};
	char * buf = NULL;
	va_list ap;
	int _errno = 0;

	_errno = errno;
	errno = ECONN;
	app_assertp(fd <= 0, ENULL, "lagi_send socket fd error:%d", fd);
	va_start(ap, action);
	r = vsnprintf(buffer, BUFFER_SIZE >> 1, action, ap);
	va_end(ap);

	errno = ESOCKET;
	n = write(fd, buffer, strlen(buffer));
	app_assertp(n <= 0, ENULL, "lagi_send write socket error:%d,errno:%d", n, errno);
	app_debug(DINFO"lagi_send write:%d:buf:\n%s", n, buffer);

	/* recv */
	n = lagi_recv(fd, & buf);
	if ( n <= 0 ) {
		if ( buf != NULL ) {
			efree(buf);
			buf = NULL;
		}
		errno = ESOCKET;
		app_assertp(n == 0, ENULL, "lagi_send read socket colse by peer:%d", n);
		app_assertp(true, ENULL, "lagi_send read socket error");
	}
	
	errno = _errno;
	return buf;
} /* }}} */

LAGI_API int _lagi_send(int fd, char * action, ...) /* {{{ */
{
	int n = 0, r = 0;
	char buffer[BUFFER_SIZE >> 1] = {0};
	va_list ap;

	app_assert(fd <= 0, ESOCKET, "_lagi_send socket fd error:%d", fd);
	va_start(ap, action);
	r = vsnprintf(buffer, BUFFER_SIZE >> 1, action, ap);
	va_end(ap);

	n = write(fd, buffer, strlen(buffer));
	app_assert(n <= 0, ESOCKET, "_lagi_send write socket error:%d", n);
	app_debug(DINFO"write:%d:buf:\n%s", n, buffer);

	return n;
} /* }}} */

LAGI_API int lagi_recv(int fd, char ** buf) /* {{{ */
{
	int n = 0, c = 0, buf_tlen = 0, buf_len = 0;
	char * buf_ptr = NULL, * tmp_p = NULL;

	/* 在高负荷服务器上需要多次读取，直到读到协议终止符 */
	buf_tlen = BUFFER_SIZE;			/* 缓冲区总大小 */
	buf_len = buf_tlen;			/* 未读缓冲区大小 */
	* buf = emalloc(buf_tlen);		/* 在服务器中 emalloc 出错时不返回, 直接退出当前脚本: exit(1) */
	app_assert(*buf == NULL, EMALLOC, "emalloc memory error.");
	memset(* buf, 0, buf_tlen);
	buf_ptr = * buf;			/* 缓冲区当前指针 */
	app_assert(fd <= 0, ESOCKET, "lagi_recv socket fd error:%d", fd);
	/* TODO: Event 先允许 Event, 之后再提供 Event 事件处理 */
	do {
		n = read(fd, buf_ptr, buf_len);
		app_assert(n < 0, ECONN, "read socket error:%d,", n);
		app_debug(DINFO"read(fd:%d):c%d,n%d,buf_len:%d:", fd, c, n, buf_len);
		buf_len -= n;
		c += n;
		if ( buf_ptr[n - 4] == '\r' && buf_ptr[n - 3] == '\n' 
				&& buf_ptr[n - 2] == '\r' && buf_ptr[n - 1] == '\n' ) {
			break;
		}
		/* close by server */
		if ( buf_len > 0 && n == 0 ) {
			app_debug(DWARN"read socket close(fd:%d):c%d,n%d,buf_len:%d:", fd, c, n, buf_len);
			close(fd);
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
			app_assert(tmp_p == NULL, EMALLOC, "emalloc memory error.");
			memset(tmp_p, 0, buf_tlen);
			memcpy(tmp_p, * buf, buf_len);
			efree(* buf);
			* buf = tmp_p;
			buf_ptr = tmp_p + buf_len;
			app_debug(DINFO"read socket buffer small(fd:%d):c:%d,n:%d,buf_t:%d,buf_len:%d:", fd, c, n, buf_tlen, buf_len);
		}
	} while ( true );

	_applog(DEBUG_FILE, * buf);
	return c;
} /* }}} */

LAGI_API char * lagi_action(int fd, char * action) /* {{{ */
{
	char * rebuf = NULL;

	rebuf = lagi_send(fd, action);
	app_assertp(rebuf == NULL, ENULL, "lagi_action(fd:%d,action:%s),ret:%d", fd, action, rebuf);

	return rebuf;
} /* }}} */

LAGI_API char * lagi_login(int fd, char * user, char * pwd) /* {{{ */
{
	char * rebuf = NULL;

	rebuf = lagi_send(fd, "Action: login\r\nUsername: %s\r\nSecret: %s\r\nEvents: off\r\n\r\n", user, pwd);
	app_assertp(rebuf == NULL, ENULL, "lagi_login(fd:%d,user:%s,pwd:%s),ret:%d", fd, user, pwd, rebuf);

	return rebuf;
} /* }}} */

LAGI_API inline int lagi_logoff(int fd) /* {{{ */
{
	int r = 0;
	char * rebuf = NULL;

	rebuf = lagi_send(fd, "Action: logoff\r\n\r\n");
	app_assert(rebuf == NULL, r, "lagi_logoff(fd:%d)ret:%d", fd, rebuf);
	app_debug(DINFO"lagi_logoff(fd:%d)", fd);

	efree(rebuf);

	return r;
} /* }}} */

LAGI_API char * lagi_command(int fd, char * command) /* {{{ */
{
	char * rebuf = NULL;

	rebuf = lagi_send(fd, "Action: command\r\nCommand: %s\r\n\r\n", command);
	/* NOTE: 取指针地址出错过, * rebuf => rebuf */
	app_assertp(rebuf == NULL, ENULL, "lagi_command(fd:%d,command:%s),ret:%d", fd, command, rebuf);

	return rebuf;
} /* }}} */

/* test */
LAGI_API char * lagi_outcall(int fd, char * channel, char * exten, char * calltel) /* {{{ */
{
	char * rebuf = NULL;

	rebuf = lagi_send(fd, "Action: originate\r\nChannel: %s\r\nWaitTime: 30\r\nCallerId: clickcall <%s>\r\nExten: %s\r\nContext: from-internal\r\nPriority: 1\r\n\r\n", channel, exten, calltel);
	app_assertp(rebuf == NULL, ENULL, "(fd:%d,channel:%s,exten:%s,calltel:%s),ret:%d", fd, channel, exten, calltel, rebuf);

	return rebuf;
} /* }}} */

