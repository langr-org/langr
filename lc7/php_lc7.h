/*
  +----------------------------------------------------------------------+
  | PHP Version 7                                                        |
  +----------------------------------------------------------------------+
  | Copyright (c) 1997-2017 The PHP Group                                |
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

#ifndef PHP_LC7_H
#define PHP_LC7_H

#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"
#include "ext/standard/file.h"

extern zend_module_entry lc7_module_entry;
#define phpext_lc7_ptr &lc7_module_entry

#define PHP_LC7_VERSION "0.3.15 (45)" /* Replace with version number for your extension */
#define LAST_COMPILE_TIME "2018-11-10 16:18:20"

static short lcrypt_key[] = {1102, 2018, 1701, 3128, 5893};

#define HY_CRYPT        "HY\t"
#define HY_CRYPT_LEN     3

#ifdef PHP_WIN32
#	define PHP_LC7_API __declspec(dllexport)
#elif defined(__GNUC__) && __GNUC__ >= 4
#	define PHP_LC7_API __attribute__ ((visibility("default")))
#else
#	define PHP_LC7_API
#endif

#ifdef ZTS
#include "TSRM.h"
#endif

/*
  	Declare any global variables you may need between the BEGIN
	and END macros here:
*/
ZEND_BEGIN_MODULE_GLOBALS(lc7)
	char *sn_key;
ZEND_END_MODULE_GLOBALS(lc7)

/* Always refer to the globals in your function as LC7_G(variable).
   You are encouraged to rename these macros something shorter, see
   examples in any other php module directory.
*/
#define LC7_G(v) ZEND_MODULE_GLOBALS_ACCESSOR(lc7, v)

#if defined(ZTS) && defined(COMPILE_DL_LC7)
ZEND_TSRMLS_CACHE_EXTERN()
#endif

#endif	/* PHP_LC7_H */


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
