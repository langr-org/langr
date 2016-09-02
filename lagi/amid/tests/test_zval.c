/** 
 * @file test_zval.c
 * @brief 
 * 
 * Copyright (C) 2011 LangR.Org
 * 
 * This is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 * 
 * @package amid
 * @author Langr <hua@langr.org> 2011/12/19 18:01
 * 
 * $Id: test_zval.c 28 2011-12-29 06:29:16Z loghua@gmail.com $
 */

#include "test_zval.h"

void _zval_ptr_dtor(zval **zval_ptr)
{
	(*zval_ptr)->refcount--;
	if ((*zval_ptr)->refcount==0) {
		zval_dtor(*zval_ptr);
		safe_free_zval_ptr_rel(*zval_ptr);
	} else if ((*zval_ptr)->refcount == 1) {
		(*zval_ptr)->is_ref = 0;
	}
}

void _zval_internal_ptr_dtor(zval **zval_ptr)
{
	(*zval_ptr)->refcount--;
	if ((*zval_ptr)->refcount==0) {
		zval_internal_dtor(*zval_ptr);
		free(*zval_ptr);
	} else if ((*zval_ptr)->refcount == 1) {
		(*zval_ptr)->is_ref = 0;
	}
}

void _zval_dtor_func(zval *zvalue)
{
	switch (zvalue->type & ~IS_CONSTANT_INDEX) {
		case IS_STRING:
		case IS_CONSTANT:
			STR_FREE_REL(zvalue->value.str.val);
			break;
		case IS_ARRAY:
		case IS_CONSTANT_ARRAY: {
				if (zvalue->value.ht) {
					//zend_hash_destroy(zvalue->value.ht);
					//FREE_HASHTABLE(zvalue->value.ht);
				}
			}
			break;
		case IS_OBJECT:
			break;
		case IS_RESOURCE:
			{
				/* destroy resource */
				//zend_list_delete(zvalue->value.lval);
			}
			break;
		case IS_LONG:
		case IS_DOUBLE:
		case IS_BOOL:
		case IS_NULL:
		default:
			return;
			break;
	}
}

void _zval_internal_dtor(zval *zvalue)
{
	switch (zvalue->type & ~IS_CONSTANT_INDEX) {
		case IS_STRING:
		case IS_CONSTANT:
			free(zvalue->value.str.val);
			break;
		case IS_ARRAY:
		case IS_CONSTANT_ARRAY:
		case IS_OBJECT:
		case IS_RESOURCE:
			//zend_error(E_CORE_ERROR, "Internal zval's can't be arrays, objects or resources");
			break;
		case IS_LONG:
		case IS_DOUBLE:
		case IS_BOOL:
		case IS_NULL:
		default:
			break;
	}
}

void zval_add_ref(zval **p)
{
	(*p)->refcount++;
}

void _zval_copy_ctor_func(zval *zvalue)
{
	switch (zvalue->type) {
		case IS_RESOURCE: {
				//zend_list_addref(zvalue->value.lval);
			}
			break;
		case IS_BOOL:
		case IS_LONG:
		case IS_NULL:
			break;
		case IS_CONSTANT:
		case IS_STRING:
			zvalue->value.str.val = (char *) zend_strndup(zvalue->value.str.val, zvalue->value.str.len);
			break;
		case IS_ARRAY:
		case IS_CONSTANT_ARRAY: {
				/*
				zval *tmp;
				HashTable *original_ht = zvalue->value.ht;
				HashTable *tmp_ht = NULL;
				TSRMLS_FETCH();

				if (zvalue->value.ht == &EG(symbol_table)) {
					return; // * do nothing * /
				}
				ALLOC_HASHTABLE_REL(tmp_ht);
				zend_hash_init(tmp_ht, 0, NULL, ZVAL_PTR_DTOR, 0);
				zend_hash_copy(tmp_ht, original_ht, (copy_ctor_func_t) zval_add_ref, (void *) &tmp, sizeof(zval *));
				zvalue->value.ht = tmp_ht;
				*/
			}
			break;
	}
}

int zend_print_variable(zval *var) 
{
	printf("type:%d,refcount:%d,is_ref:%d\n", var->type, var->refcount, var->is_ref);
	return SUCCESS;
}

char * zend_strndup(const char *s, uint32_t length)
{
	char *p;

	p = (char *) malloc(length+1);
	if (!p) {
		return (char *)NULL;
	}
	if (length) {
		memcpy(p, s, length);
	}
	p[length] = 0;
	return p;
}
