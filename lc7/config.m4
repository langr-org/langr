dnl $Id$
dnl config.m4 for extension lc7

dnl Comments in this file start with the string 'dnl'.
dnl Remove where necessary. This file will not work
dnl without editing.

dnl If your extension references something external, use with:

dnl PHP_ARG_WITH(lc7, for lc7 support,
dnl Make sure that the comment is aligned:
dnl [  --with-lc7             Include lc7 support])

dnl Otherwise use enable:

PHP_ARG_ENABLE(lc7, whether to enable lc7 support,
dnl Make sure that the comment is aligned:
[  --enable-lc7           Enable lc7 support])

if test "$PHP_LC7" != "no"; then
  dnl Write more examples of tests here...

  dnl # --with-lc7 -> check with-path
  dnl SEARCH_PATH="/usr/local /usr"     # you might want to change this
  dnl SEARCH_FOR="/include/lc7.h"  # you most likely want to change this
  dnl if test -r $PHP_LC7/$SEARCH_FOR; then # path given as parameter
  dnl   LC7_DIR=$PHP_LC7
  dnl else # search default path list
  dnl   AC_MSG_CHECKING([for lc7 files in default path])
  dnl   for i in $SEARCH_PATH ; do
  dnl     if test -r $i/$SEARCH_FOR; then
  dnl       LC7_DIR=$i
  dnl       AC_MSG_RESULT(found in $i)
  dnl     fi
  dnl   done
  dnl fi
  dnl
  dnl if test -z "$LC7_DIR"; then
  dnl   AC_MSG_RESULT([not found])
  dnl   AC_MSG_ERROR([Please reinstall the lc7 distribution])
  dnl fi

  dnl # --with-lc7 -> add include path
  dnl PHP_ADD_INCLUDE($LC7_DIR/include)

  dnl # --with-lc7 -> check for lib and symbol presence
  dnl LIBNAME=lc7 # you may want to change this
  dnl LIBSYMBOL=lc7 # you most likely want to change this 

  dnl PHP_CHECK_LIBRARY($LIBNAME,$LIBSYMBOL,
  dnl [
  dnl   PHP_ADD_LIBRARY_WITH_PATH($LIBNAME, $LC7_DIR/$PHP_LIBDIR, LC7_SHARED_LIBADD)
  dnl   AC_DEFINE(HAVE_LC7LIB,1,[ ])
  dnl ],[
  dnl   AC_MSG_ERROR([wrong lc7 lib version or lib not found])
  dnl ],[
  dnl   -L$LC7_DIR/$PHP_LIBDIR -lm
  dnl ])
  dnl
  dnl PHP_SUBST(LC7_SHARED_LIBADD)

  PHP_NEW_EXTENSION(lc7, lc7.c, $ext_shared,, -DZEND_ENABLE_STATIC_TSRMLS_CACHE=1)
fi
