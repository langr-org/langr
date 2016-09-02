/** 
 * @file debug.h
 * @brief debug module
 * 
 * Copyright (C) 2008 LangR.Org
 * @author Huang Hua <loghua@gmail.com> 2008/12/20 00:19
 * 
 * This is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 * 
 * $Id: debug.h 30 2011-12-30 02:57:03Z loghua@gmail.com $
 */

/**
 * 调试模块.
 */

#ifndef	_DEBUG_H
#define	_DEBUG_H

#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <time.h>
#include <errno.h>

#ifdef	__cplusplus
extern "C" {
#endif

/* debug */
#ifdef	QT_NO_DEBUG_OUTPUT
 #ifndef	NODEBUG
  #define	NODEBUG
 #endif
#endif
#ifndef	NODEBUG
 #define	APP_DEBUG
#endif

#ifndef	true
 #define	true	1
#endif
#ifndef	false
 #define	false	0
#endif

#ifndef	SUCCESS
 #define	SUCCESS	0
#endif
#ifndef	FAIL
 #define	FAIL	-1
#endif

/* 0~131 已经被使用, 自定义出错从 150~255 比较合适 */
#define	ENULL		NULL		/* (0)对需要返回指针的函数，返回 NULL */
#define	EMSG		150
#define	ESOCKET		151
#define	ECONN		152
#define	EFUN		153
#define	EMALLOC		154
#ifndef	ENOOP				/* 无操作, 不返回 */
 #define	ENOOP	254
#endif
#define	EASSERT		255		/* 中断程序 */

#ifndef	BUFFER_SIZE
 #define	BUFFER_SIZE	2048
#endif
#ifndef	BIG_BUFFER_SIZE
 #define	BIG_BUFFER_SIZE	4096
#endif
#ifndef	MAX_BUFFER_SIZE
 #define	MAX_BUFFER_SIZE	8192
#endif

#define	DEBUG_BUFFER_MAX	8192
/* 调试日志文件,  "stderr" 表示输出到终端 */
/*#define	DEBUG_FILE		"lgame_debug.txt"*/
/*const char DEBUG_FILE[32] = "lgame_debug-"VERSION_STRING".txt";*/
/*const char LOG_FILE[32] = "lgame-"VERSION_STRING".log";*/
#ifdef	VERSION_STRING
 #ifdef	PROGRAM_NAME
  #define	DEBUG_FILE	PROGRAM_NAME"_debug-"VERSION_STRING".txt"
  #define	LOG_FILE	PROGRAM_NAME"_run-"VERSION_STRING".log"
 #else
  #define	DEBUG_FILE	"debug-"VERSION_STRING".txt"
  #define	LOG_FILE	"run-"VERSION_STRING".log"
 #endif
#else
 #define	DEBUG_FILE	"debug.txt"
 #define	LOG_FILE	"run.log"
#endif

#define	DERROR		"[error] "
#define	DWARN		"[warn] "
#define	DINFO		"[info] "
#define	DNOTE		"[notice] "

inline int _getdate(char * );
int _applog(const char * file, char * msg);

#ifdef	APP_DEBUG
 #define	app_debug(format, ...) {\
		char __define__buffer[DEBUG_BUFFER_MAX] = {0};\
		snprintf(__define__buffer, DEBUG_BUFFER_MAX \
			, "%s():"format" errno:(%d:%s), file:%s, line:%d", __FUNCTION__, ##__VA_ARGS__, errno, strerror(errno), __FILE__, __LINE__);\
		_applog(DEBUG_FILE, __define__buffer); \
 }
#else
 #define	app_debug(format, ...)
#endif  /* end #ifdef _DEBUG */

#define	app_assert(istrue, __errno, format, ...) {\
	if ( istrue ) {\
		char __define__buffer[DEBUG_BUFFER_MAX] = {0};\
		snprintf(__define__buffer, DEBUG_BUFFER_MAX \
			, "%s():"format" errno:%d(%d:%s) file:%s, line:%d:", __FUNCTION__, ##__VA_ARGS__, __errno, errno, strerror(errno), __FILE__, __LINE__);\
		_applog(DEBUG_FILE, __define__buffer); \
		if ( __errno == EASSERT ) {\
			exit(-__errno); \
		} /*else if ( __errno == ENULL ) {\
			return NULL;\
		} */else if ( __errno != ENOOP ) {\
			return -__errno;\
		}\
	}\
}

/**
 * 返回指针, 只支持 指针 和 ENOOP.
 */
#define	app_assertp(istrue, __errno, format, ...) {\
	if ( istrue ) {\
		char __define__buffer[DEBUG_BUFFER_MAX] = {0};\
		snprintf(__define__buffer, DEBUG_BUFFER_MAX \
			, "%s():"format" errno:(%d:%s) file:%s, line:%d:", __FUNCTION__, ##__VA_ARGS__, errno, strerror(errno), __FILE__, __LINE__);\
		_applog(DEBUG_FILE, __define__buffer); \
		if ( (int)__errno != ENOOP ) {\
			return __errno;\
		}\
	}\
}

#define	app_log(format, ...) {\
	char __define__buffer[DEBUG_BUFFER_MAX] = {0};\
	snprintf(__define__buffer, DEBUG_BUFFER_MAX, format, ##__VA_ARGS__);\
	_applog(LOG_FILE, __define__buffer); \
}

/* 兼容 */
#define		l_debug	app_debug
#define		applog	app_log

#ifdef	__cplusplus
}
#endif

#endif	/* end _DEBUG_H */
