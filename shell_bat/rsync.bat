@echo off
D:
cd D:\Program Files\cwRsync\bin
if "%1" == "" goto End
rem #crontab 定时自动同步远程 server 上的文件
rem #WWW Web Site rsync l->r
rem #*/5 * * * * rsync -rvlHpogDtS /home/htdocs/betcity_0711 --exclude=betcity_0711/data --exclude=betcity_0711/html --exclude=betcity_0711/tmplCache root@210.59.147.153:/home/htdocs 2>&1 > /dev/null
rem #(window) 本地同步 注意要备份目录有斜线和没斜线时是不同的
rem #rsync -va work/ /cygdrive/h/backup/Backup/work
rem #rsync -va /cygdrive/d/Backup/work /cygdrive/h/backup/Backup/work
rem #(window) 远程同步
rem #rsync -va --password-file=/cygdrive/d/rsync.secret /cygdrive/d/Backup/ hua@192.168.1.5::backup_mode
rem #rsync -va --password-file=/cygdrive/d/rsync.secret hua@192.168.1.5::backup_mode/ /cygdrive/d/Backup
rsync -va --delete /cygdrive/e/backup/Backup /cygdrive/%1
rem rsync -va --delete /cygdrive/e/home /cygdrive/%1 --exclude=home/debug
rsync -va --delete /cygdrive/e/svn /cygdrive/%1
rsync -va --delete /cygdrive/e/Tech /cygdrive/%1
rsync -va --delete /cygdrive/e/photo /cygdrive/%1
rsync -va --delete /cygdrive/e/KuGoo /cygdrive/%1
rsync -va --delete /cygdrive/e/src/work/lgame /cygdrive/%1/src/work --exclude=lgame/test --exclude=lgame/tmp --exclude=lgame/release --exclude=lgame/debug --cvs-exclude

echo rsync ok!
:End
echo end.
