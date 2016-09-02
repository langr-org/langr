/** 
 * boot/head.S
 * Description:
 * 
 * Copyright (C) 2007 LangR.Org
 * @author Hua Huang <loghua@gmail.com> May 2007
 * 
 * This is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 * 
 * $Id: head.S 4 2008-05-06 15:57:03Z langr $
 */

/***
 * head 程序被编译到 system 模块的最前面, 它将在物理地址 0x0 处开始运行
 * NOTE! 这个程序是运行在 32 位模式下的绝对地址 0x0, 这里将会被页目录覆
 * 盖掉.
 */

 .text
 .globl idt, gdt, pg_dir, startup_32, tmp_floppy_area, _tmp_printk
 pg_dir:				# 页目录存放地址
 startup_32:
 	movl	$0x10,	%eax		# 描述符表项的选择符 10100000 
	movw	%ax,	%ds	
	movw	%ax,	%es	
	movw	%ax,	%fs	
	movw	%ax,	%gs
	movw	%ax,	%ss
	movl	$stack_start, %esp	# 设置系统堆栈 $stack_start->ss:(%esp)
	call	setup_idt
	call	setup_gdt
	pushl	$head_msg_01
	call	_tmp_printk
	
	movl	$0x10,	%eax	# 因为修改了 gdt, 这里要重新装载所有的段寄存器
	movw	%ax,	%ds	# 
	movw	%ax,	%es	# 
	movw	%ax,	%fs	
	movw	%ax,	%gs	#
	movw	%ax,	%ss
	movl	$stack_start, %esp	

/***
 * 下面用于测试 A20 地址线是否开启.
 * 方法是向内存地址 0x000000 处写一任意数, 然后看内存地址 0x100000(1M)
 * 处是否也是该值, 如果相同的话就一直比较下去, 即死循环, 表示 A20 线
 * 没有选通, 内核就不能使用 1M 以上内存.
 */
	xorl	%eax,	%eax	
1:	incl	%eax			# 检测 A20 地址线是否真的开启
	movl	%eax,	(0x000000)	# 
	cmpl	%eax,	(0x100000)	# 
	je	1b			# 如果没有开启就永远循环

/***
 * 下面检测数学协处理器芯片是否存在.
 * 通过修改控制寄存器 CR0, 在假设存在协处理器的情况下执行一个协处理器指令,
 * 如果出错没说明协处理器不存在, 然后重新设置 CR0 中的协处理器存在标志 MP(位1)
 * 并置 CR0 中的协处理器仿真位 EM (位2).
 */
	movl	%cr0,	%eax		# 
	andl	$0x80000011, %eax	# (PG 31, ET 4, PE 0)
	orl	$2,	%eax		# 设置 MP(位1)
	movl	%eax,	%cr0		# 
	call	check_x87		
	jmp	after_page_tables	# 

check_x87:
	fninit
	fstsw	%ax			
	cmpb	$0,	%al			
	je	1f			#
	movl	%cr0,	%eax		# 
	xorl	$6,	%eax		# 
	ret

.align 4				# 按 4 字节对齐内存地址
1:
	.byte	0xdb, 0xe4		# 
	ret

/***
 * setup_idt
 * 设置中断描述符表, 共 256 个
 * 这里默认中断处理都指点向 ignore_int 中断门, 
 * 中断门描述符格式可见<<80x86>>一书, 真正的中断在以后会慢慢安装
 */
setup_idt:
	cli				# 先清中断
	lea	ignore_int, %edx	# 将中断门地址值送 %edx
	movl	$0x00080000, %eax	#
	movw	%dx, %ax		#
	movw	$0x8e00, %dx		#

	lea	idt, %edi		#
	mov	$256, %ecx		
rp_sidt:
	movl	%eax, (%edi)		#
	movl	%edx, 4(%edi)		
	addl	$8, %edi
	dec	%ecx
	jne	rp_sidt			# 
	lidt	idt_descr		# 装载 idt 描述符表基地址寄存器
	ret

/***
 * setup_gdt
 * 设置全局描述符表
 */
setup_gdt:
	lgdt	gdt_descr
	ret

/***
 * 从物理地址 4K 处开始是四个页目录项指定到的四个页表, 页目录表放在物理地
 * 址 0 处, 一页可管理 1024 * 4K = 4M 物理内存. 四页共可管理 16M 物理内存
 */
.org	0x1000
pg0:

.org	0x2000
pg1:

.org	0x3000
pg2:

.org	0x4000
pg3:

.org	0x5000

/***
 * tmp_floppy_area
 */
tmp_floppy_area:
	.fill	1024, 1, 0

/***
 * 将 kernel 的主函数及其需要的参数压入堆栈
 * 然后调用分页设置函数, 在其返回时则开始正式执行 kernel
 * 当从 kernel 主函数返回后, 则机器进入死循环
 */
after_page_tables:
	pushl	$0			# 
	pushl	$0
	pushl	$0
	pushl	$L6			# 从 kernel 返回后进入死循环
	pushl	$main
	jmp	setup_paging
L6:
	jmp	L6

/* 默认中断向量句柄, 这里打印一条消息 */
int_msg:
	.asciz	"Unknow interrupt\n\r"
	.byte	0
.align	4				# 
ignore_int:
	pushl	%eax
	pushl	%ecx
	pushl	%edx
	push	%ds			# NOTE! ds, es 等虽为 16 位寄存器
	push	%es			# 但仍以 32 位形式入栈, 即占 4 字节空间
	push	%fs
	mov	$0x10, %ax		# 置段选择符
	mov	%ax, %ds
	mov	%ax, %es
	mov	%ax, %fs
	pushl	$int_msg		# 把调用 _tmp_printk 函数的参数入栈
	call	_tmp_printk		# 
	# popl	%eax
	pop	%fs
	pop	%es
	pop	%ds
	popl	%edx
	popl	%ecx
	popl	%eax
	iret				# 中断返回
	
/***
 * setup_paging
 * 设置页表
 */
.align	4
setup_paging:
	movl	$1024 * 5, %ecx		# 
	xorl	%eax, %eax		
	xorl	%edi, %edi		# 
	cld				# 清方向标志 (df = 0)
	rep				# 重复下一条命令
	stosl				# 将 ax 内容送 di所指内在地址
	/* 设置页表描述符 */
	movl	$pg0 + 7, pg_dir	# 属性: u/s r/w p
	movl	$pg1 + 7, pg_dir + 4	# 
	movl	$pg2 + 7, pg_dir + 8	# 
	movl	$pg3 + 7, pg_dir + 12	# 

	movl	$pg3 + 4092, %edi	# 
	movl	$0xfff007, %eax		#
	std
1:	stosl
	subl	$0x1000, %eax
	jge	1b

	xorl	%eax, %eax		#
	movl	%eax, %cr3
	movl	%cr0, %eax
	orl	$0x80000000, %eax	#
	movl	%eax, %cr0		#
	ret

head_msg_01:	.byte	13, 10		# \r\n
	.ascii	"loading head ..."
	.byte	0
head_msg_02:
	.ascii	"\r\ngo to main ..."
	.byte	0
/* 内核临时打印字符函数, 后面再用C去写一个 */
_kvideo:
	.long	0xb8000			# text 模式显存开始地址
_kpos:
	.long	160 * 6			# 第六行开始打印
_kattr:
	.byte	0x07			# 显示属性
.align	4
.word	0
_tmp_printk:
	popl	%ebx			# 返回地址指针
	popl	%esi			# 参数
	push	%es			# 
	mov	$0x10, %ax
	mov	%ax, %es
	movb	_kattr, %ah
	movl	_kvideo, %edi
	addl	_kpos, %edi
	cld				# 清方向
nextchar:
	lodsb				# %ds:(%esi) => %al
	stosw				# %ax => %es:(%edi)
	addl	$2, _kpos
	testb	%al, %al
	jne	nextchar
	pop	%es
	pushl	%ebx			# 返回地址再入栈
	ret

/* 设置中断, 全局描述符表寄存器基地址 */
.align	4
.word	0
idt_descr:				#
	.word	256 * 8 - 1		# 
	.long	idt

.align	4
.word	0
gdt_descr:
	.word	256 * 8 - 1		#
	.long	gdt			#

/* 设置中断, 全局描述符表 */
.align	8
idt:	.fill	256, 8, 0		# idt 未初始化
gdt:
	.quad	0x0000000000000000	# 空描述符
	.quad	0x00c09a0000000fff	# 代码段描述符 (16M)
	.quad	0x00c0920000000fff	# 数据段描述符 (16M)
	.quad	0x0000000000000000	# 系统段描述符
	.fill	252, 8, 0		# 
	.word	512 * 2			# 2k内核态系统堆栈
stack_kernel:
	.word	512 * 2			# 2k用户态系统堆栈
stack_start:
	.long	0

