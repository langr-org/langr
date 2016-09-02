@echo off
rem 筒子服务器自动停机备份,重启
rem $Id$

set TMPPATH=%PATH%
set PATH=%PATH%;C:\Program Files\MySQL\MySQL Server 5.1\bin

set logfile=tonze_run.log
D:
cd "D:\Server\Server\"
echo [%date% %time% %cd% %0] >> %logfile%
rem start/stop tonze

if "%1" == "start" goto end_no_run

find "stop" tonze.lock >> null.tmp
if errorlevel 1 goto end_have_run
if errorlevel 0 goto end_no_run

rem start server
:end_no_run
echo start server now ... >> %logfile%
choice /C yn /M "60秒钟后开始启动筒子服务器,如果您认为准备好了请按'Y'跳过等待..." /T 60 /D y /N
echo start CsArbitServer.exe ... >> %logfile%
start CsArbitServer.exe
choice /C yn /T 5 /D y /N >> null.tmp
echo start CsArbitServer.exe ok >> %logfile%
echo start CsBridgeServer.exe ... >> %logfile%
start CsBridgeServer.exe
choice /C yn /T 3 /D y /N >> null.tmp
echo start CsBridgeServer.exe ok >> %logfile%
echo start CsGameServer.exe ... >> %logfile%
start CsGameServer.exe
echo start CsGameServer.exe ok >> %logfile%
echo start > tonze.lock
echo start server ok >> %logfile%
goto start_ok

rem stop server
:end_have_run
echo stop server now ... >> %logfile%
echo 现在开始停止筒子服务器,请您选择下面的一种方式,或者等待10秒钟后默认选择'1'并继续:
echo 	[1] 停止筒子服务器, 备份资料库, 并重新启动系统
echo 	[2] 停止筒子服务器, 备份资料库
echo 	[3] 只需停止筒子服务器
echo 	[n] 什么都不做,直接退出
choice /C 123n /M "stop server, plase choice:" /T 10 /D 1 /N
if errorlevel 4 goto endn
if errorlevel 3 goto c_3
if errorlevel 2 goto c_2
if errorlevel 1 goto c_1

:c_3
echo choose 3 >> %logfile%
set stop_flag=3
goto stop_process
:c_2
echo choose 2 >> %logfile%
set stop_flag=2
goto stop_process
:c_1
echo choose 1 >> %logfile%
set stop_flag=1
rem goto stop_process

rem stop process
:stop_process
echo stop > tonze.lock
echo stopping gameserver ... >> %logfile%
:killserver
choice /C yn /T 2 /D y /N >> null.tmp
rem taskkill /im CsGameServer.exe 
taskkill /im CsGameServer.exe -f >> %logfile%
if errorlevel 128 goto endkill
if errorlevel 0 goto killserver
:endkill
taskkill /im CsBridgeServer.exe -f >> %logfile%
taskkill /im CsArbitServer.exe -f >> %logfile%

if %stop_flag% == 3 goto end
rem if %stop_flag% == 2 goto dump_mysql
rem if %stop_flag% == 1 goto dump_mysql

rem dump mysql
:dump_mysql
rem for /f %%i in ('date /t') do set filename=%%i
for /f "tokens=1-4 delims=- " %%a in ('date /t') do (set filename=%%a%%b%%c)
set filename=D:\tonze_db_bak\cookpotdb_%filename%.sql
echo mysqldump to %filename% ... %date% %time% >> %logfile%
mysqldump -uroot -p22787789 cookpotdb > %filename%
echo mysqldump ok %date% %time% >> %logfile%
echo windows update >> %logfile%
rem start wupdmgr

if %stop_flag% == 2 goto end
rem if %stop_flag% == 1 goto reboot

rem reboot computer
:reboot
echo reboot now >> %logfile%
shutdown -r -f -t 15
goto end

:endn
echo stop cancel. >> %logfile%

:start_ok
:end
set PATH=%TMPPATH%
echo end %date% %time% >> %logfile%
echo. >> %logfile%
