/*
 * Copyright (c) 2003 Bob Deblier
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

/*!\file testaes.c
 * \brief Unit test program for the Blowfish cipher.
 * \author Bob Deblier <bob.deblier@telenet.be>
 * \ingroup UNIT_m
 */

#include <stdio.h>

#include "beecrypt/aes.h"

int fromhex(byte*, const char*);
void hexdump(const byte*, size_t);

struct vector
{
	char*			key;
	char*			input;
	char*			expect;
	cipherOperation	op;
};

#define NVECTORS 4
struct vector table[NVECTORS] = {
	{ "000102030405060708090a0b0c0d0e0f",
	  "112233 hi,你好445566778899aa",
	  "69c4e0d86a7b0430d8cdb78070b4c55a",
	  ENCRYPT },
	{ "000102030405060708090a0b0c0d0e0f",
	  "69c4e0d86a7b0430d8cdb78070b4c55a",
	  "00112233445566778899aabbccddeeff",
	  DECRYPT },
	{ "000102030405060708090a0b0c0d0e0f",
	  "112233 hi,你好4455aabbccddee",
	  "69c4e0d86a7b0430d8cdb78070b4c55a",
	  ENCRYPT },
	{ "000102030405060708090a0b0c0d0e0f",
	  "69c4e0d86a7b0430d8cdb78070b4c55a",
	  "00112233445566778899aabbccddeeff",
	  DECRYPT }/*,
	{ "000102030405060708090a0b0c0d0e0f1011121314151617",
	  "00112233445566778899aabbccddeeff",
	  "dda97ca4864cdfe06eaf70a0ec0d7191",
	  ENCRYPT },
	{ "000102030405060708090a0b0c0d0e0f1011121314151617",
	  "dda97ca4864cdfe06eaf70a0ec0d7191",
	  "00112233445566778899aabbccddeeff",
	  DECRYPT },
	{ "000102030405060708090a0b0c0d0e0f101112131415161718191a1b1c1d1e1f",
	  "00112233445566778899aabbccddeeff",
	  "8ea2b7ca516745bfeafc49904b496089",
	  ENCRYPT },
	{ "000102030405060708090a0b0c0d0e0f101112131415161718191a1b1c1d1e1f",
	  "8ea2b7ca516745bfeafc49904b496089",
	  "00112233445566778899aabbccddeeff",
	  DECRYPT }*/
};

int main(int argc, char * argv[])
{
	int i, failures = 0;
	aesParam param;
	byte key[32];
	byte src[16] = {0};
	byte dst[16] = {0};
	byte buf[17] = {0};
	byte chk[16];
	size_t keybits;

	for (i = 0; i < NVECTORS; i++)
	{
		keybits = fromhex(key, table[i].key) << 3;

		if (aesSetup(&param, key, keybits, table[i].op))
			return -1;

		//fromhex(src, table[i].input);
		//fromhex(chk, table[i].expect);
		strncpy(chk, table[i].expect, 16);

		switch (table[i].op)
		{
		case ENCRYPT:
			if ( argv[1][0] == '-' && argv[1][1] == 'm') {
				strncpy(src, argv[1] + 2, 16);
			} else {
				strncpy(src, table[i].input, 16);
			}
			if (aesEncrypt(&param, (uint32_t*) dst, (const uint32_t*) src))
				return -1;
			break;
		case DECRYPT:
			strncpy(src, dst, 16);
			if (aesDecrypt(&param, (uint32_t*) dst, (const uint32_t*) src))
				return -1;
			break;
		}

		if (memcmp(dst, chk, 16))
		{
			printf("failed vector %d\n", i+1);
			failures++;
		}
		printf("key:\n");
		hexdump(key, 32);
		printf("\nsrc:%s\n", src);
		hexdump(src, 16);
		printf("\ndst:%s\n", dst);
		strncpy(buf, dst, 16);
		printf("\ndst(buf):%s\n", buf);
		hexdump(dst, 16);
		printf("\nchk:\n");
		hexdump(chk, 16);
	}

	return failures;
}

int fromhex(byte* data, const char* hexdata)
{
	int length = strlen(hexdata);
	int count = 0, index = 0;
	byte b = 0;
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

void hexdump(const byte* data, size_t size)
{
	size_t i;

	for (i = 0; i < size; i++)
	{
		printf("%02x", data[i]);
		if ((i & 0xf) == 0xf)
			printf("\n");
	}
	if ((i & 0xf))
		printf("\n");
}

