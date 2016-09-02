#!/bin/bash
# lagi.sh
# $Id: lagi.sh 10 2011-11-22 11:04:31Z loghua@gmail.com $
# ./lgame.sh
# @modify by Hua <hua@langr.org> 2011/08/23 21:13
# ./lagi.sh make [xxx]
# ./lagi.sh version.h

#PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin
#export PATH

version_file="php_lagi.h"
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
	echo "$version_file ok!"
elif [ "$1" != "make" ] || [ "$1" != "Makefile" ] ; then
	vim $1 -e -s < $vim_file
	echo "$1 ok!"
fi

# make
if [ "$1" == "make" ] ; then
	#make clean
	make $2
	echo "make done!"
fi

# edit makefile
if [ "$1" == "Makefile" ] ; then
	#make clean
	echo -e "lagi:\n\tmake clean\n\t./lagi.sh make" >> Makefile
	echo "edit Makefile done!"
fi

exit

