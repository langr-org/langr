/** 
 * @file bcrypt.h
 * @brief beecrypt interface/package function.
 * 
 * Copyright (C) 2009 LangR.Org
 * @author Hua Huang <loghua@gmail.com> 2009/11/07 18:18
 * 
 * This is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 * 
 * $Id$
 */

#ifndef	_BCRYPT_H
#define	_BCRYPT_H

#include	<string.h>
#include	<stdio.h>
/*#include	"err.h"*/
#include	"md5.h"
#include	"sha1.h"
#include	"sha256.h"
#include	"aes.h"
#include	"rsa.h"
/*#include	"dsa.h"*/
#include	"beecrypt/base64.h"
/* in debug.h */
#define	ERR_WARN	0
#define	ERR_INFO	0
#define	err_msg(level, format, ...)	printf(format, ##__VA_ARGS__)

#ifdef	__cplusplus
extern "C" {
#endif

/**
 * like atoi().
 * @param data hex result
 * @param hexdata ascii hex
 * @return hex length (bytes)
 */
inline int fromhex(char * data, const char * hexdata);

/**
 * dump hex.
 * @param data hex value
 * @param size hex data length
 */
void hex_dump(const unsigned char * data, size_t size);

/**
 * snprintf hex.
 * @param buf dump result
 * @param srclen buf length
 * @param src hex value 
 * @param tsize sizeof(type) 
 * 	(源数据是以何种类型(int 4, short 2, char 1)型放置的)
 * 	tsize 为其他值则默认按 char 处理.
 * @return int srclen ok, -1 tsize error
 */
int _hex_snprintf(char * buf, size_t srclen, unsigned char * src, int tsize);
inline int hex_snprintf(char * buf, size_t srclen, unsigned char * src);
inline int hex_snprintf4(char * buf, size_t srclen, unsigned char * src);
inline int hex_snprintf8(char * buf, size_t srclen, unsigned char * src);

/**
 * xor.
 */
inline char * XORN4(unsigned char * buf, unsigned char * d, unsigned char * s, int n);
inline char * XORN(unsigned char * d, unsigned char * s, int n);

/**
 * mpnumber to bin.
 * @see mpntohex()
 * @param buf dump result
 * @param mpdata 
 * @param intercept_len 截取掉 mpdata length,
 * 	0 不截取, n 截取掉前 n 字节 (0 < n < (mpdata->size * sizeof(mpw))) 
 * @return int dump to buf length
 */
inline int mpntobin(char * buf, mpnumber * mpdata, size_t intercept_len);
inline int mpntohex(char * buf, mpnumber * mpdata);

#ifdef	__cplusplus
}
#endif

#endif /* bcrypt.h */

