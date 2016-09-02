@echo off
rem 跑跑服务器日志自动备份,
rem $Id$

set TMPPATH=%PATH%
set PATH=%PATH%;C:\Program Files\WinRAR

set logfile=..\pp_log.log
set logpath=D:\race_server\
D:
cd "D:\race_server\"

rem 当天
for /f "tokens=1-4 delims=- " %%a in ('date /t') do (set today=%%a%%b%%c)
rem 前一天
echo wscript.echo dateadd("d",-1,date) > %tmp%\tmp.vbs  
for /f "tokens=1,2,3 delims=/- " %%i in ('cscript /nologo %tmp%\tmp.vbs') do (set y=%%i)
for /f "tokens=1,2,3 delims=/- " %%i in ('cscript /nologo %tmp%\tmp.vbs') do (set m=%%j)
for /f "tokens=1,2,3 delims=/- " %%i in ('cscript /nologo %tmp%\tmp.vbs') do (set d=%%k)
if %m% LEQ 9 set m=0%m%
if %d% LEQ 9 set d=0%d%
set lastday=%y%%m%%d%

echo %date% %time% 开始备份%lastday%跑跑日志
echo.

echo 开始 race_server...
rem D:\race_server\race_server
echo [%date% %time% %cd% %0] >> %logfile%
cd "race_server"
echo race_server\GameResult_%lastday% backup... >> %logfile%
rar a -df -ta%lastday%000000 -tb%lastday%235959 %logpath%server_GameResult_%lastday%.rar GameResult\ > ..\tmp.txt
echo %date% %time% ok >> %logfile%
echo 完成 GameResult_%lastday%
echo race_server\Log_%lastday% backup... >> %logfile%
rar a -df -ta%lastday%000000 -tb%lastday%235959 %logpath%server_Log_%lastday%.rar Log\ > ..\tmp.txt
echo %date% %time% ok >> %logfile%
echo 完成 Log_%lastday%
echo.

echo 开始 race_server2...
rem D:\race_server\race_server2
cd "..\race_server2"
echo %date% %time% >> %logfile%
echo race_server2\GameResult_%lastday% backup... >> %logfile%
rar a -df -ta%lastday%000000 -tb%lastday%235959 %logpath%server2_GameResult_%lastday%.rar GameResult\ > ..\tmp.txt
echo %date% %time% ok >> %logfile%
echo 完成 GameResult_%lastday%
echo race_server2\Log_%lastday% backup... >> %logfile%
rar a -df -ta%lastday%000000 -tb%lastday%235959 %logpath%server2_Log_%lastday%.rar Log\ > ..\tmp.txt
echo %date% %time% ok >> %logfile%
echo 完成 Log_%lastday%
echo.

echo 开始 race_server3...
rem D:\race_server\race_server3
cd "..\race_server3"
echo %date% %time% >> %logfile%
echo race_server3\GameResult_%lastday% backup... >> %logfile%
rar a -df -ta%lastday%000000 -tb%lastday%235959 %logpath%server3_GameResult_%lastday%.rar GameResult\ > ..\tmp.txt
echo %date% %time% ok >> %logfile%
echo 完成 GameResult_%lastday%
echo race_server3\Log_%lastday% backup... >> %logfile%
rar a -df -ta%lastday%000000 -tb%lastday%235959 %logpath%server3_Log_%lastday%.rar Log\ > ..\tmp.txt
echo %date% %time% ok >> %logfile%
echo 完成 Log_%lastday%
echo.

echo 开始 race_server4...
rem D:\race_server\race_server4
cd "..\race_server4"
echo %date% %time% >> %logfile%
echo race_server4\GameResult_%lastday% backup... >> %logfile%
rar a -df -ta%lastday%000000 -tb%lastday%235959 %logpath%server4_GameResult_%lastday%.rar GameResult\ > ..\tmp.txt
echo %date% %time% ok >> %logfile%
echo 完成 GameResult_%lastday%
echo race_server4\Log_%lastday% backup... >> %logfile%
rar a -df -ta%lastday%000000 -tb%lastday%235959 %logpath%server4_Log_%lastday%.rar Log\ > ..\tmp.txt
echo %date% %time% ok >> %logfile%
echo 完成 Log_%lastday%
echo.

echo 开始 race_server5...
rem D:\race_server\race_server5
cd "..\race_server5"
echo %date% %time% >> %logfile%
echo race_server5\GameResult_%lastday% backup... >> %logfile%
rar a -df -ta%lastday%000000 -tb%lastday%235959 %logpath%server5_GameResult_%lastday%.rar GameResult\ > ..\tmp.txt
echo %date% %time% ok >> %logfile%
echo 完成 GameResult_%lastday%
echo race_server5\Log_%lastday% backup... >> %logfile%
rar a -df -ta%lastday%000000 -tb%lastday%235959 %logpath%server5_Log_%lastday%.rar Log\ > ..\tmp.txt
echo %date% %time% ok >> %logfile%
echo 完成 Log_%lastday%
echo.

:end
set PATH=%TMPPATH%
echo end %date% %time% >> %logfile%
echo. >> %logfile%
