@echo off
echo ========================================
echo    Hệ thống quản lý ký túc xá
echo ========================================
echo.
echo Đang khởi động server PHP...
echo Server sẽ chạy tại: http://localhost:8000
echo.
echo Nhấn Ctrl+C để dừng server
echo ========================================
echo.

cd /d "%~dp0"

php -S localhost:8000 -t .

pause
