/** 
 * trsa_lib.c
 * Description:
 * 
 * Copyright (C) 2009 LangR.Org
 * @author Hua Huang <loghua@gmail.com> 2009/12/08 15:57
 * 
 * This is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 * 
 * $Id$
 */

#include <stdio.h>
#include "bcrypt.h"

int _rsapub_verify(char * buf, char * input, int len, char * pubkey, char * n);

int _rsapub_verify(char * buf, char * input, int len, char * pubkey, char * n)
{
	int c = 0, i = 0;
	int keylen = 0;			/* key length */
	int mlen = 0;			/* 每次加密块长度 = keylen << 2 */
	int clen = len;			/* 当前未加密 message 长度 */

	rsakp keypair;
	mpnumber m, cipher;
	
	rsakpInit(&keypair);
	mpbsethex(&keypair.n, n);
	mpnsethex(&keypair.e, pubkey);	/* public key */
	keylen = strlen(n) << 2;
	mlen = keylen >> 3;

	mpnzero(&m);
	mpnzero(&cipher);

	while ( clen ) {
		if ( clen >= mlen ) {
			i = mlen;
			clen -= mlen;
		} else if ( clen < mlen ) {
			/* 不填充 */
			i = clen;
			clen = 0;
		}
		mpnsetbin(&m, input, i);
	
		if ( rsapub(&keypair.n, &keypair.e, &m, &cipher) ) {
			return -2;
		}
		mpntobin(buf, &cipher, 0);

		buf += mlen;
		input += mlen;
		c += mlen;
	}

	return c;
}

/*static const char* rsa_n  = "bbf82f090682ce9c2338ac2b9da871f7368d07eed41043a440d6b6f07454f51fb8dfbaaf035c02ab61ea48ceeb6fcd4876ed520d60e1ec4619719d8a5b8b807fafb8e0a3dfc737723ee6b4b7d93a2584ee6a649d060953748834b2454598394ee0aab12d7b61a51f527a9a41f6c1687fe2537298ca2a8f5946f8e5fd091dbdcb";
static const char* rsa_e  = "11";
static const char* rsa_p  = "eecfae81b1b9b3c908810b10a1b5600199eb9f44aef4fda493b81a9e3d84f632124ef0236e5d1e3b7e28fae7aa040a2d5b252176459d1f397541ba2a58fb6599";
static const char* rsa_q  = "c97fb1f027f453f6341233eaaad1d9353f6c42d08866b1d05a0f2035028b9d869840b41666b42e92ea0da3b43204b5cfce3352524d0416a5a441e700af461503";
static const char* rsa_d1 = "54494ca63eba0337e4e24023fcd69a5aeb07dddc0183a4d0ac9b54b051f2b13ed9490975eab77414ff59c1f7692e9a2e202b38fc910a474174adc93c1f67c981";
static const char* rsa_d2 = "471e0290ff0af0750351b7f878864ca961adbd3a8a7e991c5c0556a94c3146a7f9803f8f6f8ae342e931fd8ae47a220d1b99a495849807fe39f9245a9836da3d";
static const char* rsa_c  = "b06c4fdabb6301198d265bdbae9423b380f271f73453885093077fcd39e2119fc98632154f5883b167a967bf402b4e9e2e0f9656e698ea3666edfb25798039f7";

static const char* rsa_m  = "d436e99569fd32a7c8a05bbc90d32c49";
*/

//static const char * rsa_n = "4d6bc2ca48210eb653c035f19cdefd045d8e620764a58afff91ecf15c1adc78ea5ff62f0b04219e10bd233d9b60dfeef28524eccb66d8dd4579cc87f277f2bd7e2c658bb6173a6644f2c503286ba31b11b1a3998d50151bea56b492790446e66a4b0202be987858d3d817d2619046f42b9a2aac0356d46ba4c95893c2b4efff3";
static const char * rsa_e = "00010001";
//static const int rsa_e = 65537;
//static const char * rsa_d = "e6e5026c0627f59fdbed78148871ed4bb133e6ec8a5c501f8c6193d44dcfe2cfce5b2161eb32fba13b38c9c2d112bde3bf0a452ed6617b75cdc77bb669487283ad9de48eb843f51fb48fc3da6d7523bfead056c35acafe1ecd69150d1d725c24dca15e52708f388b7c0019716e3f2ec25eca5526521a5c68c180a492019ad97d";

static const char * rsa_n = "819dce28ecb9a7b3d0eef0250326aa65840134ed2595ea392f583bee4eab2fb82f863327497136d3b198e01de73af5b914e956154784a4b0938db76a57ad3479";
static const char * rsa_d = "2e84d575e8a825212b2e662fd276b117280e4ad185f8cd0c630a96cfcb9a32d0b2e35de73bbc24daeedf5ff676d8be5194872e547dbf59c0944493335f789801";
//static const char * rsa_n = "8693a541203cc19384b0af2805accc4a93bffef7b5c59e2e79e853537855a12e39c7dcb1a2ec7ceef2817cb99172e1e0b31fec757d16b8d077b73c14421ef969d59d1b12313ebde1e24106a850c5b12d8c17902311fa842e0901172d6d98f38807a01ded352db6b9bac7cef39f5e92e6aae6c8251754739908364925a1cd363b";
//static const char * rsa_d = "574f7d1c96a4b45657f5257e8ac80436b24b134974d6047d54bc452b575bfed975c5544aaf504cf1ddc5e2bca63fe98a15ccb565b845409c87bf0aac7e05887c84a19afff3b544f99b7ebc19ec4543ad156c100f20d76bb7b9ef43703adc148931269bbe4e512f60ab785b1ee22e6e269cf189c94cdf7dbbc52cb5c2608c39a1";

int main(int argc, char * argv[])
{
	int failures = 0, keylen = 0, l = 0;
	char buf[8192<<2] = {0};
	char buf_msg[8192<<2] = {0};

	rsakp keypair;
	mpnumber m, cipher, decipher;
//	randomGeneratorContext rngc;

//	if (randomGeneratorContextInit(&rngc, randomGeneratorDefault()) == 0)
//	{
		rsakpInit(&keypair);

		mpbsethex(&keypair.n, rsa_n);
		mpnsethex(&keypair.e, rsa_e);
		mpnsethex(&keypair.d, rsa_d);
		keylen = strlen(rsa_n) >> 1;

		mpnzero(&m);
		mpnzero(&cipher);
		mpnzero(&decipher);

		if ( argv[1][0] == '-' && argv[1][1] == 'm') {
			mpnsetbin(&m, (argv[1] + 2), strlen(argv[1]) - 2);
		} else if ( argv[1][0] == '-' && argv[1][1] == 'h') {
			_hex_snprintf(buf, strlen(argv[1]) - 2, argv[1] + 2, sizeof(char));
			mpnsethex(&m, buf);
		}
		//mpnsethex(&m, (argv[1] + 2));

		printf("message(%d):%s\n", strlen(argv[1]) - 2, argv[1] + 2);
		hex_dump((char *) m.data, m.size << 2);
		printf("\npublic:%d\n", (int) * keypair.e.data);
		hex_dump((char *) keypair.e.data, keypair.e.size << 2);

		/* public key */
		l = rsapub_cipher(buf, argv[1] + 2, strlen(argv[1]) - 2, (char *) rsa_e, (char *) rsa_n);

		printf("\nrsapub_cipher(%d):\n", l);
		hex_dump(buf, l);

		l = rsapri_decipher(buf_msg, buf, l, (char *) rsa_d, (char *) rsa_n);

		printf("\nrsapri_decipher(%d):%s\n", l, buf_msg);
		hex_dump(buf_msg, l);

		/* 签名 */
		l = rsapri_sign(buf, argv[1] + 2, strlen(argv[1]) - 2, (char *) rsa_d, (char *) rsa_n);
		printf("\nrsapri_sign(%d):\n", l);
		hex_dump(buf, l);

		/* 验证 */
		l = rsapub_verify(argv[1] + 2, strlen(argv[1]) - 2, buf, l, (char *) rsa_e, (char *) rsa_n);
		printf("\nrsapub_verify(%d):\n", l);

		//l = _rsapub_verify(buf_msg, buf, l, (char *) rsa_e, (char *) rsa_n);
		//printf("\n_rsapub_verify(%d):\n%s\n", l, buf_msg);
		//hex_dump(buf_msg, l);

		mpnfree(&decipher);
		mpnfree(&cipher);
		mpnfree(&m);

		rsakpFree(&keypair);
//		randomGeneratorContextFree(&rngc);
//	}
	return failures;
}
