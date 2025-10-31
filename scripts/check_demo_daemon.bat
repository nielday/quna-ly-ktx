@echo off
echo ========================================
echo Utility Demo Daemon - Status
echo ========================================
echo.

cd /d "%~dp0.."

if not exist "logs\utility_demo_daemon.pid" (
    echo [STATUS] Daemon khong chay
    pause
    exit /b 0
)

set /p PID=<logs\utility_demo_daemon.pid

REM Kiểm tra process có đang chạy không
tasklist /FI "PID eq %PID%" 2>nul | find /I "%PID%" >nul

if errorlevel 1 (
    echo [STATUS] Daemon khong chay (PID %PID% khong ton tai)
    echo Dang xoa file lock...
    if exist "logs\utility_demo_daemon.lock" del /F /Q "logs\utility_demo_daemon.lock"
    if exist "logs\utility_demo_daemon.pid" del /F /Q "logs\utility_demo_daemon.pid"
) else (
    echo [STATUS] Daemon dang chay
    echo PID: %PID%
    echo.
    echo De dung: chay scripts\stop_demo_daemon.bat
)

echo.
pause

