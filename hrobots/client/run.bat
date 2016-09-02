@echo off
rem run client
rem pecl php_hcrypt_x32v1.dll php v5.3.13
rem $Id: run.bat 86 2014-06-04 11:16:39Z huanghua $
rem set rpath=D:\wamp\web\hrobots\client
rem d:\wamp\bin\php\php5.3.13\php index.php mouser

set rpath=.\
set rfile=%rpath%php5.3.13\php.exe -f

:start
rem %rfile% %rpath%index.php mouser
%rfile% %rpath%index.php

if errorlevel 9 goto start

pause

