/** 
 * trsa.c
 * test rsa algorithm
 * 
 * Copyright (C) 2009 LangR.Org
 * @author Hua Huang <loghua@gmail.com> 2009/11/07 17:52
 * 
 * This is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 * 
 * $Id$
 */

#include <stdio.h>
#include "beecrypt/rsa.h"
#include "bcrypt.h"

int main(int argc, char * argv[])
{
	rsakp keypair;
	mpnumber m, cipher, decipher;
	randomGeneratorContext randg;
	int e =0, keylen = 1024, flag = 0;
	char buf[8192] = {0};

	if ( randomGeneratorContextInit(&randg, randomGeneratorDefault()) != 0 )
	{
		return flag;
	}

	if ( argc > 1 ) {
		keylen = atoi(argv[1]);
	}
	rsakpInit(&keypair);
	if ( argc > 2 ) {
		/* e 为奇数 */
		e = ((atoi(argv[2]) >> 1) << 1) + 1;
		mpnsetw(&keypair.e, e);
	}
	rsakpMake(&keypair, &randg, keylen);

	printf("public key (%dbytes):\n", keypair.e.size << 2);
	hex_dump((char *) keypair.e.data, keypair.e.size << 2);
	hex_snprintf4(buf, keypair.e.size << 2, (char *) keypair.e.data);
	printf("\nsnprintf (%dbytes):\n%s (%d)\n", keypair.e.size << 2, buf, (int) * keypair.e.data);

	printf("\nprivate key (%dbytes):\n", keypair.d.size << 2);
	hex_dump((char *) keypair.d.data, keypair.d.size << 2);
	hex_snprintf(buf, keypair.d.size << 2, (char *) keypair.d.data);
	printf("\nsnprintf (%dbytes):\n%s\n", keypair.d.size << 2, buf);
	hex_snprintf4(buf, keypair.d.size << 2, (char *) keypair.d.data);
	printf("\nsnprintf4 (%dbytes):\n%s\n", keypair.d.size << 2, buf);

	printf("\nn (len:%dbits %dbytes sizeof(mpw):%d):\n", keylen, keypair.n.size << 2, sizeof(mpw));
	hex_dump((char *) keypair.n.modl, keypair.n.size << 2);
	_hex_snprintf(buf, keypair.n.size * sizeof(mpw), (char *) keypair.n.modl, sizeof(mpw));
	printf("\n_snprintf (%dbytes):\n%s\n", keypair.n.size << 2, buf);

	rsakpFree(&keypair);
	randomGeneratorContextFree(&randg);

	return 0;
}

