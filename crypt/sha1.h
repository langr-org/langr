/* beecrypt sha1 package function. by langr <langr@126.com> */
/* $Id$ */

#ifndef	_P_SHA1_H
#define	_P_SHA1_H

#include	<string.h>
#include	<stdio.h>
#include	"beecrypt/sha1.h"
/* in debug.h */
#define	ERR_WARN	0
#define	ERR_INFO	0
#define	err_msg(level, format, ...)	printf(format, ##__VA_ARGS__)

#ifdef	__cplusplus
extern "C" {
#endif

#define	sha1	sha1_p

inline char * sha1(char * str);
unsigned char * sha1_string(unsigned char digest[20], char * str, int len);
int sha1_file(unsigned char digest[20], char * filename);
/* buf len need 40bytes */
inline int sha1_snprintf(char buf[40], unsigned char digest[20]);
void sha1_printf(unsigned char digest[20]);

#ifdef	__cplusplus
}
#endif

#endif /* sha1.h */

