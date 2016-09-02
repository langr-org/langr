#!/bin/sh
#
#递归地查找当前目录下的 *.php 文件里面匹配的字符串
#-- `find ` 命令的输出应不超过 line buffer
#grep -ni "mail" -- `find . -name '*.php'`

for fname in `find -name '*.php'`
do
        grep -n "$1" -- $fname;
        echo $fname;
done

