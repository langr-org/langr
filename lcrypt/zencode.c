#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <zlib.h>
#include "lcrypt.h"

#define OUTBUFSIZ  100000

z_stream z;
char outbuf[OUTBUFSIZ];

/* 使用 zlib 来压缩解压缩文件 */
char * zcodecom(int mode, char * inbuf, int inbuf_len, int * resultbuf_len)
{
    int count, status;
    char * resultbuf;
    int total_count = 0;

    z.zalloc = Z_NULL;
    z.zfree = Z_NULL;
    z.opaque = Z_NULL;

    z.next_in = Z_NULL;
    z.avail_in = 0;
    if (mode == 0) {
	/* 压缩 初始化 */
	deflateInit(&z, 1);
    } else {
	/* 解压缩 初始化 */
	inflateInit(&z);
    }

    z.next_out = outbuf;
    z.avail_out = OUTBUFSIZ;
    z.next_in = inbuf;
    z.avail_in = inbuf_len;

    resultbuf = malloc(OUTBUFSIZ);

    while (1) {
	if (mode == 0) {
	/* 压缩 */
		status = deflate(&z, Z_FINISH);
	} else {
	/* 解压缩 */
		status = inflate(&z, Z_NO_FLUSH);
	}
        if (status == Z_STREAM_END) break;
        if (status != Z_OK) {
	    if (mode == 0) {
		deflateEnd(&z);
	    } else {
		inflateEnd(&z);
	    }
	    *resultbuf_len = 0;
	    return(resultbuf);
	}
	/* 内存空间不够? */
        if (z.avail_out == 0) {
	    resultbuf = realloc(resultbuf, total_count + OUTBUFSIZ);
	    memcpy(resultbuf + total_count, outbuf, OUTBUFSIZ);
	    total_count += OUTBUFSIZ;
            z.next_out = outbuf;
            z.avail_out = OUTBUFSIZ;
        }
    }
    if ((count = OUTBUFSIZ - z.avail_out) != 0) {
	resultbuf = realloc(resultbuf, total_count + OUTBUFSIZ);
	memcpy(resultbuf + total_count, outbuf, count);
	total_count += count;
    }
    if (mode == 0) {
	/* 压缩完成 */
	deflateEnd(&z);
    } else {
	/* 解压缩完成 */
	inflateEnd(&z);
    }
    *resultbuf_len = total_count;
    return(resultbuf);
}

char * zencode(char * inbuf, int inbuf_len, int * resultbuf_len)
{
	return zcodecom(0, inbuf, inbuf_len, resultbuf_len);
}

char * zdecode(char * inbuf, int inbuf_len, int * resultbuf_len)
{
	return zcodecom(1, inbuf, inbuf_len, resultbuf_len);
}

char * lpcode(int mode, char * inbuf, int inbuf_len, int * resultbuf_len, void * (* callback))
{
	if ( callback != NULL ) {
		return (char *) callback(mode, inbuf, inbuf_len, resultbuf_len);
	}
	* resultbuf_len = inbuf_len;
	return inbuf;
}

char * pcode_null(int mode, char * inbuf, int inbuf_len, int * resultbuf_len)
{
	* resultbuf_len = inbuf_len;
	return inbuf;
}

char * pcode_des(int mode, char * inbuf, int inbuf_len, int * resultbuf_len)
{
	* resultbuf_len = inbuf_len;
	return inbuf;
}
