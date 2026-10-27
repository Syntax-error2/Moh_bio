@echo off
setlocal EnableDelayedExpansion

:: ============================================================================
:: PHP2Shell (CVE-2026-109618 & CVE-2026-109619) 1-Click Windows Fix
:: For XAMPP, WampServer, Laragon, Herd, IIS, and standalone PHP on Windows
:: ============================================================================

title PHP Security Fix - CVE-2026-109618 and CVE-2026-109619

:: Auto-elevate to Administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo ================================================================
    echo Requesting Administrator privileges...
    echo ================================================================
    powershell -Command "Start-Process cmd -ArgumentList '/c \"\"%~f0\"\"' -Verb RunAs"
    exit /b
)

echo ================================================================
echo  PHP2Shell (CVE-2026-109618 ^& CVE-2026-109619) Remediation Tool
echo  Target: Windows Server / XAMPP / Web Environments
echo ================================================================
echo.

set "SCRIPT_DIR=%~dp0"
set "PS_SCRIPT=%SCRIPT_DIR%fix_php2shell.ps1"

if exist "%PS_SCRIPT%" (
    powershell -NoProfile -ExecutionPolicy Bypass -File "%PS_SCRIPT%"
) else (
    echo [!] Running inline PowerShell fix...
    powershell -NoProfile -ExecutionPolicy Bypass -Command ^
        "$paths = @('C:\xampp\php\php.ini', 'C:\php\php.ini', 'C:\laragon\bin\php\*\php.ini', 'C:\wamp64\bin\php\*\php.ini', \"$env:USERPROFILE\.config\herd\bin\*\php.ini\");" ^
        "foreach ($p in $paths) {" ^
        "    Get-ChildItem -Path $p -ErrorAction SilentlyContinue | ForEach-Object {" ^
        "        $ini = $_.FullName;" ^
        "        Write-Host \"[*] Checking $ini\";" ^
        "        Copy-Item $ini \"$ini.bak_$(Get-Date -Format 'yyyyMMdd_HHmmss')\" -Force;" ^
        "        $c = Get-Content $ini -Raw;" ^
        "        if ($c -match '(?m)^\s*session\.upload_progress\.enabled\s*=\s*(Off|0|off)') {" ^
        "            Write-Host '    [OK] Already disabled.' -ForegroundColor Green;" ^
        "        } elseif ($c -match '(?m)^\s*;?\s*session\.upload_progress\.enabled\s*=.*$') {" ^
        "            $c = [regex]::Replace($c, '(?m)^\s*;?\s*session\.upload_progress\.enabled\s*=.*$', 'session.upload_progress.enabled = Off');" ^
        "            Set-Content $ini $c -NoNewline;" ^
        "            Write-Host '    [FIXED] Set session.upload_progress.enabled = Off' -ForegroundColor Green;" ^
        "        } else {" ^
        "            Add-Content $ini \"`r`nsession.upload_progress.enabled = Off\";" ^
        "            Write-Host '    [FIXED] Appended session.upload_progress.enabled = Off' -ForegroundColor Green;" ^
        "        }" ^
        "    }" ^
        "}"
)

echo.
echo ================================================================
echo Attempting to restart active Apache and IIS services...
echo ================================================================

:: Restart Apache service if registered
net stop Apache2.4 >nul 2>&1
if %errorLevel% equ 0 (
    net start Apache2.4 >nul 2>&1
    echo [+] Apache2.4 service restarted successfully.
)

net stop Apache >nul 2>&1
if %errorLevel% equ 0 (
    net start Apache >nul 2>&1
    echo [+] Apache service restarted successfully.
)

:: Restart IIS if running
sc query W3SVC | find "RUNNING" >nul 2>&1
if %errorLevel% equ 0 (
    echo [*] Restarting IIS (iisreset)...
    iisreset /restart >nul 2>&1
    echo [+] IIS restarted successfully.
)

echo.
echo ================================================================
echo Verification:
echo ================================================================
where php >nul 2>&1
if %errorLevel% equ 0 (
    php -v
    echo.
    php -r "echo 'session.upload_progress.enabled: ' . (ini_get('session.upload_progress.enabled') ? 'ENABLED (Vulnerable)' : 'DISABLED (Secure)') . PHP_EOL;"
) else (
    echo Note: PHP is not in system PATH, but php.ini files were patched.
)

echo.
echo ================================================================
echo [DONE] Remediation complete on this server.
echo ================================================================
echo.
pause
