#!/bin/sh
# run client.
# $Id: linux.sh 63 2014-05-24 05:49:23Z huanghua $
# 
# rpath=/home/
php_path=/usr/bin/php
# 
t=1
while [ $t -eq 1 ]; do
	# $php_path -f index.php digikey
	# $php_path -f index.php mouser
	$php_path -f index.php
	if [ "$?" != "9" ] ; then
		break
	fi
done
