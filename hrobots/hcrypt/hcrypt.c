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

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"
#include "ext/standard/file.h"

#include <stdio.h>
//#include <stdarg.h>
#include <string.h>
#include <sys/types.h>
//include <sys/stat.h>
//#include <unistd.h>

#include "php_hcrypt.h"

/* If you declare any globals in php_hcrypt.h uncomment this:
ZEND_DECLARE_MODULE_GLOBALS(hcrypt)
*/

/* True global resources - no need for thread safety here */
static int le_hcrypt;
/**
 * vc9 链接报错：
 * unresolved external symbol _zend_compile_file
 * unresolved external symbol _compiler_globals_id
 * vc9 项目->属性->配置属性->C/C++->预处理器->预处理器定义 去掉： /D "LIBZEND_EXPORTS"
 */
//BEGIN_EXTERN_C()
//ZEND_API zend_op_array *(*zend_compile_file)(zend_file_handle *file_handle, int type TSRMLS_DC);
//END_EXTERN_C()
zend_op_array * (*old_compile_file)(zend_file_handle * file_handle, int type TSRMLS_DC);
zend_op_array * hh_compile_file(zend_file_handle * file_handle, int type TSRMLS_DC);
/**
 * @fn
 * @brief 将加密文件解密，并返回解密后的临时文件
 * @param 加密的文件指针
 * @return 解密文件指针
 */
FILE * hh_ext_fopen(FILE * fp);

extern zend_module_entry hcrypt_module_entry;

/* {{{ hcrypt_functions[]
 *
 * Every user visible function must have an entry in hcrypt_functions[].
 */
const zend_function_entry hcrypt_functions[] = {
	PHP_FE(confirm_hcrypt_compiled,	NULL)		/* For testing, remove later. */
	PHP_FE_END	/* Must be the last line in hcrypt_functions[] */
};
/* }}} */

/* {{{ hcrypt_module_entry
 */
zend_module_entry hcrypt_module_entry = {
#if ZEND_MODULE_API_NO >= 20010901
	STANDARD_MODULE_HEADER,
#endif
	"hcrypt",
	hcrypt_functions,
	PHP_MINIT(hcrypt),
	PHP_MSHUTDOWN(hcrypt),
	PHP_RINIT(hcrypt),		/* Replace with NULL if there's nothing to do at request start */
	PHP_RSHUTDOWN(hcrypt),	/* Replace with NULL if there's nothing to do at request end */
	PHP_MINFO(hcrypt),
#if ZEND_MODULE_API_NO >= 20010901
	"0.1", /* Replace with version number for your extension */
#endif
	STANDARD_MODULE_PROPERTIES
};
/* }}} */

#ifdef COMPILE_DL_HCRYPT
ZEND_GET_MODULE(hcrypt)
#endif

/* {{{ PHP_INI
 */
/* Remove comments and fill if you need to have entries in php.ini
PHP_INI_BEGIN()
    STD_PHP_INI_ENTRY("hcrypt.global_value",      "42", PHP_INI_ALL, OnUpdateLong, global_value, zend_hcrypt_globals, hcrypt_globals)
    STD_PHP_INI_ENTRY("hcrypt.global_string", "foobar", PHP_INI_ALL, OnUpdateString, global_string, zend_hcrypt_globals, hcrypt_globals)
PHP_INI_END()
*/
/* }}} */

/* {{{ php_hcrypt_init_globals
 */
/* Uncomment this function if you have INI entries
static void php_hcrypt_init_globals(zend_hcrypt_globals *hcrypt_globals)
{
	hcrypt_globals->global_value = 0;
	hcrypt_globals->global_string = NULL;
}
*/
/* }}} */

/* {{{ PHP_MINIT_FUNCTION
 */
PHP_MINIT_FUNCTION(hcrypt)
{
	/* If you have INI entries, uncomment these lines 
	REGISTER_INI_ENTRIES();
	*/
	//CG(extended_info) = 1;
	CG(compiler_options) |= ZEND_COMPILE_EXTENDED_INFO;

	old_compile_file = zend_compile_file;
	zend_compile_file = hh_compile_file;
	return SUCCESS;
}
/* }}} */

/* {{{ PHP_MSHUTDOWN_FUNCTION
 */
PHP_MSHUTDOWN_FUNCTION(hcrypt)
{
	/* uncomment this line if you have INI entries
	UNREGISTER_INI_ENTRIES();
	*/
	//CG(extended_info) = 1;
	CG(compiler_options) |= ZEND_COMPILE_EXTENDED_INFO;
	zend_compile_file = old_compile_file;
	return SUCCESS;
}
/* }}} */

/* Remove if there's nothing to do at request start */
/* {{{ PHP_RINIT_FUNCTION
 */
PHP_RINIT_FUNCTION(hcrypt)
{
	return SUCCESS;
}
/* }}} */

/* Remove if there's nothing to do at request end */
/* {{{ PHP_RSHUTDOWN_FUNCTION
 */
PHP_RSHUTDOWN_FUNCTION(hcrypt)
{
	return SUCCESS;
}
/* }}} */

/* {{{ PHP_MINFO_FUNCTION
 */
PHP_MINFO_FUNCTION(hcrypt)
{
	php_info_print_table_start();
	php_info_print_table_header(2, "hcrypt support", "enabled");
	php_info_print_table_row(2, "version", "v"VERSION_STRING);
	php_info_print_table_row(2, "author", "langr<hua@langr.org>");
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
/* {{{ proto string confirm_hcrypt_compiled(string arg)
   Return a string to confirm that the module is compiled in */
PHP_FUNCTION(confirm_hcrypt_compiled)
{
	char *arg = NULL;
	int arg_len, len;
	char *strg;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &arg, &arg_len) == FAILURE) {
		return;
	}

	len = spprintf(&strg, 0, "Congratulations! hcrypt. version %.78s is now compiled into PHP.", VERSION_STRING);
	RETURN_STRINGL(strg, len, 0);
}
/* }}} */

FILE *hh_ext_fopen(FILE *fp)
{
	struct	stat	stat_buf;
	char	*datap, *newdatap;
	int	datalen, newdatalen;
	int	cryptkey_len = sizeof(hh_cryptkey) / 2;
	int	i;

	/* fp 当前读指针已经指向 0 + H2SCREW_LEN */
	fstat(_fileno(fp), &stat_buf);
	datalen = stat_buf.st_size - H2SCREW_LEN;
	datap = (char*) malloc(datalen);
	fread(datap, datalen, 1, fp);
	fclose(fp);

	/* 取反，与密钥某位异或 */
	for(i=0; i<datalen; i++) {
		datap[i] = (char) hh_cryptkey[(datalen - i) % cryptkey_len] ^ (~(datap[i]));
	}

	/* 压缩或做其他处理 */
	newdatap = zdecode(datap, datalen, &newdatalen);

	/* 解密后的代码写入临时文件，并返回临时文件指针 */
	fp = tmpfile();
	fwrite(newdatap, newdatalen, 1, fp);

	free(datap);

	rewind(fp);
	return fp;
}

zend_op_array *hh_compile_file(zend_file_handle *file_handle, int type TSRMLS_DC)
{
	FILE	*fp;
	char	buf[H2SCREW_LEN + 1];
	char	fname[32];

	//app_debug(DINFO"lcrypt start: opened_path:%s, filename:%s", file_handle->opened_path, file_handle->filename);
	//php_printf("<!-- hcrypt: filename:%s -->\r\n", file_handle->filename);
	memset(fname, 0, sizeof(fname));
	if (zend_is_executing(TSRMLS_C)) {
		if (get_active_function_name(TSRMLS_C)) {
			strncpy(fname, get_active_function_name(TSRMLS_C), sizeof(fname) - 2);
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
		return old_compile_file(file_handle, type TSRMLS_CC);
	}

	/* 检查标识头是否为加密过的文件，并去掉标识头，如果不是则转由原zend处理 */
	fread(buf, H2SCREW_LEN, 1, fp);
	if (memcmp(buf, H2SCREW, H2SCREW_LEN) != 0) {
		fclose(fp);
		return old_compile_file(file_handle, type TSRMLS_CC);
	}

	/* 如果文件之前已经打开了，则先关闭 */
	if (file_handle->type == ZEND_HANDLE_FP) { fclose(file_handle->handle.fp); }
	if (file_handle->type == ZEND_HANDLE_FD) { _close(file_handle->handle.fd); }
	/* 将解密后的文件指针传给zend_compile_file 并执行 */
	file_handle->handle.fp = hh_ext_fopen(fp);
	file_handle->type = ZEND_HANDLE_FP;
	file_handle->opened_path = expand_filepath(file_handle->filename, NULL TSRMLS_CC);

	return old_compile_file(file_handle, type TSRMLS_CC);
}

/*
 * Local variables:
 * tab-width: 8
 * c-basic-offset: 8
 * End:
 * vim600: noet sw=8 ts=8 fdm=marker
 * vim<600: noet sw=8 ts=8
 */
