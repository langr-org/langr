/** 
 * bootsect.S
 * x86 体系结构的引导程序 
 * 
 * Copyright (C) 2006 LangR.Org
 * @author Hua Huang <loghua@gmail.com> Dec 2006
 * 
 * This is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 * 
 * $Id: bootsect.S 3 2008-03-18 14:42:09Z langr $
 */

/***
 * x86 体系结构的计算机在加电启动后由 BIOS 程序将引导设备的
 * 第一扇区加载到 0x7c00(31KB) 处, 并跳到此处开始执行.
 * 
 * bootsect.S 把自己移到地址 0x90000(576K) 处, 并跳转到那里,
 * 接着通过调用 BIOS 中断将 'setup' 加载到自己后面 0x90200 处,
 * 将 system 模块加载到 0x10000(64K) 处. 到现在为止, 我还不知道
 * 一般的系统模块会有多大, 这里大多是模仿的 linux .
 * 
 * SYSSIZE 是要加载的系统模块大小, 这里只是一个预设值, 
 * 在编译连接后 Makefile 中会重新写入它的准确值, 现在写一个稍
 * 大点的值应该没问题的.
 * 
 * NOTE! 这个数是按每 16bytes 一个单位来算的, 0x3000 共为
 * 0x3000<<4(0x30000) = 196KB, 其实就是在实模式下没开 A20 地址线
 * 时的段值.
 */
SYSSIZE		= 0x3000	# 要加载的 Image 大小(*16 byte = 192K)

BOOTMAGIC	= 0xaa55
BOOTSEG		= 0x7c0		
INITSEG		= 0x9000
SETUPSEG	= 0x9020
SETUPSECTS	= 4		# setup 程序的大小 (4 sector)
SYSSEG		= 0x1000
ENDSEG		= SYSSEG + SYSSIZE

/***
 * ROOT_DEV:	0x000 - 根文件系统设备使用引导软驱设备
 * 		0x301 - 第 1 个硬盘的第一个分区
 *		0x306 - 第 2 个硬盘的第一个分区
 */
ROOT_DEV	= 0

/* Real Mode */
.code16
.text

.global _start
_start:
	movw	$BOOTSEG,%ax
	movw	%ax,	%ds
	movw	$INITSEG,%ax
	movw	%ax,	%es
	movw	$256,	%cx	# 移动 256 字到 INITSEG 段处
	subw	%si,	%si	
	subw	%di,	%di
	cld			# 清方向标志
	rep
	movsw			# %ds:(%si) ==> %es:(%di)
	ljmp	$INITSEG, $go

/***
 * 从下面开始, 是从段 INITSEG 处开始执行
 * 0x4000 是一个 (>= bootsect + setup + root for stack) 的任意值
 * 12 是磁盘分区大小
 */
go:
	movw	$0x4000 - 12, %di
	movw	%cs,	%ax
	movw	%ax,	%ds
	movw	%ax,	%ss
	movw	%di,	%sp	# 堆栈指针指向 $INITSEG:0x4000 - 12
	
/***
 * 接下来将 setup 加载到 bootsect 后面
 * NOTE! 此时: %cs = %ds = %es = %ss = $INITSEG, %fs = 0,
 * %di 以经设置好了. 其实为了获得最大的读效率和防止读出错, 我们应该在这
 * 里或在装载 system 前将 BIOS 0x1e 号中断向量处(0x0:0x78)指向的软驱参数
 * 表指针(0x81,0x80):(0x79,0x78)指向的软驱参数表(12bytes)复制到 %es:(%di)
 * 处, 并修改 0x4(%di) 处每磁道扇区数为一个可能的最大值 36, 然后将
 * 0x0:0x78 指向的指针改为 %es:(%di) 值
 * 
 * 调用 BIOS 中断, 将 setup 程序(<=4*512bytes)从磁盘第 2 个扇区开始读到
 * $INITSEG:0x200 处. 如果出错, 则(CF标志置位)复位驱动器重读.
 */
load_setup:
	movw	$0x0,	%dx		# drive 0 (表当前驱动), head 0
	movw	$0x002,	%cx		# track 0, sector 2
	movw	$0x200,	%bx		# %es:%bx->数据缓冲区
	movw	$0x200 + SETUPSECTS, %ax # %ah 功能号 %al 要读的扇区数
	int	$0x13

	jnc	load_ok			# CF = 0, continue
	movw	$0x0,	%dx
	movw	$0x0,	%ax
	int	$0x13
	jmp	load_setup

/***
 * 获取磁盘驱动器参数, 特别是每磁道的扇区数
 * 这里是调用的 0x13 号 BIOS 中断功能. 当然, 我们还可以去用一些数
 * (36, 18, 15, 9) ==> (2.88M, 1.44M, 1.2M, 720K)来读测试.
 * 后面我们可能会用每磁道的扇区数来判断驱动器类型.
 * int $0x13 BIOS 调用:
 * 	%ah = 0x08, 功能号 8 取磁盘参数 
 * 	%dl = drive number (HD 置位 7 => %dl = 0x80)
 * return:
 * 	%ax = 0(status), %bl = type (AT/PS2), 
 * 	%ch = 最大磁道号低 8 位, 
 * 	%cl = sectors/track(位0-5), 磁道号高 2 位(位6-7)
 * 	%dh = 磁头数, %dl = 驱动器数
 * 	%es:%di => 软驱磁盘参数表
 * 	出错则置 CF
 */
load_ok:
	movb	$0x00,	%dl
	movw	$0x0800, %ax
	int	$0x13
	
	movb	$0x00, %ch
	#seg	%cs		# 下一条语句的操作数在 %cs 段
	movw	%cx,	sectors
	
/* print message */
	movw	$INITSEG, %ax
	movw	%ax,	%es

	movb	$0x03,	%ah	# read cursor pos
	xorb	%bh,	%bh
	int	$0x10
	
	movw	$43,	%cx	# 43 bytes
	movw	$0x0007, %bx
	movw	$msg1,	%bp
	movw	$0x1301, %ax
	int	$0x10

/* 好了, 现在我们开始装载 system (到 0x10000) 吧! */
	movw	$SYSSEG, %ax
	movw	%ax,	%es
	call	load_system
	call	close_motor

/***
 * 下面检查系统要使用的根文件系统设备.
 * 如果已定义了则直接使用, 否则就根据上面取的每磁道扇区数来判断
 * (这里只比较了三种类型). 在 Langr 中设备号与 Linux 保持一致
 */
	movw	root_dev, %ax
	cmpw	$0,	%ax
	jne	root_defined	# ZF = 0, 已定义??

	movw	sectors, %bx
	movw	$0x208,	%ax	# /dev/ps0 (2, 8) - 1.2MB
	cmpw	$15,	%bx
	je	root_defined	# ZF = 1, 相等则转移

	movb	$0x1c, %al	# /dev/PS0 (2, 28) - 1.4MB
	cmpw	$18,	%bx
	je	root_defined
	
	movb	$0x20,	%al	# /dev/fd0H2880 (2, 20) - 2.88MB
	cmpw	$36,	%bx
	je	root_defined

	movb	$0x0,	%al	# /dev/fd0 (2, 0) - autodetect
root_defined:
	movw	%ax,	root_dev
/***
 * 到此为止, 所有的程序都加载完毕, 
 * 我们跳到此程序的后面去执行 setup 程序
 */
	ljmp	$SETUPSEG, $0

/***
 * 这个子程序是读取 system 模块到 0x10000 处
 * 因为 bootsect 是在 v86 模式下运行的, 每个段最大只能有 64K,
 * 所以在每次(尽可能)读一个磁道前, 我们都测试数据有没有跨越 64K 内存边界
 * 如果在一次读磁道上全部未读扇区时超过 64K 边界, 则将此磁道分多(2)次读
 */
sread:	.word 1 + SETUPSECTS	# 磁道中已读扇区数
head:	.word 0
track:	.word 0

load_system:
	movw	%es,	%ax
	test	$0x0fff, %ax
die:	jne	die		# 如果 %es 没对齐 64K 边界则死机
	xorw	%bx,	%bx	# %bx 为段内偏移
	
rp_read:
	movw	%es,	%ax
	cmpw	$ENDSEG, %ax
	jb	ok1_read
	ret
ok1_read:
	movw	sectors, %ax	# 每磁道扇区数
	subw	sread,	%ax	# 减去当前磁道已读取的扇区数
	movw	%ax,	%cx	# = 此次能读取的扇区数
	shlw	$9,	%cx	# 转换为 bytes, %cx = %cx*512
	addw	%bx,	%cx	# + 当前段内偏移, 看是否越(64K)段
	jnc	ok2_read	# CF = 0, 
	je	ok2_read	# ZF = 0, <= 64K 则可正常读

	xorw	%ax,	%ax	# 越段, 则计算此次可读的字节
	subw	%bx,	%ax	# 0x10000 - 0xfxxx
	shrw	$9,	%ax	# 转换为扇区
ok2_read:
	call	read_track
	movw	%ax,	%cx
	addw	sread,	%ax
	cmpw	sectors, %ax	# 此磁道是否还有未读扇区?
	jne	ok3_read

	movw	$1,	%ax
	subw	head,	%ax	# 检测当前磁头号
	jne	ok4_read	# 如果是 0 磁头, 则转儿去读 1 碰头 
	incw	track		# 否则去读下一磁道
ok4_read:
	movw	%ax,	head
	xorw	%ax,	%ax
ok3_read:
	movw	%ax,	sread	# 保存当前磁道已读扇区数
	shlw	$9,	%cx
	addw	%cx,	%bx
	jnc	rp_read		#

	movw	%es,	%ax
	addw	$0x1000, %ax
	movw	%ax,	%es
	xorw	%bx,	%bx
	jmp	rp_read

read_track:
	pushw	%ax
	pushw	%bx
	pushw	%cx
	pushw	%dx
	movw	track,	%dx
	movw	sread,	%cx
	incw	%cx		# %cl = 开始读扇区
	movb	%dl,	%ch	# %ch = 当前磁道号
	movw	head,	%dx	
	movb	%dl,	%dh	# %dh = 当前磁头号
	movb	$0,	%dl	# %dl = 驱动器号
	andb	$0x01,	%dh	# 磁头号不能 > 1 (软驱)
	movb	$2,	%ah	# %ah = 功能号(读磁盘)
	int	$0x13

	jc	bad_rt		# CF = 1, 读出错??
	popw	%dx
	popw	%cx
	popw	%bx
	popw	%ax
	ret

bad_rt:
	movw	$0,	%ax	# 复位驱动器
	movw	$0,	%dx
	int	$0x13
	popw	%dx
	popw	%cx
	popw	%bx
	popw	%ax
	jmp	read_track	# 重读

/***
 * 在进入内核前关闭软驱
 */
close_motor:
	pushw	%dx
	movw	$0x3f2,	%dx	# 软驱 I/O 端口: 0x3f0~0x3f7
	xorb	$0,	%al	# 关闭 FDC, 禁止 DMA 和中断请求
	outb	%al,	%dx
	popw	%dx
	ret

sectors:
	.word	0
msg1:
	.byte	13, 10		# \r\n
	.ascii	"Welcome to Langr!"
	.byte	13, 10, 13, 10
	.ascii	"Loading system ..."
	.byte	13, 10		# 43 byte
.org	508
root_dev:
	.word	ROOT_DEV
boot_flag:
	.word	BOOTMAGIC

