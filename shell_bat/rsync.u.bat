@echo off
D:
cd D:\Program Files\cwRsync\bin
rem rsync -va /cygdrive/e/backup/Backup /cygdrive/h/
rem #crontab ͬ����̨ web �ϵ��ļ�
rem #WWW Web Site rsync l->r
rem #*/5 * * * * rsync -rvlHpogDtS /home/htdocs/web --exclude=web/data --exclude=web/html --exclude=web/tmplCache root@210.59.147.153:/home/htdocs 2>&1 > /dev/null
rem #(window) ����ͬ�� ע��Ҫ����Ŀ¼��б�ߺ�ûб��ʱ�ǲ�ͬ��
rem #rsync -va work/ /cygdrive/h/backup/Backup/work
rem #rsync -va /cygdrive/d/Backup/work /cygdrive/h/backup/Backup
rem #(window) Զ��ͬ��
rem #rsync -va --password-file=/cygdrive/d/rsync.secret /cygdrive/d/Backup/ hua@192.168.1.5::backup_mode
rem #rsync -va --password-file=/cygdrive/d/rsync.secret hua@192.168.1.5::backup_mode/ /cygdrive/d/Backup
rem rsync -va --delete /cygdrive/e/work /cygdrive/h/w --exclude=*.svn/*
rem --cvs-exclude ���� cvs, svn �Ȱ汾���Ƴ���� .svn Ŀ¼

rem rsync.bat dtou --exclude=xxx
if not "%1" == "dtou" goto EndDTOU
rem dtou: Ӳ�̵�U��
	echo rsync: dtou Ӳ�̵�U��! svn %2
	rsync -va --delete /cygdrive/e/src/work/lgame /cygdrive/j/work --exclude=lgame/test --exclude=lgame/tmp --exclude=lgame/release --exclude=lgame/debug --cvs-exclude %2
:EndDTOU
if not "%1" == "utod" goto EndUTOD
rem utod: U�̵�Ӳ��
	echo rsync: utod U�̵�Ӳ��! svn %2
	rsync -va --delete /cygdrive/j/work/lgame /cygdrive/e/src/work --exclude=lgame/test --exclude=lgame/tmp --exclude=lgame/release --exclude=lgame/debug --cvs-exclude %2
:EndUTOD
if not "%1" == "" goto EndBat
	echo rsync.bat dtou ["--exclude=lgame/resource"] Ӳ�̵�U��!
	echo rsync.bat utod U�̵�Ӳ��!
:EndBat

echo rsync ok!
E:
rem pause
rem exit
