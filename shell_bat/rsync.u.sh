#!/bin/bash
# 
#PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin
#export PATH
#crontab 同步多台 web 上的文件
#WWW Web Site rsync l->r
#*/5 * * * * rsync -rvlHpogDtS /home/htdocs/web --exclude=web/data --exclude=web/html --exclude=web/tmplCache root@210.59.147.153:/home/htdocs 2>&1 > /dev/null

# 本地同步 注意要备份目录有斜线和没斜线时是不同的
#rsync -va work/ /cygdrive/h/backup/Backup/work
#rsync -va /cygdrive/d/Backup/work /cygdrive/h/backup/Backup
#(window) 远程同步
#rsync -va --password-file=/cygdrive/d/rsync.secret /cygdrive/d/Backup/ hua@192.168.1.5::backup_mode
#rsync -va --password-file=/cygdrive/d/rsync.secret hua@192.168.1.5::backup_mode/ /cygdrive/d/Backup
#--cvs-exclude 过滤 cvs, svn 等版本控制程序的 .svn 目录

# ./rsync.sh dtou --exclude=xxx
if [ "$1" == "dtou" ] ; then
# 硬盘到U盘
	echo rsync: dtou 硬盘到U盘! svn $2
	rsync -va --delete /home/src/work/lgame /media/disk/work --exclude=lgame/test --exclude=lgame/tmp --exclude=lgame/release --exclude=lgame/debug --cvs-exclude $2
elif [ "$1" == "utod" ] ; then
# U盘到硬盘
	echo rsync: utod U盘到硬盘! svn $2
	rsync -va --delete /media/disk/work/lgame /home/src/work --exclude=lgame/test --exclude=lgame/tmp --exclude=lgame/release --exclude=lgame/debug --cvs-exclude $2
elif [ "$1" == "" ] ; then
	echo "./rsync.sh dtou [\"--exclude=lgame/resource\"] 硬盘到U盘! "
	echo "./rsync.sh utod U盘到硬盘! "
fi
#rsync -va /cygdrive/e/tmp /cygdrive/h/w

echo rsync ok!
#exit
