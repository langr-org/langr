/** 
 * @file ami.h
 * @brief 
 * 
 * Copyright (C) 2011 LangR.Org
 * 
 * This is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 * 
 * @package lagi
 * @author Langr <hua@langr.org> 2011/11/09 15:31
 * 
 * $Id: ami.h 14 2011-12-07 05:48:31Z loghua@gmail.com $
 */

#ifndef _AMI_H
#define _AMI_H

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <stdarg.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <netdb.h>
#include <arpa/inet.h>
#include <errno.h>
/*#ifdef HAVE_STDARG_H
 #include <stdarg.h>
#endif*/
#include "php_lagi.h"
#include "amid/iotimeout.h"

#ifndef	LAGI_API
 #define	LAGI_API	
#endif

/**
 * @fn
 * @brief connect agi server: host:5038
 *
 * 使用资源进行连接管理，并使用php.ini配置默认连接，登陆
 * @param host agi server ip
 * @param port agi server port
 * @return success: socket fd; failed: -errno
 * @TODO 最大连接数控制, 连接超时.
 */
LAGI_API int lagi_connect(const char * host, const short port);
LAGI_API int lagi_tcp_connect(const char * host, const char * port, const int timeo);

LAGI_API inline int lagi_logoff(int fd);
LAGI_API int lagi_close(int fd);

/**
 * @fn
 * @brief send action to asterisk server
 *
 * 调用 lagi_recv() 返回一个已经分配了内存的指针，出错返回 NULL.
 * 在不使用时，应需要 efree() 掉不再使用的非空指针。
 * @param fd socket connect
 * @param action 
 * @see lagi_recv()
 * @link http://www.voip-info.org/wiki/view/Asterisk+manager+API
 * @return success: buf point, failed: NULL
 * @TODO 读写超时.
 */
LAGI_API char * lagi_send(int fd, char * action, ...);

/**
 * @warning 此函数已经废除，不应再使用
 * @fn
 * @brief send action to asterisk server
 * @param fd socket connect
 * @param action 
 * @see lagi_send()
 * @link http://www.voip-info.org/wiki/view/Asterisk+manager+API
 * @return success: write bytes, failed: -errno
 */
LAGI_API int _lagi_send(int fd, char * action, ...);

/**
 * @fn
 * @brief recv response for asterisk server
 * 
 * 使用 emalloc() 分配内存, 在执行请求结束后本应可以自动回收，
 * 但还是应在用完后使用 efree() 函数.
 * @param fd socket connect
 * @param buf 没有分配内存的指针, lagi_recv 会自动分配内存
 * @link http://www.voip-info.org/wiki/view/Asterisk+manager+API
 * @return success: read length, failed: -errno
 * @TODO recv Event
 */
LAGI_API int lagi_recv(int fd, char ** buf);

/**
 * @fn
 * @brief agi action
 * @param fd
 * @param action
 * @link http://www.voip-info.org/wiki/view/Asterisk+Manager+API+Action
 * @return success: agi server response, failed: NULL
 */
LAGI_API char * lagi_action(int fd, char * action);

/**
 * @fn
 * @brief 
 * @param fd socket connect
 * @param user 
 * @param pwd 
 * @link http://www.voip-info.org/wiki/view/Asterisk+manager+API
 * @return Response
 */
LAGI_API char * lagi_login(int fd, char * user, char * pwd);

/**
 * @fn
 * @brief 
 * @param fd socket connect
 * @param command 
 * @link http://www.voip-info.org/wiki/view/Asterisk+Manager+API+Action+Command
 * @return success: Command Response, failed: NULL
 */
LAGI_API char * lagi_command(int fd, char * command);

/**
 * @fn
 * @brief 
 * @param fd socket connect
 * @param channel 
 * @param calltel called tel
 * @link http://www.voip-info.org/wiki/view/Asterisk+Manager+API+Action+Originate
 * @return success: Response, failed: NULL
 */
LAGI_API char * lagi_originate(int fd, char * channel, char * exten, char * calltel);
/* test */
LAGI_API char * lagi_outcall(int fd, char * channel, char * exten, char * calltel);

#endif	/* _AMI_H */

