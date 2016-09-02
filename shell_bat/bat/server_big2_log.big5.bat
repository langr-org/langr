@echo off
rem 大老二服務器日誌自動備份,
rem $Id$

set TMPPATH=%PATH%
set PATH=%PATH%;C:\Program Files\WinRAR

set logfile=..\big2_log.log
set logpath=D:\大老二文字log備份\
D:
rem cd "D:\大老二記錄"
cd "D:\大老二紀錄"
set delf=-df
rem set delf=

rem 前一(n)月
echo wscript.echo dateadd("m",-4,date) > %tmp%\tmp.vbs  
for /f "tokens=1,2,3 delims=/- " %%i in ('cscript /nologo %tmp%\tmp.vbs') do (set y=%%i)
for /f "tokens=1,2,3 delims=/- " %%i in ('cscript /nologo %tmp%\tmp.vbs') do (set m=%%j)
rem set mmdd
if %m% LEQ 9 set m=0%m%
set lastmonth=%y%%m%

if not "%1" == "" set lastmonth=%1

echo %date% %time% 開始備份%lastmonth%大老二日誌
echo.

echo 開始備份 F12錯誤紀錄...
cd "F12錯誤紀錄\"
echo [%date% %time% %0] >> %logfile%
echo F12錯誤紀錄\%lastmonth% backup... >> %logfile%
rar a %delf% %logpath%F12錯誤紀錄\%lastmonth%.rar %lastmonth%\ > ..\tmp.txt
echo %date% %time% ok >> %logfile%
echo 完成 %lastmonth%
echo.

echo 開始備份 加入離開桌紀錄...
cd "..\加入離開桌紀錄\"
echo %date% %time% >> %logfile%
echo 加入離開桌紀錄\%lastmonth% backup... >> %logfile%
rar a %delf% %logpath%加入離開桌紀錄\%lastmonth%.rar %lastmonth%\ > ..\tmp.txt
echo %date% %time% ok >> %logfile%
echo 完成 %lastmonth%
echo.

echo 開始備份 玩家傳送資料錯誤...
cd "..\玩家傳送資料錯誤\"
echo %date% %time% >> %logfile%
echo 玩家傳送資料錯誤\%lastmonth% backup... >> %logfile%
rar a %delf% %logpath%玩家傳送資料錯誤\%lastmonth%.rar %lastmonth%\ > ..\tmp.txt
echo %date% %time% ok >> %logfile%
echo 完成 %lastmonth%
echo.

echo 開始備份 聊天紀錄...
cd "..\聊天紀錄\"
echo %date% %time% >> %logfile%
echo 聊天紀錄\%lastmonth% backup... >> %logfile%
rar a %delf% %logpath%聊天紀錄\%lastmonth%.rar %lastmonth%\ > ..\tmp.txt
echo %date% %time% ok >> %logfile%
echo 完成 %lastmonth%
echo.

echo 開始備份 登入紀錄...
cd "..\登入紀錄\"
echo %date% %time% >> %logfile%
echo 登入紀錄\%lastmonth% backup... >> %logfile%
rar a %delf% %logpath%登入紀錄\%lastmonth%.rar %lastmonth%\ > ..\tmp.txt
echo %date% %time% ok >> %logfile%
echo 完成 %lastmonth%
echo.

echo 開始備份 遊戲卡住紀錄...
cd "..\遊戲卡住紀錄\"
echo %date% %time% >> %logfile%
echo 遊戲卡住紀錄\%lastmonth% backup... >> %logfile%
rar a %delf% %logpath%遊戲卡住紀錄\%lastmonth%.rar %lastmonth%\ > ..\tmp.txt
echo %date% %time% ok >> %logfile%
echo 完成 %lastmonth%
echo.

echo 開始備份 遊戲結果紀錄...
cd "..\遊戲結果紀錄\"
echo %date% %time% >> %logfile%
echo 遊戲結果紀錄\%lastmonth% backup... >> %logfile%
rar a %delf% %logpath%遊戲結果紀錄\%lastmonth%.rar %lastmonth%\ > ..\tmp.txt
echo %date% %time% ok >> %logfile%
echo 完成 %lastmonth%
echo.

echo 開始備份 遊戲錯誤...
cd "..\遊戲錯誤\"
echo %date% %time% >> %logfile%
echo 遊戲錯誤\%lastmonth% backup... >> %logfile%
rar a %delf% %logpath%遊戲錯誤\%lastmonth%.rar %lastmonth%\ > ..\tmp.txt
echo %date% %time% ok >> %logfile%
echo 完成 %lastmonth%
echo.

echo 開始備份 遊戲離開處罰...
cd "..\遊戲離開處罰\"
echo %date% %time% >> %logfile%
echo 遊戲離開處罰\%lastmonth% backup... >> %logfile%
rar a %delf% %logpath%遊戲離開處罰\%lastmonth%.rar %lastmonth%\ > ..\tmp.txt
echo %date% %time% ok >> %logfile%
echo 完成 %lastmonth%
echo.

echo 開始備份 歷程紀錄...
cd "..\歷程紀錄\"
echo %date% %time% >> %logfile%
echo 歷程紀錄\%lastmonth% backup... >> %logfile%
rar a %delf% %logpath%歷程紀錄\%lastmonth%.rar %lastmonth%\ > ..\tmp.txt
echo %date% %time% ok >> %logfile%
echo 完成 %lastmonth%
echo.

:end
set PATH=%TMPPATH%
echo end %date% %time% >> %logfile%
echo. >> %logfile%
cd D:\
