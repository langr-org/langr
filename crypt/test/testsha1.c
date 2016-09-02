/*
 * Copyright (c) 2002, 2003 Bob Deblier
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

/*!\file testsha1.c
 * \brief Unit test program for the SHA-1 algorithm ; it tests all but one of
 *        the vectors specified by FIPS PUB 180-1.
 * \author Bob Deblier <bob.deblier@telenet.be>
 * \ingroup UNIT_m
 */

/*
#include "beecrypt/sha1.h"
#include "beecrypt/memchunk.h"
*/
#include	"sha1.h"

struct vector
{
	int input_size;
	byte* input;
	byte* expect;
};

struct vector table[2] = {
	{  3, (byte*) "abc",
	      (byte*) "\xA9\x99\x3E\x36\x47\x06\x81\x6A\xBA\x3E\x25\x71\x78\x50\xC2\x6C\x9C\xD0\xD8\x9D" },
	{ 56, (byte*) "abcdbcdecdefdefgefghfghighijhijkijkljklmklmnlmnomnopnopq",
		  (byte*) "\x84\x98\x3E\x44\x1C\x3B\xD2\x6E\xBA\xAE\x4A\xA1\xF9\x51\x29\xE5\xE5\x46\x70\xF1" }
};

int main(int argc, char * argv[])
{
	/*int i, failures = 0;
	byte digest[20];
	sha1Param param;

	for (i = 0; i < 2; i++)
	{
		if (sha1Reset(&param))
			return -1;
		if (sha1Update(&param, table[i].input, table[i].input_size))
			return -1;
		if (sha1Digest(&param, digest))
			return -1;

		if (memcmp(digest, table[i].expect, 20))
		{
			printf("failed test vector %d\n", i+1);
			failures++;
		}
	}*/

	int i;
	unsigned char result[20] = {0};
	unsigned char buf[20 << 1] = {0};
	unsigned char * tmp;

	if ( argc > 1 ) {
		for ( i = 1; i < argc; i++ ) {
			if ( argv[i][0] == '-' && argv[i][1] == 's') {
				tmp = sha1(argv[i] + 2);
				printf("sha1(%s)=", argv[i] + 2);
				sha1_printf(tmp);
				printf("\n");

				sha1_snprintf(buf, tmp);
				printf("sha1_snprintf(buf, sha1(%s))=%s\n", argv[i] + 2, buf);

				free(tmp);

				sha1_string(result, argv[i] + 2, strlen(argv[i]) - 2);
				printf("sha1_string(%s)=", argv[i] + 2);
				sha1_printf(result);
				printf("\n");
			} /*else if ( strcmp(argv[i], "-t") ) {
				;
			} */else {
				sha1_file(result, argv[i]);
				printf("sha1_file(%s)=", argv[i]);
				sha1_printf(result);
				printf("\n");
			}
		}
	} else {
		printf("usage: \tsha1  [filename] [-sString]\n");
	}

	return 0;
}
