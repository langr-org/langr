/** 
 * md5.c
 * beecrytp package function
 * 
 * Copyright (C) 2009 LangR.Org
 * @author Hua Huang <loghua@gmail.com> 2009/11/03 00:59
 * 
 * This is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 * 
 * $Id$
 */

#include	"md5.h"

int md5_file(unsigned char digest[], char * filename)
{
	FILE * file;
	md5Param context;
	int len;
	unsigned char buffer[8192];/*, digest[16];*/

	if ( (file = fopen(filename, "rb") ) == NULL ) {
		err_msg(ERR_WARN, "%s can't be opened\n", filename);
		return -1;
	} else {
		md5Reset(&context);
		while ( len = fread(buffer, 1, 8192, file) ) {
			md5Update(&context, buffer, len);
		}
		md5Digest(&context, digest);

		fclose(file);
	}
	return 0;
}

inline char * md5(char * str)
{
	char * digest;
	if ( (digest = malloc(sizeof(char) << 4)) == NULL ) {
		err_msg(ERR_INFO, "malloc NULL in md5()");
		return NULL;
	}
	md5_string(digest, str, strlen(str));

	return digest;
}

unsigned char * md5_string(unsigned char digest[16], char * str, int len)
{
	md5Param context;

	md5Reset(&context);
	md5Update(&context, str, len);
	md5Digest(&context, digest);

	return digest;
}

inline int md5_snprintf(char buf[32], unsigned char digest[16])
{
	unsigned int i, len = 32;

	for ( i = 0; i < 16; i += 4 ) {
		snprintf(buf, len, "%02x%02x%02x%02x", digest[i], digest[i + 1], digest[i + 2], digest[i + 3]);
		buf += 8;
		len -= 4;
	}

	return 0;
}

void md5_printf(unsigned char digest[16])
{
	unsigned int i;

	for ( i = 0; i < 16; i++ ) {
		printf("%02x", digest[i]);
	}
}

