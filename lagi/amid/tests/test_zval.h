
#ifndef	_ZVAL_H
#define	_ZVAL_H

#include "../hashtable.h"

/*
 * zval
 */
typedef struct _zval_struct zval;

typedef union _zvalue_value {
	long lval;				/* long value */
	double dval;				/* double value */
	struct {
		char *val;
		int len;
	} str;
	hash_table *ht;				/* hash table value */
	void * pt;
} zvalue_value;

struct _zval_struct {
	/* Variable information */
	zvalue_value value;			/* value */
	uint16_t refcount;
	uint8_t type;				/* active type */
	uint8_t is_ref;
};

zval zval_used_for_init;	/* True global variable */

char * zend_strndup(const char *s, uint32_t length);
void _zval_dtor_func(zval *zvalue);

static inline void _zval_dtor(zval *zvalue)
{
        if (zvalue->type <= IS_BOOL) {
                return;
        }
	_zval_dtor_func(zvalue);
}

void _zval_copy_ctor_func(zval *zvalue);

static inline void _zval_copy_ctor(zval *zvalue)
{
        if (zvalue->type <= IS_BOOL) {
                return;
        }
	_zval_copy_ctor_func(zvalue);
}

int zend_print_variable(zval *var);
void _zval_ptr_dtor(zval **zval_ptr);
void _zval_internal_dtor(zval *zvalue);
void _zval_internal_ptr_dtor(zval **zvalue);
#define zval_copy_ctor(zvalue) _zval_copy_ctor((zvalue))
#define zval_dtor(zvalue) _zval_dtor((zvalue))
#define zval_ptr_dtor(zval_ptr) _zval_ptr_dtor((zval_ptr))
#define zval_internal_dtor(zvalue) _zval_internal_dtor((zvalue))
#define zval_internal_ptr_dtor(zvalue) _zval_internal_ptr_dtor((zvalue))

/* memcache alloc for zval's */
#define ALLOC_ZVAL(z)				\
	(z) = (zval *) malloc(sizeof(zval))

#define FREE_ZVAL(z)				\
	free(z)

#define ALLOC_ZVAL_REL(z)			\
	(z) = (zval *) malloc(sizeof(zval))

#define FREE_ZVAL_REL(z)			\
	free(z)

/* FIXME: Check if we can save if (ptr) too */
#define STR_FREE(ptr)		if (ptr) { free(ptr); }
#define STR_FREE_REL(ptr)	if (ptr) { free(ptr); }
#define STR_EMPTY_ALLOC()	zend_strndup("", sizeof("") - 1)
#define STR_REALLOC(ptr, size) \
			ptr = (char *) realloc(ptr, size);

#define ZVAL_ADDREF(pz)		(++(pz)->refcount)
#define ZVAL_DELREF(pz)		(--(pz)->refcount)
#define ZVAL_REFCOUNT(pz)	((pz)->refcount)

#define INIT_PZVAL(z)				\
	(z)->refcount = 1;			\
	(z)->is_ref = 0;	

#define INIT_ZVAL(z) z = zval_used_for_init;

/* 分配并初始化 */
#define ALLOC_INIT_ZVAL(zp)			\
	ALLOC_ZVAL(zp);				\
	INIT_ZVAL(*zp);

#define MAKE_STD_ZVAL(zv)			\
	ALLOC_ZVAL(zv);				\
	INIT_PZVAL(zv);

#define PZVAL_IS_REF(z)		((z)->is_ref)

/**
 * 分离一个被引用了的 zval.
 * zval->refcount > 1 被(其他变量)引用了的 zval.
 * zval->is_ref == 1 是引用的(其他变量) zval.??
 */
#define SEPARATE_ZVAL(ppzv)									\
	{											\
		zval *orig_ptr = *(ppzv);							\
												\
		if (orig_ptr->refcount>1) {							\
			orig_ptr->refcount--;							\
			ALLOC_ZVAL(*(ppzv));							\
			**(ppzv) = *orig_ptr;							\
			zval_copy_ctor(*(ppzv));						\
			(*(ppzv))->refcount=1;							\
			(*(ppzv))->is_ref = 0;							\
		}										\
	}

/* 分离一个非引用(但是被引用了)的 zval */
#define SEPARATE_ZVAL_IF_NOT_REF(ppzv)				\
	if (!PZVAL_IS_REF(*ppzv)) {				\
		SEPARATE_ZVAL(ppzv);				\
	}

/* 分离一个非引用(但是被引用了)的 zval, 然后把新分离的zval标示为引用. */
#define SEPARATE_ZVAL_TO_MAKE_IS_REF(ppzv)			\
	if (!PZVAL_IS_REF(*ppzv)) {				\
		SEPARATE_ZVAL(ppzv);				\
		(*(ppzv))->is_ref = 1;				\
	}

#define COPY_PZVAL_TO_ZVAL(zv, pzv)				\
	(zv) = *(pzv);						\
	if ((pzv)->refcount>1) {				\
		zval_copy_ctor(&(zv));				\
		(pzv)->refcount--;				\
	} else {						\
		FREE_ZVAL(pzv);					\
	}							\
	INIT_PZVAL(&(zv));

#define REPLACE_ZVAL_VALUE(ppzv_dest, pzv_src, copy) {		\
	int is_ref, refcount;					\
								\
	SEPARATE_ZVAL_IF_NOT_REF(ppzv_dest);			\
	is_ref = (*ppzv_dest)->is_ref;				\
	refcount = (*ppzv_dest)->refcount;			\
	zval_dtor(*ppzv_dest);					\
	**ppzv_dest = *pzv_src;					\
	if (copy) {						\
		zval_copy_ctor(*ppzv_dest);			\
	}							\
	(*ppzv_dest)->is_ref = is_ref;				\
	(*ppzv_dest)->refcount = refcount;			\
}

#define SEPARATE_ARG_IF_REF(varptr)				\
	if (PZVAL_IS_REF(varptr)) {				\
		zval *original_var = varptr;			\
		ALLOC_ZVAL(varptr);				\
		varptr->value = original_var->value;		\
		varptr->type = original_var->type;		\
		varptr->is_ref = 0;				\
		varptr->refcount = 1;				\
		zval_copy_ctor(varptr);				\
	} else {						\
		varptr->refcount++;				\
	}

/* data types */
/* All data types <= IS_BOOL have their constructor/destructors skipped */
#define IS_NULL		0
#define IS_LONG		1
#define IS_DOUBLE	2
#define IS_BOOL		3
#define IS_ARRAY	4
/*#define IS_OBJECT	5*/
#define IS_STRING	6
#define IS_RESOURCE	7
#define IS_CONSTANT	8
#define IS_CONSTANT_ARRAY	9

/* Ugly hack to support constants as static array indices */
#define IS_CONSTANT_INDEX	0x80

/* overloaded elements data types */
#define OE_IS_ARRAY	(1<<0)
#define OE_IS_OBJECT	(1<<1)
#define OE_IS_METHOD	(1<<2)


#define ZVAL_RESOURCE(z, l) {				\
		(z)->type = IS_RESOURCE;        	\
		(z)->value.lval = l;	        	\
	}

#define ZVAL_BOOL(z, b) {				\
		(z)->type = IS_BOOL;		        \
		(z)->value.lval = ((b) != 0);   	\
	}

#define ZVAL_NULL(z) {					\
		(z)->type = IS_NULL;	        	\
	}

#define ZVAL_LONG(z, l) {				\
		(z)->type = IS_LONG;	        	\
		(z)->value.lval = l;	        	\
	}

#define ZVAL_DOUBLE(z, d) {				\
		(z)->type = IS_DOUBLE;			\
		(z)->value.dval = d;			\
	}

#define ZVAL_STRING(z, s, duplicate) {			\
		char *__s=(s);				\
		(z)->value.str.len = strlen(__s);	\
		(z)->value.str.val = (duplicate?strndup(__s, (z)->value.str.len):__s);	\
		(z)->type = IS_STRING;			\
	}

#define ZVAL_STRINGL(z, s, l, duplicate) {		\
		char *__s=(s); int __l=l;		\
		(z)->value.str.len = __l;		\
		(z)->value.str.val = (duplicate?strndup(__s, __l):__s);		\
		(z)->type = IS_STRING;			\
	}

#define ZVAL_EMPTY_STRING(z) {				\
		(z)->value.str.len = 0;			\
		(z)->value.str.val = STR_EMPTY_ALLOC();	\
		(z)->type = IS_STRING;			\
	}

#define ZVAL_ZVAL(z, zv, copy, dtor) {			\
		int is_ref, refcount;			\
		is_ref = (z)->is_ref;			\
		refcount = (z)->refcount;		\
		*(z) = *(zv);				\
		if (copy) {				\
			zval_copy_ctor(z);		\
		}					\
		if (dtor) {				\
			if (!copy) {			\
				ZVAL_NULL(zv);		\
			}				\
			zval_ptr_dtor(&zv);		\
		}					\
		(z)->is_ref = is_ref;			\
		(z)->refcount = refcount;		\
	}

#define ZVAL_FALSE(z)  					ZVAL_BOOL(z, 0)
#define ZVAL_TRUE(z)  					ZVAL_BOOL(z, 1)

#define RETVAL_RESOURCE(l)				ZVAL_RESOURCE(return_value, l)
#define RETVAL_BOOL(b)					ZVAL_BOOL(return_value, b)
#define RETVAL_NULL() 					ZVAL_NULL(return_value)
#define RETVAL_LONG(l) 					ZVAL_LONG(return_value, l)
#define RETVAL_DOUBLE(d) 				ZVAL_DOUBLE(return_value, d)
#define RETVAL_STRING(s, duplicate) 			ZVAL_STRING(return_value, s, duplicate)
#define RETVAL_STRINGL(s, l, duplicate) 		ZVAL_STRINGL(return_value, s, l, duplicate)
#define RETVAL_EMPTY_STRING() 				ZVAL_EMPTY_STRING(return_value)
#define RETVAL_ZVAL(zv, copy, dtor)			ZVAL_ZVAL(return_value, zv, copy, dtor)
#define RETVAL_FALSE  					ZVAL_BOOL(return_value, 0)
#define RETVAL_TRUE   					ZVAL_BOOL(return_value, 1)

#define RETURN_RESOURCE(l) 				{ RETVAL_RESOURCE(l); return; }
#define RETURN_BOOL(b) 					{ RETVAL_BOOL(b); return; }
#define RETURN_NULL() 					{ RETVAL_NULL(); return;}
#define RETURN_LONG(l) 					{ RETVAL_LONG(l); return; }
#define RETURN_DOUBLE(d) 				{ RETVAL_DOUBLE(d); return; }
#define RETURN_STRING(s, duplicate) 			{ RETVAL_STRING(s, duplicate); return; }
#define RETURN_STRINGL(s, l, duplicate) 		{ RETVAL_STRINGL(s, l, duplicate); return; }
#define RETURN_EMPTY_STRING() 				{ RETVAL_EMPTY_STRING(); return; }
#define RETURN_ZVAL(zv, copy, dtor)			{ RETVAL_ZVAL(zv, copy, dtor); return; }
#define RETURN_FALSE  					{ RETVAL_FALSE; return; }
#define RETURN_TRUE   					{ RETVAL_TRUE; return; }


/*
#define ZEND_SET_SYMBOL(symtable, name, var)										\
	{														\
		char *_name = (name);											\
															\
		ZEND_SET_SYMBOL_WITH_LENGTH(symtable, _name, strlen(_name)+1, var, 1, 0);				\
	}

#define ZEND_SET_SYMBOL_WITH_LENGTH(symtable, name, name_length, var, _refcount, _is_ref)				\
	{														\
		zval **orig_var;											\
															\
		if (zend_hash_find(symtable, (name), (name_length), (void **) &orig_var)==SUCCESS			\
			&& PZVAL_IS_REF(*orig_var)) {									\
			(var)->refcount = (*orig_var)->refcount;							\
			(var)->is_ref = 1;										\
															\
			if (_refcount) {										\
				(var)->refcount += _refcount-1;								\
			}												\
			zval_dtor(*orig_var);										\
			**orig_var = *(var);										\
			FREE_ZVAL(var);											\
		} else {												\
			(var)->is_ref = _is_ref;									\
			if (_refcount) {										\
				(var)->refcount = _refcount;								\
			}												\
			zend_hash_update(symtable, (name), (name_length), &(var), sizeof(zval *), NULL);		\
		}													\
	}

#define ZEND_SET_GLOBAL_VAR(name, var)							\
	ZEND_SET_SYMBOL(&EG(symbol_table), name, var)

#define ZEND_SET_GLOBAL_VAR_WITH_LENGTH(name, name_length, var, _refcount, _is_ref)	\
	ZEND_SET_SYMBOL_WITH_LENGTH(&EG(symbol_table), name, name_length, var, _refcount, _is_ref)

#define ZEND_DEFINE_PROPERTY(class_ptr, name, value, mask)				\
{											\
	char *_name = (name);								\
	int namelen = strlen(_name);							\
	zend_declare_property(class_ptr, _name, namelen, value, mask TSRMLS_CC);	\
}
*/

#define HASH_OF(p) ((p)->type==IS_ARRAY ? (p)->value.ht : NULL)
#define ZVAL_IS_NULL(z)			((z)->type==IS_NULL)


#define SET_VAR_STRING(n, v) {								\
				{							\
					zval *var;					\
					ALLOC_ZVAL(var);				\
					ZVAL_STRING(var, v, 0);				\
					/*ZEND_SET_GLOBAL_VAR(n, var);*/		\
				}							\
			}

#define SET_VAR_STRINGL(n, v, l) {							\
				{							\
					zval *var;					\
					ALLOC_ZVAL(var);				\
					ZVAL_STRINGL(var, v, l, 0);			\
					/*ZEND_SET_GLOBAL_VAR(n, var);*/		\
				}							\
			}

#define SET_VAR_LONG(n, v) {								\
				{							\
					zval *var;					\
					ALLOC_ZVAL(var);				\
					ZVAL_LONG(var, v);				\
					/*ZEND_SET_GLOBAL_VAR(n, var);*/		\
				}							\
			}

#define SET_VAR_DOUBLE(n, v) {								\
				{							\
					zval *var;					\
					ALLOC_ZVAL(var);				\
					ZVAL_DOUBLE(var, v);				\
					/*ZEND_SET_GLOBAL_VAR(n, var);*/		\
				}							\
			}


#define Z_LVAL(zval)			(zval).value.lval
#define Z_BVAL(zval)			((zend_bool)(zval).value.lval)
#define Z_DVAL(zval)			(zval).value.dval
#define Z_STRVAL(zval)			(zval).value.str.val
#define Z_STRLEN(zval)			(zval).value.str.len
#define Z_ARRVAL(zval)			(zval).value.ht
#define Z_RESVAL(zval)			(zval).value.lval
/*
#define Z_OBJVAL(zval)			(zval).value.obj
#define Z_OBJ_HANDLE(zval)		(zval).value.obj.handle
#define Z_OBJ_HT(zval)			(zval).value.obj.handlers
#define Z_OBJCE(zval)			zend_get_class_entry(&(zval) TSRMLS_CC)
#define Z_OBJPROP(zval)			Z_OBJ_HT((zval))->get_properties(&(zval) TSRMLS_CC)
#define Z_OBJ_HANDLER(zval, hf)		Z_OBJ_HT((zval))->hf
*/

#define Z_LVAL_P(zval_p)		Z_LVAL(*zval_p)
#define Z_BVAL_P(zval_p)		Z_BVAL(*zval_p)
#define Z_DVAL_P(zval_p)		Z_DVAL(*zval_p)
#define Z_STRVAL_P(zval_p)		Z_STRVAL(*zval_p)
#define Z_STRLEN_P(zval_p)		Z_STRLEN(*zval_p)
#define Z_ARRVAL_P(zval_p)		Z_ARRVAL(*zval_p)
#define Z_RESVAL_P(zval_p)		Z_RESVAL(*zval_p)
/*
#define Z_OBJPROP_P(zval_p)		Z_OBJPROP(*zval_p)
#define Z_OBJCE_P(zval_p)		Z_OBJCE(*zval_p)
#define Z_OBJVAL_P(zval_p)		Z_OBJVAL(*zval_p)
#define Z_OBJ_HANDLE_P(zval_p)		Z_OBJ_HANDLE(*zval_p)
#define Z_OBJ_HT_P(zval_p)		Z_OBJ_HT(*zval_p)
#define Z_OBJ_HANDLER_P(zval_p, h)	Z_OBJ_HANDLER(*zval_p, h) 
*/

#define Z_LVAL_PP(zval_pp)		Z_LVAL(**zval_pp)
#define Z_BVAL_PP(zval_pp)		Z_BVAL(**zval_pp)
#define Z_DVAL_PP(zval_pp)		Z_DVAL(**zval_pp)
#define Z_STRVAL_PP(zval_pp)		Z_STRVAL(**zval_pp)
#define Z_STRLEN_PP(zval_pp)		Z_STRLEN(**zval_pp)
#define Z_ARRVAL_PP(zval_pp)		Z_ARRVAL(**zval_pp)
#define Z_RESVAL_PP(zval_pp)		Z_RESVAL(**zval_pp)
/*
#define Z_OBJPROP_PP(zval_pp)		Z_OBJPROP(**zval_pp)
#define Z_OBJCE_PP(zval_pp)		Z_OBJCE(**zval_pp)
#define Z_OBJVAL_PP(zval_pp)    	Z_OBJVAL(**zval_pp)
#define Z_OBJ_HANDLE_PP(zval_p) 	Z_OBJ_HANDLE(**zval_p)
#define Z_OBJ_HT_PP(zval_p)     	Z_OBJ_HT(**zval_p)
#define Z_OBJ_HANDLER_PP(zval_p, h)	Z_OBJ_HANDLER(**zval_p, h) 
*/

#define Z_TYPE(zval)			(zval).type
#define Z_TYPE_P(zval_p)		Z_TYPE(*zval_p)
#define Z_TYPE_PP(zval_pp)		Z_TYPE(**zval_pp)

#endif	/* end _ZVAL_H */
