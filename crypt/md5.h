/* beecrypt md5 package function. by langr <langr@126.com> */
/* $Id$ */

#ifndef	_P_MD5_H
#define	_P_MD5_H

#include	<string.h>
#include	<stdio.h>
#include	"beecrypt/md5.h"
/* in debug.h */
#define	ERR_WARN	0
#define	ERR_INFO	0
#define	err_msg(level, format, ...)	printf(format, ##__VA_ARGS__)

#ifdef	__cplusplus
extern "C" {
#endif

#define	md5	md5_p

inline char * md5(char * str);
unsigned char * md5_string(unsigned char digest[16], char * str, int len);
int md5_file(unsigned char digest[16], char * filename);
inline int md5_snprintf(char buf[32], unsigned char digest[16]);
void md5_printf(unsigned char digest[16]);

#ifdef	__cplusplus
}
#endif

#endif /* md5.h */

