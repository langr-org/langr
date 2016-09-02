#include <stdio.h>
#include <stdlib.h>
#include <string.h>
//#include <zlib.h>
#include "php_hcrypt.h"

#define OUTBUFSIZ  100000

//z_stream z;
char outbuf[OUTBUFSIZ];

/* 使用 zlib 来压缩解压缩文件 */
char * zcodecom(int mode, char * inbuf, int inbuf_len, int * resultbuf_len)
{
    if (mode == 0) {
	/* 压缩 初始化 */
	//deflateInit(&z, 1);
    } else {
	/* 解压缩 初始化 */
	//inflateInit(&z);
    }

    //resultbuf = malloc(OUTBUFSIZ);

    *resultbuf_len = inbuf_len;
    return (inbuf);
}

char * zencode(char * inbuf, int inbuf_len, int * resultbuf_len)
{
	return zcodecom(0, inbuf, inbuf_len, resultbuf_len);
}

char * zdecode(char * inbuf, int inbuf_len, int * resultbuf_len)
{
	return zcodecom(1, inbuf, inbuf_len, resultbuf_len);
}

