/** 
 * @file test_val.c
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
 * @author Langr <hua@langr.org> 2011/12/19 14:02
 * 
 * $Id: test_val.c 30 2011-12-30 02:57:03Z loghua@gmail.com $
 */

#include "../zval.h"

int main(int argc, char ** argv)
{
	zval t1, * pt2, ** ppt3;

	INIT_ZVAL(t1);
	Z_TYPE(t1) = IS_LONG;
	Z_LVAL(t1) = 54321;
	zval_print(&t1);

	ALLOC_INIT_ZVAL(pt2);
	ZVAL_STRING(pt2, "this is string val.", 1);
	zval_print(pt2);

	*ppt3 = pt2;
	ZVAL_ADDREF(*ppt3);
	zval_print(*ppt3);

	//zval_copy_ctor(*ppt3);
	SEPARATE_ZVAL(ppt3);
	zval_print(*ppt3);
	zval_print(pt2);

	Z_TYPE(t1) = IS_BOOL;
	Z_LVAL(t1) = 1;
	zval_print(&t1);
	Z_TYPE(t1) = IS_DOUBLE;
	Z_DVAL(t1) = 20.12;
	zval_print(&t1);

	/*
	zval_dtor(pt2);
	FREE_ZVAL(pt2);
	zval_dtor(*ppt3);
	FREE_ZVAL(*ppt3);
	*/
}
