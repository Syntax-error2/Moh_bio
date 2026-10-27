@echo off
setlocal EnableDelayedExpansion
title MOB BIO - DTR Auto-Export Controller (Infirmary Server)

cd /d "%~dp0"

set "TASK_NAME_15=MOB_BIO_DTR_Export_15th"
set "TASK_NAME_30=MOB_BIO_DTR_Export_30th"
set "RUNNER_PATH=%~dp0run_auto_export.bat"
set "DEFAULT_TIME=17:00"

rem Handle command-line arguments if provided
if /i "%~1"=="on" goto :ENABLE_TASKS
if /i "%~1"=="enable" goto :ENABLE_TASKS
if /i "%~1"=="off" goto :DISABLE_TASKS
if /i "%~1"=="disable" goto :DISABLE_TASKS
if /i "%~1"=="status" goto :CHECK_STATUS
if /i "%~1"=="run" goto :RUN_NOW
if /i "%~1"=="test" goto :RUN_NOW

:MENU
cls
echo ====================================================================
echo       MOB BIO - DTR AUTO-EXPORT CONTROLLER (INFIRMARY SERVER)
echo ====================================================================
echo.

rem Check current status of both tasks
set "STATUS_15=NOT SCHEDULED"
set "STATUS_30=NOT SCHEDULED"

for /f "tokens=1,2,3 delims=," %%A in ('schtasks /query /tn "%TASK_NAME_15%" /fo CSV /nh 2^>nul') do (
    set "RAW_STAT=%%~C"
    if /i "!RAW_STAT!"=="Ready" set "STATUS_15=ON (Ready)"
    if /i "!RAW_STAT!"=="Running" set "STATUS_15=ON (Running)"
    if /i "!RAW_STAT!"=="Disabled" set "STATUS_15=OFF (Disabled)"
)

for /f "tokens=1,2,3 delims=," %%A in ('schtasks /query /tn "%TASK_NAME_30%" /fo CSV /nh 2^>nul') do (
    set "RAW_STAT=%%~C"
    if /i "!RAW_STAT!"=="Ready" set "STATUS_30=ON (Ready)"
    if /i "!RAW_STAT!"=="Running" set "STATUS_30=ON (Running)"
    if /i "!RAW_STAT!"=="Disabled" set "STATUS_30=OFF (Disabled)"
)

echo   15th of the Month Task : [ !STATUS_15! ]
echo   30th/End-of-Month Task : [ !STATUS_30! ]
echo   Runner Batch Script    : %RUNNER_PATH%
echo   Spare Target Folder    : %~dp0dtr_exports\
echo.
echo   NOTE: TURN ON ONLY on the Infirmary Server!
echo         The Main / New Server should keep this TURNED OFF.
echo ====================================================================
echo.
echo   [1] TURN ON Auto-Export  (Schedule at 5:00 PM on 15th and End-of-Month)
echo   [2] TURN OFF Auto-Export (Disable and remove schedule)
echo   [3] View Detailed Task Scheduler Info
echo   [4] Run Test Export NOW
echo   [5] View Auto-Export Log (dtr_exports\auto_export.log)
echo   [6] Open Spare Folder in File Explorer
echo   [0] Exit
echo.
echo ====================================================================
set /p "CHOICE=Enter your choice [0-6]: "

if "%CHOICE%"=="1" goto :ENABLE_TASKS
if "%CHOICE%"=="2" goto :DISABLE_TASKS
if "%CHOICE%"=="3" goto :CHECK_STATUS
if "%CHOICE%"=="4" goto :RUN_NOW
if "%CHOICE%"=="5" goto :VIEW_LOG
if "%CHOICE%"=="6" goto :OPEN_FOLDER
if "%CHOICE%"=="0" exit /b
goto :MENU

:ENABLE_TASKS
cls
echo ====================================================================
echo                   ENABLING DTR AUTO-EXPORT SCHEDULE
echo ====================================================================
echo.
echo Target Schedule:
echo   1. Runs on the 15th of every month at %DEFAULT_TIME% (5:00 PM)
echo   2. Runs on the 30th/End of every month at %DEFAULT_TIME% (5:00 PM)
echo.
echo Creating Task 1: %TASK_NAME_15% (15th of the month)...
schtasks /create /sc monthly /d 15 /tn "%TASK_NAME_15%" /tr "%RUNNER_PATH%" /st %DEFAULT_TIME% /f
if %errorlevel% neq 0 (
    echo [ERROR] Failed to create 15th schedule. If prompted, please run as administrator.
) else (
    echo [SUCCESS] 15th schedule enabled successfully.
)

echo.
echo Creating Task 2: %TASK_NAME_30% (End-of-Month / 30th)...
schtasks /create /sc monthly /mo LASTDAY /m * /tn "%TASK_NAME_30%" /tr "%RUNNER_PATH%" /st %DEFAULT_TIME% /f
if %errorlevel% neq 0 (
    echo [ERROR] Failed to create End-of-Month schedule. If prompted, please run as administrator.
) else (
    echo [SUCCESS] End-of-Month schedule enabled successfully.
)

echo.
echo ====================================================================
echo [STATUS] Auto-Export is now ACTIVE for the 15th and 30th!
echo Packages will automatically be saved into: %~dp0dtr_exports\
echo ====================================================================
echo.
if "%~1"=="" pause & goto :MENU
exit /b 0

:DISABLE_TASKS
cls
echo ====================================================================
echo                  DISABLING DTR AUTO-EXPORT SCHEDULE
echo ====================================================================
echo.
echo Deleting Task 1: %TASK_NAME_15%...
schtasks /delete /tn "%TASK_NAME_15%" /f >nul 2>&1
if %errorlevel% equ 0 (
    echo [SUCCESS] %TASK_NAME_15% has been removed.
) else (
    echo [INFO] %TASK_NAME_15% was not active.
)

echo Deleting Task 2: %TASK_NAME_30%...
schtasks /delete /tn "%TASK_NAME_30%" /f >nul 2>&1
if %errorlevel% equ 0 (
    echo [SUCCESS] %TASK_NAME_30% has been removed.
) else (
    echo [INFO] %TASK_NAME_30% was not active.
)

echo.
echo ====================================================================
echo [STATUS] Auto-Export schedule is now TURNED OFF.
echo No automated background exports will occur.
echo ====================================================================
echo.
if "%~1"=="" pause & goto :MENU
exit /b 0

:CHECK_STATUS
cls
echo ====================================================================
echo                 DETAILED SCHEDULE CONFIGURATION
echo ====================================================================
echo.
echo --- TASK 1: %TASK_NAME_15% ---
schtasks /query /tn "%TASK_NAME_15%" /fo LIST /v 2>nul
if %errorlevel% neq 0 echo Task is NOT installed or configured.
echo.
echo --- TASK 2: %TASK_NAME_30% ---
schtasks /query /tn "%TASK_NAME_30%" /fo LIST /v 2>nul
if %errorlevel% neq 0 echo Task is NOT installed or configured.
echo.
echo ====================================================================
if "%~1"=="" pause & goto :MENU
exit /b 0

:RUN_NOW
cls
echo ====================================================================
echo                     RUNNING AUTO-EXPORT NOW...
echo ====================================================================
echo.
call "%RUNNER_PATH%"
echo.
echo Recent log entries:
echo --------------------------------------------------------------------
if exist "dtr_exports\auto_export.log" (
    powershell -Command "Get-Content 'dtr_exports\auto_export.log' -Tail 15"
) else (
    echo Log file not created yet.
)
echo --------------------------------------------------------------------
echo.
if "%~1"=="" pause & goto :MENU
exit /b 0

:VIEW_LOG
cls
echo ====================================================================
echo                 AUTO-EXPORT LOG (auto_export.log)
echo ====================================================================
echo.
if exist "dtr_exports\auto_export.log" (
    type "dtr_exports\auto_export.log"
) else (
    echo No logs recorded yet. Run an export first.
)
echo.
echo ====================================================================
pause
goto :MENU

:OPEN_FOLDER
explorer "%~dp0dtr_exports"
goto :MENU
