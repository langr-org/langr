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
 * @brief �� sigaction() ��д�˿ɿ��� signal() ����, 
 * ����� SIGALRM �ź��޸��˲����뱻�жϵĺ���.
 * @NOTE: ���������ע��, ��ǰ signal_t() ��ϵͳ signal() ��������:
 * 1. signal_t() ��װ�ź� handler ��һֱ��Ч, ������ÿִ��һ�κ�ָ���ϵͳĬ��.
 * 2. signal_t() ������������ź�, ��������ǰ�ź�.
 * 3. signal_t() �� SIGALRM ���ʵ�����, �ѱ��ڰ�װ���źź�, ��������ϵͳ�����ܱ����ź��ж�.
 * @param 
 * @return last handler function.
 */
void * signal_t(int signum, void (* handler)(int)) /* {{{ */
{
	struct sigaction act, act_old;

	act.sa_handler = handler;
	act.sa_flags = 0;
	/* �źŴ���������ʱ, ����������(���˱������)�ź� */
	sigemptyset(& act.sa_mask);
	/**
	 * handler ֻʹ�� 1 �κ�ͻָ���ϵͳĬ��ֵ,
	 * ���źŴ���������ʱ, ���������ź� (SA_NOMASK ��POSIX����)
	 */
	/*act.sa_flags = SA_ONESHOT | SA_NOMASK;*/
	/*act.sa_flags = SA_RESETHAND | SA_NODEFER;*/	/* ͬ��, POSIX �淶���� */

	if ( signum == SIGALRM ) {
#ifdef	SA_INTERRUPT
		act.sa_flags |= SA_INTERRUPT;
#endif
	} else {
#ifdef	SA_RESTART
		/* �������жϵĵ��� */
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

/* ���� signal_t, signal ���Ƿ������ */
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
