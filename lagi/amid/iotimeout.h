/** 
 * @file iotimeout.h
 * @brief 
 * 
 * Copyright (C) 2011 LangR.Org
 * 
 * This is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 * 
 * @package amid
 * @author Langr <hua@langr.org> 2011/11/21 14:37
 * 
 * $Id: iotimeout.h 8 2011-11-21 11:37:13Z loghua@gmail.com $
 */

#ifndef	_IOTIMEOUT_H
#define	_IOTIMEOUT_H

#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <time.h>
#include <errno.h>
#include <unistd.h>
#include <sys/socket.h>
#include <sys/types.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <fcntl.h>

#include "../debug.h"

#define FD_TIMEO_R		SO_RCVTIMEO
#define FD_TIMEO_W		SO_SNDTIMEO
#define FD_TIMEO_RW		3

#ifdef	__cplusplus
extern "C" {
#endif

/**
 * @fn
 * @brief 设置 sockfd 非阻塞,
 */
inline void fd_set_nonblocking(int sockfd);

/**
 * @fn
 * @brief 设置 sockfd 阻塞,
 */
inline void fd_set_blocking(int sockfd);

/**
 * @fn
 * @brief select 设置 socket 读写超时.
 * 此函数可适用于各种 I/O 函数前调用, 但要检测返回状态;
 * 在调用时函数会阻塞在此, 直到超时或指定 sockfd 可读写;
 * 然后根据此函数返回状态是否为 0 或 正整数 来调用真正的读写函数.
 * @param sockfd
 * @param seconds =0 阻塞
 * @param flags FD_TIMEO_R / FD_TIMEO_W
 * @return 0 timeout; >0, sockfd 可读写; <0, 出错, 置 errno.
 */
int fd_timeo(int sockfd, int seconds, int flags);

/**
 * @fn
 * @brief 使用 setsockopt() SO_SNDTIMEO, SO_RCVTIMEO 设置超时,
 * setsockopt() 一次设置对 sockfd 永久有效.
 * 设置后的 sockfd I/O 函数操作(read, readv, recv, recvfrom, recvmsg,
 * write, send...)返回 0 sockfd close; <0 出错, 且 errno = EWOULDBLOCK.
 * @param sockfd
 * @param seconds =0 阻塞
 * @param flags SO_RCVTIMEO / SO_SNDTIMEO
 * @return 0 success; -1 error.
 */
int sockfd_timeo(int sockfd, int seconds, int flags);

/**
 * @fn
 * @brief 使用 select 设置 socket connect 超时.
 * ?求确认! 因为 connect 不能使用 setsockopt() SO_SNDTIMEO, SO_RCVTIMEO 设置超时.
 * (setsockopt(SO_SNDTIMEO) errno == EINPROGRESS)
 * (alarm() errno == EINTR)
 * (fcntl(fd, F_SETFL, O_NONBLOCK),ioctl(fd, FIONBIO, *) errno == EALREADY)
 * @param seconds 超时时间, 0 不超时(阻塞)
 * @return 0 success; -1 error, 出错存于 errno.
 */
int connect_t(int sockfd, const struct sockaddr * addr, int addr_len, int seconds);
/* setsockopt() */
int connect_t2(int sockfd, const struct sockaddr * addr, int addr_len, int seconds);

#ifdef	__cplusplus
}
#endif

#endif	/* end _IOTIMEOUT_H */
