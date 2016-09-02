/** 
 * bcrypt.c
 * beecrypt interface/package function.
 * 
 * Copyright (C) 2009 LangR.Org
 * @author Hua Huang <loghua@gmail.com> 2009/11/06 00:44
 * 
 * This is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 * 
 * $Id$
 */

#include	"bcrypt.h"

inline int fromhex(char * data, const char * hexdata)
{
	int length = strlen(hexdata);
	int count = 0, index = 0;
	char b = 0;
	char ch;

	if (length & 1)
		count = 1;

	while (index++ < length)
	{
		ch = *(hexdata++);

		b <<= 4;
		if (ch >= '0' && ch <= '9')
			b += (ch - '0');
		else if (ch >= 'A' && ch <= 'F')
			b += (ch - 'A') + 10;
		else if (ch >= 'a' && ch <= 'f')
			b += (ch - 'a') + 10;

		count++;
		if (count == 2)
		{
			*(data++) = b;
			b = 0;
			count = 0;
		}
	}
	return (length+1) >> 1;
}

void hex_dump(const unsigned char * data, size_t size)
{
	size_t i;

	for (i = 0; i < size; i++)
	{
		printf("%02x", data[i]);
		if ((i & 0xf) == 0xf) {
			printf("\n");
		} else {
			printf(":");
		}
	}
	if ((i & 0xf))
		printf("\n");
}

void __hex_dump(const unsigned char * data, size_t size)
{
	size_t i = size - 1;

	for (i; i >= 0; i--)
	{
		printf("%02x", data[i]);
		if ((i & 0xf) == 0xf) {
			printf("\n");
		} else {
			printf(":");
		}
	}
	if ((i & 0xf))
		printf("\n");
}

inline char * XORN4(unsigned char * buf, unsigned char * d, unsigned char * s, int n)
{
	while ( n ) {
		n--;
		buf[n] = d[n] ^ s[n];
	}

	return buf;
}

inline char * XORN(unsigned char * d, unsigned char * s, int n)
{
	return XORN4(d, d, s, n);
}

inline int mpntobin(char * buf, mpnumber * mpdata, size_t intercept_len)
{
	/* 4-byte alignment */
	unsigned int i, len, tsize = sizeof(mpw), srclen = mpdata->size * tsize;
	char * src = (char *) mpdata->data;
	char * tmp = buf;
	
	for ( i = 0; i < srclen; i += tsize ) {
		switch ( tsize ) {
		case 8 :
			* (buf++) = src[i + 7];
			* (buf++) = src[i + 6];
			* (buf++) = src[i + 5];
			* (buf++) = src[i + 4];
			* (buf++) = src[i + 3];
			* (buf++) = src[i + 2];
			* (buf++) = src[i + 1];
			* (buf++) = src[i];
			break;
		case 4 :
		default :
			* (buf++) = src[i + 3];
			* (buf++) = src[i + 2];
			* (buf++) = src[i + 1];
			* (buf++) = src[i];
			break;
		}
	}

	/* interception */
	if ( intercept_len > 0 && intercept_len < srclen ) {
		len = srclen - intercept_len;
		for ( i = 0; i < len; i++ ) {
			tmp[i] = tmp[intercept_len + i];
			tmp[intercept_len + i] = 0;
		}

		return intercept_len;
	}

	return srclen;
}

inline int mpntohex(char * buf, mpnumber * mpdata)
{
	return _hex_snprintf(buf, mpdata->size * sizeof(mpw), (char *) mpdata->data, sizeof(mpw));
}

inline int hex_snprintf(char * buf, size_t srclen, unsigned char * src)
{
	return _hex_snprintf(buf, srclen, src, 1);
}

inline int hex_snprintf4(char * buf, size_t srclen, unsigned char * src)
{
	return _hex_snprintf(buf, srclen, src, 4);
}

inline int hex_snprintf8(char * buf, size_t srclen, unsigned char * src)
{
	return _hex_snprintf(buf, srclen, src, 8);
}

int _hex_snprintf(char * buf, size_t srclen, unsigned char * src, int tsize)
{
	unsigned int i, len = (srclen << 1) + 1;	/* add 1 endl */
	
	for ( i = 0; i < srclen; i += tsize ) {
		switch ( tsize ) {
		case 8 :
			snprintf(buf, len, "%02x%02x%02x%02x%02x%02x%02x%02x", src[i + 7], src[i + 6], src[i + 5], src[i + 4], src[i + 3], src[i + 2], src[i + 1], src[i]);
			break;
		case 4 :
			snprintf(buf, len, "%02x%02x%02x%02x", src[i + 3], src[i + 2], src[i + 1], src[i]);
			break;
		case 2 :
			snprintf(buf, len, "%02x%02x", src[i + 1], src[i]);
			break;
		case 1 :
		default :
			snprintf(buf, len, "%02x", src[i]);
			break;
		}
		buf += tsize << 1;
		len -= tsize << 1;
	}

	return srclen;
}
