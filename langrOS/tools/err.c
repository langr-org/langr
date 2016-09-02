/**
 * err.c
 * 出错处理函数
 *
 * @author Log loghua@gamil.com 06.4.5
 * Copyright (C) 2006 Log <loghua@gmail.com>
 *
 * $Id: err.c 8 2009-10-15 03:55:40Z hua $
 */
 
#include "err.h"

int	daemon_proc;		/* daemon_init() */

static void err_doit(int errnoflag, int level, const char *fmt, va_list ap);

char	*pname = NULL;		/* caller can set this from argv[0] */

/***
 * 与系统调用有关的非致命性错误,
 * 打印一条消息并返回.
 */
void 
err_ret(const char *fmt, ...)
{
	va_list	ap;
	
	va_start(ap, fmt);
	err_doit(1, LOG_INFO, fmt, ap);
	va_end(ap);
	
	return ;
}

/***
 * 与系统调用有关的致命性错误,
 * 打印一条消息并结束进程.
 */
void 
err_sys(const char *fmt, ...)
{
	va_list	ap;
	
	va_start(ap, fmt);
	err_doit(1, LOG_ERR, fmt, ap);
	va_end(ap);
	
	exit(1);
}

/***
 * Fatal error related to a system call,
 * Print a message, dump core, and terminate.
 *
 */
void 
err_dump(const char *fmt, ...)
{
	va_list	ap;
	
	va_start(ap, fmt);
	err_doit(1, LOG_ERR, fmt, ap);
	va_end(ap);
	abort();			/* dump core and terminate */
	
	exit(1);
}

/***
 * Nonfatal error unrelated to a system call,
 * Print a message and return.
 * 
 */
void 
err_msg(const char *fmt, ...)
{
	va_list	ap;
	
	va_start(ap, fmt);
	err_doit(0, LOG_INFO, fmt, ap);
	va_end(ap);
	
	return ;
}

/***
 * Fatal error unrelated to a system call,
 * Print a message and terminate.
 * 
 */
void 
err_quit(const char *fmt, ...)
{
	va_list	ap;
	
	va_start(ap, fmt);
	err_doit(0, LOG_ERR, fmt, ap);
	va_end(ap);
	
	exit(1);
}

/***
 * 打印一条消息并返回调用者,
 * 调用者指定 errnoflag 值.
 */
static void 
err_doit(int errnoflag, int level, const char *fmt, va_list ap)
{
	int	errno_save;
	char	buf[MAXLINE];
	
	errno_save = errno;		/* value caller might want printed */
	vsprintf(buf, fmt, ap);
	if (errnoflag)
		sprintf(buf + strlen(buf), ": %s", strerror(errno_save));
	strcat(buf, "\n");
	
	if (daemon_proc)
	{
		syslog(level, buf);
	}
	else
	{
		fflush(stdout);		/* in case stdout and stderr are the same */
		fputs(buf, stderr);
		fflush(NULL);		/* flushes all stdio output streams */
	}
	return ;
}

