/* $Id: sched.h 7 2009-10-15 03:41:16Z hua $ */
#ifndef _LANGR_SCHED_H
#define _LANGR_SCHED_H

#define NR_TASKS	64		/* 系统最多任务数 */
#define HZ		100		/* 系统时钟滴答频率(100hz, 10ms) */

#define FIRST_TASK	task[0]			/* 任务 0 */
#define LAST_TASK	task[NR_TASKS - 1]	/* 最后一个任务 */

#endif	/* _LANGR_SCHED_H */
