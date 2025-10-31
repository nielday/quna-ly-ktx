@echo off
echo ========================================
echo Utility Demo Daemon - Stop
echo ========================================
echo.

cd /d "%~dp0.."

if not exist "logs\utility_demo_daemon.pid" (
    echo Daemon khong chay!
    pause
    exit /b 0
)

set /p PID=<logs\utility_demo_daemon.pid

echo Dang dung daemon (PID: %PID%)...
echo.

REM Kill process trên Windows
taskkill /PID %PID% /F >nul 2>&1

REM Đợi một chút
timeout /t 1 /nobreak >nul

REM Xóa lock và pid file
if exist "logs\utility_demo_daemon.lock" del /F /Q "logs\utility_demo_daemon.lock"
if exist "logs\utility_demo_daemon.pid" del /F /Q "logs\utility_demo_daemon.pid"

REM Kiểm tra lại
tasklist /FI "PID eq %PID%" 2>nul | find /I "%PID%" >nul
if errorlevel 1 (
    echo [SUCCESS] Da dung daemon thanh cong!
) else (
    echo [WARNING] Process van con chay. Thu kill lai...
    taskkill /PID %PID% /F /T
)

echo.
pause

