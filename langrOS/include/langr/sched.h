/* $Id: sched.h 7 2009-10-15 03:41:16Z hua $ */
#ifndef _LANGR_SCHED_H
#define _LANGR_SCHED_H

#define NR_TASKS	64		/* ϵͳ��������� */
#define HZ		100		/* ϵͳʱ�ӵδ�Ƶ��(100hz, 10ms) */

#define FIRST_TASK	task[0]			/* ���� 0 */
#define LAST_TASK	task[NR_TASKS - 1]	/* ���һ������ */

#endif	/* _LANGR_SCHED_H */
