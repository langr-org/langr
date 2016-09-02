.code16
.text

.global _start
_start:
	movw	$0xb800,%ax
	movw	%ax,	%es
	movw	%ax,	%ds
print:	xorw	%di,	%di
	movb	$0x48,	%es:(%di)
	#inc	%di
	#inc	%di
	movb	$0x69,	%es:(%di)
die:	jmp	print
.org	510
	.word	0xaa55
