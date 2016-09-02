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
 * $Id: alarm.c 7 2011-11-21 07:32:57Z loghua@gmail.com $
 */

#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <signal.h>

/**
 * @struct alarm_heap
 * @brief 信号堆
 */
struct alarm_heap {
	uint32_t id;
	uint32_t time;
	void (* func)(int);
	struct alarm_heap * next;
};
typedef struct alarm_heap alarm_heap;

void (* do_signal_alarm_old)(int signo);
void (* do_signal_alarm_new)(int signo);

alarm_heap * alarm_insert(alarm_heap * alarm_head, alarm_heap * alarm_id);

alarm_heap * alarm_insert(alarm_heap * alarm_head, alarm_heap * alarm_id) /* {{{ */
{
	;
} /* }}} */

alarm_heap * alarm_delete(alarm_heap * alarm_head, alarm_heap * alarm_id) /* {{{ */
{
	;
} /* }}} */

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
int signal_t(int signum, void (* handler)(int)) /* {{{ */
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
 * @brief 在单进程中支持多个 alarm() 定时器.
 * alarm_id() 调用时, 向 alarm_heap 堆中加入定时时间和信号处理函数;
 * 并在 signal 中安装公共的信号处理函数来从 alarm_heap 中取当前的信号处理函数;
 * 公共信号处理函数在每执行一个信号处理后, 再从 alarm_heap 中寻找并安装
 * 下一个定时器, 直到没有需要安装的定时器.
 * @param 
 * @return 
 */
int alarm_id(unsigned int seconds, void (* func)(int)) /* {{{ */
{
	static uint32_t count = 0;
	static alarm_heap * alarm_head = NULL;
	unsigned int prev = 0;
	void (* prev_func) = NULL;

	/* 上一个定时器剩下的秒数, 如果有的话 */
	prev = alarm(seconds);
	/**
	 * 如果大于当前的定时器时间, 则减去当前定时器的时间;
	 * 并安装当前的定时信号处理功能函数.
	 * 如果小于当前定时器的时间, 则当前定时器时间减去上一个剩下的时间;
	 */
	if ( prev > seconds ) {
		prev -= seconds;
	} else if ( prev > 0 ) {
		seconds -= prev;
	}
} /* }}} */

/**
 * @fn
 * @brief 
 * @param 
 * @return 
 */
static void do_signal_alarm(int signo) /* {{{ */
{
	return ;
} /* }}} */

