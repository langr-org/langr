/** 
 * aes.c
 * beecrytp interface/package function
 * 
 * Copyright (C) 2009 LangR.Org
 * @author Hua Huang <loghua@gmail.com> 2009/11/05 23:35
 * 
 * This is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 * 
 * $Id$
 */

#include	"aes.h"

const int AES_BUFLEN = 8192;	/* 16-byte alignment */

inline int aes(char * buf, char * input, int input_len, char * key, int key_bits, cipherOperation op)
{
	switch ( op ) {
	case ENCRYPT : 
		return aes_encrypt(buf, input, input_len, key);
		break;
	case DECRYPT :
		return aes_decrypt(buf, input, input_len, key);
		break;
	default :
		break;
	}

	return input_len;
}

int aes_encrypt(char * buf, char * input, int input_len, char * key)
{
	char message[16] = {0}, * msg;
	char * cipher, _cipher[16];	/* CBC mode crypt */
	char key_hash[32];		/* sha256 */
	int c = 0, key_bits = 256;
	int padding = 0;		/* Padding */
	aesParam context;

	if ( input_len < 1 ) {
		return -5;
	}

	sha256_string(key_hash, key, strlen(key));
	if ( aesSetup(&context, key_hash, key_bits, ENCRYPT) ) {
		return -1;
	}

	cipher = key_hash;
	while ( input_len ) {
		msg = input;
		if ( input_len >= 16 ) {
			input_len -= 16;
		} else if ( input_len < 16 ) {
			memset(message, 0, 16);
			memcpy(message, input, input_len);
			/* ANSI X923 Padding: ff ff 00 00 00 00 00 06 */
			padding = 16 - input_len;
			message[15] = (char) padding;
			msg = message;
			input_len = 0;
		}
		/* CBC mode crypt */
		XORN4(_cipher, cipher, msg, 16);
		msg = _cipher;
		
		if ( aesEncrypt(&context, (uint32_t *) buf, (uint32_t *) msg) ) {
			return -2;
		}
		cipher = buf;

		c += 16;
		buf += 16;
		input += 16;
	}

	return c;
}

int aes_decrypt(char * buf, char * input, int input_len, char * key)
{
	int i = 0, c = 0, key_bits = 256;
	int unpadding = 0;		/* take out ANSI X923 Padding */
	char * cipher;			/* CBC mode crypt */
	char key_hash[32];		/* sha256 */
	char * tmp_buf;
	aesParam context;

	/* 其实 input_len 必须 16 字节对齐 */
	if ( input_len < 16 ) {
		return -5;
	}

	sha256_string(key_hash, key, strlen(key));
	if ( aesSetup(&context, key_hash, key_bits, DECRYPT) ) {
		return -1;
	}

	cipher = key_hash;
	tmp_buf = buf;
	while ( input_len ) {
		if ( input_len >= 16 ) {
			input_len -= 16;
		} else if ( input_len < 16 ) {
			input_len = 0;
		}
		
		if ( aesDecrypt(&context, (uint32_t *) buf, (uint32_t *) input) ) {
			return -2;
		}
		/* CBC mode crypt restore */
		XORN(buf, cipher, 16);
		cipher = input;

		c += 16;
		buf += 16;
		input += 16;
	}

	if ( tmp_buf[c - 1] > 0 && tmp_buf[c - 1] < 16 ) {
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

int aes_enfile(char * output_filename, char * input_filename, char * key)
{
	FILE * infile, * outfile;
	unsigned char buffer[AES_BUFLEN], * msg_buf;
	unsigned char outbuf[AES_BUFLEN], * cipher_buf;
	int c = 0, current_len = 0, len = 0, key_bits = 256;
	int padding = 0;		/* ANSI X923 Padding */
	char key_hash[32];		/* sha256 */
	char * cipher;			/* CBC mode crypt */
	aesParam context;

	if ( (infile = fopen(input_filename, "rb") ) == NULL ) {
		err_msg(ERR_WARN, "%s can't be opened\n", input_filename);
		return -51;
	} else if ( (outfile = fopen(output_filename, "wb") ) == NULL ) {
		err_msg(ERR_WARN, "%s can't be opened\n", output_filename);
		return -52;
	}

	sha256_string(key_hash, key, strlen(key));
	if ( aesSetup(&context, key_hash, key_bits, ENCRYPT) ) {
		return -1;
	}

	cipher = key_hash;
	while ( len = fread(buffer, 1, AES_BUFLEN, infile) ) {
		msg_buf = buffer;
		cipher_buf = outbuf;
		/* 此次 encrypt 后的密文长度, 当 len 没有16-byte对齐时, current_len != len */
		current_len = 0;
		while ( len ) {
			if ( len >= 16 ) {
				len -= 16;
			} else if ( len < 16 ) {	/* padding */
				padding = 16 - len;
				msg_buf[15] = (char) padding;
				len = 0;
			}
			/* CBC mode crypt */
			XORN(msg_buf, cipher, 16);

			if ( aesEncrypt(&context, (uint32_t *) cipher_buf, (uint32_t *) msg_buf) ) {
				return -2;
			}
			cipher = cipher_buf;
	
			current_len += 16;
			cipher_buf += 16;
			msg_buf += 16;
		}

		c += current_len;
		if ( fwrite(outbuf, 1, current_len, outfile) != current_len ) {
			err_msg(ERR_WARN, "%s fwrite error:%d\n", output_filename, current_len);
		}
		memset(buffer, 0, AES_BUFLEN);
		/* 在 outbuf 结束时, 指向最后一个16bytes的内容需要保存, 以便 CBC mode 计算下一次密文 */
		/*memset(outbuf, 0, AES_BUFLEN);*/
	}

	fclose(infile);
	fclose(outfile);
	
	return c;
}

int aes_defile(char * output_filename, char * input_filename, char * key)
{
	FILE * infile, * outfile;
	unsigned char buffer[AES_BUFLEN], * cipher_buf;
	unsigned char outbuf[AES_BUFLEN], * msg_buf;
	unsigned char outbuf2[AES_BUFLEN], * msg_buf2;
	int i = 0, c = 0, current_len = 0, pre_len = 0, len = 0, key_bits = 256;
	int unpadding = 0;		/* take out ANSI X923 Padding */
	char cipher_tmp[16], * cipher;	/* CBC mode crypt */
	char key_hash[32];		/* sha256 */
	aesParam context;

	if ( (infile = fopen(input_filename, "rb") ) == NULL ) {
		err_msg(ERR_WARN, "%s can't be opened\n", input_filename);
		return -51;
	} else if ( (outfile = fopen(output_filename, "wb") ) == NULL ) {
		err_msg(ERR_WARN, "%s can't be opened\n", output_filename);
		return -52;
	}

	sha256_string(key_hash, key, strlen(key));
	if ( aesSetup(&context, key_hash, key_bits, DECRYPT) ) {
		return -1;
	}

	cipher = key_hash;
	msg_buf = outbuf;
	pre_len = 0;
	while ( len = fread(buffer, 1, AES_BUFLEN, infile) ) {
		i++;
		cipher_buf = buffer;
		/* 此次 decrypt 后的明文长度 */
		current_len = 0;
		while ( len ) {
			if ( len >= 16 ) {
				len -= 16;
			} else if ( len < 16 ) {
				len = 0;
			}

			if ( aesDecrypt(&context, (uint32_t *) msg_buf, (uint32_t *) cipher_buf) ) {
				return -2;
			}
			/* CBC mode crypt restore */
			XORN(msg_buf, cipher, 16);
			cipher = cipher_buf;
	
			current_len += 16;
			cipher_buf += 16;
			msg_buf += 16;
		}
		/* 在 buffer 结束时, 指向最后一个16bytes的内容需要保存, 以便 CBC mode 计算下一次明文 */
		memcpy(cipher_tmp, cipher, 16);
		cipher = cipher_tmp;

		c += current_len;
		if ( pre_len > 0 ) {
			if ( fwrite(msg_buf2, 1, pre_len, outfile) != pre_len ) {
				err_msg(ERR_WARN, "%s fwrite error:%d\n", output_filename, pre_len);
			}
		}
		pre_len = current_len;
		/* msg_buf2 指向尚未写入的缓冲区 */
		if ( (i % 2) == 1 ) {
			msg_buf2 = outbuf;
			msg_buf = outbuf2;
		} else {
			msg_buf2 = outbuf2;
			msg_buf = outbuf;
		}
	}

	if ( msg_buf2[pre_len - 1] > 0 && msg_buf2[pre_len - 1] < 16 ) {
		unpadding = msg_buf2[pre_len - 1];
		for ( i = 1; i < unpadding; i++ ) {
			if ( msg_buf2[pre_len - 1 - i] != 0 ) {
				break;
			}
		}
		if ( i == unpadding ) {
			pre_len -= unpadding;
		}
	}
	
	if ( fwrite(msg_buf2, 1, pre_len, outfile) != pre_len ) {
		err_msg(ERR_WARN, "%s fwrite error:%d\n", output_filename, pre_len);
	}

	fclose(infile);
	fclose(outfile);
	
	return c;
}

int aes_file(char * output_filename, char * input_filename, char * key, cipherOperation op)
{
	if ( op == ENCRYPT ) { 
		return aes_enfile(output_filename, input_filename, key);
	} else if ( op == DECRYPT ) {
		return aes_defile(output_filename, input_filename, key);
	}
	
	return -1;
}

