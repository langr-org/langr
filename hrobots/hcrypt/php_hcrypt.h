/*
  +----------------------------------------------------------------------+
  | PHP Version 5                                                        |
  +----------------------------------------------------------------------+
  | Copyright (c) 1997-2013 The PHP Group                                |
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

/* $Id$ */

#ifndef PHP_HCRYPT_H
#define PHP_HCRYPT_H

#ifdef	__cplusplus
extern "C" {
#endif
	
#ifdef	__cplusplus
}
#endif

/* */
#define	PROGRAM_NAME		"hrebots"
#define	VERSION_STRING		"0.4"
#define	LAST_COMPILE_TIME	"2014-06-18 15:48:09"

/* pwd */
static short hh_cryptkey[] = {8508, 2014, 1058, 650, 7239, 9504, 27859, 38594};

/* 标示头 */
#define H2SCREW        "HQKG<hua@langr.org>"
#define H2SCREW_LEN     19

char * zcodecom(int mode, char * inbuf, int inbuf_len, int * resultbuf_len);
char * zencode(char * inbuf, int inbuf_len, int * resultbuf_len);
char * zdecode(char * inbuf, int inbuf_len, int * resultbuf_len);
/* */

#define phpext_hcrypt_ptr &hcrypt_module_entry

#ifdef PHP_WIN32
#	define PHP_HCRYPT_API __declspec(dllexport)
#elif defined(__GNUC__) && __GNUC__ >= 4
#	define PHP_HCRYPT_API __attribute__ ((visibility("default")))
#else
#	define PHP_HCRYPT_API
#endif

#ifdef ZTS
#include "TSRM.h"
#endif

PHP_MINIT_FUNCTION(hcrypt);
PHP_MSHUTDOWN_FUNCTION(hcrypt);
PHP_RINIT_FUNCTION(hcrypt);
PHP_RSHUTDOWN_FUNCTION(hcrypt);
PHP_MINFO_FUNCTION(hcrypt);

PHP_FUNCTION(confirm_hcrypt_compiled);	/* For testing, remove later. */

/* 
  	Declare any global variables you may need between the BEGIN
	and END macros here:     

ZEND_BEGIN_MODULE_GLOBALS(hcrypt)
	long  global_value;
	char *global_string;
ZEND_END_MODULE_GLOBALS(hcrypt)
*/

/* In every utility function you add that needs to use variables 
   in php_hcrypt_globals, call TSRMLS_FETCH(); after declaring other 
   variables used by that function, or better yet, pass in TSRMLS_CC
   after the last function argument and declare your utility function
   with TSRMLS_DC after the last declared argument.  Always refer to
   the globals in your function as HCRYPT_G(variable).  You are 
   encouraged to rename these macros something shorter, see
   examples in any other php module directory.
*/

#ifdef ZTS
#define HCRYPT_G(v) TSRMG(hcrypt_globals_id, zend_hcrypt_globals *, v)
#else
#define HCRYPT_G(v) (hcrypt_globals.v)
#endif

#endif	/* PHP_HCRYPT_H */

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
