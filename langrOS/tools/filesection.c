/**
 * filesection.c
 * 文件分割机
 * 为了能不加修改的运行在 Unix-like 和 Windows 上,
 * 此程序使用的是标准 I/O, 所以速度会不很理想,
 * 
 * version: 0.6
 * @author Log loghua@gamil.com 06.6.11
 *
 * Copyright (C) 2006 Log <loghua@gmail.com>
 */
/* $Id: filesection.c 8 2009-10-15 03:55:40Z hua $ */

/***
 *usage:
 *	filesection [section size] filename
 *		-u filename
 * v0.6 06.12.27
 * v0.2 06.3.29 
 * v0.1 06.3.28 by Log
 */
 
#include <stdio.h>
#include <string.h>
#include <stdlib.h>		/* int atoi(const char *nptr) */

#define SEG_SIZE	1433			/* 1433K ==> 1.4M */

void usage(void)
{
	printf("usage: filesection [-u] [sectioning size(Mb)] filename\n");
	printf("\t不带参数为分割文件\n\t-u 合并已分割的文件\n");
	printf("Copyright (C) 2006 HuaHuang <langr@126.com>\n");

	exit(1);
}

/* 将整数 s 转换为字符串,放入 d 并返回 */
char *itos(int s, char *d)
{
	int	i = 1, j, k;
	char	tmp[11];
	
	k = s;
	for (i=0; ; i++)
	{
		j = k%10;
		tmp[i] = j + 48;
		k = (k - j)/10;
		if (k < 10)
		{
			tmp[i + 1] = k + 48;
			tmp[i + 2] = 0;
			break;
		}	
	}
	
	j = strlen(tmp);
	for (i=0; i<j; i++)
		d[i] = tmp[j-1-i];
	d[i] = 0;	/* 此时 i 以经加了 1,就说怎么多打了一个字符,搞得我找了半个晚上*/
	return d;
}

/***
 * 将文件 sfile 分割为 seg_size 大小的块,
 * 当 seg_size = 0 时, 块大小为默认的 1.4M
 */
int section(const int seg_size, char *sfile)
{
	int	s_size = SEG_SIZE, ch;
	int	i;				/* 被分割的文件个数 */
	long int	sek = 0;
	char	tmp[100], sub[11];
	FILE	*dfp, *sfp;

	
	if ( (sfp = fopen(sfile, "rb")) == NULL )
	{
		printf("open file error: %s\n", sfile);
		return -1;
	}

	if (seg_size > 0)
		s_size = seg_size << 10;		/* sge_size Mb */

	s_size <<= 10;				/* (s_size * 1024) bytes */
	for (i=1; ; i++)
	{
		strcpy(tmp, sfile);
		strcat(tmp, ".");
		strcat(tmp, itos(i, sub));		/* tmp.i */
		if ( (dfp = fopen(tmp, "wb+")) ==NULL )
		{
			printf("creat file error: %s", tmp);
			return -1;
		}
		
		for(sek = 0; sek < s_size; sek++)
		{
			ch = getc(sfp);
			if ( ch == EOF && feof(sfp) ) {	
				fclose(sfp);
				fclose(dfp);
				return 0;		/* 结束程序 */
			}
			putc(ch, dfp);
		}
		
		fclose(dfp);
	}
}

/***
 * 将由 section() 分割的文件合并  
 * dfile 必须与 sfile 同名
 */
int coalition(char *dfile)
{
	int	i, ch;
	char	tmp[100], sub[11];
	FILE	*dfp, *sfp;
	
	if ( (dfp = fopen(dfile, "wb+")) == NULL )
	{
		printf("create file error: %s\n", dfile);
		return -1;
	}
	
	for (i=1; ; i++)
	{
		strcpy(tmp, dfile);
		strcat(tmp, ".");
		strcat(tmp, itos(i, sub));		/* tmp.i */
		if ( (sfp = fopen(tmp, "rb")) ==NULL )
		{
			fclose(dfp);		/* 合并完成 */
			return 0;
		}
		for( ; ; ) 
		{
			ch = getc(sfp);
			if ( ch == EOF && feof(sfp) ) {
				fclose(sfp);
				break;
			}
			putc(ch, dfp);
		}
	}
	
}

int main(int argc, char *argv[])
{
	int	sect_size = 0;
	
	if (argc > 3 || argc < 2)
		usage();
	
	if (argc == 2)
	{	
		if (section(0, argv[1]) == -1) {	
			printf("sectioning error\n");
			return -1;
		} else {
			printf("sectioning done\n");
			return 0;
		}
	}
	else if (argc == 3 && strcmp(argv[1], "-u") == 0)
	{
		if (coalition(argv[2]) == -1) {
			printf("coalition error\n");
			return -1;
		} else {
			printf("coalition done\n");
			return 0;
		}
	}
	else
	{
		if (section(atoi(argv[1]), argv[2]) == -1) {	
			printf("sectioning error\n");
			return -1;
		} else {
			printf("sectioning done\n");
			return 0;
		}
	}
	
	return 0;
}

