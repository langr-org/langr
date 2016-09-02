/** 
 * rsa.c
 * beecrytp interface/package function
 * 
 * Copyright (C) 2009 LangR.Org
 * @author Hua Huang <loghua@gmail.com> 2009/11/22 16:26
 * 
 * This is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 * 
 * $Id$
 */

#include	"rsa.h"

inline int rsakp_make(int keylen)
{
	return _rsakp_make(keylen, 65537);
}

inline int _rsakp_make(int keylen, int pubkey)
{
	return 0;
}

int rsapub_cipher(char * buf, char * input, int len, char * pubkey, char * n)
{
	int c = 0;
	int keylen = 0;			/* key length */
	int mlen = 0;			/* 每次加密块长度 = keylen << 2 */
	int clen = len;			/* 当前未加密 message 长度 */
	char * message, * current_msg;

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
			clen -= mlen;
			current_msg = input;
		} else if ( clen < mlen ) {
			/* 最后一段不足 mlen 块长度明文, 进行 ANSI X923 填充 */
			if ( (message = malloc(mlen)) == NULL ) {
				return -1;
			}
			memset(message, 0, mlen);
			memcpy(message, input, clen);
			message[mlen - 1] = mlen - clen;
			clen = 0;
			current_msg = message;
		}
		mpnsetbin(&m, current_msg, mlen);
	
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

int rsapri_decipher(char * buf, char * input, int len, char * prikey, char * n)
{
	int i, c = 0;
	int unpadding = 0;		/* take out ANSI X923 Padding */
	int keylen = 0;			/* key length */
	int mlen = 0;			/* 每次解密块长度 = keylen << 2 */
	int clen = len;			/* 当前未解密 cipher 长度 */
	char * tmp_buf;

	rsakp keypair;
	mpnumber cipher, decipher;
	
	rsakpInit(&keypair);
	mpbsethex(&keypair.n, n);
	mpnsethex(&keypair.d, prikey);	/* priate key */
	keylen = strlen(n) << 2;
	mlen = keylen >> 3;

	mpnzero(&cipher);
	mpnzero(&decipher);

	tmp_buf = buf;
	/* 其实 clen 必须 mlen 字节对齐 */
	while ( clen ) {
		if ( clen >= mlen ) {
			clen -= mlen;
		} else if ( clen < mlen ) {
			clen = 0;
		}
		mpnsetbin(&cipher, input, mlen);
	
		if ( rsapri(&keypair.n, &keypair.d, &cipher, &decipher) ) {
			return -2;
		}
		mpntobin(buf, &decipher, 0);

		buf += mlen;
		input += mlen;
		c += mlen;
	}

	if ( tmp_buf[c - 1] > 0 && tmp_buf[c - 1] < mlen ) {
		unpadding = tmp_buf[c - 1];
		for ( i = 1; i < unpadding; i++ ) {
			if ( tmp_buf[c - 1 - i] != 0 ) {
				break;
			}
		}
		if ( i == unpadding ) {
			c -= unpadding;
		}
	}

	return c;
}

int rsapri_sign(char * buf, char * input, int len, char * prikey, char * n)
{
	int c = 0;
	int keylen = 0;			/* key length */
	int mlen = 0;			/* 每次加密块长度 = keylen << 2 */
	int clen = len;			/* 当前未加密 message 长度 */
	char * message, * current_msg;

	rsakp keypair;
	mpnumber m, cipher;
	
	rsakpInit(&keypair);
	mpbsethex(&keypair.n, n);
	mpnsethex(&keypair.d, prikey);	/* priate key */
	keylen = strlen(n) << 2;
	mlen = keylen >> 3;

	mpnzero(&m);
	mpnzero(&cipher);

	while ( clen ) {
		if ( clen >= mlen ) {
			clen -= mlen;
			current_msg = input;
		} else if ( clen < mlen ) {
			/* 最后一段不足 mlen 块长度明文, 进行 ANSI X923 填充 */
			if ( (message = malloc(mlen)) == NULL ) {
				return -1;
			}
			memset(message, 0, mlen);
			memcpy(message, input, clen);
			message[mlen - 1] = mlen - clen;
			clen = 0;
			current_msg = message;
		}
		mpnsetbin(&m, current_msg, mlen);
	
		if ( rsapri(&keypair.n, &keypair.d, &m, &cipher) ) {
			return -2;
		}
		mpntobin(buf, &cipher, 0);

		buf += mlen;
		input += mlen;
		c += mlen;
	}

	return c;
}

int rsapub_verify(char * msg, int msg_len, char * cipher, int cipher_len, char * pubkey, char * n)
{
	int flag = 0;
	int keylen = 0;			/* key length */
	int mlen = 0;			/* 每次加密块长度 = keylen << 2 */
	int clen = msg_len;		/* 当前未验证 message 长度 */
	char * message, * current_msg;

	rsakp keypair;
	mpnumber m, c, mc;
	
	rsakpInit(&keypair);
	mpbsethex(&keypair.n, n);
	mpnsethex(&keypair.e, pubkey);	/* public key */
	keylen = strlen(n) << 2;
	mlen = keylen >> 3;

	mpnzero(&m);
	mpnzero(&c);
	mpnzero(&mc);

	while ( clen ) {
		if ( clen >= mlen ) {
			clen -= mlen;
			current_msg = msg;
		} else if ( clen < mlen ) {
			/* 最后一段不足 mlen 块长度明文, 进行 ANSI X923 填充 */
			if ( (message = malloc(mlen)) == NULL ) {
				return -1;
			}
			memset(message, 0, mlen);
			memcpy(message, msg, clen);
			message[mlen - 1] = mlen - clen;
			clen = 0;
			current_msg = message;
		}
		mpnsetbin(&m, current_msg, mlen);
		mpnsetbin(&c, cipher, mlen);
		
		rsapub(&keypair.n, &keypair.e, &c, &mc);
		if ( mpeq(m.size, m.data, mc.data) == 0 ) {
			flag++;
		}
		/*if ( rsavrfy(&keypair.n, &keypair.e, &m, &c) != 1 ) {
			printf("rsavrfy error:\n");
			flag++;
		}*/

		msg += mlen;
		cipher += mlen;
	}

	return flag;
}

