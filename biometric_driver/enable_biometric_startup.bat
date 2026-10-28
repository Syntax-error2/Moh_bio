@echo off
setlocal enabledelayedexpansion
title Ultimate Biometric Bridge Auto-Startup Installer

echo ====================================================================
echo   ZKTeco Biometric Bridge Auto-Startup Installer (Live 20R / SLK 20R)
echo ====================================================================
echo.

set "CURRENT_DIR=%~dp0"
if "%CURRENT_DIR:~-1%"=="\" set "CURRENT_DIR=%CURRENT_DIR:~0,-1%"

if exist "%CURRENT_DIR%\biometric_driver\ZKBiometricAPI.exe" (
    set "DRIVER_DIR=%CURRENT_DIR%\biometric_driver"
) else if exist "%CURRENT_DIR%\ZKBiometricAPI.exe" (
    set "DRIVER_DIR=%CURRENT_DIR%"
) else (
    echo [ERROR] Could not find ZKBiometricAPI.exe!
    pause
    exit /b 1
)

set "EXE_PATH=%DRIVER_DIR%\ZKBiometricAPI.exe"
set "BAT_PATH=%DRIVER_DIR%\start_bridge.bat"

echo [1/4] Stopping any previous bridge instances...
taskkill /F /IM ZKBiometricAPI.exe 2>nul
ping 127.0.0.1 -n 2 >nul

del "%APPDATA%\Microsoft\Windows\Start Menu\Programs\Startup\ZKBiometricAPI.lnk" 2>nul
del "%APPDATA%\Microsoft\Windows\Start Menu\Programs\Startup\LaunchBiometric.vbs" 2>nul
powershell -Command "Unregister-ScheduledTask -TaskName 'BiometricBridge' -Confirm:$false" 2>nul
powershell -Command "Unregister-ScheduledTask -TaskName 'ZKTeco_Biometric_Bridge' -Confirm:$false" 2>nul

echo [2/4] Installing Windows Startup script for automatic boot loading...
set "VBS_FILE=%APPDATA%\Microsoft\Windows\Start Menu\Programs\Startup\LaunchBiometric.vbs"

(
    echo ' ZKTeco Biometric Bridge Auto-Start Script
    echo Set WshShell = CreateObject("WScript.Shell"^)
    echo Set objWMIService = GetObject("winmgmts:\\.\root\cimv2"^)
    echo.
    echo ' Wait 6 seconds for USB controller and drivers to enumerate after boot
    echo WScript.Sleep 6000
    echo.
    echo ' Prevent duplicate instances
    echo Set colProcesses = objWMIService.ExecQuery("Select * from Win32_Process Where Name = 'ZKBiometricAPI.exe'"^)
    echo If colProcesses.Count = 0 Then
    echo     WshShell.CurrentDirectory = "%DRIVER_DIR%"
    echo     WshShell.Run Chr(34^) ^& "%EXE_PATH%" ^& Chr(34^), 1, False
    echo End If
) > "%VBS_FILE%"

echo       Created: %VBS_FILE%
schtasks /create /tn "ZKTeco_Biometric_Bridge" /tr "wscript.exe \"%VBS_FILE%\"" /sc onlogon /rl highest /f >nul 2>&1

echo [3/4] Creating 1-Click Desktop Shortcut for operators...
powershell -Command "$ws = New-Object -ComObject WScript.Shell; $desk = [Environment]::GetFolderPath('Desktop'); $s = $ws.CreateShortcut(\"$desk\Start ZKTeco Biometric Bridge.lnk\"); $s.TargetPath = '%BAT_PATH%'; $s.WorkingDirectory = '%DRIVER_DIR%'; $s.Description = 'Start ZKTeco Biometric Bridge (Live 20R / SLK 20R)'; if (Test-Path '%EXE_PATH%') { $s.IconLocation = '%EXE_PATH%,0' }; $s.Save(); Write-Host '      Created Desktop Shortcut: Start ZKTeco Biometric Bridge.lnk' -ForegroundColor Green"

echo [4/4] Launching fresh ZKTeco Biometric Bridge window...
cd /d "%DRIVER_DIR%"
start "" "%EXE_PATH%"
ping 127.0.0.1 -n 3 >nul

echo.
echo ====================================================================
echo   [SUCCESS] Automatic Startup and Desktop Shortcut are Configured!
echo ====================================================================
echo.
echo   * The bridge will automatically launch every time Windows starts.
echo   * Both Live 20R and SLK 20R devices are auto-detected on port 15270.
echo   * You can test or restart anytime by clicking "Start ZKTeco Biometric Bridge" on your Desktop.
echo.
powershell -Command "try { $res = (Invoke-WebRequest -Uri 'http://127.0.0.1:15270/ZK_Finger/Status' -TimeoutSec 3 -UseBasicParsing).Content; Write-Host 'Scanner Status: ' $res -ForegroundColor Green } catch { Write-Host 'Scanner bridge window opened.' -ForegroundColor Green }"
echo.
pause