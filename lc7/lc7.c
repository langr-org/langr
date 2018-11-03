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

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"
#include "php_lc7.h"

/* If you declare any globals in php_lc7.h uncomment this:
ZEND_DECLARE_MODULE_GLOBALS(lc7)
*/

/* True global resources - no need for thread safety here */
static int le_lc7;
zend_op_array * (*org_compile_file)(zend_file_handle * file_handle, int type);
zend_op_array * lcrypt_compile_file(zend_file_handle * file_handle, int type);
FILE * lcrypt_ext_fopen(FILE * fp);

/**
 * ext_fopen
 */
FILE *lcrypt_ext_fopen(FILE *fp)
{
	struct	stat stat_buf;
	char	*datap;
	//char	*newdatap;
	int	datalen;
	//int newdatalen;
	int	cryptkey_len = sizeof(lcrypt_key) / 2;
	int	i;

	/* fp 当前读指针已经指向 0 + HY_CRYPT_LEN */
	fstat(fileno(fp), & stat_buf);
	datalen = stat_buf.st_size - HY_CRYPT_LEN;
	datap = (char *) malloc(datalen);
	fread(datap, datalen, 1, fp);
	fclose(fp);

	/* */
	for(i = 0; i < datalen; i++) {
		datap[i] = (char) lcrypt_key[(datalen - i) % cryptkey_len] ^ (~(datap[i]));
	}

	/* 解 */
	//newdatap = zdecode(datap, datalen, &newdatalen);

	/* 解密后的代码写入临时文件，并返回临时文件指针 */
	fp = tmpfile();
	//fwrite(newdatap, newdatalen, 1, fp);
	fwrite(datap, datalen, 1, fp);

	free(datap);
	//free(newdatap);

	rewind(fp);
	return fp;
}

/**
 * compile
 */
zend_op_array *lcrypt_compile_file(zend_file_handle *file_handle, int type)
{
	FILE	*fp;
	char	buf[HY_CRYPT_LEN + 1];
	char	fname[32];

	//php_printf("<!-- lc7: filename:%s fp:%s-->\r\n", file_handle->filename, file_handle->opened_path);

	/* 打开要编译执行的文件，失败就调用原zend编译处理函数 */
	fp = fopen(file_handle->filename, "r");
	if (!fp) {
		return org_compile_file(file_handle, type);
	}

	/* 检查标识头是否为加密过的文件，并去掉标识头，如果不是则转由原zend处理 */
	fread(buf, HY_CRYPT_LEN, 1, fp);
	if (memcmp(buf, HY_CRYPT, HY_CRYPT_LEN) != 0) {
		fclose(fp);
		return org_compile_file(file_handle, type);
	}

	/*  */
	if (file_handle->type == ZEND_HANDLE_FP) {
		fclose(file_handle->handle.fp);
	}
	if (file_handle->type == ZEND_HANDLE_FD) {
		close(file_handle->handle.fd);
	}
	file_handle->handle.fp = lcrypt_ext_fopen(fp);
	file_handle->type = ZEND_HANDLE_FP;
	//file_handle->opened_path = expand_filepath(file_handle->filename, NULL);

	//app_debug(DINFO"lc7 end: opened_path:%s, filename:%s", file_handle->opened_path, file_handle->filename);
	//php_printf("<!-- lc7 ok: filename:%s -->\r\n", file_handle->filename);
	return org_compile_file(file_handle, type);
}

/* {{{ PHP_INI
 */
/* Remove comments and fill if you need to have entries in php.ini
PHP_INI_BEGIN()
    STD_PHP_INI_ENTRY("lc7.global_value",      "42", PHP_INI_ALL, OnUpdateLong, global_value, zend_lc7_globals, lc7_globals)
    STD_PHP_INI_ENTRY("lc7.global_string", "foobar", PHP_INI_ALL, OnUpdateString, global_string, zend_lc7_globals, lc7_globals)
PHP_INI_END()
*/
/* }}} */

/* Remove the following function when you have successfully modified config.m4
   so that your module can be compiled into PHP, it exists only for testing
   purposes. */

/* Every user-visible function in PHP should document itself in the source */
/* {{{ proto string confirm_lc7_compiled(string arg)
   Return a string to confirm that the module is compiled in */
PHP_FUNCTION(confirm_lc7_compiled)
{
	char *arg = NULL;
	size_t arg_len, len;
	zend_string *strg;

	if (zend_parse_parameters(ZEND_NUM_ARGS(), "s", &arg, &arg_len) == FAILURE) {
		return;
	}

	strg = strpprintf(0, "Congratulations! You have successfully modified ext/%.78s/config.m4. Module %.78s is now compiled into PHP.", "lc7", arg);

	RETURN_STR(strg);
}
/* }}} */
/* The previous line is meant for vim and emacs, so it can correctly fold and
   unfold functions in source code. See the corresponding marks just before
   function definition, where the functions purpose is also documented. Please
   follow this convention for the convenience of others editing your code.
*/


/* {{{ php_lc7_init_globals
 */
/* Uncomment this function if you have INI entries
static void php_lc7_init_globals(zend_lc7_globals *lc7_globals)
{
	lc7_globals->global_value = 0;
	lc7_globals->global_string = NULL;
}
*/
/* }}} */

/* {{{ PHP_MINIT_FUNCTION
 */
PHP_MINIT_FUNCTION(lc7)
{
	/* If you have INI entries, uncomment these lines
	REGISTER_INI_ENTRIES();
	*/
	org_compile_file = zend_compile_file;
	zend_compile_file = lcrypt_compile_file;
	return SUCCESS;
}
/* }}} */

/* {{{ PHP_MSHUTDOWN_FUNCTION
 */
PHP_MSHUTDOWN_FUNCTION(lc7)
{
	/* uncomment this line if you have INI entries
	UNREGISTER_INI_ENTRIES();
	*/
	zend_compile_file = org_compile_file;
	return SUCCESS;
}
/* }}} */

/* Remove if there's nothing to do at request start */
/* {{{ PHP_RINIT_FUNCTION
 */
PHP_RINIT_FUNCTION(lc7)
{
#if defined(COMPILE_DL_LC7) && defined(ZTS)
	ZEND_TSRMLS_CACHE_UPDATE();
#endif
	return SUCCESS;
}
/* }}} */

/* Remove if there's nothing to do at request end */
/* {{{ PHP_RSHUTDOWN_FUNCTION
 */
PHP_RSHUTDOWN_FUNCTION(lc7)
{
	return SUCCESS;
}
/* }}} */

/* {{{ PHP_MINFO_FUNCTION
 */
PHP_MINFO_FUNCTION(lc7)
{
	php_info_print_table_start();
	php_info_print_table_header(2, "lc7 support", "enabled");
	php_info_print_table_row(2, "lc7 version", PHP_LC7_VERSION);
	php_info_print_table_end();

	/* Remove comments if you have entries in php.ini
	DISPLAY_INI_ENTRIES();
	*/
}
/* }}} */

/* {{{ lc7_functions[]
 *
 * Every user visible function must have an entry in lc7_functions[].
 */
const zend_function_entry lc7_functions[] = {
	PHP_FE(confirm_lc7_compiled,	NULL)		/* For testing, remove later. */
	PHP_FE_END	/* Must be the last line in lc7_functions[] */
};
/* }}} */

/* {{{ lc7_module_entry
 */
zend_module_entry lc7_module_entry = {
	STANDARD_MODULE_HEADER,
	"lc7",
	lc7_functions,
	PHP_MINIT(lc7),
	PHP_MSHUTDOWN(lc7),
	PHP_RINIT(lc7),		/* Replace with NULL if there's nothing to do at request start */
	PHP_RSHUTDOWN(lc7),	/* Replace with NULL if there's nothing to do at request end */
	PHP_MINFO(lc7),
	PHP_LC7_VERSION,
	STANDARD_MODULE_PROPERTIES
};
/* }}} */

#ifdef COMPILE_DL_LC7
#ifdef ZTS
ZEND_TSRMLS_CACHE_DEFINE()
#endif
ZEND_GET_MODULE(lc7)
#endif

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
