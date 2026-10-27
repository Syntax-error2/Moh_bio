@echo off
rem MOB BIO - Silent Runner for Scheduled Auto-Export
cd /d "%~dp0"

rem Auto-detect PHP executable
set "PHP_BIN=C:\xampp\php\php.exe"
if not exist "%PHP_BIN%" (
    if exist "%~dp0..\..\php\php.exe" set "PHP_BIN=%~dp0..\..\php\php.exe"
)
if not exist "%PHP_BIN%" set "PHP_BIN=php.exe"

if not exist "dtr_exports" mkdir "dtr_exports"

rem Append run header
echo [%date% %time%] Scheduled auto-export started >> "dtr_exports\auto_export.log"

rem Execute auto-export script
"%PHP_BIN%" auto_export_dtr.php >> "dtr_exports\auto_export.log" 2>&1

echo [%date% %time%] Scheduled auto-export finished >> "dtr_exports\auto_export.log"
echo. >> "dtr_exports\auto_export.log"
