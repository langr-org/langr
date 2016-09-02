#!/bin/bash
#
# svg.sh infile outfile
# server run: xvfb :1 -screen 0 800x600x24 &
# $Id: svg.sh 539 2012-04-20 01:55:33Z huangh $

shell_cmd="./svg";

ISRUN=`ps aux | grep Xvfb | cat -n | tail -1 | awk '{print $1}'`
# if grep Xvfb <= 2
# 请注意, 需要给 当前执行脚本的用户加 sudo 权限
# vim /etc/sudoers add:
# asterisk ALL = NOPASSWD: /usr/bin/Xvfb
if [ "$ISRUN" -lt "2" ] ; then
	echo "Xvfb not run"
	sudo Xvfb :1 -screen 0 800x600x24 >/dev/null 2>&1 &
fi

if [ "$1" == "" ] ; then
	echo "Usage: $0 infile outfile"  
else
	chmod 744 $1
	DISPLAY=localhost:1.0 $shell_cmd $1 $2 &
fi

sleep 1
killall -I svg
