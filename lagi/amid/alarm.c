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
 * @brief �źŶ�
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
 * @brief �� sigaction() ��д�˿ɿ��� signal() ����, 
 * ����� SIGALRM �ź��޸��˲����뱻�жϵĺ���.
 * @NOTE: ���������ע��, ��ǰ signal_t() ��ϵͳ signal() ��������:
 * 1. signal_t() ��װ�ź� handler ��һֱ��Ч, ������ÿִ��һ�κ�ָ���ϵͳĬ��.
 * 2. signal_t() ������������ź�, ��������ǰ�ź�.
 * 3. signal_t() �� SIGALRM ���ʵ�����, �ѱ��ڰ�װ���źź�, ��������ϵͳ�����ܱ����ź��ж�.
 * @param 
 * @return last handler function.
 */
int signal_t(int signum, void (* handler)(int)) /* {{{ */
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
 * @brief �ڵ�������֧�ֶ�� alarm() ��ʱ��.
 * alarm_id() ����ʱ, �� alarm_heap ���м��붨ʱʱ����źŴ�����;
 * ���� signal �а�װ�������źŴ��������� alarm_heap ��ȡ��ǰ���źŴ�����;
 * �����źŴ�������ÿִ��һ���źŴ����, �ٴ� alarm_heap ��Ѱ�Ҳ���װ
 * ��һ����ʱ��, ֱ��û����Ҫ��װ�Ķ�ʱ��.
 * @param 
 * @return 
 */
int alarm_id(unsigned int seconds, void (* func)(int)) /* {{{ */
{
	static uint32_t count = 0;
	static alarm_heap * alarm_head = NULL;
	unsigned int prev = 0;
	void (* prev_func) = NULL;

	/* ��һ����ʱ��ʣ�µ�����, ����еĻ� */
	prev = alarm(seconds);
	/**
	 * ������ڵ�ǰ�Ķ�ʱ��ʱ��, ���ȥ��ǰ��ʱ����ʱ��;
	 * ����װ��ǰ�Ķ�ʱ�źŴ����ܺ���.
	 * ���С�ڵ�ǰ��ʱ����ʱ��, ��ǰ��ʱ��ʱ���ȥ��һ��ʣ�µ�ʱ��;
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

