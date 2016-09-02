/** 
 * @file test_io.c
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
 * @author Langr <hua@langr.org> 2011/11/21 09:36
 * 
 * $Id: test_io.c 28 2011-12-29 06:29:16Z loghua@gmail.com $
 */

#include "../iotimeout.h"
#include "../../debug.h"

/*
#undef app_debug
#define app_debug	printf
*/

/**
 * test connect_t(), fd_timeo(), sockfd_timeo()
 * ./test 192.168.1.226 5038
 */
int main(int argc, char * argv[])
{
	struct sockaddr_in servaddr;
	int fd = 0, r = 0, timeo = 0;
	char buffer[BUFFER_SIZE >> 1] = {0};

	fd = socket(AF_INET, SOCK_STREAM, 0);
	memset(& servaddr, 0, sizeof(servaddr));
	servaddr.sin_family = AF_INET;
	servaddr.sin_port = htons(atoi(argv[2]));
	r = inet_pton(AF_INET, argv[1], & servaddr.sin_addr);
	
	app_debug(DINFO"argc:%d,argv:%s,%s,%s,%s", argc, argv[0], argv[1], argv[2], argv[3]);
	printf(DINFO"argc:%d,argv:%s,%s,%s\n", argc, argv[0], argv[1], argv[2], argv[3]);

	if ( argc > 3 ) {
		timeo = atoi(argv[3]);
	}

	/* connect() nonblocking select() timeout */
	app_debug(DINFO"nonblocking test: connect_t start fd:%d,", fd);
	r = connect_t(fd, (struct sockaddr *) & servaddr, sizeof(servaddr), timeo);
	/* 读超时测试, 当测试时间为 2 倍数时设置超时 */
	if ( timeo % 2 == 0 ) {
		app_debug(DINFO"connect_t: read timeo, fd:%d,timeo:%d,", fd, timeo);
		sockfd_timeo(fd, timeo, SO_RCVTIMEO);
	}
	app_debug(DINFO"connect_t: ok, fd:%d,r:%d,timeo:%d,", fd, r, timeo);
	r = read(fd, buffer, sizeof(buffer));
	app_assert(true, ENOOP, "connect_t read:%d:%s", r, buffer);
	/* 读超时测试 */
	memset(buffer, 0, sizeof(buffer));
	r = read(fd, buffer, sizeof(buffer));
	app_assert(true, ENOOP, "connect_t read timeout test:%d:%s", r, buffer);

	printf(DINFO"connect read:%d:%s\n", r, buffer);
	close(fd);

	memset(buffer, 0, sizeof(buffer));
	fd = socket(AF_INET, SOCK_STREAM, 0);
	/* connect() setsockopt() timeout 写超时  */
	app_debug(DINFO"setsockopt test: connect_t2 start fd:%d,timdo:%d,", fd, timeo);
	r = connect_t2(fd, (struct sockaddr *) & servaddr, sizeof(servaddr), timeo);
	app_debug(DINFO"connect_t2: ok, fd:%d,r:%d,", fd, r);
	r = read(fd, buffer, sizeof(buffer));
	app_debug(DINFO"connect_t2 read:%d:%s", r, buffer);
	/* 读超时测试, 当测试时间为 3 倍数时设置超时 */
	if ( timeo % 3 == 0 ) {
		app_debug(DINFO"connect_t2: read timeo, fd:%d,timeo:%d,", fd, timeo);
		sockfd_timeo(fd, timeo, SO_RCVTIMEO);
	}
	memset(buffer, 0, sizeof(buffer));
	r = read(fd, buffer, sizeof(buffer));
	app_debug(DINFO"connect_t2 read timeout test:%d:%s", r, buffer);
	memset(buffer, 0, sizeof(buffer));
	r = read(fd, buffer, sizeof(buffer));
	app_debug(DINFO"connect_t2 read timeout test:%d:%s", r, buffer);

	printf(DINFO"connect read:%d:%s\n", r, buffer);

	return 0;
}
