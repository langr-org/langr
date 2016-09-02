/** 
 * sha256.c
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

#include	"sha256.h"

int sha256_file(unsigned char digest[], char * filename)
{
	FILE * file;
	sha256Param context;
	int len;
	unsigned char buffer[8192];

	if ( (file = fopen(filename, "rb") ) == NULL ) {
		err_msg(ERR_WARN, "%s can't be opened\n", filename);
		return -1;
	} else {
		sha256Reset(&context);
		while ( len = fread(buffer, 1, 8192, file) ) {
			sha256Update(&context, buffer, len);
		}
		sha256Digest(&context, digest);

		fclose(file);
	}
	return 0;
}

inline char * sha256(char * str)
{
	char * digest;
	if ( (digest = malloc(32)) == NULL ) {
		err_msg(ERR_INFO, "malloc NULL in sha256()");
		return NULL;
	}
	sha256_string(digest, str, strlen(str));

	return digest;
}

unsigned char * sha256_string(unsigned char digest[32], char * str, int len)
{
	sha256Param context;

	sha256Reset(&context);
	sha256Update(&context, str, len);
	sha256Digest(&context, digest);

	return digest;
}

inline int sha256_snprintf(char buf[64], unsigned char digest[32])
{
	unsigned int i, len = 64;

	for ( i = 0; i < 32; i += 4 ) {
		snprintf(buf, len, "%02x%02x%02x%02x", digest[i], digest[i + 1], digest[i + 2], digest[i + 3]);
		buf += 8;
		len -= 4;
	}

	return 0;
}

void sha256_printf(unsigned char digest[32])
{
	unsigned int i;

	for ( i = 0; i < 32; i++ ) {
		printf("%02x", digest[i]);
	}
}

