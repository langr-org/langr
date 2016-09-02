/** 
 * kernel/main.c
 * Description:
 * 
 * Copyright (C) 2008 LangR.Org
 * @author Hua Huang <loghua@gmail.com>  1月 2008
 * 
 * This is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 * 
 * $Id: main.c 1 2008-01-23 06:01:15Z hua $
 */

#define	_LIBRARY_		/* 是为了包括在 unistd.h 中的内嵌汇编代码等信息 */
#include	<unistd.h>
#include	<time.h>

/***
 * 
 */
static inline _syscall0(int, fork)
static inline _syscall0(int, pause)
static inline _syscall1(int, setup, void *, BIOS)
static inline _syscall0(int, sync)

#include	<langr/tty.h>
#include	<langr/sched.h>
#include	<langr/head.h>
#include	<asm/system.h>
#include	<asm/io.h>

#include	<stddef.h>
#include	<stdarg.h>
#include	<unistd.h>
#include	<fcntl.h>
#include	<sys/types.h>

#include	<langr/fs.h>
