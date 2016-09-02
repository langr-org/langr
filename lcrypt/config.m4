dnl $Id$
dnl config.m4 for extension lcrypt

dnl Comments in this file start with the string 'dnl'.
dnl Remove where necessary. This file will not work
dnl without editing.

dnl If your extension references something external, use with:

dnl PHP_ARG_WITH(lcrypt, for lcrypt support,
dnl Make sure that the comment is aligned:
dnl [  --with-lcrypt             Include lcrypt support])

dnl Otherwise use enable:

PHP_ARG_ENABLE(lcrypt, whether to enable lcrypt support,
dnl Make sure that the comment is aligned:
[  --enable-lcrypt           Enable lcrypt support])

if test "$PHP_LCRYPT" != "no"; then
  dnl Write more examples of tests here...

  dnl # --with-lcrypt -> check with-path
  dnl SEARCH_PATH="/usr/local /usr"     # you might want to change this
  dnl SEARCH_FOR="/include/lcrypt.h"  # you most likely want to change this
  dnl if test -r $PHP_LCRYPT/$SEARCH_FOR; then # path given as parameter
  dnl   LCRYPT_DIR=$PHP_LCRYPT
  dnl else # search default path list
  dnl   AC_MSG_CHECKING([for lcrypt files in default path])
  dnl   for i in $SEARCH_PATH ; do
  dnl     if test -r $i/$SEARCH_FOR; then
  dnl       LCRYPT_DIR=$i
  dnl       AC_MSG_RESULT(found in $i)
  dnl     fi
  dnl   done
  dnl fi
  dnl
  dnl if test -z "$LCRYPT_DIR"; then
  dnl   AC_MSG_RESULT([not found])
  dnl   AC_MSG_ERROR([Please reinstall the lcrypt distribution])
  dnl fi

  dnl # --with-lcrypt -> add include path
  dnl PHP_ADD_INCLUDE($LCRYPT_DIR/include)

  dnl # --with-lcrypt -> check for lib and symbol presence
  dnl LIBNAME=lcrypt # you may want to change this
  dnl LIBSYMBOL=lcrypt # you most likely want to change this 

  dnl PHP_CHECK_LIBRARY($LIBNAME,$LIBSYMBOL,
  dnl [
  dnl   PHP_ADD_LIBRARY_WITH_PATH($LIBNAME, $LCRYPT_DIR/lib, LCRYPT_SHARED_LIBADD)
  dnl   AC_DEFINE(HAVE_LCRYPTLIB,1,[ ])
  dnl ],[
  dnl   AC_MSG_ERROR([wrong lcrypt lib version or lib not found])
  dnl ],[
  dnl   -L$LCRYPT_DIR/lib -lm -ldl
  dnl ])
  dnl
  dnl PHP_SUBST(LCRYPT_SHARED_LIBADD)

  PHP_NEW_EXTENSION(lcrypt, lcrypt.c zencode.c debug.c, $ext_shared)
fi
