@echo off
set "SCRIPT_DIR=%~dp0biometric_driver\"
if exist "%SCRIPT_DIR%start_bridge.bat" (
    call "%SCRIPT_DIR%start_bridge.bat" %*
) else (
    echo [ERROR] start_bridge.bat not found in %SCRIPT_DIR%!
    pause
)
