/* beecrypt rsa interface/package function. by langr <langr@126.com> */
/* $Id$ */

#ifndef	_P_RSA_H
#define	_P_RSA_H

#include	<string.h>
#include	<stdio.h>
#include	"beecrypt/rsa.h"
/* in debug.h */
#define	ERR_WARN	0
#define	ERR_INFO	0
#define	err_msg(level, format, ...)	printf(format, ##__VA_ARGS__)

#ifdef	__cplusplus
extern "C" {
#endif

#define	rsa	rsa_p

/***
 * make rsa key pair
 * @param keylen: 1024~4096 bits (128~512 bytes), default 1024
 * @param pubkey default 65537
 */
inline int rsakp_make(int keylen);
inline int _rsakp_make(int keylen, int pubkey);

/***
 * rsa encrypt/decrypt
 * key bits default by 1024bits
 * @param buf encrypt result
 * @param input need encrypt data
 * @param len input data length
 * @param key encrypt key, hex ascii
 * @param n hex ascii 
 * @return >0 use buf length, -1(other) error
 */
int rsapub_cipher(char * buf, char * input, int len, char * pubkey, char * n);
int rsapri_decipher(char * buf, char * input, int len, char * prikey, char * n);

/***
 * rsa signing
 * 将传入数据使用 rsa priate key 加密签名, 结果放入 buf
 * @param buf signing result
 * @param input need signing data
 * @param len input length
 * @param key private key, hex ascii
 * @param n (public key (n, e)) hex ascii 
 * @return >0 use buf length, -1(other) error
 */
int rsapri_sign(char * buf, char * input, int len, char * prikey, char * n);

/***
 * rsa verification
 * 将传入的消息和消息签名使用 rsa public key 验证, 
 * 如果解密后的明文与签名前的明文相同则验证正常.
 * @param msg verification data message
 * @param msg_len msg length
 * @param cipher cipher text
 * @param cipher_len cipher length
 * @param key private key, hex ascii
 * @param n (public key (n, e)) hex ascii 
 * @return 0 success, >= 1 failure count
 */
int rsapub_verify(char * msg, int msg_len, char * cipher, int cipher_len, char * pubkey, char * n);

#ifdef	__cplusplus
}
#endif

#endif /* rsa.h */

