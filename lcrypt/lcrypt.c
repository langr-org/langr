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

/* $Id: lcrypt.c 3 2011-11-14 10:33:31Z loghua@gmail.com $ */

#include "php_lcrypt.h"
#include "lcrypt.h"

/* If you declare any globals in php_lcrypt.h uncomment this:
ZEND_DECLARE_MODULE_GLOBALS(lcrypt)
*/

/* True global resources - no need for thread safety here */
static int le_lcrypt;
ZEND_API zend_op_array * (*org_compile_file)(zend_file_handle * file_handle, int type TSRMLS_DC);
ZEND_API zend_op_array * pm9screw_compile_file(zend_file_handle * file_handle, int type TSRMLS_DC);
FILE * pm9screw_ext_fopen(FILE * fp);

/* {{{ lcrypt_functions[]
 *
 * Every user visible function must have an entry in lcrypt_functions[].
 */
zend_function_entry lcrypt_functions[] = {
	PHP_FE(confirm_lcrypt_compiled,	NULL)		/* For testing, remove later. */
	{NULL, NULL, NULL}	/* Must be the last line in lcrypt_functions[] */
};
/* }}} */

/* {{{ lcrypt_module_entry
 */
zend_module_entry lcrypt_module_entry = {
#if ZEND_MODULE_API_NO >= 20010901
	STANDARD_MODULE_HEADER,
#endif
	"lcrypt",
	lcrypt_functions,
	PHP_MINIT(lcrypt),
	PHP_MSHUTDOWN(lcrypt),
	PHP_RINIT(lcrypt),		/* Replace with NULL if there's nothing to do at request start */
	PHP_RSHUTDOWN(lcrypt),	/* Replace with NULL if there's nothing to do at request end */
	PHP_MINFO(lcrypt),
#if ZEND_MODULE_API_NO >= 20010901
	"0.1", /* Replace with version number for your extension */
#endif
	STANDARD_MODULE_PROPERTIES
};
/* }}} */

#ifdef COMPILE_DL_LCRYPT
ZEND_GET_MODULE(lcrypt)
#endif

/* {{{ PHP_INI
 */
/* Remove comments and fill if you need to have entries in php.ini
PHP_INI_BEGIN()
    STD_PHP_INI_ENTRY("lcrypt.global_value",      "42", PHP_INI_ALL, OnUpdateLong, global_value, zend_lcrypt_globals, lcrypt_globals)
    STD_PHP_INI_ENTRY("lcrypt.global_string", "foobar", PHP_INI_ALL, OnUpdateString, global_string, zend_lcrypt_globals, lcrypt_globals)
PHP_INI_END()
*/
/* }}} */

/* {{{ php_lcrypt_init_globals
 */
/* Uncomment this function if you have INI entries
static void php_lcrypt_init_globals(zend_lcrypt_globals *lcrypt_globals)
{
	lcrypt_globals->global_value = 0;
	lcrypt_globals->global_string = NULL;
}
*/
/* }}} */

/* {{{ PHP_MINIT_FUNCTION
 */
PHP_MINIT_FUNCTION(lcrypt)
{
	CG(extended_info) = 1;

	org_compile_file = zend_compile_file;
	zend_compile_file = pm9screw_compile_file;
	return SUCCESS;
}
/* }}} */

/* {{{ PHP_MSHUTDOWN_FUNCTION
 */
PHP_MSHUTDOWN_FUNCTION(lcrypt)
{
	CG(extended_info) = 1;
	zend_compile_file = org_compile_file;
	return SUCCESS;
}
/* }}} */

/* Remove if there's nothing to do at request start */
/* {{{ PHP_RINIT_FUNCTION
 */
PHP_RINIT_FUNCTION(lcrypt)
{
	return SUCCESS;
}
/* }}} */

/* Remove if there's nothing to do at request end */
/* {{{ PHP_RSHUTDOWN_FUNCTION
 */
PHP_RSHUTDOWN_FUNCTION(lcrypt)
{
	return SUCCESS;
}
/* }}} */

/* {{{ PHP_MINFO_FUNCTION
 */
PHP_MINFO_FUNCTION(lcrypt)
{
	php_info_print_table_start();
	php_info_print_table_header(2, "lcrypt support", "enabled");
	php_info_print_table_end();

	/* Remove comments if you have entries in php.ini
	DISPLAY_INI_ENTRIES();
	*/
}
/* }}} */


/* Remove the following function when you have succesfully modified config.m4
   so that your module can be compiled into PHP, it exists only for testing
   purposes. */

/* Every user-visible function in PHP should document itself in the source */
/* {{{ proto string confirm_lcrypt_compiled(string arg)
   Return a string to confirm that the module is compiled in */
PHP_FUNCTION(confirm_lcrypt_compiled)
{
	char *arg = NULL;
	int arg_len, len;
	char string[256];

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &arg, &arg_len) == FAILURE) {
		return;
	}

	len = sprintf(string, "Congratulations! You have successfully modified ext/%.78s/config.m4. Module %.78s is now compiled into PHP.", "lcrypt", arg);
	RETURN_STRINGL(string, len, 1);
}
/* }}} */

FILE *pm9screw_ext_fopen(FILE *fp)
{
	struct	stat	stat_buf;
	char	*datap, *newdatap;
	int	datalen, newdatalen;
	int	cryptkey_len = sizeof pm9screw_mycryptkey / 2;
	int	i;

	/* fp 当前读指针已经指向 0 + PM9SCREW_LEN */
	fstat(fileno(fp), &stat_buf);
	datalen = stat_buf.st_size - PM9SCREW_LEN;
	datap = (char*)malloc(datalen);
	fread(datap, datalen, 1, fp);
	fclose(fp);

	/* 取反，与密钥某位异或 */
	for(i=0; i<datalen; i++) {
		datap[i] = (char)pm9screw_mycryptkey[(datalen - i) % cryptkey_len] ^ (~(datap[i]));
	}

	/* 解密？有没有那回事哦 */
	newdatap = zdecode(datap, datalen, &newdatalen);

	/* 解密后的代码写入临时文件，并返回临时文件指针 */
	fp = tmpfile();
	fwrite(newdatap, newdatalen, 1, fp);

	free(datap);
	free(newdatap);

	rewind(fp);
	return fp;
}

ZEND_API zend_op_array *pm9screw_compile_file(zend_file_handle *file_handle, int type TSRMLS_DC)
{
	FILE	*fp;
	char	buf[PM9SCREW_LEN + 1];
	char	fname[32];

	app_debug(DINFO"lcrypt start: opened_path:%s, filename:%s", file_handle->opened_path, file_handle->filename);
	php_printf("<!-- lcrypt: filename:%s -->\r\n", file_handle->filename);
	memset(fname, 0, sizeof fname);
	if (zend_is_executing(TSRMLS_C)) {
		if (get_active_function_name(TSRMLS_C)) {
			strncpy(fname, get_active_function_name(TSRMLS_C), sizeof fname - 2);
		}
	}
	if (fname[0]) {
		if ( strcasecmp(fname, "show_source") == 0
		  || strcasecmp(fname, "highlight_file") == 0) {
			return NULL;
		}
	}

	/* 打开要编译执行的文件，失败就调用原zend编译处理函数 */
	fp = fopen(file_handle->filename, "r");
	if (!fp) {
		return org_compile_file(file_handle, type TSRMLS_CC);
	}

	/* 检查标识头是否为加密过的文件，并去掉标识头，如果不是则转由原zend处理 */
	fread(buf, PM9SCREW_LEN, 1, fp);
	if (memcmp(buf, PM9SCREW, PM9SCREW_LEN) != 0) {
		fclose(fp);
		return org_compile_file(file_handle, type TSRMLS_CC);
	}

	/*  */
	if (file_handle->type == ZEND_HANDLE_FP) fclose(file_handle->handle.fp);
	if (file_handle->type == ZEND_HANDLE_FD) close(file_handle->handle.fd);
	file_handle->handle.fp = pm9screw_ext_fopen(fp);
	file_handle->type = ZEND_HANDLE_FP;
	file_handle->opened_path = expand_filepath(file_handle->filename, NULL TSRMLS_CC);

	app_debug(DINFO"lcrypt end: opened_path:%s, filename:%s", file_handle->opened_path, file_handle->filename);
	php_printf("<!-- lcrypt ok: filename:%s -->\r\n", file_handle->filename);
	return org_compile_file(file_handle, type TSRMLS_CC);
}

