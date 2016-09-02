#!/bin/sh
# category collect.
# $Id: server.sh 85 2014-06-04 10:59:44Z huanghua $
# rpath=/web/kitsmall/rebots/hrebots/server
# php_path=/usr/bin/php
php_path=/usr/bin/php
log_file="Runtime/Logs/push."$(date +%Y-%m-%d)".log"

# push to bigdata
t=1
while [ $t -eq 1 ]; do
	# $php_path -f index.php Push/index/mod/digikey/order/desc
	# $php_path -f index.php Push/prices/mod/digikey/order/desc
	if [ "$1" == "prices" ] ; then
		$php_path -f index.php Push/prices/mod/$2
	elif [ "$1" != "" ] ; then
		$php_path -f index.php Push/index/mod/$1
	elif [ "$1" == "" ] ; then
		$php_path -f index.php Push/index/mod/mouser
	fi
	cur_time=$(date +%Y-%m-%d' '%H:%M:%S)' restart push data by shell...'
	echo $cur_time
	echo $cur_time >> $log_file
done
#
