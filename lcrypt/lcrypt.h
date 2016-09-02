/*
  +----------------------------------------------------------------------+
  | PHP Version 5                                                        |
  +----------------------------------------------------------------------+
  | Copyright (c) 1997-2006 The PHP Group                                |
  +----------------------------------------------------------------------+
  | This source file is subject to version 3.01 of the PHP license,      |
  | that is bundled with this package in the file LICENSE, and is        |
  | available through the world-wide-web at the following url:           |
  | http://www.php.net/license/3_01.txt                                  |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to          |
  | license@php.net so we can mail you a copy immediately.               |
  +----------------------------------------------------------------------+
  | Author:                                                              |
  +----------------------------------------------------------------------+
*/

/* $Id: lcrypt.h 6 2011-11-18 10:48:33Z loghua@gmail.com $ */

#ifndef _LCRYPT_H
#define _LCRYPT_H

#include <stdio.h>
#include <stdarg.h>
#include <string.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <unistd.h>

static short pm9screw_mycryptkey[] = {11152, 368, 192, 1281, 62};

#define PM9SCREW        "\tPHPCRYPT\t"
#define PM9SCREW_LEN     10

char * zcodecom(int mode, char * inbuf, int inbuf_len, int * resultbuf_len);
char * zencode(char * inbuf, int inbuf_len, int * resultbuf_len);
char * zdecode(char * inbuf, int inbuf_len, int * resultbuf_len);

extern char * (* pcode1)(int mode, char * inbuf, int inbuf_len, int * resultbuf_len);
extern char * (* pcode2)(int mode, char * inbuf, int inbuf_len, int * resultbuf_len);

char * lpcode(int mode, char * inbuf, int inbuf_len, int * resultbuf_len, void * (* callback));
char * pcode_null(int mode, char * inbuf, int inbuf_len, int * resultbuf_len);
char * pcode_des(int mode, char * inbuf, int inbuf_len, int * resultbuf_len);

pcode1 = pcode_null;
pcode2 = pcode_des;

#endif	/* _LCRYPT_H */

