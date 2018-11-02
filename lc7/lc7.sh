#!/bin/bash
# lc7.sh
# $Id: lc7.sh 3 2011-11-14 10:33:31Z loghua@gmail.com $
# @modify by Hua <hua@langr.org> 2011/08/23 21:13
# ./lc7.sh make [xxx]
# ./lc7.sh version.h

#PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin
#export PATH

version_file="php_lc7.h"
vim_file="version.vim"

#echo "%s/^\(#define\t*VERSION_STRING\t*\\\"[a-z0-9]\{1,4\}\.[0-9]\{1,3\}\.\)\([0-9]\{1,5\}\)\(\\\"\)$/\=submatch(1).(submatch(2)+1).submatch(3)/g" >> tmp.vim
#echo "%s/^\(#define\t*LAST_COMPILE_TIME\t*\\\"\)\(20[0-9][0-9][0-9 /\-:]\{15\}\)\(\\\"\)$/\=submatch(1).strftime(\"%Y-%m-%d %H:%M:%S\").submatch(3)/g" >> tmp.vim
#echo "w!" >> tmp.vim
#echo "q" >> tmp.vim

# version
if [ "$1" == "" ] || [ "$1" == "make" ] ; then
    #vim $version_file -S $vim_file	/* windows xp */
    #vim $version_file -s $vim_file
    vim $version_file -e -s < $vim_file
elif [ "$1" != "make" ] ; then
    vim $1 -e -s < $vim_file
fi
echo "$version_file ok!"

# make
if [ "$1" == "make" ] ; then
    make $2
    echo "make done!"
fi

exit

