@echo off
rem �j�ѤG�A�Ⱦ���x�۰ʳƥ�,
rem $Id$

set TMPPATH=%PATH%
set PATH=%PATH%;C:\Program Files\WinRAR

set logfile=..\big2_log.log
set logpath=D:\�j�ѤG��rlog�ƥ�\
D:
rem cd "D:\�j�ѤG�O��"
cd "D:\�j�ѤG����"
set delf=-df
rem set delf=

rem �e�@(n)��
echo wscript.echo dateadd("m",-4,date) > %tmp%\tmp.vbs  
for /f "tokens=1,2,3 delims=/- " %%i in ('cscript /nologo %tmp%\tmp.vbs') do (set y=%%i)
for /f "tokens=1,2,3 delims=/- " %%i in ('cscript /nologo %tmp%\tmp.vbs') do (set m=%%j)
rem set mmdd
if %m% LEQ 9 set m=0%m%
set lastmonth=%y%%m%

if not "%1" == "" set lastmonth=%1

echo %date% %time% �}�l�ƥ�%lastmonth%�j�ѤG��x
echo.

echo �}�l�ƥ� F12���~����...
cd "F12���~����\"
echo [%date% %time% %0] >> %logfile%
echo F12���~����\%lastmonth% backup... >> %logfile%
rar a %delf% %logpath%F12���~����\%lastmonth%.rar %lastmonth%\ > ..\tmp.txt
echo %date% %time% ok >> %logfile%
echo ���� %lastmonth%
echo.

echo �}�l�ƥ� �[�J���}�����...
cd "..\�[�J���}�����\"
echo %date% %time% >> %logfile%
echo �[�J���}�����\%lastmonth% backup... >> %logfile%
rar a %delf% %logpath%�[�J���}�����\%lastmonth%.rar %lastmonth%\ > ..\tmp.txt
echo %date% %time% ok >> %logfile%
echo ���� %lastmonth%
echo.

echo �}�l�ƥ� ���a�ǰe��ƿ��~...
cd "..\���a�ǰe��ƿ��~\"
echo %date% %time% >> %logfile%
echo ���a�ǰe��ƿ��~\%lastmonth% backup... >> %logfile%
rar a %delf% %logpath%���a�ǰe��ƿ��~\%lastmonth%.rar %lastmonth%\ > ..\tmp.txt
echo %date% %time% ok >> %logfile%
echo ���� %lastmonth%
echo.

echo �}�l�ƥ� ��Ѭ���...
cd "..\��Ѭ���\"
echo %date% %time% >> %logfile%
echo ��Ѭ���\%lastmonth% backup... >> %logfile%
rar a %delf% %logpath%��Ѭ���\%lastmonth%.rar %lastmonth%\ > ..\tmp.txt
echo %date% %time% ok >> %logfile%
echo ���� %lastmonth%
echo.

echo �}�l�ƥ� �n�J����...
cd "..\�n�J����\"
echo %date% %time% >> %logfile%
echo �n�J����\%lastmonth% backup... >> %logfile%
rar a %delf% %logpath%�n�J����\%lastmonth%.rar %lastmonth%\ > ..\tmp.txt
echo %date% %time% ok >> %logfile%
echo ���� %lastmonth%
echo.

echo �}�l�ƥ� �C���d�����...
cd "..\�C���d�����\"
echo %date% %time% >> %logfile%
echo �C���d�����\%lastmonth% backup... >> %logfile%
rar a %delf% %logpath%�C���d�����\%lastmonth%.rar %lastmonth%\ > ..\tmp.txt
echo %date% %time% ok >> %logfile%
echo ���� %lastmonth%
echo.

echo �}�l�ƥ� �C�����G����...
cd "..\�C�����G����\"
echo %date% %time% >> %logfile%
echo �C�����G����\%lastmonth% backup... >> %logfile%
rar a %delf% %logpath%�C�����G����\%lastmonth%.rar %lastmonth%\ > ..\tmp.txt
echo %date% %time% ok >> %logfile%
echo ���� %lastmonth%
echo.

echo �}�l�ƥ� �C�����~...
cd "..\�C�����~\"
echo %date% %time% >> %logfile%
echo �C�����~\%lastmonth% backup... >> %logfile%
rar a %delf% %logpath%�C�����~\%lastmonth%.rar %lastmonth%\ > ..\tmp.txt
echo %date% %time% ok >> %logfile%
echo ���� %lastmonth%
echo.

echo �}�l�ƥ� �C�����}�B�@...
cd "..\�C�����}�B�@\"
echo %date% %time% >> %logfile%
echo �C�����}�B�@\%lastmonth% backup... >> %logfile%
rar a %delf% %logpath%�C�����}�B�@\%lastmonth%.rar %lastmonth%\ > ..\tmp.txt
echo %date% %time% ok >> %logfile%
echo ���� %lastmonth%
echo.

echo �}�l�ƥ� ���{����...
cd "..\���{����\"
echo %date% %time% >> %logfile%
echo ���{����\%lastmonth% backup... >> %logfile%
rar a %delf% %logpath%���{����\%lastmonth%.rar %lastmonth%\ > ..\tmp.txt
echo %date% %time% ok >> %logfile%
echo ���� %lastmonth%
echo.

:end
set PATH=%TMPPATH%
echo end %date% %time% >> %logfile%
echo. >> %logfile%
cd D:\
