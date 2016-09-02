/* $Id: err.h 8 2009-10-15 03:55:40Z hua $ */

#ifndef __ERR_H
#define	__ERR_H

#include <errno.h>			/* for definition of errno */
#include <stdarg.h>			/* ANSI C header file */
#include <stdio.h>
#include <string.h>
#include <syslog.h>

#ifndef MAXLINE
 #define MAXLINE		4096
#endif

void	err_ret(const char *fmt, ...);
void	err_sys(const char *fmt, ...);
void	err_dump(const char *fmt, ...);
void	err_msg(const char *fmt, ...);
void	err_quit(const char *fmt, ...);

#endif /* __ERR_H */

