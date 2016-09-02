/** 
 * include/asm/io.h
 * Description:
 * 
 * Copyright (C) 2008 LangR.Org
 * @author Hua Huang <loghua@gmail.com>  5月 2008
 * 
 * This is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 * 
 * $Id: io.h 7 2009-10-15 03:41:16Z hua $
 */

/***
 * I/O 端口字节输出函数
 * args: value - 输出字节, port - 输出端口
 */
#define	outb(value, port)	\
	asm ("outb %%al, %%dx"::"a" (value), "d" (port))

/***
 * I/O 端口字节输入函数
 * args: port - 输入端口
 * return: _v - 读取的字节
 */
#define inb(port) ({	\
	unsigned char _v;	\
	asm volatile ("inb %%dx, %%al":"=a" (_v):"d" (port));	\
	_v;	\
	})

/***
 * 带延迟的 I/O 端口字节输出函数
 * args: value - 输出字节, port - 输出端口
 */
#define	outb_p(value, port)		\
	asm ("outb %%al, %%dx\n"	\
		"\tjmp 1f\n"		\
		"1:\tjmp 1f\n"		\
		"1:"::"a" (value), "d" (port))

/***
 * 带延迟的 I/O 端口字节输入函数
 * args: port - 输出端口
 * return: _v - 读取字节数
 */
#define	inb_p(port) ({			\
	unsigned char _v;		\
	asm ("outb %%al, %%dx\n"	\
		"\tjmp 1f\n"		\
		"1:\tjmp 1f\n"		\
		"1:":"=a" (_v):"d" (port));	\
	_v;				\
	})

