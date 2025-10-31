@echo off
echo ========================================
echo Utility Demo Daemon - Start
echo ========================================
echo.

cd /d "%~dp0.."

REM Kiểm tra daemon đã chạy chưa
if exist "logs\utility_demo_daemon.lock" (
    echo Daemon da chay! Dung process cu truoc khi start lai.
    echo De dung: chay scripts\stop_demo_daemon.bat
    pause
    exit /b 1
)

REM Tạo thư mục logs nếu chưa có
if not exist "logs" mkdir logs

echo Dang khoi dong daemon...
echo.

REM Chạy daemon ở background (Windows)
start /B "" php scripts\utility_demo_daemon.php

timeout /t 2 /nobreak >nul

REM Kiểm tra xem đã chạy chưa
if exist "logs\utility_demo_daemon.lock" (
    set /p PID=<logs\utility_demo_daemon.pid
    echo.
    echo [SUCCESS] Daemon da khoi dong thanh cong!
    echo PID: %PID%
    echo.
    echo De xem log: tail -f logs\app_*.log
    echo De dung: chay scripts\stop_demo_daemon.bat
) else (
    echo [ERROR] Khong the khoi dong daemon!
    echo Kiem tra lai file logs de xem loi.
)

echo.
pause

