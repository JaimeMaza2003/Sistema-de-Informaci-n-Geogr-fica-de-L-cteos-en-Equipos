@echo off
title Servidor SIG Lacteos Cotopaxi
echo ========================================================
echo   Iniciando Servidor Web SIG Lacteos Cotopaxi
echo ========================================================
echo.
echo Abriendo en: http://localhost:8000
echo.
echo Presiona Ctrl + C para detener el servidor.
echo ========================================================
echo.

start http://localhost:8000

"C:\xampp\php\php.exe" -S 0.0.0.0:8000 -t public

pause
