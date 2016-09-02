dnl $Id$
dnl config.m4 for extension lagi

dnl Comments in this file start with the string 'dnl'.
dnl Remove where necessary. This file will not work
dnl without editing.

dnl If your extension references something external, use with:

dnl PHP_ARG_WITH(lagi, for lagi support,
dnl Make sure that the comment is aligned:
dnl [  --with-lagi             Include lagi support])

dnl Otherwise use enable:

PHP_ARG_ENABLE(lagi, whether to enable lagi support,
dnl Make sure that the comment is aligned:
[  --enable-lagi           Enable lagi support])

if test "$PHP_LAGI" != "no"; then
  dnl Write more examples of tests here...

  dnl # --with-lagi -> check with-path
  dnl SEARCH_PATH="/usr/local /usr"     # you might want to change this
  dnl SEARCH_FOR="/include/lagi.h"  # you most likely want to change this
  dnl if test -r $PHP_LAGI/$SEARCH_FOR; then # path given as parameter
  dnl   LAGI_DIR=$PHP_LAGI
  dnl else # search default path list
  dnl   AC_MSG_CHECKING([for lagi files in default path])
  dnl   for i in $SEARCH_PATH ; do
  dnl     if test -r $i/$SEARCH_FOR; then
  dnl       LAGI_DIR=$i
  dnl       AC_MSG_RESULT(found in $i)
  dnl     fi
  dnl   done
  dnl fi
  dnl
  dnl if test -z "$LAGI_DIR"; then
  dnl   AC_MSG_RESULT([not found])
  dnl   AC_MSG_ERROR([Please reinstall the lagi distribution])
  dnl fi

  dnl # --with-lagi -> add include path
  dnl PHP_ADD_INCLUDE($LAGI_DIR/include)

  dnl # --with-lagi -> check for lib and symbol presence
  dnl LIBNAME=lagi # you may want to change this
  dnl LIBSYMBOL=lagi # you most likely want to change this 

  dnl PHP_CHECK_LIBRARY($LIBNAME,$LIBSYMBOL,
  dnl [
  dnl   PHP_ADD_LIBRARY_WITH_PATH($LIBNAME, $LAGI_DIR/lib, LAGI_SHARED_LIBADD)
  dnl   AC_DEFINE(HAVE_LAGILIB,1,[ ])
  dnl ],[
  dnl   AC_MSG_ERROR([wrong lagi lib version or lib not found])
  dnl ],[
  dnl   -L$LAGI_DIR/lib -lm
  dnl ])
  dnl
  dnl PHP_SUBST(LAGI_SHARED_LIBADD)

  PHP_NEW_EXTENSION(lagi, debug.c lagi.c ami.c amid/iotimeout.c, $ext_shared)
fi
