/* beecrypt aes interface/package function. by langr <langr@126.com> */
/* $Id$ */

#ifndef	_P_AES_H
#define	_P_AES_H

#include	<string.h>
#include	<stdio.h>
#include	"beecrypt/aes.h"
#include	"bcrypt.h"
#include	"sha256.h"

#ifdef	__cplusplus
extern "C" {
#endif

#define	aes	aes_p

/**
 * general operation by aes algorithm.
 * @param buf encrypt or decrypt result
 * @param input message or cipher
 * @param input_len input length
 * @param key password
 * @param key_bits: 128, 192, 256
 * @param cipherOperation: ENCRYPT | DECRYPT
 * @return result length
 */
inline int aes(char * buf, char * input, int input_len, char * key, int key_bits, cipherOperation op);
int aes_file(char * output_filename, char * input_filename, char * key, cipherOperation op);

/**
 * aes encrypt/decrypt.
 * key bits default by 256bits (use sha256).
 * @param buf encrypt result
 * @param input need encrypt data
 * @param input_len data length
 * @param key encrypt key
 * @return 0 ok, -1 aesSetup error, -2 aesEncrypt error ...
 */
int aes_encrypt(char * buf, char * input, int input_len, char * key);
int aes_decrypt(char * buf, char * input, int input_len, char * key);
int aes_enfile(char * output_filename, char * input_filename, char * key);
int aes_defile(char * output_filename, char * input_filename, char * key);

#ifdef	__cplusplus
}
#endif

#endif /* aes.h */

