/** 
 * include/langr/video.h
 * Description:
 * 
 * Copyright (C) 2008 LangR.Org
 * @author Hua Huang <loghua@gmail.com> 五月 2008
 * 
 * This is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 * 
 * $Id: video.h 7 2009-10-15 03:41:16Z hua $
 */

#ifndef _LANGR_VIDEO_H
#define _LANGR_VIDEO_H

/* 取 setup 子程序在引导系统时设置的参数 */
#define ORIG_X			(*(unsigned char *)0x90000)	/* 光标列号 */
#define ORIG_Y			(*(unsigned char *)0x90001)	/* 光标行号 */
#define ORIG_VIDEO_PAGE		(*(unsigned short *)0x90004)			/* 显示页面 */
#define ORIG_VIDEO_MODE		((*(unsigned short *)0x90006) & 0xff)		/* 显示模式 */
#define ORIG_VIDEO_COLS		(((*(unsigned short *)0x90006) & 0xff00) >> 8)	/* 字符列数 */
#define ORIG_VIDEO_LINES	(25)						/* 字符行数 */
#define ORIG_VIDEO_EGA_AX	(*(unsigned short *)0x90008)	/* ? */
#define ORIG_VIDEO_EGA_BX	(*(unsigned short *)0x9000a)	/* 显存内存大小和色彩模式 */
#define ORIG_VIDEO_EGA_CX	(*(unsigned short *)0x9000c)	/* 显示卡特性参数 */
/* 显示模式类型符号常数 */
#define	VIDEO_TYPE_MDA		0x10	/* 单色文本 */
#define	VIDEO_TYPE_CGA		0x11	/* 彩色文本 */
#define	VIDEO_TYPE_EGAM		0x20	/* EGA/VGA 单色 */
#define	VIDEO_TYPE_EGAC		0x21	/* EGA/VGA 彩色 */

extern _tmp_printk(char *);		/* 内核起动初期的打印消息函数 */
extern void keyboard_interrupt(void);	/* 键盘中断处理函数 */
static void sysbeep(void);		/* 系统蜂鸣函数 */

#endif	/* _LANGR_VIDEO_H */
