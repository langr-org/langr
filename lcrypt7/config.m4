dnl $Id$
dnl config.m4 for extension lcrypt7

dnl Comments in this file start with the string 'dnl'.
dnl Remove where necessary. This file will not work
dnl without editing.

dnl If your extension references something external, use with:

dnl PHP_ARG_WITH(lcrypt7, for lcrypt7 support,
dnl Make sure that the comment is aligned:
dnl [  --with-lcrypt7             Include lcrypt7 support])

dnl Otherwise use enable:

PHP_ARG_ENABLE(lcrypt7, whether to enable lcrypt7 support,
Make sure that the comment is aligned:
[  --enable-lcrypt7           Enable lcrypt7 support])

if test "$PHP_LCRYPT7" != "no"; then
  dnl Write more examples of tests here...

  dnl # --with-lcrypt7 -> check with-path
  dnl SEARCH_PATH="/usr/local /usr"     # you might want to change this
  dnl SEARCH_FOR="/include/lcrypt7.h"  # you most likely want to change this
  dnl if test -r $PHP_LCRYPT7/$SEARCH_FOR; then # path given as parameter
  dnl   LCRYPT7_DIR=$PHP_LCRYPT7
  dnl else # search default path list
  dnl   AC_MSG_CHECKING([for lcrypt7 files in default path])
  dnl   for i in $SEARCH_PATH ; do
  dnl     if test -r $i/$SEARCH_FOR; then
  dnl       LCRYPT7_DIR=$i
  dnl       AC_MSG_RESULT(found in $i)
  dnl     fi
  dnl   done
  dnl fi
  dnl
  dnl if test -z "$LCRYPT7_DIR"; then
  dnl   AC_MSG_RESULT([not found])
  dnl   AC_MSG_ERROR([Please reinstall the lcrypt7 distribution])
  dnl fi

  dnl # --with-lcrypt7 -> add include path
  dnl PHP_ADD_INCLUDE($LCRYPT7_DIR/include)

  dnl # --with-lcrypt7 -> check for lib and symbol presence
  dnl LIBNAME=lcrypt7 # you may want to change this
  dnl LIBSYMBOL=lcrypt7 # you most likely want to change this 

  dnl PHP_CHECK_LIBRARY($LIBNAME,$LIBSYMBOL,
  dnl [
  dnl   PHP_ADD_LIBRARY_WITH_PATH($LIBNAME, $LCRYPT7_DIR/$PHP_LIBDIR, LCRYPT7_SHARED_LIBADD)
  dnl   AC_DEFINE(HAVE_LCRYPT7LIB,1,[ ])
  dnl ],[
  dnl   AC_MSG_ERROR([wrong lcrypt7 lib version or lib not found])
  dnl ],[
  dnl   -L$LCRYPT7_DIR/$PHP_LIBDIR -lm
  dnl ])
  dnl
  dnl PHP_SUBST(LCRYPT7_SHARED_LIBADD)

  PHP_NEW_EXTENSION(lcrypt7, lcrypt7.c zencode.c, $ext_shared,, -DZEND_ENABLE_STATIC_TSRMLS_CACHE=1)
fi
