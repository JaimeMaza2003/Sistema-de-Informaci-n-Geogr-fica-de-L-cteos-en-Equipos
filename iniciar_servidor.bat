@echo off
title Servidor SIG Lacteos Cotopaxi
echo ========================================================
echo   Iniciando Servidor Web SIG Lacteos Cotopaxi
echo ========================================================
echo.
echo Abriendo en: http://localhost:8080
echo En red local: http://192.168.0.133:8080
echo.
echo Presiona Ctrl + C para detener el servidor.
echo ========================================================
echo.

start http://localhost:8080

if exist "C:\xampp3.36\php\php.exe" (
    "C:\xampp3.36\php\php.exe" -S 0.0.0.0:8080 -t public
) else if exist "C:\xampp\php\php.exe" (
    "C:\xampp\php\php.exe" -S 0.0.0.0:8080 -t public
) else (
    php -S 0.0.0.0:8080 -t public
)

pause
