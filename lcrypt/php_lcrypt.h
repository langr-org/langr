/*
  +----------------------------------------------------------------------+
  | PHP Version 5                                                        |
  +----------------------------------------------------------------------+
  | Copyright (c) 1997-2006 The PHP Group                                |
  +----------------------------------------------------------------------+
  | This source file is subject to version 3.01 of the PHP license,      |
  | that is bundled with this package in the file LICENSE, and is        |
  | available through the world-wide-web at the following url:           |
  | http://www.php.net/license/3_01.txt                                  |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to          |
  | license@php.net so we can mail you a copy immediately.               |
  +----------------------------------------------------------------------+
  | Author:                                                              |
  +----------------------------------------------------------------------+
*/

/* $Id: php_lcrypt.h 6 2011-11-18 10:48:33Z loghua@gmail.com $ */

#ifndef PHP_LCRYPT_H
#define PHP_LCRYPT_H

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"
#include "ext/standard/file.h"

#define	PROGRAM_NAME		"lcrypt"
#define	VERSION_STRING		"0.3.9"
#define	LAST_COMPILE_TIME	"2011-11-14 18:10:09"
#include "debug.h"

extern zend_module_entry lcrypt_module_entry;
#define phpext_lcrypt_ptr &lcrypt_module_entry

#ifdef PHP_WIN32
#define PHP_LCRYPT_API __declspec(dllexport)
#else
#define PHP_LCRYPT_API
#endif

#ifdef ZTS
#include "TSRM.h"
#endif

PHP_MINIT_FUNCTION(lcrypt);
PHP_MSHUTDOWN_FUNCTION(lcrypt);
PHP_RINIT_FUNCTION(lcrypt);
PHP_RSHUTDOWN_FUNCTION(lcrypt);
PHP_MINFO_FUNCTION(lcrypt);

PHP_FUNCTION(confirm_lcrypt_compiled);	/* For testing, remove later. */

/* 
  	Declare any global variables you may need between the BEGIN
	and END macros here:     

ZEND_BEGIN_MODULE_GLOBALS(lcrypt)
	long  global_value;
	char *global_string;
ZEND_END_MODULE_GLOBALS(lcrypt)
*/

/* In every utility function you add that needs to use variables 
   in php_lcrypt_globals, call TSRMLS_FETCH(); after declaring other 
   variables used by that function, or better yet, pass in TSRMLS_CC
   after the last function argument and declare your utility function
   with TSRMLS_DC after the last declared argument.  Always refer to
   the globals in your function as LCRYPT_G(variable).  You are 
   encouraged to rename these macros something shorter, see
   examples in any other php module directory.
*/

#ifdef ZTS
#define LCRYPT_G(v) TSRMG(lcrypt_globals_id, zend_lcrypt_globals *, v)
#else
#define LCRYPT_G(v) (lcrypt_globals.v)
#endif

#endif	/* PHP_LCRYPT_H */

