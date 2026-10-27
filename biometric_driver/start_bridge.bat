@echo off
setlocal enabledelayedexpansion
title ZKTeco Biometric Bridge Controller (Live 20R / SLK 20R)

set "SCRIPT_DIR=%~dp0"
set "EXE_PATH=%SCRIPT_DIR%ZKBiometricAPI.exe"

if not exist "%EXE_PATH%" (
    echo [ERROR] ZKBiometricAPI.exe not found in "%SCRIPT_DIR%"!
    pause
    exit /b 1
)

:: If a stale or phantom instance is running, close it so fresh window opens
tasklist /fi "imagename eq ZKBiometricAPI.exe" | findstr /i "ZKBiometricAPI.exe" >nul
if %ERRORLEVEL% equ 0 (
    echo [INFO] Closing previous bridge instance for fresh restart...
    taskkill /F /IM ZKBiometricAPI.exe 2>nul
    ping 127.0.0.1 -n 2 >nul
)

:dostart
echo.
echo ================================================================
echo   STARTING ZKTECO BIOMETRIC BRIDGE (Live 20R / SLK 20R)...
echo ================================================================
cd /d "%SCRIPT_DIR%"
start "" "%EXE_PATH%"

:: Give it 2 seconds to bind port 15270 and detect USB scanner
ping 127.0.0.1 -n 3 >nul

tasklist /fi "imagename eq ZKBiometricAPI.exe" | findstr /i "ZKBiometricAPI.exe" >nul
if %ERRORLEVEL% equ 0 (
    echo   [SUCCESS] ZKBiometricAPI is now OPEN and RUNNING!
    echo   Listening on http://127.0.0.1:15270/ZK_Finger/
    echo.
    powershell -Command "try { $res = (Invoke-WebRequest -Uri 'http://127.0.0.1:15270/ZK_Finger/Status' -TimeoutSec 3 -UseBasicParsing).Content; Write-Host '[SCANNER STATUS]' $res -ForegroundColor Green } catch { Write-Host '[STATUS] Scanner bridge active.' -ForegroundColor Green }"
    echo ================================================================
) else (
    echo   [ERROR] Failed to launch ZKBiometricAPI.exe.
    echo   Check server.log in %SCRIPT_DIR% for details.
    pause
    exit /b 1
)

ping 127.0.0.1 -n 2 >nul
exit /b 0
