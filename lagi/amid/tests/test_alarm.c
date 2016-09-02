/** 
 * @file alarm.c
 * @brief 
 * 
 * Copyright (C) 2011 LangR.Org
 * 
 * This is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 * 
 * @package test
 * @author Langr <hua@langr.org> 2011/11/17 18:05
 * 
 * $Id: test_alarm.c 28 2011-12-29 06:29:16Z loghua@gmail.com $
 */

#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <signal.h>
#include "../../debug.h"

static int alarmid = 0;

/**
 * @fn
 * @brief 用 sigaction() 重写了可靠的 signal() 函数, 
 * 并针对 SIGALRM 信号修改了不重入被中断的函数.
 * @NOTE: 请留意代码注释, 当前 signal_t() 与系统 signal() 少许区别:
 * 1. signal_t() 安装信号 handler 后一直有效, 不会在每执行一次后恢复到系统默认.
 * 2. signal_t() 不阻塞额外的信号, 但阻塞当前信号.
 * 3. signal_t() 对 SIGALRM 有适当处理, 已便在安装此信号后, 受阻塞的系统调用能被该信号中断.
 * @param 
 * @return last handler function.
 */
void * signal_t(int signum, void (* handler)(int)) /* {{{ */
{
	struct sigaction act, act_old;

	act.sa_handler = handler;
	act.sa_flags = 0;
	/* 信号处理函数运行时, 不阻塞额外(除了被捕获的)信号 */
	sigemptyset(& act.sa_mask);
	/**
	 * handler 只使用 1 次后就恢复到系统默认值,
	 * 当信号处理函数运行时, 不阻塞该信号 (SA_NOMASK 的POSIX名字)
	 */
	/*act.sa_flags = SA_ONESHOT | SA_NOMASK;*/
	/*act.sa_flags = SA_RESETHAND | SA_NODEFER;*/	/* 同上, POSIX 规范定义 */

	if ( signum == SIGALRM ) {
#ifdef	SA_INTERRUPT
		act.sa_flags |= SA_INTERRUPT;
#endif
	} else {
#ifdef	SA_RESTART
		/* 重启被中断的调用 */
		act.sa_flags |= SA_RESTART;
#endif
	}

	if ( sigaction(signum, & act, & act_old) < 0 ) {
		return (SIG_ERR);
	}
	
	return (act_old.sa_handler);
} /* }}} */

/**
 * @fn
 * @brief 
 * @param 
 * @return 
 */
static void do_signal_alarm(int signo) /* {{{ */
{
	int s = 0;
	alarmid += 1;
	s = alarm(3);
	signal(SIGALRM, do_signal_alarm);
	app_debug(DINFO"do_signal_alarm(%d):, id:%d,last:%d", signo, alarmid, s);
	return ;
} /* }}} */

/* 测试 signal_t, signal 各是否可重入 */
int main(int argc, char * argv[])
{
	int i = 0, s = 0;

	if ( argc >= 2 ) {
		signal(SIGALRM, do_signal_alarm);
		app_debug(DINFO"signal():start, id:%d,last:%d", alarmid, s);
	} else {
		signal_t(SIGALRM, do_signal_alarm);
		app_debug(DINFO"signal_t():start, id:%d,last:%d", alarmid, s);
	}
	s = alarm(4);
	
	pause();
	app_debug(DINFO"pause():end id:%d,last:%d", alarmid, s);
	sleep(20);
	app_debug(DINFO"sleep():end, id:%d,last:%d", alarmid, s);

	return 0;
}
