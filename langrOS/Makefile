#
# Makefile
# 
# Copyright (C) 2008 LangR.Org
# @author Hua Huang <loghua@gmail.com>  1月 2008
# 
# This is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2, or (at your option)
# any later version.
# 
# $Id: Makefile 4 2008-05-06 15:57:03Z langr $
#

VERSION		= 0
PATCHLEVEL	= 0
SUBLEVEL	= 0
EXTRAVERSION	= 0

KERNELRELEASE	= $(VERSION).$(PATCHLEVEL).$(SUBLEVEL)$(EXTRAVERSION)

TOPDIR = `pwd`
HPATH		= $(TOPDIR)/include

AS		= as
LD		= ld
CC		= gcc
CPP		= $(CC) -E
AR		= $(CROSS_COMPILE)ar
NM		= $(CROSS_COMPILE)nm
STRIP		= $(CROSS_COMPILE)strip
OBJCOPY		= $(CROSS_COMPILE)objcopy
OBJDUMP		= $(CROSS_COMPILE)objdump

# 打开所有警告, 不从标准头文件中查找, 指定头文件为 $(HPATH)
CFLAGS		= -Wall -nostdinc -I$(HPATH)
CPPFLAGS	= -nostdinc -I$(HPATH)
# -s 忽略输出文件中所有的符号信息, -x 删除所有本地符号
# 指定模拟器为 elf_i386, 指定.text节在输出文件中的绝对地址 0x0 处, 
# 指定入口点为 seartup_32, 打印一个连接位图到标准输出
LDFLAGS		= -s -x -m elf_i386 -Ttext 0x0 -e startup_32 -M

# 指定默认根设备
# 可指定值为 FLOPPY, CURRENT, /dev/xxx 或者为空
ROOT_DEV	= #FLOPPY

all:	Image

Image:	boot/bootsect boot/setup kernel/kernel tools/build
#	tools/build boot/bootsect boot/setup kernel/kernel $(ROOT_DEV) > Image
	cp boot/bootsect tools/Image.tmp.01
	cp boot/setup tools/Image.tmp.02
#	cp kernel/kernel tools/Image.tmp.03
	tools/extract kernel/kernel 4096 $ -o tools/Image.tmp.03
	tools/build -u tools/Image.tmp
	mv tools/Image.tmp tools/Image

#disk:	Image
#	dd bs=8192 if=Image of=/dev/fd0

#tools/build: tools/extract.c
tools/build: tools/filesection.c
	$(CC) -o $@ $<

boot/bootsect: boot/bootsect.o
	$(LD) -Ttext 0x0 -s --oformat binary -o $@ $<

boot/bootsect.o: boot/bootsect.S
	$(AS) -o $@ $<

boot/setup: boot/setup.o
	$(LD) -Ttext 0x0 -s --oformat binary -e begtext -o $@ $<

boot/setup.o: setup.S
	$(AS) -o $@ $<

kernel/kernel: boot/head.o kernel/main.o
	$(LD) $(LDFLAGS) -o $@ boot/head.o kernel/main.o > tools/System.map

boot/head.o: boot/head.S
	$(AS) -o $@ $<

kernel/main.o: kernel/_main.c
	$(CC) $(CFLAGS) -c -o $@ $<

#default:
#	make -C $(KDIR) SUBDIRS=$(PWD) modules

install:
	echo '^_^' 

clean:
	rm -f *.o *.ko .*.cmd .*.flags *.mod.c *.tmp.*

