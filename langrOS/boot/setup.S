/** 
 * boot/setup.S
 * Description:
 * 
 * Copyright (C) 2006, 2007 LangR.Org
 * @author Hua Huang <loghua@gmail.com> Dec 2006
 * 
 * This is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 * 
 * $Id: setup.S 3 2008-03-18 14:42:09Z langr $
 */

/***
 * setup 主要是通过 BIOS 中断调用, 读取机器系统数据, 并将数据保存到
 * 0x90000(bootsect) 处, 供其后程序使用.然后将 system(0x10000) 模块
 * 移到内存绝对地址 0x0000 处.
 * 接着加载临时的 idtr 和 gdtr, 开启 A20 地址线, 初始化主从 8259A
 * 芯片. 然后进入保护模式并把执行权交给 system 模块, 开始启动系统.
 */
INITSEG		= 0x9000
SYSSEG		= 0x1000
SETUPSEG	= 0x9020

.code16
.globl begtext, begdata, begbss, endtext, enddata, endbss

.text
begtext:
.data
begdata:
.bss
begbss:
.text

start:
	movw	$INITSEG, %ax
	movw	%ax,	%ds

	movb	$0x03,	%ah	# 读光标位置, 并保存以备后用
	xorb	%bh,	%bh	# %bh = 页号
	int	$0x10		# %dh = 行号, %dl = 列号
	movw	%dx,	(0x0)	# 将 %dx (光标) 保存到 $INITSEG:0x0 处
	
	movb	$0x88,	%ah	# 取扩展内存大小 (KB)
	int	$0x15	
	movw	%ax,	(0x2)	# %ax = 1M 以后的扩展内存 (KB)

	movb	$0x0f,	%ah	# 取显示卡的当前显示模式
	int	$0x10
	movw	%bx,	(0x4)	# %bh = 当前显示页 0x90004
	movw	%ax,	(0x6)	# %ah = 字符列数 0x90007,
				# %al = 显示模式 0x90006

	movb	$0x12,	%ah	# 检查显示方式 (EGA/VGA) 并取其参数
	movb	$0x10,	%bl	# 功能号: %ah = 0x12, %bl = 0x10
	int	$0x10
	movw	%ax,	(0x8)	# 

	/***
	 * 显示状态: %bh
	 * 	0x00 - 彩色, I/O 端口 = 0x3d0-0x3df
	 * 	0x01 - 单色, I/O 端口 = 0x3b0-0x3bf
	 * 显示内存: %bl
	 * 	0x00 - 64K, 0x01 - 128K, 0x02 - 192K, 0x03 - 256K
	 */
	movw	%bx,	(0xa)	# %bh = 显示状态, %bl = 显示内存
	movw	%cx,	(0xc)	# 显卡特性参数
	
	/***
	 * 复制硬盘参数表到 0x90080, 0x90090
	 * 第一个硬盘参数表在中断向量 0x41 处, 中断向量 0x46 的
	 * 向量值指向第二个硬盘的参数表. 每个表长 16 字节.
	 */
	movw	$0x0,	%ax	# 取第一块硬盘的信息
	movw	%ax,	%ds	
	lds	(4 * 0x41), %si	# 将段值送 %ds, 偏移送 %si
	movw	$INITSEG, %ax
	movw	%ax,	%es
	movw	$0x0080, %di	# 0x9000:0x0080 -> %es:%di
	movw	$0x10,	%cx	# 共 16 字节
	rep			# 重复将 %ds:%si 所指地址内容传到
				# %es:%di 所指地址
	movsb			# 字节传送指令

	movw	$0x0,	%ax	# 取第二块硬盘的信息, 如果有的话
	/* 这里省略了, 因为在很长时间内我肯定用不上 ... */

/***
 * 下面我们要开始做进入保护模式方面的准备了
 * 首先将 system 模块移动到正确的位置, 先前 bootsect 将其移到了
 * 0x10000-0x8ffff (64-512K) 处, 现在我们把它移到 0x00000 处
 */
	 cli			# 先清中断
	 movw	$0x0,	%ax	
	 cld			# 清方向标志位 (DF = 0)
do_move:
	movw	%ax,	%es	# 目的地址初始为 0x0000:0x0 (%es:%di)
	addw	$0x1000, %ax	
	cmp	$0x9000, %ax	# 已经移完了最后 64K 代码?
	jz	end_move	# ZF = 1, 相等则转移(值等于0)
	movw	%ax,	%ds	# 源地址初始为 0x1000:0x0 (%ds:%si)
	subw	%di,	%di
	subw	%si,	%si	
	movw	$0x8000, %cx	# 一次移动 0x8000 字 
				# (64K字节,实模式下的一个段大小)
	rep
	movsw			# 字传送指令
	jmp	do_move

/***
 * 这里我们开始加载段描述符
 * 系统寄存器 GDTR, IDTR 长度为 6B, 其中 2B 段界线, 4B 段基地址
 * 每个 gdt 和 idt 表项占 8B. 
 * 关于 32 位保护模式下的编程知识, 可以参考一下
 * 杨季文的 <<80x86汇编语言程序设计教程>> 一书第十章
 */
end_move:
	movw	$SETUPSEG, %ax	# %ds 指向本程序的段基地址
	movw	%ax,	%ds	
	lidt	idt_48		# 加载 idt (6B, 2B limit, 4B base)
	lgdt	gdt_48		# 加载 gdt (这些都是临时的)

/* 开启 A20 地址线 */
	call	empty_8042	# 等待输入缓冲器空, 
				# 只有输入缓冲器为空时才可对其写
	movb	$0xd1,	%al	# 0xd1 命令码表示要写数据到 8042 P2 口.
	outb	%al,	$0x64	# P2 口的位 1 用于 A20 线选通.
	call	empty_8042	# 
	movb	$0xdf,	%al	# 选通 A20 地址线参数
	outb	%al,	$0x60	# 数据要写到 0x60 口
	call	empty_8042	# 输入缓冲器为空则表示 A20 线已经选通

/***
 * 重新对 8259 中断控制器进行编程 (IRQ0-IRQ15)
 * 我们将它们放在Intel 保留的硬件中断后面 (int 0x20-0x2f).
 * 在那里它们不会引起冲突.
 * 因为 PC 机的 BIOS 将中断放在了 0x08-0x0f, 这些中断也被用于内部
 * 硬件中断. 我们必须重新对 8259 中断控制器进行编程.
 * 主 8259A 芯片 I/O 端基地址是 0x20, 从芯片 0xa0.
 */
 	movb	$0x11,	%al	# 初始化序列命令
	outb	%al,	$0x20	# 发送到主 8259A
	.word	0x00eb,	0x00eb	# 两条跳转指令机器码 (jmp $ + 2)
	outb	%al,	$0xa0	# 到从 8259A
	.word	0x00eb,	0x00eb	# 跳到下一条指令, 起延时作用

	movb	$0x20,	%al	# 将 8259 硬件中断放到 int 0x20-0x2f
	outb	%al,	$0x21	# 送主芯片 ICW2 命令字, 起始中断号
	.word	0x00eb,	0x00eb	
	movb	$0x28,	%al
	outb	%al,	$0xa1	# 送从芯片 ICW2 命令, 从芯片起始中断号
	.word	0x00eb,	0x00eb	

	movb	$0x04,	%al	# 主 ICW3
	outb	%al,	$0x21	
	.word	0x00eb,	0x00eb	
	movb	$0x02,	%al	# 从 ICW3
	outb	%al,	$0xa1	
	.word	0x00eb,	0x00eb	

	movb	$0x01,	%al	# 主 ICW4, 8086 模式
	outb	%al,	$0x21	
	.word	0x00eb,	0x00eb	
	outb	%al,	$0xa1	# 从 ICW4
	.word	0x00eb,	0x00eb	

	movb	$0xff,	%al	# 屏蔽主从芯片所有中断请求
	outb	%al,	$0x21	
	.word	0x00eb,	0x00eb	
	outb	%al,	$0xa1	

/***
 * 现在我们要进入 32 位保护模式了!
 * 首先加载机器状态字, 80x86 的控制寄存器 CR0 的位 0 置 1 则允许保护.
 * 使用 lmsw 加载 CR0 寄存器.
 * 在保护模式下 2B 的段寄存器装载的是段选择符, 即 GDTR 所指地址的偏移
 * 而段寄存器的不可见部分装载的是段描述符, 即段选择符偏移处的 8B 内容
 * 详细资料请参见 <<80x86>>
 */
	movw	$0x0001, %ax	# 
	lmsw	%ax		
	ljmp	$8,	$0	# 段选择符: 8 指第二项 GDT, RPL = 0

/***
 * 检查键盘命令队列是否为空.
 * 只有当输入缓冲器为空时 (状态寄存器位 1 = 0) 才可以对其进行写命令
 */
empty_8042:
	.word	0x00eb,	0x00eb
	inb	$0x64,	%al	# 8042 状态寄存器端口
	test	$2,	%al	# 测试位 1, 输入缓冲器满?
	jnz	empty_8042	# 满, 则循环 (ZF = 0, 不等) !!!
	ret

/***
 * 临时的全局描述符表
 * 这里只有三个表项, 第一个不用, 
 * 第二个为系统代码段描述符, 第三个为系统数据段描述符
 */
gdt:
	.word	0, 0, 0, 0	# 第一个 GDT 表项, 不用

	.word	0x07ff		# 8M limit (2048 * 4096)
	.word	0x0000		# 基地址 = 0
	.word	0x9a00		# 代码段: read/exec (10011010)
	.word	0x00c0		# 粒度 = 4K 

	.word	0x07ff		# 
	.word	0x0000		# 
	.word	0x9200		# 数据段: read/write (10010010) 
	.word	0x00c0		# 

idt_48:
	.word	0
	.word	0, 0		# 

gdt_48:
	.word	0x800		# 
	.word	512 + gdt, 0x9	# gdt 段基地址: 0x90000,
				# 偏移: 0x200 + gdt (bootsect + gdt)

.text
endtext:
.data
enddata:
.bss
endbss:

