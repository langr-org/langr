/** 
 * @file debug.h
 * @brief debug module
 * 
 * Copyright (C) 2009 LangR.Org
 * @author Huang Hua <loghua@gmail.com> 2008/12/20 00:19
 * 
 * This is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 * 
 * $Id: debug.h 11 2011-03-02 14:58:25Z langr $
 */

/**
 * 调试模块.
 */

#ifndef	_DEBUG_H
#define	_DEBUG_H

#include	<stdlib.h>
#include	<stdio.h>
#include	<string.h>

#ifdef	__cplusplus
extern "C" {
#endif

/* debug */
#ifndef	QT_NO_DEBUG_OUTPUT
 #define	LGAME_DEBUG
#endif

#define	VERSION_STRING	"v1"
#define	DEBUG_BUFFER_MAX	4096
/* 调试日志文件,  "stderr" 表示输出到终端 */
/*#define	DEBUG_FILE		"lgame_debug.txt"*/
const char DEBUG_FILE[32] = "lgame_debug-"VERSION_STRING".txt";
const char LOG_FILE[32] = "lgame-"VERSION_STRING".log";

#define	DERROR		"[error] "
#define	DWARN		"[warn] "
#define	DINFO		"[info] "
#define	DNOTE		"[notice] "

inline int __getdate(char * );
int _applog(const char * file, char * msg);

#ifdef	LGAME_DEBUG
 #define	l_debug(format, ...) {\
		char buffer[DEBUG_BUFFER_MAX+1] = {0};\
		snprintf(buffer, DEBUG_BUFFER_MAX \
			, format" file:%s, line:%d", ##__VA_ARGS__, __FILE__, __LINE__);\
		_applog(DEBUG_FILE, buffer); \
 }
#else
 #define	l_debug(format, ...)
#endif  /* end #ifdef _DEBUG */

#define	applog(format, ...) {\
	char buffer[DEBUG_BUFFER_MAX+1] = {0};\
	snprintf(buffer, DEBUG_BUFFER_MAX, format, ##__VA_ARGS__);\
	_applog(LOG_FILE, buffer); \
}

#ifdef	__cplusplus
}
#endif

#endif	/* end _DEBUG_H */
