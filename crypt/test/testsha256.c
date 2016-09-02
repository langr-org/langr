/*
 * testsha256.c
 *
 * Unit test program for SHA-256; it implements the test vectors from the draft FIPS document.
 *
 * Copyright (c) 2002, 2003 Bob Deblier <bob.deblier@telenet.be>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 */

#include <stdio.h>

/*#include "beecrypt/sha256.h"*/
#include "sha256.h"

struct vector
{
	int		input_size;
	byte*	input;
	byte*	expect;
};


struct vector table[2] = {
	{  3, (byte*) "abc",
	      (byte*) "\xba\x78\x16\xbf\x8f\x01\xcf\xea\x41\x41\x40\xde\x5d\xae\x22\x23\xb0\x03\x61\xa3\x96\x17\x7a\x9c\xb4\x10\xff\x61\xf2\x00\x15\xad" },
	{ 56, (byte*) "abcdbcdecdefdefgefghfghighijhijkijkljklmklmnlmnomnopnopq",
	      (byte*) "\x24\x8d\x6a\x61\xd2\x06\x38\xb8\xe5\xc0\x26\x93\x0c\x3e\x60\x39\xa3\x3c\xe4\x59\x64\xff\x21\x67\xf6\xec\xed\xd4\x19\xdb\x06\xc1" }
};

int main(int argc, char * argv[])
{
	/*int i, failures = 0;
	sha256Param param;
	byte digest[32];

	for (i = 0; i < 2; i++)
	{
		if (sha256Reset(&param))
			return -1;
		if (sha256Update(&param, table[i].input, table[i].input_size))
			return -1;
		if (sha256Digest(&param, digest))
			return -1;

		if (memcmp(digest, table[i].expect, 32))
		{
			printf("failed test vector %d\n", i+1);
			failures++;
		}
	}*/

	int i;
	unsigned char result[32] = {0};
	unsigned char buf[32 << 1] = {0};
	unsigned char * tmp;

	if ( argc > 1 ) {
		for ( i = 1; i < argc; i++ ) {
			if ( argv[i][0] == '-' && argv[i][1] == 's') {
				tmp = sha256(argv[i] + 2);
				printf("sha256(%s)=", argv[i] + 2);
				sha256_printf(tmp);
				printf("\n");

				sha256_snprintf(buf, tmp);
				printf("sha256_snprintf(buf, sha256(%s))=%s\n", argv[i] + 2, buf);

				free(tmp);

				sha256_string(result, argv[i] + 2, strlen(argv[i]) - 2);
				printf("sha256_string(%s)=", argv[i] + 2);
				sha256_printf(result);
				printf("\n");
			} /*else if ( strcmp(argv[i], "-t") ) {
				;
			} */else {
				sha256_file(result, argv[i]);
				printf("sha256_file(%s)=", argv[i]);
				sha256_printf(result);
				printf("\n");
			}
		}
	} else {
		printf("usage: \tsha256  [filename] [-sString]\n");
	}

	return 0;
}
