/** 
 * @file zencode.h
 * @brief 
 * 
 * Copyright (C) 2018 LangR.Org
 * 
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package lcrypt7
 * @author Langr <hua@langr.org> 2018/11/01 22:37
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 
 * $Id$
 */

#ifndef _ZENCODE_H
#define _ZENCODE_H

#include <stdio.h>
#include <stdarg.h>
#include <string.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <unistd.h>

static short lcrypt_key[] = {1102, 2018, 1701, 3128, 5893};

#define HY_CRYPT        "HY\t"
#define HY_CRYPT_LEN     3

char * zcodecom(int mode, char * inbuf, int inbuf_len, int * resultbuf_len);
char * zencode(char * inbuf, int inbuf_len, int * resultbuf_len);
char * zdecode(char * inbuf, int inbuf_len, int * resultbuf_len);

#endif  /* _ZENCODE_H */

