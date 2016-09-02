#!/bin/sh
# category collect.
# $Id: server.sh 85 2014-06-04 10:59:44Z huanghua $
# rpath=/web/kitsmall/rebots/hrebots/server
log_file="Runtime/Logs/push."$(date +%Y)".log"
URL="http://www.kitsmall.com"

STATUS_CODE=`curl -o /dev/null -m 10 --connect-timeout 10 -s -w %{http_code} $URL`
#echo "$CheckURL Status Code:\t$STATUS_CODE"
if [ "$STATUS_CODE" = "502" ]; then
	cur_time=$(date +%Y-%m-%d' '%H:%M:%S)' restart php-fpm by shell...'
	echo $cur_time >> $log_file
	/etc/init.d/php-fpm restart
fi
#
