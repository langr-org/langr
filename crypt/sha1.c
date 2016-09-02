/** 
 * sha1.c
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

#include	"sha1.h"

int sha1_file(unsigned char digest[], char * filename)
{
	FILE * file;
	sha1Param context;
	int len;
	unsigned char buffer[8192];

	if ( (file = fopen(filename, "rb") ) == NULL ) {
		err_msg(ERR_WARN, "%s can't be opened\n", filename);
		return -1;
	} else {
		sha1Reset(&context);
		while ( len = fread(buffer, 1, 8192, file) ) {
			sha1Update(&context, buffer, len);
		}
		sha1Digest(&context, digest);

		fclose(file);
	}
	return 0;
}

inline char * sha1(char * str)
{
	char * digest;
	if ( (digest = malloc(20)) == NULL ) {
		err_msg(ERR_INFO, "malloc NULL in sha1()");
		return NULL;
	}
	sha1_string(digest, str, strlen(str));

	return digest;
}

unsigned char * sha1_string(unsigned char digest[20], char * str, int len)
{
	sha1Param context;

	sha1Reset(&context);
	sha1Update(&context, str, len);
	sha1Digest(&context, digest);

	return digest;
}

inline int sha1_snprintf(char buf[40], unsigned char digest[20])
{
	unsigned int i, len = 40;

	for ( i = 0; i < 20; i += 4 ) {
		snprintf(buf, len, "%02x%02x%02x%02x", digest[i], digest[i + 1], digest[i + 2], digest[i + 3]);
		buf += 8;
		len -= 4;
	}

	return 0;
}

void sha1_printf(unsigned char digest[20])
{
	unsigned int i;

	for ( i = 0; i < 20; i++ ) {
		printf("%02x", digest[i]);
	}
}

