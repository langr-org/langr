/** 
 * kernel/_main.c
 * 用来测试引导设置程序
 * 
 * Copyright (C) 2008 LangR.Org
 * @author Hua Huang <loghua@gmail.com>  1月 2008
 * 
 * This is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 * 
 * $Id: _main.c 5 2008-05-07 11:30:13Z hua $
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

#define	VIDEO_TYPE_MDA		0x10	/* 单色文本 */
#define	VIDEO_TYPE_CGA		0x11	/* 彩色文本 */
#define	VIDEO_TYPE_EGAM		0x20	/* EGA/VGA 单色 */
#define	VIDEO_TYPE_EGAC		0x21	/* EGA/VGA 彩色 */

static unsigned long video_mem_start	= 0xb8000;	/* 显存开始地址 */
static unsigned long video_mem_end	= 0xbc000;	/*  */
static unsigned short video_port_reg	= 0x3d4;	/* 显存控制索引寄存器 */
static unsigned short video_port_val	= 0x3d5;	/* 数据寄存器 */

static unsigned long origin	= 0xb8000;		/* 滚屏起始地址 (EGA/VGA) */
static unsigned long scr_end;				/* 滚屏起始地址 (EGA/VGA) */
static unsigned long pos	= 0xb8000;		/* 光标位置 */
static unsigned char attr	= 0x07;			/* 显示属性() */

long user_stack[1024 >> 2];
struct {
	long	* a;
	short	b;
} stack_start	= { &user_stack[1024 >> 2], 0x10};	/* 系统堆栈指针 */

extern _tmp_printk(char *);
int	strlen(char *);
void	printk(char *);

void main(void)
{
	int	i = 0, c = 5;

	for( i; i < c; i++ ) {
		_tmp_printk((char *)i);
		_tmp_printk("Hello, langr, I'm kernel!!!\n");
	}
}

/***
 * 设置滚屏起始显示内存地址
 */
static inline void set_origin(void)
{
	/* cli() */

	outb_p(12, video_port_reg);
	outb_p(0xff & ((origin - video_mem_start) >> 9), video_port_val);
	outb_p(13, video_port_reg);
	outb_p(0xff & ((origin - video_mem_start) >> 1), video_port_val);

	/* sti() */
}

/***
 * 内核打印输出函数
 */
void printk(char *str)
{
	int	i, len;
	char	c;

	len	= strlen(str);
	for ( i = 0; i < len; i++) {
		c	= str[i];
		asm ("movb attr, %%ah\n\t"
			"movw %%ax, %1\n\t"
			::"a" (c),"m" (*(short *)pos));
		pos	+= 2;
	}

	set_origin();
}

int strlen(char *str)       
{
	int i;

	for(i = 0; str[i] != '\0'; i++)
		;

	return i;
}

