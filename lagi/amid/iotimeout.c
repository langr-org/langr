/** 
 * @file iotimeout.c
 * @brief 
 * 
 * Copyright (C) 2011 LangR.Org
 * 
 * This is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 * 
 * @package test
 * @author Langr <hua@langr.org> 2011/11/18 18:41
 * 
 * $Id: iotimeout.c 13 2011-12-02 10:53:22Z loghua@gmail.com $
 */

#include "iotimeout.h"

inline void fd_set_nonblocking(int sockfd) /* {{{ */
{
	fcntl(sockfd, F_SETFL, O_NONBLOCK);
	/*fcntl(sockfd, F_SETFL, fcntl(sockfd, F_GETFL, 0) | O_NONBLOCK);*/
} /* }}} */

inline void fd_set_blocking(int sockfd) /* {{{ */
{
	fcntl(sockfd, F_SETFL, 0);
} /* }}} */

int fd_timeo(int sockfd, int seconds, int flags) /* {{{ */
{
	int ret = 0;
	fd_set rset;
	fd_set wset;
	struct timeval tv;

	FD_ZERO(& rset);
	FD_ZERO(& wset);
	tv.tv_sec = seconds;
	tv.tv_usec = 0;
	
	if ( (flags & FD_TIMEO_RW) == FD_TIMEO_RW ) {
		FD_SET(sockfd, & rset);
		FD_SET(sockfd, & wset);
	} else if ( (flags & FD_TIMEO_R) == FD_TIMEO_R ) {
		FD_SET(sockfd, & rset);
	} else if ( (flags & FD_TIMEO_W) == FD_TIMEO_W ) {
		FD_SET(sockfd, & wset);
	}
	
	ret = select(sockfd + 1, & rset, & wset, NULL, & tv);
	return ret;
} /* }}} */

int sockfd_timeo(int sockfd, int seconds, int flags) /* {{{ */
{
	struct timeval tv;

	tv.tv_sec = seconds;
	tv.tv_usec = 0;
	if ( seconds == 0 ) {
		return 0;
	}

	return setsockopt(sockfd, SOL_SOCKET, flags, & tv, sizeof(tv));
} /* }}} */

int connect_t(int sockfd, const struct sockaddr * addr, int addr_len, int seconds) /* {{{ */
{
	int ret = 0;
	int ret_len = sizeof(ret);
	struct timeval tv;

	if ( seconds == 0 ) {
		ret = connect(sockfd, addr, addr_len);
		if ( ret < 0 ) {
			app_debug(DERROR"connect_t():connect() error:%d", ret);
			close(sockfd);
			return -1;
		}

		return ret;
	}

	/* ÉèÖÃ socket ·Ç×èÈû */
	fd_set_nonblocking(sockfd);
	if ( connect(sockfd, addr, addr_len) < 0 ) {
		ret = fd_timeo(sockfd, seconds, FD_TIMEO_W);
		if ( ret == 0 ) {
		/* timeout */
			app_debug(DERROR"connect_t():fd_timeo() timeout:%d", ret);
			errno = ETIMEDOUT;
			close(sockfd);
			return -1;
		} else if ( ret < 0 ) {
		/* error */
			app_debug(DERROR"connect_t():fd_timeo() error:%d", ret);
			close(sockfd);
			return -1;
		} 
		/* ok? */
		getsockopt(sockfd, SOL_SOCKET, SO_ERROR, & ret, & ret_len);
		if ( ret != 0 ) {
			app_debug(DERROR"connect_t():getsockopt() error:%d", ret);
			close(sockfd);
			return -1;
		}
		/* ok! */
	}
	app_debug(DINFO"connect_t():ok:%d", ret);
	/* ÉèÖÃ socket ×èÈû */
	fd_set_blocking(sockfd);

	return 0;
} /* }}} */

int connect_t2(int sockfd, const struct sockaddr * addr, int addr_len, int seconds) /* {{{ */
{
	int ret = 0;

	ret = sockfd_timeo(sockfd, seconds, SO_SNDTIMEO);
	if ( ret == 0 ) {
		ret = connect(sockfd, addr, addr_len);
		if ( ret < 0 ) {
			app_debug(DERROR"connect() error:%d", ret);
			errno = ETIMEDOUT;
			close(sockfd);
			return -1;
		}
	}

	return ret;
} /* }}} */
