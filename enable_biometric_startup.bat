@echo off
echo Ultimate Biometric Bridge Auto-Starter Installer (MOH BIO)...

:: 1. Clean up previous attempts
del "%APPDATA%\Microsoft\Windows\Start Menu\Programs\Startup\LaunchBiometric.vbs" 2>nul
powershell -Command "Unregister-ScheduledTask -TaskName 'BiometricBridge' -Confirm:$false" 2>nul

:: 2. Create the VBScript in the Startup folder
set "vbsFile=%APPDATA%\Microsoft\Windows\Start Menu\Programs\Startup\LaunchBiometric.vbs"

echo Set WshShell = CreateObject("WScript.Shell") > "%vbsFile%"
:: Wait 15 seconds (15000 ms) for USB drivers to fully initialize after a brownout
echo WScript.Sleep 15000 >> "%vbsFile%"
:: Set exact working directory so it finds its files in MOH_BIO
echo WshShell.CurrentDirectory = "C:\xampp\htdocs\moh_bio\biometric_driver" >> "%vbsFile%"
:: Launch the program (1 means display the window normally)
echo WshShell.Run "C:\xampp\htdocs\moh_bio\biometric_driver\ZKBiometricAPI.exe", 1, False >> "%vbsFile%"

echo.
echo SUCCESS! The MOH BIO auto-starter has been installed.
pause