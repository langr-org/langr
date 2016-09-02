#!/bin/bash
#
# svg.sh infile outfile
# server run: xvfb :1 -screen 0 800x600x24 &
# $Id$

shell_cmd="./svg";

if [ "$1" == "" ] ; then
	echo "Usage: $0 infile outfile"  
else
	DISPLAY=localhost:1.0 $shell_cmd $1 $2 &
fi

sleep 0.5
killall -I svg
