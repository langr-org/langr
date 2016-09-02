@echo off
D:
cd D:\Program Files\cwRsync\bin
rem rsync -va /cygdrive/e/backup/Backup /cygdrive/h/
rem #crontab 同步多台 web 上的文件
rem #WWW Web Site rsync l->r
rem #*/5 * * * * rsync -rvlHpogDtS /home/htdocs/web --exclude=web/data --exclude=web/html --exclude=web/tmplCache root@210.59.147.153:/home/htdocs 2>&1 > /dev/null
rem #(window) 本地同步 注意要备份目录有斜线和没斜线时是不同的
rem #rsync -va work/ /cygdrive/h/backup/Backup/work
rem #rsync -va /cygdrive/d/Backup/work /cygdrive/h/backup/Backup
rem #(window) 远程同步
rem #rsync -va --password-file=/cygdrive/d/rsync.secret /cygdrive/d/Backup/ hua@192.168.1.5::backup_mode
rem #rsync -va --password-file=/cygdrive/d/rsync.secret hua@192.168.1.5::backup_mode/ /cygdrive/d/Backup
rem rsync -va --delete /cygdrive/e/work /cygdrive/h/w --exclude=*.svn/*
rem --cvs-exclude 过滤 cvs, svn 等版本控制程序的 .svn 目录

rem rsync.bat dtou --exclude=xxx
if not "%1" == "dtou" goto EndDTOU
rem dtou: 硬盘到U盘
	echo rsync: dtou 硬盘到U盘! svn %2
	rsync -va --delete /cygdrive/e/src/work/lgame /cygdrive/j/work --exclude=lgame/test --exclude=lgame/tmp --exclude=lgame/release --exclude=lgame/debug --cvs-exclude %2
:EndDTOU
if not "%1" == "utod" goto EndUTOD
rem utod: U盘到硬盘
	echo rsync: utod U盘到硬盘! svn %2
	rsync -va --delete /cygdrive/j/work/lgame /cygdrive/e/src/work --exclude=lgame/test --exclude=lgame/tmp --exclude=lgame/release --exclude=lgame/debug --cvs-exclude %2
:EndUTOD
if not "%1" == "" goto EndBat
	echo rsync.bat dtou ["--exclude=lgame/resource"] 硬盘到U盘!
	echo rsync.bat utod U盘到硬盘!
:EndBat

echo rsync ok!
E:
rem pause
rem exit
