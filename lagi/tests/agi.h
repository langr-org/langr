/** 
 * @file agi.h
 * @brief lagi asterisk gateway interface
 * 
 * Copyright (C) 2011 LangR.Org
 * 
 * This is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 * 
 * @package tests
 * @author Langr <hua@langr.org> 2011/11/08 18:27
 * 
 * $Id: agi.h 4 2011-11-10 00:57:06Z loghua@gmail.com $
 */

#ifndef _AGI_H
#define _AGI_H

#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <sys/types.h>
#include <errno.h>

#ifndef LAGI_API
 #define LAGI_API	
#endif

#define AGI_RES_OK		200

#define AST_STATE_DOWN		0
#define AST_STATE_RESERVED	1
#define AST_STATE_OFFHOOK	2
#define AST_STATE_DIALING	3
#define AST_STATE_RING		4
#define AST_STATE_RINGING	5
#define AST_STATE_UP		6
#define AST_STATE_BUSY		7
#define AST_STATE_DIALING_OFFHOOK	8
#define AST_STATE_PRERING	9

#define AST_CONFIG_DIR		"/etc/asterisk"
#define DEFAULT_PHPAGI_CONFIG	"/phpagi.conf"
#define AST_SPOOL_DIR		"/var/spool/asterisk/"
#define AST_TMP_DIR		AST_SPOOL_DIR"/tmp/"

LAGI_API int lagi_agi_command(int fileno, const char * cmd);

#ifdef __cplusplus
extern "C" {
#endif

#ifdef __cplusplus
}
#endif

#endif /* end _AGI_H */
