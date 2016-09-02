/** 
 * drivers/char/console.c
 * 控制台显示输入输出驱动程序
 * 
 * Copyright (C) 2008 LangR.Org
 * @author Hua Huang <loghua@gmail.com>  5月 2008
 * 
 * This is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 * 
 * $Id: console.c 7 2009-10-15 03:41:16Z hua $
 */

#include <langr/sched.h>
#include <langr/tty.h>
#include <asm/io.h>
#include <asm/system.h>
#include <langr/video.h>

#define NPAR	16

static unsigned long video_mem_start	= 0xb8000;	/* 显存开始地址 */
static unsigned long video_mem_end	= 0xbc000;	/*  */
static unsigned short video_port_reg	= 0x3d4;	/* 显存控制索引寄存器 */
static unsigned short video_port_val	= 0x3d5;	/* 数据寄存器 */

/* 以下变量用卷屏操作 */
static unsigned long origin	= 0xb8000;		/* 滚屏起始地址 (EGA/VGA) */
static unsigned long scr_end;				/* 滚屏起始地址 (EGA/VGA) */
static unsigned long pos	= 0xb8000;		/* 光标对应的显存位置 */
static unsigned long x, y;				/* 当前光标位置 */
static unsigned long top, bottom;			/* 滚动时顶行底行行号 */
static unsigned char attr	= 0x07;			/* 字符显示属性 */
static unsigned long state = 0;				/* ANSI 转义字符序列处理状态 */
static unsigned long npar, par[NPAR];			/* ANSI 转义字符序列参数个数和参数数组 */

