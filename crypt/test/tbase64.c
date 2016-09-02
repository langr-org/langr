/** 
 * tbase64.c
 * Description:
 * 
 * Copyright (C) 2009 LangR.Org
 * @author Hua Huang <loghua@gmail.com> 2009/12/09 10:27
 * 
 * This is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 * 
 * $Id$
 */

#include	"beecrypt/base64.h"
#include	"bcrypt.h"

int main(int argc, char * argv[])
{
	int failures = 0, keylen = 0, i = 0;
	size_t * len, l = 0;
	char * encode, * decode, * data ;
	char buf[8192<<2] = {0};
	char buf_msg[8192<<2] = {0};
	memchunk * mem, m;
	len = & l;
	//* data = decode;
	mem = & m;

	printf("message(%d):\n%s\n", strlen(argv[1]) - 2, argv[1] + 2);
	if ( argv[1][0] == '-' && argv[1][1] == 'm') {
		encode = b64encode(argv[1] + 2, strlen(argv[1]) - 2);
		printf("\nb64encode(%d):\n%s\n", strlen(encode), encode);
		hex_dump(encode, strlen(encode));
		* len = strlen(encode);
		printf("\nlen(%d,%x)\n", * len, data);
		i = b64decode(encode, (void **) & data, len);
		data[*len] = '\0';
		printf("\nlen(%d,%x)\n", * len, data);
		printf("\nb64decode(%d):\n%s\n", * len, data);
		
		free(encode);
		free(data);

		mem->size = strlen(argv[1]) - 2;
		mem->data = argv[1] + 2;
		printf("\nlen(%d,%x)\n", mem->size, mem->data);
		encode = b64enc(mem);
		printf("\nb64enc(%d):\n%s\n", strlen(encode), encode);
		hex_dump(encode, strlen(encode));

		mem = b64dec(encode);
		mem->data[mem->size] = '\0';
		printf("\nb64dec(%d):\n%s\n", mem->size, mem->data);
		memchunkFree(mem);
	} else if ( argv[1][0] == '-' && argv[1][1] == 'd' ) {
		* len = strlen(argv[1]) - 2;
		i = b64decode(argv[1] + 2, (void **) & data, len);
		data[*len] = '\0';
		printf("\nb64decode(%d):\n%s\n", * len, data);
		free(data);

		mem = b64dec(argv[1] + 2);
		mem->data[mem->size] = '\0';
		printf("\nb64dec(%d):\n%s\n", mem->size, mem->data);
		memchunkFree(mem);
	}
	//_hex_snprintf(buf, strlen(argv[1]) - 2, argv[1] + 2, sizeof(char));

	return failures;
}

