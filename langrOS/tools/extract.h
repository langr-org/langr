/* $Id: extract.h 8 2009-10-15 03:55:40Z hua $ */

#ifndef	__EXTRACT_H
#define	__EXTRACT_H

#include	<stdio.h>
#include	<string.h>
#include	<stdlib.h>
#include	<sys/types.h>
#include	<sys/stat.h>
#include	<linux/fs.h>
#include	<unistd.h>
#include	<fcntl.h>

#define	VERSION_INFO	"version 0.3 by HuaHuang <loghua@gmail.com>\n"
#define	MINIX_HEADER	32
#define	ELF_HEADER	1024
#define	GCC_HEADER	1024

#define FILE_MODE	(S_IRUSR | S_IWUSR | S_IRGRP | S_IROTH)		/* -rw-r--r-- */
#define DIR_MODE	(FILE_MODE | S_IXUSR | S_IXGRP | S_IXOTH )	/* drwxr-xr-x */
#define	BUFSIZE		8192

int	extract(int outfd, int start_byte, int len, int infd, char fill_char);

#endif /* __EXTRACT_H */

