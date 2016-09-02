/* beecrypt sha256 package function. by langr <langr@126.com> */
/* $Id$ */

#ifndef	_P_SHA256_H
#define	_P_SHA256_H

#include	<string.h>
#include	<stdio.h>
#include	"beecrypt/sha256.h"
/* in debug.h */
#define	ERR_WARN	0
#define	ERR_INFO	0
#define	err_msg(level, format, ...)	printf(format, ##__VA_ARGS__)

#ifdef	__cplusplus
extern "C" {
#endif

#define	sha256	sha256_p

inline char * sha256(char * str);
unsigned char * sha256_string(unsigned char digest[32], char * str, int len);
int sha256_file(unsigned char digest[32], char * filename);
inline int sha256_snprintf(char buf[64], unsigned char digest[32]);
void sha256_printf(unsigned char digest[32]);

#ifdef	__cplusplus
}
#endif

#endif /* sha256.h */

