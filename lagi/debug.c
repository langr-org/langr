/** 
 * debug.c
 * Description:
 * 
 * Copyright (C) 2008 LangR.Org
 * @author Hua Huang <loghua@gmail.com> 十月 2008
 * 
 * This is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 * 
 * $Id: debug.c 3 2011-11-07 01:18:17Z loghua@gmail.com $
 */

#include	"debug.h"

/* msg 在调用之前用 snprintf() 格式化 */
int _applog(const char * file, char * msg)
{
	char timebuf[32] = {0};
	char msgbuf[DEBUG_BUFFER_MAX] = {0};
	_getdate(timebuf);
	/* printf */
	if ( strcmp(file, "stderr") == 0 ) {
		/*printf("[%s] %s\n", timebuf, msg);*/
		snprintf(msgbuf, DEBUG_BUFFER_MAX, "[%s] %s\n", timebuf, msg);
		fwrite(msgbuf, 1, strlen(msgbuf), stderr);
		return 2;
	/* log file */
	} else if ( file != NULL ) {
		FILE * fp = fopen(file, "a");
		if ( fp == NULL ) {
			/*printf("open log file error: %s\n", file);*/
			snprintf(msgbuf, DEBUG_BUFFER_MAX, "[%s] %s (open log file error: %s)\n", timebuf, msg, file);
			fwrite(msgbuf, 1, strlen(msgbuf), stderr);
			return -1;
		}
		snprintf(msgbuf, DEBUG_BUFFER_MAX, "[%s] %s\n", timebuf, msg);
		fwrite(msgbuf, 1, strlen(msgbuf), fp);
		/*fflush(fp);*/
		fclose(fp);
		return 0;
	}

	return -1;
}

inline int _getdate(char * dstr)
{
	time_t t;
	struct tm * c;
	/*char * wday[] = {"Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"};*/
	time(& t);
	c = localtime(& t);
	/*sprintf(dstr, "%02d-%02d-%02d %02d:%02d:%02d %s", 
	 		(1900+c->tm_year), (1+c->tm_mon), c->tm_mday, 
			c->tm_hour, c->tm_min, c->tm_sec, wday[c->tm_wday]);*/
	sprintf(dstr, "%02d-%02d-%02d %02d:%02d:%02d", 
			(1900+c->tm_year), (1+c->tm_mon), c->tm_mday, c->tm_hour, c->tm_min, c->tm_sec);

	return 0;
}

