/** 
 * tools/extract.c
 * 文件提取程序, 主要是为了构建核心 Image.
 * 
 * Copyright (C) 2006 LangR.Org
 * @author Hua Huang <loghua@gmail.com> Dec 2006
 * 
 * This is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 * 
 * $Id: extract.c 1 2008-01-23 06:01:15Z hua $
 */

/***
 * 文件提取程序.
 * 将输入文件的指定部分(从 startByte 开始, 共 len Bytes)写到输出文件,
 * 没有指定输出文件则输出到标准输出
 * startByte = 0, 从文件头开始
 * len = $, 到文件尾
 * int8_t 为当 len 超过文件尾部时, 用 int8_t 填补超过部分.
 */

#include	"err.h"
#include	"extract.h"

void
usage(void)
{
	fprintf(stderr, "%s\n%s\n%s\n%s\n%s\n%s\n",
		"usage: extract infile startByte len [-f int8_t] [-o outfile]",
		"	if len = '$' mean to file end.",
		"	-h --help	Print this message and exit",
		"	-o <file>	Place the output into <file>",
		"	-f <int8_t>	Fill up character",
		"	-v --version	Print version and exit"
		);
	exit(1);
}

int
main(int argc, char *argv[])
{
	int	n, start_byte = 0, len = -1, infd, outfd = STDOUT_FILENO;
	char	fill_char = 0;
	
	if (argc < 2 || argc > 8)
		usage();
	if (strcmp(argv[1], "-v") == 0 ||
		strcmp(argv[1], "-h") == 0 ||
		strcmp(argv[1], "--version") == 0 ||
		strcmp(argv[1], "--help") == 0) {
		printf(VERSION_INFO);
		usage();
		}
	if (argc > 4) {
		if (strcmp(argv[4], "-f") == 0)
			fill_char = (char) atoi(argv[5]);
		else if (strcmp(argv[4], "-o") == 0) {
			if ((outfd = open(argv[5], O_RDWR|O_CREAT|O_APPEND, FILE_MODE)) < 0)
				err_sys("open error: %s", argv[5]);
		}
		else
			usage();
		if (argc > 6 && strcmp(argv[6], "-o") == 0) {
			if ((outfd = open(argv[7], O_RDWR|O_CREAT|O_APPEND, FILE_MODE)) < 0)
				err_sys("open error: %s", argv[7]);
		}
	}
	if (argc > 2)
		start_byte = atoi(argv[2]);
	if (argc > 3 && strcmp(argv[3], "$") != 0)
		len = atoi(argv[3]);
	
	if ((infd = open(argv[1], O_RDONLY, 0)) < 0)
		err_sys("open error: %s", argv[1]);
	
	n = extract(outfd, start_byte, len, infd, fill_char);
	
	close(infd);
	close(outfd);
	return n;
}

int
extract(int outfd, int start_byte, int len, int infd, char fill_char)
{
	int	n, rlen, buflen, cbuf = 0;
	char	buf[BUFSIZE];
	
	rlen = len;
	buflen = sizeof(buf);
	if ((n = lseek(infd, start_byte, SEEK_SET)) < 0)
		err_sys("lseek error: %d", start_byte);
	while (1) {
		if (rlen > 0)
			cbuf = (rlen > buflen) ? buflen : rlen;
		else if (rlen < 0)
			cbuf = buflen;

		if ((n = read(infd, buf, cbuf)) < 0)
			err_sys("read error");
		if (write(outfd, buf, n) < n)
			err_sys("write error");

		rlen -= n;
		if (n != 0) {		
			if (rlen == 0)	/* 没到文件尾, 但任务已完成 */
				return 0;
		} 
		else {
			if (rlen <= 0)	/* 到文件尾, 任务已完成 */
				return 1;
			else {		/* 到文件尾, 但任务未完成 */
				memset(buf, fill_char, buflen);

				while (rlen) {
					cbuf = (rlen > buflen) ? buflen : rlen;
					if (write(outfd, buf, cbuf) < cbuf)
						err_sys("write error: %c", fill_char);
					rlen -= cbuf;
				}
				return 2;
			}
		}
	}
}

