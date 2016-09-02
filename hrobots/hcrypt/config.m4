dnl $Id$
dnl config.m4 for extension hcrypt

dnl Comments in this file start with the string 'dnl'.
dnl Remove where necessary. This file will not work
dnl without editing.

dnl If your extension references something external, use with:

dnl PHP_ARG_WITH(hcrypt, for hcrypt support,
dnl Make sure that the comment is aligned:
dnl [  --with-hcrypt             Include hcrypt support])

dnl Otherwise use enable:

dnl PHP_ARG_ENABLE(hcrypt, whether to enable hcrypt support,
dnl Make sure that the comment is aligned:
dnl [  --enable-hcrypt           Enable hcrypt support])

if test "$PHP_HCRYPT" != "no"; then
  dnl Write more examples of tests here...

  dnl # --with-hcrypt -> check with-path
  dnl SEARCH_PATH="/usr/local /usr"     # you might want to change this
  dnl SEARCH_FOR="/include/hcrypt.h"  # you most likely want to change this
  dnl if test -r $PHP_HCRYPT/$SEARCH_FOR; then # path given as parameter
  dnl   HCRYPT_DIR=$PHP_HCRYPT
  dnl else # search default path list
  dnl   AC_MSG_CHECKING([for hcrypt files in default path])
  dnl   for i in $SEARCH_PATH ; do
  dnl     if test -r $i/$SEARCH_FOR; then
  dnl       HCRYPT_DIR=$i
  dnl       AC_MSG_RESULT(found in $i)
  dnl     fi
  dnl   done
  dnl fi
  dnl
  dnl if test -z "$HCRYPT_DIR"; then
  dnl   AC_MSG_RESULT([not found])
  dnl   AC_MSG_ERROR([Please reinstall the hcrypt distribution])
  dnl fi

  dnl # --with-hcrypt -> add include path
  dnl PHP_ADD_INCLUDE($HCRYPT_DIR/include)

  dnl # --with-hcrypt -> check for lib and symbol presence
  dnl LIBNAME=hcrypt # you may want to change this
  dnl LIBSYMBOL=hcrypt # you most likely want to change this 

  dnl PHP_CHECK_LIBRARY($LIBNAME,$LIBSYMBOL,
  dnl [
  dnl   PHP_ADD_LIBRARY_WITH_PATH($LIBNAME, $HCRYPT_DIR/lib, HCRYPT_SHARED_LIBADD)
  dnl   AC_DEFINE(HAVE_HCRYPTLIB,1,[ ])
  dnl ],[
  dnl   AC_MSG_ERROR([wrong hcrypt lib version or lib not found])
  dnl ],[
  dnl   -L$HCRYPT_DIR/lib -lm
  dnl ])
  dnl
  dnl PHP_SUBST(HCRYPT_SHARED_LIBADD)

  PHP_NEW_EXTENSION(hcrypt, hcrypt.c, $ext_shared)
fi
