@echo off
rem ���l�A�Ⱦ���x�۰ʳƥ�,
rem $Id$

set TMPPATH=%PATH%
set PATH=%PATH%;C:\Program Files\WinRAR

set logfile=tonze_log.log
set logpath=D:\tonze_log_bak\
D:
cd "D:\Server\Server\"

rem ���
for /f "tokens=1-4 delims=- " %%a in ('date /t') do (set today=%%a%%b%%c)
rem �e�@��
echo wscript.echo dateadd("d",-1,date) > %tmp%\tmp.vbs  
for /f "tokens=1,2,3 delims=/- " %%i in ('cscript /nologo %tmp%\tmp.vbs') do (set y=%%i)
for /f "tokens=1,2,3 delims=/- " %%i in ('cscript /nologo %tmp%\tmp.vbs') do (set m=%%j)
for /f "tokens=1,2,3 delims=/- " %%i in ('cscript /nologo %tmp%\tmp.vbs') do (set d=%%k)
rem set mmdd
rem if %m% LEQ 9 set m=0%m%
rem if %d% LEQ 9 set d=0%d%
set lastday=%y%%m%%d%

if not "%1" == "" set lastday=%1

echo %date% %time% �}�l�ƥ�%lastday%���l��x
echo.

echo �}�l tonze_server\GroupLog...
rem D:\Server\Server
echo [%date% %time% %0] >> %logfile%
echo GroupLog_%lastday% backup... >> %logfile%
rar a -r -df -ta%lastday%000000 -tb%lastday%235959 %logpath%GroupLog_%lastday%.rar GroupLog\ > ..\tmp.txt
echo %date% %time% ok >> %logfile%
echo ���� GroupLog_%lastday%
echo.

:end
set PATH=%TMPPATH%
echo end %date% %time% >> %logfile%
echo. >> %logfile%
